<p align="center">
  <img src="public/favicon.svg" width="90" height="90" alt="SERVISA Logo">
</p>

<h1 align="center">SERVISA (Smart Personal Vehicle Maintenance)</h1>

<p align="center">
  <strong>Web App Manajemen Perawatan Kendaraan Cerdas & Diagnosa Berbasis AI</strong><br>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Google_Gemini-Vision_AI-8E75B2?style=for-the-badge&logo=google-gemini&logoColor=white" alt="Gemini AI">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

---

## 📌 Tentang Proyek

**SERVISA** adalah platform cerdas pemeliharaan kendaraan roda dua pribadi yang memecahkan masalah kebiasaan lalai servis (*service neglect*). Berbeda dari sekadar aplikasi pencatat nota servis statis, SERVISA mengusung 4 inovasi utama:

1. **Smart Odometer Real-Time Decay Engine**: Mengestimasi kenaikan angka kilometer harian otomatis berdasarkan *daily average usage*, sehingga peringatan servis selalu relevan tanpa harus memasukkan kilometer setiap hari.
2. **AI Vision Speedometer Scanner**: Memanfaatkan **Google Gemini Vision API** untuk mengekstrak angka odometer secara langsung dari foto dashboard/speedometer motor serta mengenali lampu indikator *Malfunction Indicator Lamp* (MIL).
3. **Kalkulator Kerugian (*Cost of Neglect*)**: Edukasi interaktif berbasis data finansial yang membandingkan biaya servis preventif berkala vs efek domino pembengkakan biaya akibat kerusakan fatal (Mendukung **SDG 12: Konsumsi dan Produksi yang Bertanggung Jawab**).
4. **Buku Paspor Servis Terverifikasi & Ekspor PDF**: Riwayat digital lengkap perawatan kendaraan yang dapat dicetak resmi sebagai bukti riwayat servis saat kendaraan hendak dijual kembali, menjaga *resale value* motor tetap tinggi.

---

## 🛠️ Persyaratan Sistem (Prerequisites)

- **PHP**: `^8.2`
- **Composer**: `^2.x`
- **Node.js**: `^18.x` atau `^20.x` & `npm`
- **Database**: MySQL / MariaDB

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah berikut untuk menjalankan SERVISA di komputer lokal:

### 1. Clone Repositori
```bash
git clone https://github.com/bintangalsyahadat/system-vehicle-maintenance.git
cd system-vehicle-maintenance
```

### 2. Install Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file environment contoh:
```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan sesuaikan koneksi database MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=servisa_db
DB_USERNAME=root
DB_PASSWORD=

# Gemini Vision API Key (untuk Menu 2)
GEMINI_API_KEY=your_gemini_api_key_here
```

### 4. Migrasi & Seeder Database
Jalankan migrasi untuk membuat 6 tabel relasional inti beserta data demo bawaan pabrik:
```bash
php artisan migrate:fresh --seed
```

> **Akun Demo Bawaan:**
> - Email: `demo@servisa.com`
> - Password: `password123`
> - Kendaraan: *Honda Vario 160 ABS (2023)* lengkap dengan 5 part vital pabrikan (Oli Mesin, Oli Gardan, Busi, Kampas Rem, V-Belt).

### 5. Build Aset Frontend
```bash
npm run build
# atau untuk mode live preview:
npm run dev
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: `http://localhost:8000`

---

## 🌿 Alur Git Workflow Tim

Workflow disederhanakan agar proses pengembangan cepat dan efisien tanpa branch `dev`:

1. Selalu tarik kode terbaru dari branch `main`:
   ```bash
   git checkout main
   git pull origin main
   ```
2. Buat branch fitur baru langsung dari `main`:
   ```bash
   git checkout -b feat/menu-X-nama-fitur
   ```
3. Lakukan commit dengan standar prefix tag repositori:
   - `[ADD]` : Menambah fitur baru, file baru, model/controller baru.
   - `[IMP]` : Perbaikan tampilan UI/UX, optimalisasi styling.
   - `[FIX]` : Perbaikan bug.
   - `[DOC]` : Pembaruan dokumentasi.
4. Push branch fitur Anda dan buat Pull Request (PR) ke `main`:
   ```bash
   git push -u origin feat/menu-X-nama-fitur
   ```
   *(Catatan: Dilarang push langsung ke branch `main`)*

---

## 📄 Lisensi
Proyek ini dikembangkan secara open-source di bawah lisensi [MIT License](LICENSE).
