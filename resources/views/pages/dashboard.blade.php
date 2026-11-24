@extends('layouts.app')

@section('title', 'Dashboard Servis')

@section('greeting', $timeGreeting . ', ' . $userName . '!')

@section('content')
<div class="space-y-8" x-data="dashboardManager(@js($calendarEvents), '{{ now()->format('Y-m-d') }}', {{ now()->year }}, {{ now()->month - 1 }})">

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

                    <!-- Ilustrasi Motor Skuter Retro / Modern (SVG Adaptif) -->
                    <div class="my-6 flex justify-center items-center py-2">
                        <div class="relative w-56 h-36 sm:w-64 sm:h-40 flex items-center justify-center">
                            <!-- Background Glow Halus -->
                            <div class="absolute inset-0 bg-brand-secondary/30 dark:bg-brand-main/10 rounded-full blur-2xl"></div>

                            <!-- Vector Graphic Scooter -->
                            <svg class="w-full h-full drop-shadow-sm select-none" viewBox="0 0 320 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Bayangan Tanah -->
                                <ellipse cx="160" cy="180" rx="100" ry="8" fill="currentColor" class="text-slate-200 dark:text-slate-800/60" />
                                
                                <!-- Roda Belakang -->
                                <circle cx="85" cy="148" r="28" fill="#1E293B" stroke="#0F172A" stroke-width="6" />
                                <circle cx="85" cy="148" r="14" fill="#94A3B8" />
                                <circle cx="85" cy="148" r="6" fill="#F8FAFC" />

                                <!-- Roda Depan -->
                                <circle cx="235" cy="148" r="28" fill="#1E293B" stroke="#0F172A" stroke-width="6" />
                                <circle cx="235" cy="148" r="14" fill="#94A3B8" />
                                <circle cx="235" cy="148" r="6" fill="#F8FAFC" />

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

                                <!-- Lampu Depan (Headlamp) -->
                                <path d="M242 82 C248 85 248 95 242 98 Z" fill="#FDE047" />

                                <!-- Slebor Depan -->
                                <path d="M220 125 C225 115 245 115 250 125" stroke="currentColor" class="text-brand-main" stroke-width="6" stroke-linecap="round" />

                                <!-- Knalpot Belakang -->
                                <rect x="55" y="148" width="35" height="10" rx="5" fill="#475569" stroke="#1E293B" stroke-width="2" />
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

                    <!-- Action Buttons: Ubah KM & Estimasi Harian -->
                    <div class="flex flex-wrap items-center gap-2.5 mt-4">
                        <button type="button"
                                @click="recalibrateModal = true" 
                                class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-brand-main hover:bg-brand-dark text-white font-bold text-xs shadow-md shadow-brand-main/20 transition hover:scale-105 active:scale-95">
                            <i data-lucide="gauge" class="w-3.5 h-3.5"></i>
                            <span>Ubah KM</span>
                        </button>

                        <div class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 font-semibold text-xs">
                            <span class="w-2 h-2 rounded-full bg-brand-main dark:bg-brand-accent animate-pulse"></span>
                            <span>+ {{ $activeVehicle->daily_average_km }} KM hari ini</span>
                        </div>
                    </div>

                    <!-- 2 Kolom Meta Info (Dihitung & Servis Terdekat) -->
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800/80 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-slate-50/80 dark:bg-slate-800/50 rounded-2xl p-3.5 border border-slate-100 dark:border-slate-800">
                            <span class="text-[11px] font-semibold text-slate-400 block">Dihitung</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 mt-0.5 block">
                                Hari ini, {{ now()->format('H.i') }}
                            </span>
                        </div>

                        <div class="bg-slate-50/80 dark:bg-slate-800/50 rounded-2xl p-3.5 border border-slate-100 dark:border-slate-800">
                            <span class="text-[11px] font-semibold text-slate-400 block">Servis terdekat</span>
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

                <!-- KARTU 2: BANNER JADWAL SERVIS MENDEKAT -->
                @if ($closestPart)
                    <div class="bg-brand-main text-white rounded-3xl p-6 sm:p-7 shadow-lg shadow-brand-main/20 relative overflow-hidden transition-all">
                        <!-- Lingkaran Dekorasi Background -->
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="absolute -left-6 -top-6 w-24 h-24 bg-white/5 rounded-full blur-xl pointer-events-none"></div>

                        <div class="relative z-10">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
                                <h3 class="text-base sm:text-lg font-black tracking-wide">Jadwal Servis Mendekat</h3>
                            </div>
                            
                            <p class="text-xs sm:text-sm text-white/90 mt-2 leading-relaxed max-w-xl">
                                Servis <span class="font-bold underline decoration-white/40 underline-offset-2">{{ strtolower($closestPart->name) }}</span> dijadwalkan pada <span class="font-extrabold">{{ $closestPart->predicted_date_formatted }}</span>.
                            </p>

                            <button type="button"
                                    @click="openDetailModal({{ json_encode($closestPart) }})" 
                                    class="mt-5 px-5 py-2.5 rounded-full bg-white text-brand-main font-bold text-xs shadow hover:bg-slate-50 transition transform hover:scale-105 active:scale-95 inline-flex items-center space-x-2">
                                <span>Lihat Detail</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                @endif

            </div>

            <!-- KOLOM KANAN: KALENDER SERVIS INTERAKTIF -->
            <div class="lg:col-span-5">
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-7 border border-slate-100 dark:border-slate-800 shadow-sm transition-colors space-y-5">
                    
                    <!-- Judul & Subtitle Kalender -->
                    <div>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white">Kalender Servis</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Tanggal bertanda adalah prediksi servis komponen oleh AI.
                        </p>
                    </div>

                    <!-- Header Navigasi Bulan & Tahun -->
                    <div class="flex items-center justify-between py-2 border-y border-slate-100 dark:border-slate-800">
                        <button type="button"
                                @click="calendar.prevMonth()" 
                                class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 transition"
                                title="Bulan Sebelumnya">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>

                        <span class="text-sm font-bold text-slate-800 dark:text-white" 
                              x-text="calendar.monthNames[calendar.month] + ' ' + calendar.year">
                        </span>

                        <button type="button"
                                @click="calendar.nextMonth()" 
                                class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 transition"
                                title="Bulan Berikutnya">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Kalender Grid -->
                    <div>
                        <!-- Header Hari (Sen - Min) -->
                        <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-slate-400 mb-2">
                            <template x-for="dayName in calendar.dayNames" :key="dayName">
                                <span class="py-1" x-text="dayName"></span>
                            </template>
                        </div>

                        <!-- Grid Tanggal -->
                        <div class="grid grid-cols-7 gap-1 text-center">
                            <template x-for="(day, idx) in calendar.getDays()" :key="idx">
                                <div class="min-h-[46px] flex flex-col items-center justify-start p-0.5">
                                    <template x-if="day.dayNumber">
                                        <button type="button"
                                                @click="calendar.selectDate(day.dateKey)"
                                                class="w-full flex flex-col items-center justify-center py-1 rounded-xl transition relative group">
                                            
                                            <!-- Lingkaran Tanggal -->
                                            <div class="w-7 h-7 flex items-center justify-center text-xs rounded-full font-bold transition"
                                                 :class="{
                                                     'bg-brand-main text-white shadow-sm shadow-brand-main/30': day.hasEvent,
                                                     'ring-2 ring-brand-main text-brand-main dark:text-brand-accent': day.isToday && !day.hasEvent,
                                                     'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white': day.isSelected && !day.hasEvent && !day.isToday,
                                                     'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50': !day.hasEvent && !day.isToday && !day.isSelected
                                                 }">
                                                <span x-text="day.dayNumber"></span>
                                            </div>

                                            <!-- Label Singkat Komponen di Bawah Tanggal (Persis Desain) -->
                                            <template x-if="day.hasEvent">
                                                <span class="text-[9px] font-black text-brand-main dark:text-brand-accent truncate max-w-[42px] leading-tight mt-0.5" 
                                                      x-text="day.eventList[0] ? day.eventList[0].short_name : 'Servis'">
                                                </span>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Panel Keterangan Tanggal Terpilih (Bottom Date Box) -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 dark:text-white" 
                                  x-text="calendar.formatDateIndo(calendar.selectedDate)">
                            </span>
                            <template x-if="calendar.events[calendar.selectedDate]">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-secondary/40 text-brand-main dark:bg-brand-main/20 dark:text-brand-accent">
                                    Prediksi Servis
                                </span>
                            </template>
                        </div>

                        <!-- Jika Ada Jadwal di Tanggal Terpilih -->
                        <template x-if="calendar.events[calendar.selectedDate]">
                            <div class="mt-3 space-y-2">
                                <template x-for="evt in calendar.events[calendar.selectedDate]" :key="evt.id">
                                    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/60">
                                        <div class="flex items-center space-x-2">
                                            <i data-lucide="wrench" class="w-3.5 h-3.5 text-brand-main dark:text-brand-accent"></i>
                                            <span class="text-xs font-bold text-slate-800 dark:text-white" x-text="evt.title"></span>
                                        </div>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" 
                                              :class="{
                                                  'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400': evt.status === 'danger',
                                                  'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400': evt.status === 'warning',
                                                  'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400': evt.status === 'safe'
                                              }" 
                                              x-text="evt.status_label">
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Jika Tidak Ada Jadwal -->
                        <template x-if="!calendar.events[calendar.selectedDate]">
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                Tidak ada jadwal servis.
                            </p>
                        </template>
                    </div>

                </div>
            </div>

        </div>

        <!-- ==================== 2. BAGIAN TENGAH: KESEHATAN KENDARAAN (5 PART VITAL) ==================== -->
        <div class="space-y-4">
            <div>
                <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Kesehatan Kendaraan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed max-w-4xl">
                    Pantau kondisi oli, busi, aki, dan komponen motor Anda lainnya. Tanggal servis diprediksi dari estimasi {{ $activeVehicle->daily_average_km }} KM per hari, jadi Anda tahu kapan perlu merawatnya sebelum terlambat.
                </p>
            </div>

            <!-- Grid Kartu Komponen -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                
                @foreach ($parts as $part)
                    <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-md transition-all group">
                        <div>
                            <!-- Header Kartu: Icon & Status Badge -->
                            <div class="flex items-center justify-between">
                                <div class="w-9 h-9 rounded-2xl bg-brand-secondary/30 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent">
                                    <i data-lucide="{{ $part->icon }}" class="w-4 h-4"></i>
                                </div>

                                <!-- Status Badge (Danger, Warning, Safe) -->
                                @if ($part->status === 'danger')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60">
                                        Danger
                                    </span>
                                @elseif ($part->status === 'warning')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
                                        Warning
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                                        Safe
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
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block mt-0.5">
                                    {{ $part->predicted_date_formatted }}
                                </span>
                            </div>

                            <!-- Progress Bar Kesehatan Part -->
                            <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full mt-3 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $part->status === 'danger' ? 'bg-rose-500' : ($part->status === 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}" 
                                     style="width: {{ $part->percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Action Buttons di Bawah Kartu -->
                        <div class="mt-5 space-y-2">
                            @if ($part->status === 'danger' || $part->status === 'warning')
                                <!-- Tombol Sudah Diservis (Solid Brand) -->
                                <button type="button"
                                        @click="openQuickServiceModal({{ json_encode($part) }})" 
                                        class="w-full py-2 px-3 rounded-full bg-brand-main hover:bg-brand-dark text-white font-bold text-xs flex items-center justify-center space-x-1.5 shadow-sm shadow-brand-main/20 transition hover:scale-[1.02] active:scale-[0.98]">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Sudah diservis</span>
                                </button>
                            @endif

                            <!-- Tombol Ubah Tanggal (Outline Button) -->
                            <button type="button"
                                    @click="openEditDateModal({{ json_encode($part) }})" 
                                    class="w-full py-2 px-3 rounded-full border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs flex items-center justify-center space-x-1.5 transition">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Ubah tanggal</span>
                            </button>
                        </div>
                    </div>
                @endforeach

                <!-- KARTU TAMBAH KOMPONEN / PENGINGAT BARU (+ Tambah Komponen) -->
                <div @click="addReminderModal = true" 
                     class="rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700/80 hover:border-brand-main dark:hover:border-brand-accent p-5 flex flex-col items-center justify-center text-center cursor-pointer group transition-all min-h-[220px]">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 group-hover:bg-brand-secondary/40 dark:group-hover:bg-brand-main/20 flex items-center justify-center text-slate-400 group-hover:text-brand-main dark:group-hover:text-brand-accent transition">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400 group-hover:text-brand-main dark:group-hover:text-brand-accent mt-3 transition">
                        + Tambah Komponen
                    </span>
                </div>

            </div>
        </div>

        <!-- ==================== 3. BAGIAN BAWAH: AKSI CEPAT ==================== -->
        <div class="space-y-4 pt-2">
            <div>
                <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Aksi Cepat</h2>
            </div>

            <!-- Grid 4 Tombol Aksi Cepat Sesuai Desain Foto -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                
                <!-- 1. Kalkulator Kerugian -->
                <a href="{{ route('calculator') }}" 
                   class="group bg-brand-main hover:bg-brand-dark text-white rounded-3xl p-5 shadow-sm shadow-brand-main/15 transition transform hover:-translate-y-1 active:translate-y-0 flex flex-col justify-between min-h-[110px]">
                    <div class="w-9 h-9 rounded-2xl bg-white/15 flex items-center justify-center text-white">
                        <i data-lucide="calculator" class="w-5 h-5"></i>
                    </div>
                    <span class="font-extrabold text-xs sm:text-sm mt-3 leading-tight">
                        Kalkulator Kerugian
                    </span>
                </a>

                <!-- 2. Diagnosa Darurat -->
                <a href="{{ route('diagnosa') }}" 
                   class="group bg-brand-main hover:bg-brand-dark text-white rounded-3xl p-5 shadow-sm shadow-brand-main/15 transition transform hover:-translate-y-1 active:translate-y-0 flex flex-col justify-between min-h-[110px]">
                    <div class="w-9 h-9 rounded-2xl bg-white/15 flex items-center justify-center text-white">
                        <i data-lucide="stethoscope" class="w-5 h-5"></i>
                    </div>
                    <span class="font-extrabold text-xs sm:text-sm mt-3 leading-tight">
                        Diagnosa Darurat
                    </span>
                </a>

                <!-- 3. Riwayat Servis -->
                <a href="{{ route('history') }}" 
                   class="group bg-brand-main hover:bg-brand-dark text-white rounded-3xl p-5 shadow-sm shadow-brand-main/15 transition transform hover:-translate-y-1 active:translate-y-0 flex flex-col justify-between min-h-[110px]">
                    <div class="w-9 h-9 rounded-2xl bg-white/15 flex items-center justify-center text-white">
                        <i data-lucide="history" class="w-5 h-5"></i>
                    </div>
                    <span class="font-extrabold text-xs sm:text-sm mt-3 leading-tight">
                        Riwayat Servis
                    </span>
                </a>

                <!-- 4. Tambah Kendaraan -->
                <button type="button"
                        @click="addVehicleModal = true" 
                        class="group bg-brand-main hover:bg-brand-dark text-white rounded-3xl p-5 shadow-sm shadow-brand-main/15 transition transform hover:-translate-y-1 active:translate-y-0 flex flex-col justify-between text-left min-h-[110px]">
                    <div class="w-9 h-9 rounded-2xl bg-white/15 flex items-center justify-center text-white">
                        <i data-lucide="bike" class="w-5 h-5"></i>
                    </div>
                    <span class="font-extrabold text-xs sm:text-sm mt-3 leading-tight">
                        Tambah Kendaraan
                    </span>
                </button>

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
    <!-- MODAL 4: TAMBAH KOMPONEN / PENGINGAT SERVIS BARU                          -->
    <!-- ========================================================================= -->
    <div x-show="addReminderModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="addReminderModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100 dark:border-slate-800">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Tambah Jadwal Servis Komponen</h3>
                </div>
                <button type="button" @click="addReminderModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            @if ($activeVehicle)
                <form action="{{ route('dashboard.reminders.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $activeVehicle->id }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Pilih Komponen <span class="text-rose-500">*</span>
                        </label>
                        <select name="maintenance_item_id" 
                                required 
                                class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                            @foreach ($allMaintenanceItems as $item)
                                <option value="{{ $item->id }}">{{ $item->item_name }} (Interval {{ number_format($item->interval_km, 0, ',', '.') }} KM)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Target Tanggal Servis <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" 
                               name="scheduled_date" 
                               value="{{ now()->addDays(14)->toDateString() }}"
                               required 
                               class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Target Odometer (KM, Opsional)
                        </label>
                        <input type="number" 
                               name="custom_km" 
                               placeholder="Contoh: 16000"
                               min="0"
                               class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-brand-main focus:border-brand-main">
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="addReminderModal = false" class="px-4 py-2 rounded-full border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-full bg-brand-main hover:bg-brand-dark text-white text-xs font-bold shadow-md shadow-brand-main/20">
                            Tambahkan ke Kalender
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 5: DETAIL JADWAL SERVIS MENDEKAT                                    -->
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
            addReminderModal: false,
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

            calendar: {
                year: currentYear,
                month: currentMonth,
                today: todayStr,
                selectedDate: todayStr,
                events: eventsData || {},
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                dayNames: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],

                prevMonth() {
                    if (this.month === 0) {
                        this.month = 11;
                        this.year--;
                    } else {
                        this.month--;
                    }
                    setTimeout(() => window.lucide && window.lucide.createIcons(), 50);
                },

                nextMonth() {
                    if (this.month === 11) {
                        this.month = 0;
                        this.year++;
                    } else {
                        this.month++;
                    }
                    setTimeout(() => window.lucide && window.lucide.createIcons(), 50);
                },

                getDays() {
                    const firstDayIndex = new Date(this.year, this.month, 1).getDay();
                    const startingDay = firstDayIndex === 0 ? 6 : firstDayIndex - 1;
                    const daysInMonth = new Date(this.year, this.month + 1, 0).getDate();

                    const days = [];
                    for (let i = 0; i < startingDay; i++) {
                        days.push({ dayNumber: null, dateKey: null });
                    }

                    for (let d = 1; d <= daysInMonth; d++) {
                        const mm = String(this.month + 1).padStart(2, '0');
                        const dd = String(d).padStart(2, '0');
                        const dateKey = this.year + '-' + mm + '-' + dd;
                        days.push({
                            dayNumber: d,
                            dateKey: dateKey,
                            isToday: dateKey === this.today,
                            isSelected: dateKey === this.selectedDate,
                            hasEvent: !!this.events[dateKey],
                            eventList: this.events[dateKey] || []
                        });
                    }
                    return days;
                },

                selectDate(dateKey) {
                    if (dateKey) {
                        this.selectedDate = dateKey;
                        setTimeout(() => window.lucide && window.lucide.createIcons(), 50);
                    }
                },

                formatDateIndo(dateStr) {
                    if (!dateStr) return '';
                    const parts = dateStr.split('-');
                    if (parts.length !== 3) return dateStr;
                    const d = new Date(parts[0], parts[1] - 1, parts[2]);
                    const dayNamesLong = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    return dayNamesLong[d.getDay()] + ', ' + parseInt(parts[2], 10) + ' ' + this.monthNames[d.getMonth()] + ' ' + parts[0];
                }
            }
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endsection
