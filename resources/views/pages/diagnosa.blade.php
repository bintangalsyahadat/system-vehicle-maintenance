@extends('layouts.app')

@section('title', 'Diagnosa AI Vision')
@section('greeting', 'Diagnosa Speedometer AI')

@section('content')
<div class="space-y-6" x-data="speedometerScanner()">

    <!-- Flash Alert Notifications -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center space-x-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(!$activeVehicle)
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-12 text-center text-slate-400 space-y-3 border border-slate-100 dark:border-slate-700/80">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                <i data-lucide="bike" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-700 dark:text-slate-200">Belum Ada Kendaraan Dipilih</h3>
            <p class="text-xs max-w-sm mx-auto">Silakan pilih atau tambahkan kendaraan Anda di Garasi untuk melakukan diagnosa AI.</p>
            <a href="{{ route('garage') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-brand-main text-white text-xs font-bold shadow-md shadow-brand-main/20">
                <i data-lucide="bike" class="w-4 h-4"></i>
                <span>Buka Garasi Kendaraan</span>
            </a>
        </div>
    @else

        <!-- ==================== HEADER MOTOR AKTIF ==================== -->
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 border border-slate-100 dark:border-slate-700/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent flex-shrink-0">
                    <i data-lucide="scan" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="text-xl font-black text-slate-800 dark:text-white">{{ $activeVehicle->model_name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-secondary/40 dark:bg-slate-800 text-brand-main dark:text-brand-accent">
                            {{ $activeVehicle->brand }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5 flex items-center space-x-2">
                        <span>Plat: <strong>{{ $activeVehicle->license_plate ?: 'Tanpa Plat' }}</strong></span>
                        <span>&bull;</span>
                        <span>Odo Terkini: <strong>{{ number_format($activeVehicle->current_odometer, 0, ',', '.') }} KM</strong></span>
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Gemini 1.5 Flash Vision</span>
                </span>
            </div>
        </div>

        <!-- ==================== KARTU HASIL SCAN TERAKHIR (JIKA ADA) ==================== -->
        @php
            $latestScanId = session('latest_scan_id');
            $scan = $latestScanId ? \App\Models\AiScanHistory::find($latestScanId) : null;
            $detectedOdo = session('detected_odometer');
        @endphp
        @if($scan)
            @php
                $isCritical = $scan->severity_level === 'CRITICAL';
                $isWarning = $scan->severity_level === 'WARNING';
            @endphp
            <div class="p-6 rounded-3xl border {{ $isCritical ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/60 ring-2 ring-rose-500/20' : ($isWarning ? 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-900/60 ring-2 ring-amber-500/20' : 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900/60') }} space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-700/80">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl {{ $isCritical ? 'bg-rose-500 text-white' : ($isWarning ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white') }} flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $isCritical ? 'alert-octagon' : ($isWarning ? 'alert-triangle' : 'check-circle-2') }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $isCritical ? 'text-rose-600 dark:text-rose-400' : ($isWarning ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                                Tingkat Keparahan: {{ $scan->severity_level }}
                            </span>
                            <h3 class="text-base font-black text-slate-800 dark:text-white">{{ $scan->detected_indicator }}</h3>
                        </div>
                    </div>

                    <!-- Sinkronkan Odometer jika terdeteksi -->
                    @if($detectedOdo)
                        <form action="{{ route('vehicles.syncOdometer', $activeVehicle->id) }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            <input type="hidden" name="odometer" value="{{ $detectedOdo }}">
                            <div class="text-right mr-1">
                                <span class="block text-[10px] text-slate-400">Odometer Terdeteksi</span>
                                <span class="font-mono font-bold text-xs text-slate-700 dark:text-slate-200">{{ number_format($detectedOdo, 0, ',', '.') }} KM</span>
                            </div>
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-brand-main text-white text-xs font-bold hover:bg-brand-dark transition shadow-sm flex items-center space-x-1">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                <span>Sinkronkan ODO</span>
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Protokol Darurat Bertahap -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-brand-main dark:text-brand-accent"></i>
                        <span>Protokol Darurat Pengendara</span>
                    </h4>
                    
                    @php
                        // Bersihkan dan pisahkan protokol menjadi item-item bertahap
                        $rawAction = $scan->emergency_action;
                        // Standarisasi: pecah berdasarkan newline atau pola angka '1. ', '2. ', '3. '
                        $steps = preg_split('/(?:\r\n|\r|\n|(?<=[.!?])\s*(?=[1-9]\.))/', $rawAction);
                        $steps = array_filter(array_map('trim', $steps));
                    @endphp

                    <div class="space-y-2">
                        @forelse($steps as $step)
                            <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-700/80 text-xs text-slate-700 dark:text-slate-200 flex items-start space-x-3">
                                <div class="w-5 h-5 rounded-lg bg-brand-secondary/50 dark:bg-brand-main/20 text-brand-main dark:text-brand-accent flex items-center justify-center font-bold text-[11px] flex-shrink-0 mt-0.5">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="flex-1 leading-relaxed font-medium">
                                    {{ preg_replace('/^[1-9]\.\s*/', '', $step) }}
                                </div>
                            </div>
                        @empty
                            <div class="p-4 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-700/80 text-xs text-slate-700 dark:text-slate-200">
                                {{ $scan->emergency_action }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Estimasi Biaya Perbaikan -->
                @if($scan->estimated_cost_max > 0)
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-white/60 dark:bg-slate-900/70 border border-slate-200/60 dark:border-slate-700/80 text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Estimasi Biaya Perbaikan di Bengkel:</span>
                        <span class="font-bold text-brand-main dark:text-brand-accent font-mono text-sm">
                            Rp {{ number_format($scan->estimated_cost_min, 0, ',', '.') }} - Rp {{ number_format($scan->estimated_cost_max, 0, ',', '.') }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <!-- ==================== AREA PEMINDAI (CTA CARD + PANDUAN) ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Card Pemicu Buka Scanner Modal (8 Kolom) -->
            <div class="lg:col-span-8 bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-700/80 shadow-sm flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-brand-secondary/40 dark:bg-brand-main/20 text-brand-main dark:text-brand-accent">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                        <span>AI Vision Speedometer Diagnostics</span>
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white">
                        Pindai Panel Speedometer & Indikator Motor
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-xl">
                        AI akan membaca angka Odometer terkini dan mendeteksi lampu peringatan Malfunction Indicator Lamp (MIL), Check Engine, Aki, atau Suhu Mesin untuk memberikan pertolongan pertama secara akurat.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center gap-3">
                    <button type="button" 
                            @click="openScannerModal('camera')" 
                            class="px-5 py-3 rounded-2xl bg-brand-main hover:bg-brand-dark text-white text-xs font-bold transition shadow-md shadow-brand-main/25 hover:shadow-brand-main/40 flex items-center space-x-2">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                        <span>Buka Kamera Langsung</span>
                    </button>
                    <button type="button" 
                            @click="openScannerModal('upload')" 
                            class="px-5 py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center space-x-2 border border-slate-200 dark:border-slate-700">
                        <i data-lucide="image" class="w-4 h-4"></i>
                        <span>Unggah Foto dari Galeri</span>
                    </button>
                </div>
            </div>

            <!-- Panduan & Edukasi Lampu Indikator MIL (4 Kolom) -->
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 border border-slate-100 dark:border-slate-700/80 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm flex items-center space-x-2">
                        <i data-lucide="info" class="w-4 h-4 text-brand-main dark:text-brand-accent"></i>
                        <span>Panduan Lampu Speedometer</span>
                    </h3>
                    <div class="space-y-3 text-xs">
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700/80 space-y-1">
                            <div class="font-bold text-rose-500 flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Lampu Check Engine / MIL</span>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 text-[11px] leading-relaxed">
                                Berkedip atau menyala terus menandakan anomali pada sensor injeksi/ECM.
                            </p>
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700/80 space-y-1">
                            <div class="font-bold text-amber-500 flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Lampu Indikator Aki / Baterai</span>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 text-[11px] leading-relaxed">
                                Tegangan aki berada di bawah standar atau sistem pengisian bermasalah.
                            </p>
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700/80 space-y-1">
                            <div class="font-bold text-red-600 flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                <span>Lampu Suhu / Radiator</span>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 text-[11px] leading-relaxed">
                                Suhu coolant mesin melampaui batas aman (overheating). Segera matikan mesin!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ==================== MODAL POPUP PEMINDAI SPEEDOMETER ==================== -->
        <div x-show="isModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.outside="closeScannerModal()" 
                 class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 border border-slate-100 dark:border-slate-700/80 flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700/80">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent">
                            <i data-lucide="scan" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white text-sm">Pindai Panel Speedometer</h3>
                            <p class="text-[10px] text-slate-400">{{ $activeVehicle->brand }} {{ $activeVehicle->model_name }}</p>
                        </div>
                    </div>

                    <!-- Switch Mode Kamera / File -->
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold">
                            <button type="button" 
                                    @click="switchMode('camera')" 
                                    :class="mode === 'camera' ? 'bg-white dark:bg-slate-700 text-brand-main dark:text-brand-accent shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                    class="px-2.5 py-1 rounded-lg transition flex items-center space-x-1 text-[11px]">
                                <i data-lucide="camera" class="w-3 h-3"></i>
                                <span>Kamera</span>
                            </button>
                            <button type="button" 
                                    @click="switchMode('upload')" 
                                    :class="mode === 'upload' ? 'bg-white dark:bg-slate-700 text-brand-main dark:text-brand-accent shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                    class="px-2.5 py-1 rounded-lg transition flex items-center space-x-1 text-[11px]">
                                <i data-lucide="upload" class="w-3 h-3"></i>
                                <span>Galeri</span>
                            </button>
                        </div>
                        <button type="button" @click="closeScannerModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Form Analisis AI -->
                <form id="scanForm" action="{{ route('diagnosa.analyze') }}" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" class="space-y-4">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $activeVehicle->id }}">
                    <input type="hidden" name="image_base64" x-model="imageBase64">

                    <!-- Viewfinder Screen Container -->
                    <div class="relative w-full aspect-[4/3] sm:aspect-[16/10] bg-slate-900 rounded-2xl overflow-hidden border border-slate-300 dark:border-slate-700 flex items-center justify-center">
                        
                        <!-- 1. Mode Kamera Live Stream -->
                        <div x-show="mode === 'camera' && !previewUrl" class="w-full h-full relative flex items-center justify-center">
                            <video x-ref="videoElement" autoplay playsinline muted class="w-full h-full object-cover"></video>
                            
                            <!-- Scanner HUD Overlay -->
                            <div class="absolute inset-0 pointer-events-none flex flex-col justify-between p-4">
                                <div class="flex justify-between items-center text-white/80 text-[10px] font-mono">
                                    <span class="flex items-center space-x-1 bg-black/50 px-2 py-0.5 rounded-md backdrop-blur-sm">
                                        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping mr-1"></span> LIVE CAMERA
                                    </span>
                                    <span class="bg-black/50 px-2 py-0.5 rounded-md backdrop-blur-sm">ARAHKAN KE SPEEDOMETER</span>
                                </div>
                                <!-- Target Reticle Grid -->
                                <div class="w-48 h-28 sm:w-60 sm:h-36 mx-auto border-2 border-brand-main/70 rounded-2xl relative shadow-lg shadow-brand-main/20 flex items-center justify-center">
                                    <span class="text-[9px] text-white/70 font-mono bg-black/50 px-2 py-0.5 rounded">Fokus Speedometer</span>
                                    <div class="absolute -top-1 -left-1 w-3 h-3 border-t-2 border-l-2 border-white"></div>
                                    <div class="absolute -top-1 -right-1 w-3 h-3 border-t-2 border-r-2 border-white"></div>
                                    <div class="absolute -bottom-1 -left-1 w-3 h-3 border-b-2 border-l-2 border-white"></div>
                                    <div class="absolute -bottom-1 -right-1 w-3 h-3 border-b-2 border-r-2 border-white"></div>
                                </div>
                                <div class="text-center text-white/70 text-[9px] bg-black/50 py-0.5 px-2 rounded-full mx-auto backdrop-blur-sm">
                                    Pastikan ODO & lampu indikator terlihat terang
                                </div>
                            </div>
                        </div>

                        <!-- 2. Mode Upload File -->
                        <div x-show="mode === 'upload' && !previewUrl" 
                             @click="$refs.fileInput.click()" 
                             class="cursor-pointer text-center p-8 space-y-3">
                            <div class="w-14 h-14 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center mx-auto hover:scale-105 transition">
                                <i data-lucide="image" class="w-7 h-7 text-brand-main dark:text-brand-accent"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-200">Klik untuk Pilih Foto Speedometer</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">JPG, PNG, atau WebP</p>
                            </div>
                            <input type="file" x-ref="fileInput" name="image_file" accept="image/*" class="hidden" @change="handleFileSelect($event)">
                        </div>

                        <!-- 3. Preview Gambar yang Sudah Diambil / Dipilih -->
                        <div x-show="previewUrl" class="w-full h-full relative">
                            <img :src="previewUrl" class="w-full h-full object-contain bg-black/90">
                            <button type="button" 
                                    @click="retakeImage()" 
                                    class="absolute top-3 right-3 px-3 py-1.5 rounded-xl bg-black/60 hover:bg-black/80 text-white text-[11px] font-semibold backdrop-blur-sm transition flex items-center space-x-1">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Ambil Ulang</span>
                            </button>
                        </div>

                    </div>

                    <!-- Tombol Aksi Kontrol -->
                    <div class="pt-2 flex items-center justify-between gap-3">
                        <button type="button" 
                                @click="closeScannerModal()" 
                                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Tutup
                        </button>

                        <div class="flex items-center space-x-2">
                            <!-- Capture Button (Saat live camera) -->
                            <button type="button" 
                                    x-show="mode === 'camera' && !previewUrl" 
                                    @click="captureFromCamera()" 
                                    class="px-5 py-2.5 rounded-xl bg-brand-main hover:bg-brand-dark text-white text-xs font-bold transition shadow-md shadow-brand-main/30 flex items-center space-x-1.5">
                                <i data-lucide="aperture" class="w-4 h-4"></i>
                                <span>Ambil Foto</span>
                            </button>

                            <!-- Analyze Submit Button -->
                            <button type="submit" 
                                    x-show="previewUrl" 
                                    :disabled="isSubmitting" 
                                    class="px-5 py-2.5 rounded-xl bg-brand-main hover:bg-brand-dark text-white text-xs font-bold transition shadow-md shadow-brand-main/30 flex items-center space-x-1.5 disabled:opacity-60">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                <span x-text="isSubmitting ? 'Menganalisis...' : 'Analisis Kerusakan AI'"></span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- ==================== RIWAYAT DIAGNOSA TERAKHIR ==================== -->
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 border border-slate-100 dark:border-slate-700/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Riwayat Hasil Diagnosa</h3>
                    <p class="text-xs text-slate-400">Catatan pemindaian speedometer yang pernah dilakukan pada kendaraan ini.</p>
                </div>
                <span class="text-xs font-semibold text-slate-400">{{ $scanHistories->count() }} Kali Pemindaian</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($scanHistories as $history)
                    @php
                        $isCrit = $history->severity_level === 'CRITICAL';
                        $isWarn = $history->severity_level === 'WARNING';
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 hover:border-slate-200 dark:hover:border-slate-700 transition">
                        <div class="flex items-center space-x-3.5">
                            @if($history->image_url)
                                <img src="{{ $history->image_url }}" alt="Scan Speedometer" class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0 bg-slate-900">
                            @else
                                <div class="w-12 h-12 rounded-xl {{ $isCrit ? 'bg-rose-100 dark:bg-rose-950/40 text-rose-500' : ($isWarn ? 'bg-amber-100 dark:bg-amber-950/40 text-amber-500' : 'bg-emerald-100 dark:bg-emerald-950/40 text-emerald-500') }} flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $isCrit ? 'alert-octagon' : ($isWarn ? 'alert-triangle' : 'check-circle-2') }}" class="w-6 h-6"></i>
                                </div>
                            @endif
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-bold text-slate-800 dark:text-white text-xs">{{ $history->detected_indicator }}</h4>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $history->is_resolved ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $history->is_resolved ? 'Selesai' : 'Perlu Cek' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 flex items-center space-x-2">
                                    <span>{{ $history->created_at->diffForHumans() }}</span>
                                    <span>&bull;</span>
                                    <span>Tingkat: <strong class="{{ $isCrit ? 'text-rose-500' : ($isWarn ? 'text-amber-500' : 'text-emerald-500') }}">{{ $history->severity_level }}</strong></span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 w-full sm:w-auto justify-between sm:justify-end pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-700/80">
                            @if($history->estimated_cost_max > 0)
                                <div class="text-left sm:text-right text-xs">
                                    <span class="block text-[10px] text-slate-400">Est. Biaya</span>
                                    <span class="font-bold text-brand-main dark:text-brand-accent font-mono text-xs">
                                        Rp {{ number_format($history->estimated_cost_min, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            @if(!$history->is_resolved)
                                <form action="{{ route('history.resolveAiScan', $history->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-bold transition shadow-sm flex items-center space-x-1"
                                            title="Tandai masalah ini sudah diperbaiki">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Selesai</span>
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center space-x-1 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat pemindaian speedometer untuk kendaraan ini.
                    </div>
                @endforelse
            </div>
        </div>

    @endif

</div>

<!-- Alpine Scanner Component Logic -->
<script>
function speedometerScanner() {
    return {
        isModalOpen: false,
        mode: 'camera', // 'camera' atau 'upload'
        previewUrl: null,
        imageBase64: '',
        isSubmitting: false,
        mediaStream: null,

        init() {
            // Auto-buka scanner modal jika user datang dari tombol cepat Dashboard (?scan=camera)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('scan') === 'camera') {
                this.$nextTick(() => {
                    this.openScannerModal('camera');
                });
            }
        },

        openScannerModal(initialMode = 'camera') {
            this.isModalOpen = true;
            this.mode = initialMode;
            this.retakeImage();
            this.$nextTick(() => {
                if (initialMode === 'camera') {
                    this.startCamera();
                } else {
                    this.stopCamera();
                }
            });
        },

        closeScannerModal() {
            this.isModalOpen = false;
            this.stopCamera();
            this.retakeImage();
        },

        switchMode(newMode) {
            this.mode = newMode;
            this.retakeImage();
            if (newMode === 'camera') {
                this.startCamera();
            } else {
                this.stopCamera();
            }
        },

        startCamera() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } } 
                }).then(stream => {
                    this.mediaStream = stream;
                    if (this.$refs.videoElement) {
                        this.$refs.videoElement.srcObject = stream;
                    }
                }).catch(err => {
                    console.warn("Kamera tidak tersedia atau ditolak, fallback ke upload file:", err);
                    this.mode = 'upload';
                });
            } else {
                this.mode = 'upload';
            }
        },

        stopCamera() {
            if (this.mediaStream) {
                this.mediaStream.getTracks().forEach(track => track.stop());
                this.mediaStream = null;
            }
        },

        captureFromCamera() {
            const video = this.$refs.videoElement;
            if (!video) return;

            const canvas = document.createElement('canvas');
            canvas.width = Math.min(800, video.videoWidth || 800);
            canvas.height = (canvas.width / (video.videoWidth || 800)) * (video.videoHeight || 600);
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Kompresi ketat ke JPEG quality 0.7 (Hemat Token Vision)
            this.previewUrl = canvas.toDataURL('image/jpeg', 0.7);
            this.imageBase64 = this.previewUrl;
            this.stopCamera();
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    // Kompresi client-side maksimal 800px untuk penghematan kuota token
                    const canvas = document.createElement('canvas');
                    const maxDim = 800;
                    let width = img.width;
                    let height = img.height;

                    if (width > height && width > maxDim) {
                        height = Math.round((height * maxDim) / width);
                        width = maxDim;
                    } else if (height > maxDim) {
                        width = Math.round((width * maxDim) / height);
                        height = maxDim;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    this.previewUrl = canvas.toDataURL('image/jpeg', 0.75);
                    this.imageBase64 = this.previewUrl;
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        retakeImage() {
            this.previewUrl = null;
            this.imageBase64 = '';
            this.isSubmitting = false;
            if (this.mode === 'camera') {
                this.startCamera();
            }
        }
    };
}
</script>
@endsection
