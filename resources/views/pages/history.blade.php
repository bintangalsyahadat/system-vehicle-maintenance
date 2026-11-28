@extends('layouts.app')

@section('title', 'Buku Paspor Servis & Nota Digital')

@section('greeting')
<div class="flex items-center space-x-2 text-sm sm:text-base">
    <a href="{{ route('garage') }}" class="text-slate-400 hover:text-brand-main dark:hover:text-brand-accent transition flex items-center space-x-1">
        <i data-lucide="bike" class="w-4 h-4"></i>
        <span>Garasi Kendaraan</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-600"></i>
    <span class="font-bold text-slate-800 dark:text-white">Riwayat & Paspor</span>
</div>
@endsection

@section('content')
<div class="space-y-6" x-data="historyManager()">

    <!-- Flash Message Notification -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center space-x-2">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(!$activeVehicle)
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-12 text-center text-slate-400 space-y-3 border border-slate-100 dark:border-slate-700/80">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                <i data-lucide="bike" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-700 dark:text-slate-200">Belum Ada Kendaraan Dipilih</h3>
            <p class="text-xs max-w-sm mx-auto">Silakan tambahkan atau pilih kendaraan Anda di Garasi untuk melihat Buku Paspor Servis.</p>
            <a href="{{ route('garage') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-brand-main text-white text-xs font-bold shadow-md shadow-brand-main/20">
                <i data-lucide="bike" class="w-4 h-4"></i>
                <span>Buka Garasi Kendaraan</span>
            </a>
        </div>
    @else

        <!-- ==================== 1. FINANCIAL & PASSPORT SUMMARY CARD ==================== -->
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-700/80 shadow-sm flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6 transition-colors">
            <!-- Left Info: Motor Aktif & Passport Status -->
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent flex-shrink-0">
                    <i data-lucide="clipboard-check" class="w-8 h-8"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white">{{ $activeVehicle->model_name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            {{ $passportScore }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center space-x-2">
                        <span>{{ $activeVehicle->brand }}</span>
                        <span>&bull;</span>
                        <span class="font-mono">{{ $activeVehicle->license_plate ?: 'Tanpa Plat' }}</span>
                        <span>&bull;</span>
                        <span>Odo: <strong>{{ number_format($activeVehicle->current_odometer, 0, ',', '.') }} KM</strong></span>
                    </p>
                </div>
            </div>

            <!-- Middle Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 flex-1 lg:max-w-xl">
                <!-- Total Biaya Servis -->
                <div class="bg-slate-50 dark:bg-slate-900/70 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-700/80">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pengeluaran</span>
                    <span class="text-sm sm:text-base font-black text-slate-800 dark:text-white mt-0.5 block">
                        Rp {{ number_format($totalCost, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Frekuensi Servis -->
                <div class="bg-slate-50 dark:bg-slate-900/70 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-700/80">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Catatan Servis</span>
                    <span class="text-sm sm:text-base font-black text-slate-800 dark:text-white mt-0.5 block">
                        {{ $totalServices }} Log Selesai
                    </span>
                </div>

                <!-- Riwayat Diagnosa AI -->
                <div class="bg-slate-50 dark:bg-slate-900/70 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-700/80 col-span-2 sm:col-span-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Diagnosa AI</span>
                    <span class="text-sm sm:text-base font-black text-slate-800 dark:text-white mt-0.5 block">
                        {{ $totalAiScans }} Riwayat Scan
                    </span>
                </div>
            </div>

            <!-- Right Buttons: Scan Nota, Manual Entry, & Print PDF -->
            <div class="flex flex-col sm:flex-row lg:flex-col gap-2 justify-end">
                <button type="button" 
                        @click="openReceiptScanModal()" 
                        class="flex items-center justify-center space-x-2 px-4 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-black transition shadow-md shadow-emerald-600/25">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                    <span>Scan Nota</span>
                </button>

                <div class="flex items-center space-x-2">
                    <button type="button" 
                            @click="openManualAddModal()" 
                            class="flex-1 flex items-center justify-center space-x-1 px-3 py-2.5 rounded-2xl bg-brand-main hover:bg-brand-dark text-white text-xs font-bold transition shadow-md shadow-brand-main/20">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Manual</span>
                    </button>

                    <a href="{{ route('history.passport', $activeVehicle->id) }}" target="_blank"
                       class="flex-1 flex items-center justify-center space-x-1 px-3 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition shadow-sm">
                        <i data-lucide="printer" class="w-4 h-4 text-brand-main dark:text-brand-accent"></i>
                        <span>Cetak</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ==================== 2. TABS & FILTER CONTROLS ==================== -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Tab Selector -->
            <div class="flex items-center space-x-1.5 bg-slate-100 dark:bg-slate-900/80 p-1 rounded-2xl border border-slate-200/60 dark:border-slate-700/80">
                <button type="button" 
                        @click="activeTab = 'service_logs'" 
                        :class="activeTab === 'service_logs' ? 'bg-white dark:bg-[#1E293B] text-brand-main dark:text-brand-accent font-bold shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-medium'"
                        class="flex items-center space-x-2 px-4 py-2 rounded-xl text-xs transition">
                    <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                    <span>Log Servis Bengkel ({{ $totalServices }})</span>
                </button>

                <button type="button" 
                        @click="activeTab = 'ai_scans'" 
                        :class="activeTab === 'ai_scans' ? 'bg-white dark:bg-[#1E293B] text-brand-main dark:text-brand-accent font-bold shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-medium'"
                        class="flex items-center space-x-2 px-4 py-2 rounded-xl text-xs transition">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>Riwayat Diagnosa AI ({{ $totalAiScans }})</span>
                </button>
            </div>

            <!-- Fast Filter Dropdown (Hanya aktif saat di tab service_logs) -->
            <div x-show="activeTab === 'service_logs'" class="flex items-center space-x-2">
                <select x-model="selectedItem" 
                        class="text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1E293B] text-slate-700 dark:text-slate-200 py-1.5 px-3 focus:border-brand-main focus:ring-brand-main/20">
                    <option value="all">Semua Komponen</option>
                    @foreach($maintenanceItems as $item)
                        <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- ==================== 3. TAB 1: LOG SERVIS BENGKEL ==================== -->
        <div x-show="activeTab === 'service_logs'" class="space-y-4">
            @forelse($groupedLogs as $log)
                <div x-show="selectedItem === 'all' || ({{ json_encode($log->part_ids) }}).includes(parseInt(selectedItem))"
                     class="bg-white dark:bg-[#1E293B] rounded-3xl p-5 sm:p-6 border border-slate-100 dark:border-slate-700/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:border-slate-200 dark:hover:border-slate-700">
                    
                    <div class="flex items-start space-x-4">
                        <!-- Gambar Thumbnail Nota atau Icon Bengkel -->
                        @if($log->receipt_image_url)
                            <div class="relative group cursor-pointer flex-shrink-0 mt-0.5" @click="previewReceipt('{{ $log->receipt_image_url }}')">
                                <img src="{{ $log->receipt_image_url }}" alt="Nota Bengkel" 
                                     class="w-14 h-14 rounded-2xl object-cover border-2 border-emerald-500/40 shadow-sm">
                                <div class="absolute inset-0 bg-slate-900/40 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                    <i data-lucide="zoom-in" class="w-4 h-4 text-white"></i>
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-2xl bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent flex-shrink-0 mt-0.5">
                                <i data-lucide="wrench" class="w-5 h-5"></i>
                            </div>
                        @endif

                        <div class="space-y-2 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-extrabold text-slate-800 dark:text-white text-base">
                                    {{ $log->part_names->isNotEmpty() ? 'Servis & Penggantian Suku Cadang' : 'Servis Berkala / Pemeriksaan Umum' }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ $log->workshop_name ?: 'Bengkel Mandiri' }}
                                </span>
                                @if($log->receipt_image_url)
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        <i data-lucide="shield-check" class="w-3 h-3"></i>
                                        <span>Terverifikasi Nota Asli</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Tag Komponen yang Diservis (Multi-Part Badges) -->
                            @if($log->part_names->isNotEmpty())
                                <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                    @foreach($log->part_names as $partName)
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-brand-secondary/30 dark:bg-brand-main/20 text-brand-main dark:text-brand-accent border border-brand-secondary/50 dark:border-brand-main/30">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            <span>{{ $partName }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <p class="text-xs text-slate-400 flex items-center space-x-3 pt-0.5">
                                <span class="flex items-center space-x-1">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span>{{ \Carbon\Carbon::parse($log->service_date)->translatedFormat('d F Y') }}</span>
                                </span>
                                <span>&bull;</span>
                                <span class="flex items-center space-x-1 font-mono">
                                    <i data-lucide="gauge" class="w-3.5 h-3.5"></i>
                                    <span>{{ number_format($log->odometer_at_service, 0, ',', '.') }} KM</span>
                                </span>
                            </p>
                            @if($log->notes)
                                <p class="text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/70 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/80 mt-2">
                                    {{ $log->notes }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Cost & Action Buttons (Edit & Hapus) -->
                    <div class="flex md:flex-col items-center md:items-end justify-between border-t md:border-t-0 pt-3 md:pt-0 border-slate-100 dark:border-slate-700/80 space-y-2">
                        <span class="text-base font-black text-brand-main dark:text-brand-accent">
                            Rp {{ number_format($log->actual_cost, 0, ',', '.') }}
                        </span>

                        <div class="flex items-center space-x-1.5 pt-1">
                            <!-- Tombol Edit Servis -->
                            <button type="button" 
                                    @click="openEditModal({{ json_encode($log) }})" 
                                    class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition text-xs flex items-center space-x-1"
                                    title="Edit Catatan Servis">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline font-semibold">Edit</span>
                            </button>

                            <!-- Tombol Hapus Servis -->
                            <button type="button" 
                                    @click="confirmDeleteModal({{ json_encode($log) }})" 
                                    class="p-2 rounded-xl border border-rose-200 dark:border-rose-900/40 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-rose-600 dark:text-rose-400 transition text-xs flex items-center space-x-1"
                                    title="Hapus Catatan Servis">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline font-semibold">Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-12 text-center text-slate-400 space-y-3 border border-slate-100 dark:border-slate-700/80">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                        <i data-lucide="clipboard" class="w-8 h-8"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Belum Ada Log Servis</h4>
                    <p class="text-xs max-w-sm mx-auto">Catat riwayat servis pertama motor ini untuk mulai membangun Paspor Servis digital resmi.</p>
                    <div class="pt-2 flex justify-center space-x-3">
                        <button @click="openReceiptScanModal()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 flex items-center space-x-1.5">
                            <i data-lucide="camera" class="w-4 h-4"></i>
                            <span>Scan Nota</span>
                        </button>
                        <button @click="openManualAddModal()" class="px-4 py-2 rounded-xl bg-brand-main text-white text-xs font-bold shadow-md shadow-brand-main/20">
                            Catat Manual
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- ==================== 4. TAB 2: RIWAYAT DIAGNOSA AI ==================== -->
        <div x-show="activeTab === 'ai_scans'" class="space-y-4">
            @forelse($aiScans as $scan)
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-5 sm:p-6 border border-slate-100 dark:border-slate-700/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all">
                    <div class="flex items-start space-x-4">
                        @if($scan->image_url)
                            <img src="{{ $scan->image_url }}" alt="Foto Scan" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0 bg-slate-900 mt-0.5">
                        @else
                            <div class="w-12 h-12 rounded-2xl {{ $scan->severity_level === 'CRITICAL' ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-500' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-500' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                            </div>
                        @endif
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <h3 class="font-extrabold text-slate-800 dark:text-white text-base">
                                    {{ $scan->detected_indicator }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $scan->severity_level === 'CRITICAL' ? 'bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400' }}">
                                    {{ $scan->severity_level }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 flex items-center space-x-2">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                <span>{{ \Carbon\Carbon::parse($scan->created_at)->translatedFormat('d F Y, H:i') }} WIB</span>
                            </p>
                            <p class="text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/70 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/80 mt-2">
                                <strong>Protokol:</strong> {{ $scan->emergency_action }}
                            </p>
                            @if($scan->estimated_cost_min && $scan->estimated_cost_max)
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Estimasi Biaya AI: Rp {{ number_format($scan->estimated_cost_min, 0, ',', '.') }} - Rp {{ number_format($scan->estimated_cost_max, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Status & Resolution Action -->
                    <div class="flex md:flex-col items-center md:items-end justify-between border-t md:border-t-0 pt-3 md:pt-0 border-slate-100 dark:border-slate-700/80">
                        @if($scan->is_resolved)
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                <span>Terselesaikan</span>
                            </span>
                        @else
                            <form action="{{ route('history.resolveAiScan', $scan->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm flex items-center space-x-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Tandai Selesai</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-12 text-center text-slate-400 space-y-3 border border-slate-100 dark:border-slate-700/80">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                        <i data-lucide="sparkles" class="w-8 h-8"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Belum Ada Riwayat Scan AI</h4>
                    <p class="text-xs max-w-sm mx-auto">Gunakan fitur Diagnosa Kamera di Menu 2 untuk memindai lampu indikator MIL speedometer motor Anda.</p>
                    <a href="{{ route('diagnosa') }}" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-brand-main text-white text-xs font-bold shadow-sm">
                        <i data-lucide="stethoscope" class="w-4 h-4"></i>
                        <span>Scan Sekarang</span>
                    </a>
                </div>
            @endforelse
        </div>

    @endif

    <!-- ==================== 5. MODAL 1: SCAN NOTA BENGKEL DENGAN AI VISION ==================== -->
    <div x-show="scanReceiptModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="closeReceiptScanModal()" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700 flex flex-col max-h-[90vh]">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i data-lucide="scan" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-sm sm:text-base">Scan Nota Servis Bengkel (AI OCR)</h3>
                        <p class="text-[10px] text-slate-400">Ekstraksi otomatis nama bengkel, tanggal, KM, dan rincian item nota</p>
                    </div>
                </div>
                <button @click="closeReceiptScanModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Viewfinder / File Upload Box -->
            <div class="space-y-4 overflow-y-auto no-scrollbar flex-1">

                <!-- Inline Error Banner (Menggantikan alert() bawaan browser) -->
                <div x-show="receiptScanError" x-cloak class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/70 text-rose-700 dark:text-rose-300 text-xs flex items-start justify-between gap-3">
                    <div class="flex items-start space-x-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <div class="space-y-1">
                            <p class="font-semibold" x-text="receiptScanError"></p>
                            <button type="button" @click="closeReceiptScanModal(); openManualAddModal();" class="underline font-bold hover:text-rose-900 dark:hover:text-rose-100">
                                Lanjut input manual tanpa scan AI
                            </button>
                        </div>
                    </div>
                    <button type="button" @click="receiptScanError = ''" class="text-rose-400 hover:text-rose-600">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <!-- State 1: Pilih Sumber Gambar (Kamera Live vs Upload Galeri) -->
                <div x-show="!receiptCapturedImage" class="space-y-3">
                    <div class="flex items-center justify-center space-x-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                        <button type="button" 
                                @click="switchScanMode('camera')"
                                :class="receiptScanMode === 'camera' ? 'bg-white dark:bg-slate-700 text-brand-main dark:text-brand-accent shadow-sm' : 'text-slate-500'"
                                class="flex-1 py-1.5 text-xs font-bold rounded-lg transition">
                            Kamera Langsung
                        </button>
                        <button type="button" 
                                @click="switchScanMode('file')"
                                :class="receiptScanMode === 'file' ? 'bg-white dark:bg-slate-700 text-brand-main dark:text-brand-accent shadow-sm' : 'text-slate-500'"
                                class="flex-1 py-1.5 text-xs font-bold rounded-lg transition">
                            Unggah Foto / PDF
                        </button>
                    </div>

                    <!-- Mode Kamera -->
                    <div x-show="receiptScanMode === 'camera'" class="relative rounded-2xl overflow-hidden bg-slate-900 aspect-[4/3] flex items-center justify-center">
                        <video x-ref="receiptVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                        <div class="absolute inset-4 border-2 border-dashed border-emerald-400/60 rounded-xl pointer-events-none flex flex-col justify-between p-2">
                            <span class="text-[10px] text-emerald-300 font-bold bg-slate-900/60 px-2 py-0.5 rounded self-start">Arahkan ke Nota Bengkel</span>
                            <span class="text-[10px] text-emerald-300 font-bold bg-slate-900/60 px-2 py-0.5 rounded self-end">Pastikan tulisan terbaca jelas</span>
                        </div>
                    </div>

                    <!-- Mode Unggah File -->
                    <div x-show="receiptScanMode === 'file'" 
                         @click="$refs.receiptFileInput.click()"
                         class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-8 text-center cursor-pointer hover:border-emerald-500 dark:hover:border-emerald-500 transition space-y-2 bg-slate-50 dark:bg-slate-900/70">
                        <input type="file" x-ref="receiptFileInput" @change="handleReceiptFileSelect" accept="image/*,application/pdf" class="hidden">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                            <i data-lucide="image-plus" class="w-6 h-6"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Klik untuk memilih foto nota fisik atau dokumen invoice PDF</p>
                        <p class="text-[10px] text-slate-400">Mendukung format JPG, PNG, WebP, dan PDF Dokumen (Maks 10MB)</p>
                    </div>
                </div>

                <!-- State 2: Preview Hasil Foto / Nota / PDF yang Dipilih -->
                <div x-show="receiptCapturedImage" class="space-y-3">
                    <div class="relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 aspect-[4/3] bg-slate-900 flex items-center justify-center">
                        <template x-if="isPdfFile">
                            <div class="text-center p-6 text-white space-y-3">
                                <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center mx-auto">
                                    <i data-lucide="file-text" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm" x-text="receiptFileName || 'Dokumen Nota PDF'"></h4>
                                    <span class="text-[11px] text-slate-400">Dokumen PDF siap dipindai oleh Gemini AI</span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!isPdfFile">
                            <img :src="receiptCapturedImage" alt="Nota Bengkel" class="w-full h-full object-contain">
                        </template>
                        <button type="button" 
                                @click="resetReceiptCapture()" 
                                class="absolute top-2 right-2 px-2.5 py-1 rounded-lg bg-slate-900/80 text-white text-[10px] font-bold hover:bg-slate-900 transition flex items-center space-x-1">
                            <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                            <span>Pilih Ulang</span>
                        </button>
                    </div>

                    <!-- Indikator Loading AI -->
                    <div x-show="isAnalyzingReceipt" class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center space-y-2">
                        <div class="w-6 h-6 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                        <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Google Gemini AI sedang mengekstrak teks nota...</p>
                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400">Mendeteksi nama bengkel, tanggal servis, total biaya, dan rincian suku cadang</p>
                    </div>
                </div>

            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700 flex space-x-2">
                <button type="button" 
                        @click="closeReceiptScanModal()" 
                        class="w-1/3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Batal
                </button>

                <template x-if="!receiptCapturedImage && receiptScanMode === 'camera'">
                    <button type="button" 
                            @click="captureReceiptFromCamera()" 
                            class="w-2/3 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-md shadow-emerald-600/30 flex items-center justify-center space-x-2">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                        <span>Ambil Foto Nota</span>
                    </button>
                </template>

                <template x-if="receiptCapturedImage && !isAnalyzingReceipt">
                    <button type="button" 
                            @click="processReceiptAnalysis()" 
                            class="w-2/3 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-md shadow-emerald-600/30 flex items-center justify-center space-x-2">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>Proses Ekstraksi AI</span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- ==================== 6. MODAL 2: FORM CATAT / EDIT SERVIS LENGKAP (ORDER LINES CRUD) ==================== -->
    <div x-show="serviceFormModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="serviceFormModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700 flex flex-col max-h-[90vh]">
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="clipboard-pen" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base" x-text="isEditMode ? 'Edit Catatan Servis' : 'Konfirmasi & Catat Servis'"></h3>
                        <p class="text-[11px] text-slate-400">{{ $activeVehicle ? $activeVehicle->model_name : '' }}</p>
                    </div>
                </div>
                <button @click="serviceFormModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Body with Scrollable Area -->
            <form :action="isEditMode ? '{{ route('history.updateLog') }}' : '{{ route('history.storeLog') }}'" 
                  method="POST" 
                  class="space-y-3.5 text-left overflow-y-auto no-scrollbar flex-1 pr-1">
                @csrf
                <template x-if="isEditMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="vehicle_id" value="{{ $activeVehicle ? $activeVehicle->id : '' }}">
                <input type="hidden" name="receipt_image_url" :value="formData.receipt_image_url">

                <template x-if="isEditMode">
                    <div>
                        <template x-for="id in formData.log_ids" :key="id">
                            <input type="hidden" name="log_ids[]" :value="id">
                        </template>
                    </div>
                </template>

                <!-- Thumbnail Nota Jika Ada -->
                <div x-show="formData.receipt_image_url" class="p-2.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 flex items-center space-x-3">
                    <img :src="formData.receipt_image_url" alt="Nota" class="w-12 h-12 rounded-xl object-cover border border-emerald-300 dark:border-emerald-700 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Nota Terlampir</span>
                        <p class="text-xs text-slate-700 dark:text-slate-200 truncate">Foto nota tersimpan otomatis sebagai bukti verifikasi resmi.</p>
                    </div>
                </div>

                <!-- Informasi Bengkel, Tanggal & Odometer -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Bengkel</label>
                        <input type="text" name="workshop_name" x-model="formData.workshop_name" required placeholder="Misal: AHASS, Bengkel Mandiri"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Tanggal Servis</label>
                        <input type="date" name="service_date" x-model="formData.service_date" required
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Odometer saat Servis (KM)</label>
                        <input type="number" name="odometer_at_service" x-model="formData.odometer_at_service" required min="0"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Total Biaya Nota (Rp)</label>
                        <input type="number" name="actual_cost" x-model="formData.actual_cost" required min="0"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-bold focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    </div>
                </div>

                <!-- Bagian Rincian Item Nota (Multi Order Lines) - Hanya di mode Buat Baru -->
                <div x-show="!isEditMode" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                            Rincian Item Nota / Suku Cadang
                        </label>
                        <button type="button" @click="addOrderLine()" class="text-[11px] font-bold text-brand-main dark:text-brand-accent hover:underline flex items-center space-x-1">
                            <i data-lucide="plus" class="w-3 h-3"></i>
                            <span>Tambah Item</span>
                        </button>
                    </div>

                    <div class="space-y-2 max-h-48 overflow-y-auto no-scrollbar p-1">
                        <template x-for="(line, index) in formData.items" :key="index">
                            <div class="p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-700/80 flex items-center space-x-2">
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center space-x-1.5">
                                        <select :name="'items[' + index + '][maintenance_item_id]'" 
                                                x-model="line.maintenance_item_id"
                                                class="w-1/2 text-[11px] rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 py-1 px-2">
                                            <option value="">-- Part Umum / Jasa --</option>
                                            @foreach($maintenanceItems as $item)
                                                <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" 
                                               :name="'items[' + index + '][item_name]'" 
                                               x-model="line.name" 
                                               placeholder="Nama item / suku cadang" 
                                               class="w-1/2 text-[11px] rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 py-1 px-2">
                                    </div>
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-[10px] text-slate-400">Rp</span>
                                        <input type="number" 
                                               :name="'items[' + index + '][cost]'" 
                                               x-model.number="line.price" 
                                               @input="calculateTotalFromLines()"
                                               placeholder="Harga item" 
                                               class="w-full text-[11px] rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 py-1 px-2">
                                    </div>
                                </div>
                                <button type="button" @click="removeOrderLine(index)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                                    <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </template>

                        <div x-show="formData.items.length === 0" class="p-3 text-center text-[11px] text-slate-400 italic bg-slate-50 dark:bg-slate-900/30 rounded-xl">
                            Belum ada baris item. Seluruh biaya akan dicatat sebagai servis berkala umum.
                        </div>
                    </div>
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" x-model="formData.notes" rows="2" placeholder="Catatan mekanik, keluhan motor, atau no. garansi..."
                              class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3"></textarea>
                </div>

                <div class="pt-2 flex space-x-2">
                    <button type="button" @click="serviceFormModal = false" 
                            class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-brand-main hover:bg-brand-dark text-xs font-bold text-white transition shadow-md shadow-brand-main/30">
                        <span x-text="isEditMode ? 'Simpan Perubahan' : 'Simpan ke Paspor'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== 7. MODAL 3: PREVIEW FOTO NOTA ASLI ==================== -->
    <div x-show="previewModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="previewModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-lg w-full p-4 shadow-2xl space-y-3 border border-transparent dark:border-slate-700 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700/80">
                <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center space-x-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                    <span>Bukti Nota Servis Terverifikasi</span>
                </span>
                <button @click="previewModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="flex-1 overflow-auto rounded-2xl bg-slate-950 flex items-center justify-center p-2">
                <template x-if="previewImageUrl.toLowerCase().endsWith('.pdf')">
                    <iframe :src="previewImageUrl" class="w-full h-[65vh] rounded-xl border-0"></iframe>
                </template>
                <template x-if="!previewImageUrl.toLowerCase().endsWith('.pdf')">
                    <img :src="previewImageUrl" alt="Nota Asli" class="max-h-[70vh] object-contain rounded-xl">
                </template>
            </div>
        </div>
    </div>

    <!-- ==================== 8. MODAL 4: KONFIRMASI HAPUS SERVIS ==================== -->
    <div x-show="deleteConfirmModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="deleteConfirmModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700 text-center">
            
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-500 flex items-center justify-center mx-auto">
                <i data-lucide="trash-2" class="w-6 h-6"></i>
            </div>

            <div class="space-y-1">
                <h4 class="font-extrabold text-slate-800 dark:text-white text-base">Hapus Catatan Servis?</h4>
                <p class="text-xs text-slate-400">Data servis dan penggantian part ini akan dihapus secara permanen dari Paspor Servis.</p>
            </div>

            <form action="{{ route('history.destroyLog') }}" method="POST" class="space-y-2 pt-2">
                @csrf
                @method('DELETE')
                <input type="hidden" name="vehicle_id" value="{{ $activeVehicle ? $activeVehicle->id : '' }}">
                <template x-for="id in deleteLogIds" :key="id">
                    <input type="hidden" name="log_ids[]" :value="id">
                </template>

                <div class="flex space-x-2">
                    <button type="button" @click="deleteConfirmModal = false" 
                            class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-md shadow-rose-600/30">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Alpine.js History Manager Controller -->
<script>
function historyManager() {
    return {
        activeTab: 'service_logs',
        selectedItem: 'all',
        scanReceiptModal: false,
        serviceFormModal: false,
        previewModal: false,
        deleteConfirmModal: false,
        isEditMode: false,
        
        // Receipt Scanner State
        receiptScanMode: 'camera', // 'camera' or 'file'
        receiptStream: null,
        receiptCapturedImage: null,
        receiptFileName: '',
        isPdfFile: false,
        isAnalyzingReceipt: false,
        receiptScanError: '',
        previewImageUrl: '',
        deleteLogIds: [],

        // Form Data State
        formData: {
            workshop_name: '',
            service_date: '{{ date("Y-m-d") }}',
            odometer_at_service: {{ $activeVehicle ? $activeVehicle->current_odometer : 0 }},
            actual_cost: 0,
            notes: '',
            receipt_image_url: '',
            items: [],
            log_ids: [],
        },

        init() {
            this.$watch('scanReceiptModal', (val) => {
                if (!val && this.receiptStream) {
                    this.stopCameraStream();
                }
            });

            // Auto-buka modal scan jika datang dari Aksi Cepat Dashboard (?scan=receipt)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('scan') === 'receipt') {
                this.$nextTick(() => {
                    this.openReceiptScanModal();
                });
            }
        },

        openReceiptScanModal() {
            this.scanReceiptModal = true;
            this.receiptCapturedImage = null;
            this.receiptScanError = '';
            this.receiptScanMode = 'camera';
            this.$nextTick(() => {
                this.startCameraStream();
            });
        },

        closeReceiptScanModal() {
            this.stopCameraStream();
            this.scanReceiptModal = false;
        },

        switchScanMode(mode) {
            this.receiptScanMode = mode;
            if (mode === 'camera') {
                this.$nextTick(() => this.startCameraStream());
            } else {
                this.stopCameraStream();
            }
        },

        startCameraStream() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 960 } } 
                })
                .then((stream) => {
                    this.receiptStream = stream;
                    if (this.$refs.receiptVideo) {
                        this.$refs.receiptVideo.srcObject = stream;
                    }
                })
                .catch((err) => {
                    console.warn('Camera access denied or unavailable:', err);
                    this.receiptScanMode = 'file';
                });
            } else {
                this.receiptScanMode = 'file';
            }
        },

        stopCameraStream() {
            if (this.receiptStream) {
                this.receiptStream.getTracks().forEach(track => track.stop());
                this.receiptStream = null;
            }
        },

        captureReceiptFromCamera() {
            if (!this.$refs.receiptVideo) return;
            const video = this.$refs.receiptVideo;
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 800;
            canvas.height = video.videoHeight || 600;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            this.receiptCapturedImage = canvas.toDataURL('image/jpeg', 0.85);
            this.stopCameraStream();
        },

        handleReceiptFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.receiptFileName = file.name;
            this.isPdfFile = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
            const reader = new FileReader();
            reader.onload = (e) => {
                this.receiptCapturedImage = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        resetReceiptCapture() {
            this.receiptCapturedImage = null;
            this.receiptFileName = '';
            this.isPdfFile = false;
            this.receiptScanError = '';
            if (this.receiptScanMode === 'camera') {
                this.$nextTick(() => this.startCameraStream());
            }
        },

        processReceiptAnalysis() {
            if (!this.receiptCapturedImage) return;
            this.isAnalyzingReceipt = true;
            this.receiptScanError = '';

            fetch('{{ route("history.scanReceipt") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    receipt_image: this.receiptCapturedImage,
                    vehicle_id: {{ $activeVehicle ? $activeVehicle->id : 'null' }}
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isAnalyzingReceipt = false;
                if (data.success && data.data) {
                    const extracted = data.data;
                    this.formData.workshop_name = extracted.workshop_name || 'AHASS Honda';
                    this.formData.service_date = extracted.service_date || '{{ date("Y-m-d") }}';
                    this.formData.odometer_at_service = extracted.odometer_km || {{ $activeVehicle ? $activeVehicle->current_odometer : 0 }};
                    this.formData.actual_cost = extracted.total_cost || 0;
                    this.formData.notes = extracted.notes || '';
                    this.formData.receipt_image_url = data.image_url || '';
                    this.formData.items = (extracted.items || []).map(item => ({
                        maintenance_item_id: item.matched_part_id || '',
                        name: item.name || '',
                        price: item.price || 0
                    }));
                    this.formData.log_ids = [];

                    this.isEditMode = false;
                    this.scanReceiptModal = false;
                    this.serviceFormModal = true;
                } else {
                    this.receiptScanError = data.message || 'Gagal mengekstrak nota. Pastikan gambar terbaca jelas atau lanjutkan input manual.';
                }
            })
            .catch(err => {
                this.isAnalyzingReceipt = false;
                console.error('Scan Error:', err);
                this.receiptScanError = 'Terjadi kendala koneksi saat memindai nota. Silakan coba lagi atau lanjutkan input manual.';
            });
        },

        openManualAddModal() {
            this.isEditMode = false;
            this.formData = {
                workshop_name: '',
                service_date: '{{ date("Y-m-d") }}',
                odometer_at_service: {{ $activeVehicle ? $activeVehicle->current_odometer : 0 }},
                actual_cost: 0,
                notes: '',
                receipt_image_url: '',
                items: [
                    { maintenance_item_id: 1, name: 'Oli Mesin SPX2', price: 65000 }
                ],
                log_ids: [],
            };
            this.calculateTotalFromLines();
            this.serviceFormModal = true;
        },

        openEditModal(log) {
            this.isEditMode = true;
            this.formData = {
                workshop_name: log.workshop_name || 'Bengkel Mandiri',
                service_date: log.service_date,
                odometer_at_service: log.odometer_at_service,
                actual_cost: log.actual_cost,
                notes: log.notes || '',
                receipt_image_url: log.receipt_image_url || '',
                items: [],
                log_ids: log.log_ids || [log.id],
            };
            this.serviceFormModal = true;
        },

        confirmDeleteModal(log) {
            this.deleteLogIds = log.log_ids || [log.id];
            this.deleteConfirmModal = true;
        },

        previewReceipt(url) {
            this.previewImageUrl = url;
            this.previewModal = true;
        },

        addOrderLine() {
            this.formData.items.push({
                maintenance_item_id: '',
                name: '',
                price: 0
            });
        },

        removeOrderLine(index) {
            this.formData.items.splice(index, 1);
            this.calculateTotalFromLines();
        },

        calculateTotalFromLines() {
            const sum = this.formData.items.reduce((acc, item) => acc + (parseFloat(item.price) || 0), 0);
            if (sum > 0) {
                this.formData.actual_cost = sum;
            }
        }
    };
}
</script>
@endsection
