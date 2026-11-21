# SERVISA (Smart Personal Vehicle Maintenance & AI Diagnostics)
> Standard & Guidelines for AI Agents and Developers (SEVENT X Telkom University Purwokerto - SDGs 12 & 3).

Dokumen ini adalah **aturan utama (single source of truth)** untuk Claude Code, Claude Desktop, Cursor, dan AI Coding Assistant saat bekerja di repositori ini.

---

## 1. Arsitektur & Aturan Teknis Monolitik
- **Framework**: Laravel 11 (Monolith) + Blade + Tailwind CSS + Alpine.js + Vite.
- **Database**: MySQL / MariaDB (relasional).
- **Styling**: Tailwind CSS v3.
  - **DILARANG** menggunakan hardcoded hex colors seperti `#B83A74`, `#0B2A78`, `#F5DBE8`, dsb.
  - **WAJIB** menggunakan CSS Variables utilities yang sudah dikonfigurasi:
    - Main: `text-brand-main`, `bg-brand-main`
    - Secondary: `text-brand-secondary`, `bg-brand-secondary/40`
    - Third: `text-brand-third`, `bg-brand-third`
    - Dark mode: `dark:bg-slate-800`, `dark:text-brand-accent`, `dark:bg-[#1E293B]`
  - Aplikasi memiliki **2 Tema Warna Dinamis** (`theme-pink` & `theme-blue`) serta **Dark Mode** (`.dark`) yang dikontrol melalui Alpine.js & localStorage. Semua komponen harus adaptif terhadap kedua tema tersebut.
- **Icons**: Gunakan Lucide Icons via inline SVG atau `data-lucide="icon-name"`.

---

## 2. Struktur Tim & Pembagian Tugas (Feature Scopes)
Setiap developer/agent **HANYA BOLEH** mengubah file yang berada di dalam scope tugasnya. Dilarang merombak master layout `resources/views/layouts/app.blade.php` atau skema database utama tanpa persetujuan Lead Dev.

### **Menu 1: Dashboard Servis & Smart Odometer (Dev 1 - Lead)**
- **Branch**: `feat/menu-1-dashboard`
- **Scope File**:
  - `resources/views/pages/dashboard.blade.php`
  - `app/Http/Controllers/DashboardController.php`
- **Tanggung Jawab**:
  - Smart Odometer real-time decay engine display (update harian otomatis + manual modal).
  - 5 Part Vital Cards (Oli Mesin, Oli Gardan, Busi, Kampas Rem, V-Belt) dengan status visual (Aman/Waspada/Kritis).
  - Kalender Servis interaktif (rekomendasi jadwal servis AI).

### **Menu 2: AI Vision Speedometer Scanner (Dev 2)**
- **Branch**: `feat/menu-2-scan-ai`
- **Scope File**:
  - `resources/views/pages/diagnosa.blade.php`
  - `app/Http/Controllers/DiagnosaController.php`
  - `app/Services/GeminiVisionService.php`
- **Tanggung Jawab**:
  - Viewfinder kamera live / upload foto speedometer dari galeri.
  - Integrasi API Gemini Flash untuk ekstraksi teks Odometer (OCR) dan deteksi lampu indikator MIL (Malfunction Indicator Lamp).
  - Kartu Protokol Darurat & riwayat scan diagnosis.

### **Menu 3: Kalkulator Kerugian / Cost of Neglect (Dev 3)**
- **Branch**: `feat/menu-3-kalkulator`
- **Scope File**:
  - `resources/views/pages/calculator.blade.php`
  - `app/Http/Controllers/CalculatorController.php`
- **Tanggung Jawab**:
  - Slider interaktif keterlambatan servis (1 - 6 bulan).
  - Komparasi Biaya: Box Hijau (Rutin Servis) vs Box Merah (Efek Domino Kerusakan).
  - Analisis finansial edukatif pencegahan pemborosan (SDG 12).

### **Menu 4: Buku Paspor Servis Terverifikasi & Ekspor PDF (Dev 4)**
- **Branch**: `feat/menu-4-riwayat-pdf`
- **Scope File**:
  - `resources/views/pages/history.blade.php`
  - `app/Http/Controllers/HistoryController.php`
  - `resources/views/pdf/service-passport.blade.php`
- **Tanggung Jawab**:
  - Tab Log Servis Bengkel & Tab Riwayat Diagnosa AI.
  - Kartu ringkasan total pengeluaran dan status kesehatan motor.
  - Fitur unduh / cetak PDF Buku Paspor Servis resmi.

---

## 3. Git Workflow & Aturan Branching
- **Branch `main`**: Branch produksi/staging utama. Dilindungi (**Dilarang keras push langsung ke `main`**).
- **Branch Fitur**: Dibuat **langsung dari `main`** (tanpa branch `dev` agar alur kerja cepat dan ramping).
  - Format penamaan branch fitur:
    - `feat/menu-1-dashboard`
    - `feat/menu-2-scan-ai`
    - `feat/menu-3-kalkulator`
    - `feat/menu-4-riwayat-pdf`
    - `fix/nama-bug` (untuk perbaikan bug tertentu)

### Alur Kerja:
1. Selalu pastikan branch `main` lokal Anda paling baru:
   ```bash
   git checkout main && git pull origin main
   ```
2. Buat dan masuk ke branch fitur baru langsung dari `main`:
   ```bash
   git checkout -b feat/menu-X-nama-fitur
   ```
3. Kerjakan fitur sesuai scope file masing-masing. Jangan memodifikasi file di luar modul.
4. Lakukan testing dan build asset:
   ```bash
   npm run build
   ```
5. Commit perubahan menggunakan format standar (`[ADD]`, `[IMP]`, `[FIX]`, dsb.).
6. Push branch fitur Anda ke remote repository:
   ```bash
   git push -u origin feat/menu-X-nama-fitur
   ```
7. Buat Pull Request (PR) di GitHub dari branch fitur Anda $\rightarrow$ ke `main`. Lead Dev akan mereview dan melakukan merge.

---

## 4. Standar Format Commit Message
Repositori ini menggunakan prefix tag commit yang konsisten.

### Prefix Tag yang Digunakan:
- `[ADD]` : Menambah fitur baru, file baru, model, controller, atau komponen baru.
- `[IMP]` : Improvement/perbaikan tampilan, refactoring kode, optimalisasi UI/UX.
- `[FIX]` : Memperbaiki error, bug, atau typo.
- `[DOC]` : Memperbarui dokumentasi, README, atau AGENTS.md.
- `[REF]` : Refactor kode tanpa mengubah fungsi bisnis.

### Format Standar:
```text
[TAG] deskripsi singkat perubahan dalam kalimat imperatif

- Poin penjelasan detail 1
- Poin penjelasan detail 2
```

### Contoh Nyata dari Commit History Repositori:
```text
[IMP] integrate pink-blue color swatches and dark mode refinements

 - Add CSS variable RGB channels with full opacity/alpha support in Tailwind config
 - Decouple hardcoded colors in app layout, vehicle switcher, and modals to dynamic brand utilities
 - Improve color swatch UI selector and active indicator states for dark mode
 - Style user avatar and navigation elements to harmonize across both themes
```
```text
[ADD] add 6 relational tables and initial seeders

 - Add phone_whatsapp column to users table
 - Create migrations for vehicles, maintenance_items, service_reminders, service_logs, and ai_scan_histories
 - Setup Eloquent models with 1:N relationships and real-time ODO decay accessor
 - Create initial seeders with admin, demo rider, and 5 factory maintenance parts
```

---

## 5. Batasan & Larangan Keras (Guardrails)
1. **Dilarang keras mengubah konfigurasi skema tabel inti** (`users`, `vehicles`, `maintenance_items`, `service_reminders`, `service_logs`, `ai_scan_histories`) tanpa kesepakatan tim.
2. **Dilarang mematikan atau membypass Dark Mode & Dual Theme system**. Semua komponen baru wajib menyertakan styling `dark:...` dan menggunakan token kelas `brand`.
3. **Dilarang menulis CSS inline (`style="..."`)** untuk warna utama, gunakan selalu utilitas Tailwind.
4. **Dilarang menggunakan dialog/pop-up bawaan browser (`alert()`, `confirm()`, `prompt()`)**. Semua dialog konfirmasi, peringatan, atau input wajib menggunakan modal/pop-up kustom (Alpine.js / Tailwind) agar UI/UX konsisten, modern, dan rapi.
5. **Gunakan route berpenamaan (named routes)** pada Blade view: `route('dashboard')`, `route('calculator')`, `route('diagnosa')`, `route('garage')`.
6. **Dilarang push langsung ke `main`**. Semua pekerjaan harus melalui branch fitur $\rightarrow$ buat Pull Request (PR) ke `main`.
