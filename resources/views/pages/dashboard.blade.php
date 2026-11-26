@extends('layouts.app')

@section('title', 'Dashboard Servis')

@section('greeting', $timeGreeting . ', ' . $userName . '!')

@section('content')
<div class="space-y-8" x-data="dashboardManager('{{ now()->format('Y-m-d') }}')">

    <!-- Flash Messages (Notifikasi Kustom) -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if (!$activeVehicle)
        <!-- Kondisi Jika Belum Ada Kendaraan di Garasi -->
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-8 sm:p-12 text-center border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="w-20 h-20 rounded-full bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent mx-auto mb-4">
                <i data-lucide="bike" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">Garasi Anda Masih Kosong</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-2">
                Tambahkan motor pertama Anda untuk mengaktifkan Smart Odometer Decay Engine, prediksi servis otomatis, dan pemantauan 5 part vital.
            </p>
            <button @click="addVehicleModal = true" class="mt-6 px-6 py-3 rounded-full bg-brand-main hover:bg-brand-dark text-white font-bold text-sm shadow-md shadow-brand-main/20 transition hover:scale-105 active:scale-95 inline-flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Kendaraan Sekarang</span>
            </button>
        </div>
    @else

        <!-- ==================== 1. BAGIAN ATAS: ODOMETER ENGINE & KALENDER SERVIS ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- KOLOM KIRI (SMART ODOMETER & BANNER REKOMENDASI SERVIS) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- KARTU 1: SMART ODOMETER DECAY ENGINE -->
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-7 border border-slate-100 dark:border-slate-800 shadow-sm transition-colors relative overflow-hidden">
                    
                    <!-- Top Header Kartu Odometer -->
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                                {{ $activeVehicle->brand }} {{ $activeVehicle->model_name }}
                            </h2>
                            <p class="text-xs font-semibold text-slate-400 mt-0.5">
                                {{ $activeVehicle->license_plate ?: 'Plat Nomor Belum Diatur' }}
                            </p>
                        </div>
                        
                        <!-- Estimasi Badge -->
                        <div class="bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700/60 rounded-2xl px-3.5 py-2 text-right">
                            <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Estimasi</span>
                            <span class="block text-xs sm:text-sm font-black text-slate-800 dark:text-white mt-0.5">
                                {{ $activeVehicle->daily_average_km }} KM / hari
                            </span>
                        </div>
                    </div>

                    <!-- Ilustrasi Motor Skuter Retro / Modern (SVG Adaptif + Animasi Realistis) -->
                    <div class="my-4 sm:my-6 flex justify-center items-center py-2">
                        <div class="relative w-full max-w-[340px] sm:max-w-[420px] h-52 sm:h-64 flex items-center justify-center">
                            <!-- Background Glow Halus -->
                            <div class="absolute inset-0 bg-brand-secondary/40 dark:bg-brand-main/15 rounded-full blur-3xl transform scale-125"></div>

                            <!-- Animasi CSS Khusus Motor Melaju -->
                            <style>
                                @keyframes bikeRide {
                                    0%, 100% {
                                        transform: translateY(0px) rotate(0deg);
                                    }
                                    25% {
                                        transform: translateY(-3.5px) rotate(-0.5deg);
                                    }
                                    50% {
                                        transform: translateY(0px) rotate(0deg);
                                    }
                                    75% {
                                        transform: translateY(-2px) rotate(0.4deg);
                                    }
                                }
                                @keyframes shadowPulse {
                                    0%, 100% {
                                        transform: scaleX(1);
                                        opacity: 0.8;
                                    }
                                    25% {
                                        transform: scaleX(0.92);
                                        opacity: 0.55;
                                    }
                                    50% {
                                        transform: scaleX(1);
                                        opacity: 0.8;
                                    }
                                    75% {
                                        transform: scaleX(0.95);
                                        opacity: 0.65;
                                    }
                                }
                                @keyframes speedDash {
                                    0% {
                                        transform: translateX(0);
                                        opacity: 0;
                                    }
                                    30% {
                                        opacity: 0.8;
                                    }
                                    100% {
                                        transform: translateX(-35px);
                                        opacity: 0;
                                    }
                                }
                                .animate-bike-ride {
                                    animation: bikeRide 1.6s ease-in-out infinite;
                                    transform-origin: center bottom;
                                }
                                .animate-shadow-pulse {
                                    animation: shadowPulse 1.6s ease-in-out infinite;
                                    transform-origin: center;
                                }
                                .animate-speed-dash-1 {
                                    animation: speedDash 1s ease-out infinite;
                                }
                                .animate-speed-dash-2 {
                                    animation: speedDash 1.2s ease-out 0.3s infinite;
                                }
                                .animate-speed-dash-3 {
                                    animation: speedDash 0.9s ease-out 0.6s infinite;
                                }
                            </style>

                            <!-- Vector Graphic Scooter -->
                            <svg class="w-full h-full drop-shadow-md select-none" viewBox="15 45 270 145" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Bayangan Tanah Dinamis -->
                                <ellipse cx="160" cy="180" rx="105" ry="8" fill="currentColor" class="text-slate-200 dark:text-slate-800/80 animate-shadow-pulse" />

                                <!-- Efek Garis Angin Kecepatan di Belakang Motor -->
                                <g>
                                    <line x1="60" y1="150" x2="35" y2="150" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-speed-dash-1" stroke-width="2.5" stroke-linecap="round" />
                                    <line x1="50" y1="138" x2="20" y2="138" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-speed-dash-2" stroke-width="2" stroke-linecap="round" />
                                    <line x1="65" y1="162" x2="38" y2="162" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-speed-dash-3" stroke-width="2" stroke-linecap="round" />
                                </g>
                                
                                <!-- Seluruh Motor (Bodi + Suspensi + Roda) Bergerak Bersama Mengayun Halus -->
                                <g class="animate-bike-ride">
                                    <!-- Roda Belakang Lengkap dengan Velg Spoke Berputar Native SVG -->
                                    <g>
                                        <!-- Ban Luar Hitam -->
                                        <circle cx="85" cy="148" r="28" fill="#1E293B" stroke="#0F172A" stroke-width="6" />
                                        <circle cx="85" cy="148" r="14" fill="#94A3B8" />

                                        <!-- Velg Spokes Berputar Murni 360 Derajat Seputar Poros (cx=85, cy=148) -->
                                        <g>
                                            <line x1="85" y1="123" x2="85" y2="173" stroke="#334155" stroke-width="3" stroke-linecap="round" />
                                            <line x1="60" y1="148" x2="110" y2="148" stroke="#334155" stroke-width="3" stroke-linecap="round" />
                                            <line x1="67" y1="130" x2="103" y2="166" stroke="#475569" stroke-width="2.5" stroke-linecap="round" />
                                            <line x1="67" y1="166" x2="103" y2="130" stroke="#475569" stroke-width="2.5" stroke-linecap="round" />
                                            <circle cx="85" cy="138" r="3" fill="#F8FAFC" />
                                            <animateTransform 
                                                attributeName="transform" 
                                                type="rotate" 
                                                from="0 85 148" 
                                                to="360 85 148" 
                                                dur="0.55s" 
                                                repeatCount="indefinite" />
                                        </g>

                                        <!-- Poros Hub Roda Belakang -->
                                        <circle cx="85" cy="148" r="6" fill="#F8FAFC" stroke="#0F172A" stroke-width="1.5" />
                                    </g>

                                    <!-- Roda Depan Lengkap dengan Velg Spoke Berputar Native SVG -->
                                    <g>
                                        <!-- Ban Luar Hitam -->
                                        <circle cx="235" cy="148" r="28" fill="#1E293B" stroke="#0F172A" stroke-width="6" />
                                        <circle cx="235" cy="148" r="14" fill="#94A3B8" />

                                        <!-- Velg Spokes Berputar Murni 360 Derajat Seputar Poros (cx=235, cy=148) -->
                                        <g>
                                            <line x1="235" y1="123" x2="235" y2="173" stroke="#334155" stroke-width="3" stroke-linecap="round" />
                                            <line x1="210" y1="148" x2="260" y2="148" stroke="#334155" stroke-width="3" stroke-linecap="round" />
                                            <line x1="217" y1="130" x2="253" y2="166" stroke="#475569" stroke-width="2.5" stroke-linecap="round" />
                                            <line x1="217" y1="166" x2="253" y2="130" stroke="#475569" stroke-width="2.5" stroke-linecap="round" />
                                            <circle cx="235" cy="138" r="3" fill="#F8FAFC" />
                                            <animateTransform 
                                                attributeName="transform" 
                                                type="rotate" 
                                                from="0 235 148" 
                                                to="360 235 148" 
                                                dur="0.55s" 
                                                repeatCount="indefinite" />
                                        </g>

                                        <!-- Poros Hub Roda Depan -->
                                        <circle cx="235" cy="148" r="6" fill="#F8FAFC" stroke="#0F172A" stroke-width="1.5" />
                                    </g>

                                    <!-- Body Frame Bawah -->
                                    <path d="M90 148 L145 148 L170 120 L220 120 L235 148" stroke="currentColor" class="text-slate-400 dark:text-slate-600" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" />

                                    <!-- Main Body Motor (Dinamis Warna Brand) -->
                                    <path d="M100 135 C100 115 115 95 140 95 L190 95 C205 95 215 105 210 125 L195 140 C175 145 120 145 100 135 Z" 
                                          class="fill-brand-main" />

                                    <!-- Jok / Tempat Duduk -->
                                    <path d="M115 93 C115 88 122 84 135 84 L175 84 C185 84 190 88 188 93 Z" fill="#0F172A" />

                                    <!-- Footstep / Deck Depan -->
                                    <path d="M150 140 L195 140" stroke="#334155" stroke-width="6" stroke-linecap="round" />

                                    <!-- Stang & Spion -->
                                    <path d="M210 120 L235 70" stroke="currentColor" class="text-slate-500 dark:text-slate-400" stroke-width="6" stroke-linecap="round" />
                                    <path d="M225 70 L245 70" stroke="#0F172A" stroke-width="8" stroke-linecap="round" />
                                    <circle cx="240" cy="55" r="7" fill="currentColor" class="text-slate-300 dark:text-slate-600" stroke="#0F172A" stroke-width="2" />
                                    <path d="M238 62 L237 70" stroke="#0F172A" stroke-width="3" />

                                    <!-- Lampu Depan (Headlamp Cahaya Lembut) -->
                                    <path d="M242 82 C248 85 248 95 242 98 Z" fill="#FDE047" />
                                    <!-- Sorotan Cahaya Headlamp Halus -->
                                    <polygon points="247,84 275,76 275,104 247,96" fill="#FDE047" opacity="0.25" />

                                    <!-- Slebor Depan -->
                                    <path d="M220 125 C225 115 245 115 250 125" stroke="currentColor" class="text-brand-main" stroke-width="6" stroke-linecap="round" />

                                    <!-- Knalpot Belakang -->
                                    <rect x="55" y="148" width="35" height="10" rx="5" fill="#475569" stroke="#1E293B" stroke-width="2" />
                                </g>
                            </svg>
                        </div>
                    </div>

                    <!-- Tampilan Angka Odometer Saat Ini -->
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block tracking-wide">Odometer saat ini</span>
                        <div class="flex items-baseline space-x-2 mt-0.5">
                            <span class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                                {{ number_format($activeVehicle->current_odometer, 0, ',', '.') }}
                            </span>
                            <span class="text-lg font-black text-brand-main dark:text-brand-accent tracking-wide">KM</span>
                        </div>
                    </div>

                    <!-- Action Buttons: Scan Nota Servis, Scan Diagnosa, Ubah KM, & Estimasi Harian -->
                    <div class="flex flex-wrap items-center gap-2.5 mt-5">
                        <a href="{{ route('history') }}?scan=receipt" 
                           class="inline-flex items-center space-x-2 px-4 sm:px-5 py-2.5 rounded-full bg-brand-main hover:bg-brand-dark text-white font-extrabold text-xs shadow-md shadow-brand-main/25 transition transform hover:scale-105 active:scale-95">
                            <i data-lucide="scan" class="w-4 h-4"></i>
                            <span>Scan Nota Servis</span>
                        </a>

                        <a href="{{ route('diagnosa') }}?scan=camera" 
                           class="inline-flex items-center space-x-2 px-4 sm:px-5 py-2.5 rounded-full bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-extrabold text-xs shadow-sm transition transform hover:scale-105 active:scale-95">
                            <i data-lucide="stethoscope" class="w-4 h-4 text-white"></i>
                            <span>Scan Diagnosa</span>
                        </a>

                        <button type="button"
                                @click="recalibrateModal = true" 
                                class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition">
                            <i data-lucide="gauge" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>Ubah KM</span>
                        </button>

                        <div class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-full border border-slate-200 dark:border-slate-700/80 bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-200 font-semibold text-xs shadow-inner dark:shadow-none">
                            <span class="w-2 h-2 rounded-full bg-brand-main dark:bg-brand-accent animate-pulse"></span>
                            <span>+ {{ $activeVehicle->daily_average_km }} KM/hari</span>
                        </div>
                    </div>

                    <!-- 2 Kolom Meta Info (Dihitung & Servis Terdekat) dengan Kontras Jelas -->
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-700/80 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-slate-100/80 dark:bg-slate-900/70 rounded-2xl p-3.5 border border-slate-200/80 dark:border-slate-700/70 shadow-sm">
                            <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-400 block">Dihitung</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 mt-0.5 block">
                                Hari ini, {{ now()->format('H.i') }}
                            </span>
                        </div>

                        <div class="bg-slate-100/80 dark:bg-slate-900/70 rounded-2xl p-3.5 border border-slate-200/80 dark:border-slate-700/70 shadow-sm">
                            <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-400 block">Servis terdekat</span>
                            <span class="text-xs font-bold text-brand-main dark:text-brand-accent mt-0.5 block truncate">
                                {{ $closestPart ? $closestPart->short_name . ', ' . $closestPart->predicted_date_formatted : 'Belum Ada Jadwal' }}
                            </span>
                        </div>
                    </div>

                    <!-- Footnote Keterangan Estimasi -->
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-4 text-center sm:text-left leading-relaxed">
                        Jarak tempuh diperbarui otomatis berdasarkan estimasi harian.
                    </p>
                </div>



            </div>

            <!-- ==================== KOLOM KANAN: KARTU KOMPONEN KESEHATAN (GRID 2 KOLOM) ==================== -->
            <div class="lg:col-span-5">
                <!-- Grid 2 Kolom Konsisten untuk 6 Komponen Vital -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @foreach ($parts as $part)
                        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-4 sm:p-5 border {{ ($closestPart && $closestPart->id === $part->id) ? 'border-brand-main/50 dark:border-brand-main/60 ring-2 ring-brand-main/15 shadow-md shadow-brand-main/5' : 'border-slate-100 dark:border-slate-800 shadow-sm' }} flex flex-col justify-between hover:shadow-md transition-all group">
                            <div>
                                <!-- Header Kartu: Icon, Servis Mendekat Pin, & Status Badge -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 rounded-2xl bg-brand-secondary/30 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent">
                                            <i data-lucide="{{ $part->icon }}" class="w-4 h-4"></i>
                                        </div>

                                        @if ($closestPart && $closestPart->id === $part->id)
                                            <!-- Badge Penanda Servis Terdekat -->
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-brand-main/10 dark:bg-brand-main/25 text-brand-main dark:text-brand-accent border border-brand-main/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-brand-main dark:bg-brand-accent animate-ping"></span>
                                                <span>Servis Mendekat</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Status Badge (Kritis, Waspada, Aman) -->
                                    @if ($part->status === 'danger')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60">
                                            {{ $part->status_label }}
                                        </span>
                                    @elseif ($part->status === 'warning')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
                                            {{ $part->status_label }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                                            {{ $part->status_label }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Nama Komponen -->
                                <h4 class="font-black text-sm text-slate-800 dark:text-white mt-3 truncate" title="{{ $part->name }}">
                                    {{ $part->short_name }}
                                </h4>

                                <!-- Prediksi Tanggal Servis -->
                                <div class="mt-2">
                                    <span class="text-[10px] font-medium text-slate-400 block leading-tight">Prediksi servis</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block mt-0.5 truncate">
                                        {{ $part->predicted_date_formatted }}
                                    </span>
                                </div>

                                <!-- Progress Bar Kesehatan Part (Trek Jelas di Dark Mode) -->
                                <div class="w-full h-2 bg-slate-100 dark:bg-slate-900/90 border border-transparent dark:border-slate-700/60 rounded-full mt-3 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $part->status === 'danger' ? 'bg-rose-500' : ($part->status === 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}" 
                                         style="width: {{ $part->percentage }}%"></div>
                                </div>
                            </div>

                            <!-- Action Buttons di Bawah Kartu -->
                            <div class="mt-4 space-y-2">
                                @if ($part->status === 'danger' || $part->status === 'warning')
                                    <button type="button"
                                            @click="openQuickServiceModal({{ json_encode($part) }})" 
                                            class="w-full py-2 px-3 rounded-full bg-brand-main hover:bg-brand-dark text-white font-bold text-xs flex items-center justify-center space-x-1.5 shadow-sm shadow-brand-main/20 transition hover:scale-[1.02] active:scale-[0.98]">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Sudah diservis</span>
                                    </button>
                                @endif

                                <button type="button"
                                        @click="openEditDateModal({{ json_encode($part) }})" 
                                        class="w-full py-2 px-3 rounded-full border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs flex items-center justify-center space-x-1.5 transition">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>Ubah tanggal</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- ==================== 2. BAGIAN BAWAH: TABEL RIWAYAT SERVIS KENDARAAN AKTIF ==================== -->
        <div class="space-y-4 pt-1">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight flex items-center space-x-2">
                        <i data-lucide="history" class="w-5 h-5 text-brand-main dark:text-brand-accent"></i>
                        <span>Riwayat Servis Kendaraan</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Rekam jejak pengerjaan servis bengkel & suku cadang untuk {{ $activeVehicle->model_name }}
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('history') }}" 
                       class="text-xs font-bold text-brand-main dark:text-brand-accent hover:underline flex items-center space-x-1">
                        <span>Lihat Semua</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- 1. TAMPILAN MOBILE (Card List Rapi Khusus Layar Kecil / HP) -->
            <div class="block md:hidden space-y-3">
                @forelse($recentServiceLogs as $log)
                    <div class="bg-white dark:bg-[#1E293B] rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm space-y-3 transition">
                        <!-- Baris Atas: Tanggal, Status Nota & Biaya -->
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 dark:border-slate-800/80 pb-2.5">
                            <div>
                                <span class="font-extrabold text-slate-800 dark:text-white text-xs block">
                                    {{ $log->service_date_formatted }}
                                </span>
                                <div class="text-[10px] text-slate-400 flex items-center space-x-1 mt-0.5">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-slate-400"></i>
                                    <span class="truncate max-w-[150px]">{{ $log->workshop_name }}</span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="font-black text-brand-main dark:text-brand-accent text-sm block">
                                    Rp {{ number_format($log->actual_cost, 0, ',', '.') }}
                                </span>
                                @if($log->is_verified)
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 mt-0.5">
                                        <i data-lucide="shield-check" class="w-2.5 h-2.5"></i>
                                        <span>Nota Asli</span>
                                    </span>
                                @else
                                    <span class="text-[9px] text-slate-400 mt-0.5 block">Input Manual</span>
                                @endif
                            </div>
                        </div>

                        <!-- Baris Tengah: Komponen yang Diservis -->
                        <div>
                            @if($log->part_names->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($log->part_names as $partName)
                                        <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-brand-secondary/30 dark:bg-brand-main/20 text-brand-main dark:text-brand-accent">
                                            <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                            <span>{{ $partName }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                    Servis Berkala / Cek Umum
                                </span>
                            @endif

                            @if($log->notes)
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 bg-slate-50 dark:bg-slate-900/50 p-2 rounded-xl border border-slate-100 dark:border-slate-800/60 leading-relaxed">
                                    {{ $log->notes }}
                                </p>
                            @endif
                        </div>

                        <!-- Baris Bawah: Odometer -->
                        <div class="flex items-center justify-between pt-1 text-[11px] text-slate-400">
                            <span>Jarak Tempuh Servis:</span>
                            <span class="font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ number_format($log->odometer_at_service, 0, ',', '.') }} KM
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-[#1E293B] rounded-2xl p-8 text-center text-slate-400 space-y-2 border border-slate-100 dark:border-slate-800">
                        <i data-lucide="clipboard" class="w-6 h-6 mx-auto text-slate-400"></i>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Belum ada riwayat servis untuk motor ini</p>
                    </div>
                @endforelse
            </div>

            <!-- 2. TAMPILAN DESKTOP/TABLET (Tabel Penuh Layar Lebar >= md) -->
            <div class="hidden md:block bg-white dark:bg-[#1E293B] rounded-3xl border border-slate-200/80 dark:border-slate-700/70 shadow-sm overflow-hidden">
                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900/80">
                                <th class="py-3.5 px-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tanggal & Bengkel</th>
                                <th class="py-3.5 px-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Komponen & Catatan</th>
                                <th class="py-3.5 px-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Odometer</th>
                                <th class="py-3.5 px-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Nota</th>
                                <th class="py-3.5 px-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-right">Biaya Servis</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80 dark:divide-slate-700/70 text-xs">
                            @forelse($recentServiceLogs as $log)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                    <!-- Tanggal & Bengkel -->
                                    <td class="py-4 px-5 align-top">
                                        <div class="font-extrabold text-slate-800 dark:text-white text-xs">
                                            {{ $log->service_date_formatted }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 flex items-center space-x-1 mt-0.5">
                                            <i data-lucide="map-pin" class="w-3 h-3 text-slate-400"></i>
                                            <span>{{ $log->workshop_name }}</span>
                                        </div>
                                    </td>

                                    <!-- Komponen & Catatan -->
                                    <td class="py-4 px-5 align-top max-w-xs">
                                        @if($log->part_names->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 mb-1">
                                                @foreach($log->part_names as $partName)
                                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-brand-secondary/30 dark:bg-brand-main/20 text-brand-main dark:text-brand-accent">
                                                        <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                                        <span>{{ $partName }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">
                                                Servis Berkala / Cek Umum
                                            </span>
                                        @endif

                                        @if($log->notes)
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">
                                                {{ $log->notes }}
                                            </p>
                                        @endif
                                    </td>

                                    <!-- Odometer -->
                                    <td class="py-4 px-5 align-top font-mono font-bold text-slate-700 dark:text-slate-300">
                                        {{ number_format($log->odometer_at_service, 0, ',', '.') }} KM
                                    </td>

                                    <!-- Status Nota -->
                                    <td class="py-4 px-5 align-top">
                                        @if($log->is_verified)
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                <i data-lucide="shield-check" class="w-3 h-3"></i>
                                                <span>Nota Terverifikasi</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                                <span>Manual</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Biaya Servis -->
                                    <td class="py-4 px-5 align-top text-right font-black text-brand-main dark:text-brand-accent text-sm">
                                        Rp {{ number_format($log->actual_cost, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400 space-y-2">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                                            <i data-lucide="clipboard" class="w-5 h-5"></i>
                                        </div>
                                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Belum ada riwayat servis untuk kendaraan ini</p>
                                        <p class="text-[11px] text-slate-400">Gunakan tombol "Scan Nota Servis" di atas untuk mencatat servis pertama.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 1: UBAH KM / KALIBRASI ODOMETER                                    -->
    <!-- ========================================================================= -->
    <div x-show="recalibrateModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="recalibrateModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100 dark:border-slate-800">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Kalibrasi Odometer Motor</h3>
                </div>
                <button type="button" @click="recalibrateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            @if ($activeVehicle)
                <form action="{{ route('vehicles.recalibrate', $activeVehicle) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Angka Odometer Saat Ini (KM) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" 
                               name="current_odometer_km" 
                               value="{{ $activeVehicle->current_odometer }}"
                               required 
                               min="0"
                               class="w-full text-sm rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                        <span class="text-[11px] text-slate-400 mt-1 block">
                            Masukkan angka kilometer riil yang tertera pada speedometer Anda hari ini.
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Estimasi Jarak Tempuh Harian (KM/hari) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" 
                               step="0.1" 
                               name="daily_average_km" 
                               value="{{ $activeVehicle->daily_average_km }}"
                               required 
                               min="1" 
                               max="500"
                               class="w-full text-sm rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                        <span class="text-[11px] text-slate-400 mt-1 block">
                            Digunakan oleh Smart Decay Engine untuk memprediksi tanggal servis secara otomatis.
                        </span>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="recalibrateModal = false" class="px-4 py-2 rounded-full border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-full bg-brand-main hover:bg-brand-dark text-white text-xs font-bold shadow-md shadow-brand-main/20">
                            Simpan Kalibrasi
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: SUDAH DISERVIS (QUICK SERVICE LOG)                               -->
    <!-- ========================================================================= -->
    <div x-show="quickServiceModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="quickServiceModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100 dark:border-slate-800">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Catat Servis Selesai</h3>
                </div>
                <button type="button" @click="quickServiceModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            @if ($activeVehicle)
                <form action="{{ route('dashboard.quickService') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $activeVehicle->id }}">
                    <input type="hidden" name="maintenance_item_id" :value="selectedPart.id">

                    <!-- Info Komponen -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-3 rounded-2xl border border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-semibold block">Komponen yang Diservis:</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-white" x-text="selectedPart.name"></span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-secondary/40 text-brand-main dark:bg-brand-main/20 dark:text-brand-accent">
                            Reset ke 100%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Tanggal Servis <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" 
                                   name="service_date" 
                                   value="{{ now()->toDateString() }}"
                                   required 
                                   class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Odometer Saat Servis <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" 
                                   name="odometer_at_service" 
                                   value="{{ $activeVehicle->current_odometer }}"
                                   required 
                                   min="0"
                                   class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Biaya Riil Penggantian (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" 
                               name="actual_cost" 
                               placeholder="Contoh: 85000"
                               required 
                               min="0"
                               class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Nama Bengkel / Toko (Opsional)
                        </label>
                        <input type="text" 
                               name="workshop_name" 
                               placeholder="AHASS / Bengkel Mandiri"
                               class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="quickServiceModal = false" class="px-4 py-2 rounded-full border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-full bg-brand-main hover:bg-brand-dark text-white text-xs font-bold shadow-md shadow-brand-main/20">
                            Simpan ke Paspor Servis
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: UBAH TANGGAL / JADWAL PENGINGAT SERVIS                           -->
    <!-- ========================================================================= -->
    <div x-show="editDateModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="editDateModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100 dark:border-slate-800">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Atur Jadwal Servis</h3>
                </div>
                <button type="button" @click="editDateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            @if ($activeVehicle)
                <form action="{{ route('dashboard.reminders.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $activeVehicle->id }}">
                    <input type="hidden" name="maintenance_item_id" :value="selectedPart.id">

                    <!-- Info Komponen -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-3 rounded-2xl border border-slate-100 dark:border-slate-700/60">
                        <span class="text-[10px] text-slate-400 font-semibold block">Komponen:</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white" x-text="selectedPart.name"></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Servis Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" 
                               name="scheduled_date" 
                               :value="selectedPart.scheduled_date_raw"
                               required 
                               class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                        <span class="text-[11px] text-slate-400 mt-1 block">
                            Tanggal ini akan disorot di Kalender Servis AI Anda.
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Target Odometer Khusus (KM, Opsional)
                        </label>
                        <input type="number" 
                               name="custom_km" 
                               placeholder="Contoh: 15000"
                               min="0"
                               class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="editDateModal = false" class="px-4 py-2 rounded-full border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-full bg-brand-main hover:bg-brand-dark text-white text-xs font-bold shadow-md shadow-brand-main/20">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: DETAIL JADWAL SERVIS MENDEKAT                                    -->
    <!-- ========================================================================= -->
    <div x-show="detailModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="detailModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100 dark:border-slate-800">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="info" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Detail Prediksi Servis</h3>
                </div>
                <button type="button" @click="detailModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/60">
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Komponen</span>
                    <h4 class="text-base font-black text-slate-800 dark:text-white mt-0.5" x-text="selectedPart.name"></h4>
                    
                    <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-slate-200/60 dark:border-slate-700/60">
                        <div>
                            <span class="text-[10px] text-slate-400 font-medium block">Tanggal Servis:</span>
                            <span class="text-xs font-bold text-brand-main dark:text-brand-accent" x-text="selectedPart.predicted_date_formatted"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-medium block">Sisa Jarak Tempuh:</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200" x-text="selectedPart.km_remaining + ' KM'"></span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-2xl text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed flex items-start space-x-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                    <span>Lakukan servis sebelum sisa jarak tempuh habis untuk mencegah keausan komponen dan pembengkakan biaya perbaikan.</span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                <button type="button" @click="detailModal = false" class="px-4 py-2 rounded-full border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    Tutup
                </button>
                <button type="button" @click="detailModal = false; openQuickServiceModal(selectedPart)" class="px-5 py-2 rounded-full bg-brand-main hover:bg-brand-dark text-white text-xs font-bold shadow-md shadow-brand-main/20">
                    Servis Sekarang
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Script Definisi Alpine Component dashboardManager -->
<script>
    function dashboardManager(eventsData, todayStr, currentYear, currentMonth) {
        return {
            recalibrateModal: false,
            quickServiceModal: false,
            editDateModal: false,
            detailModal: false,

            selectedPart: {
                id: null,
                name: '',
                short_name: '',
                percentage: 100,
                km_remaining: 0,
                interval_km: 3000,
                status: 'safe',
                scheduled_date_raw: todayStr,
                predicted_date_formatted: ''
            },

            openQuickServiceModal(part) {
                this.selectedPart = Object.assign({}, part);
                this.quickServiceModal = true;
                this.refreshIcons();
            },

            openEditDateModal(part) {
                this.selectedPart = Object.assign({}, part);
                this.editDateModal = true;
                this.refreshIcons();
            },

            openDetailModal(part) {
                if (!part) return;
                this.selectedPart = Object.assign({}, part);
                this.detailModal = true;
                this.refreshIcons();
            },

            refreshIcons() {
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endsection
