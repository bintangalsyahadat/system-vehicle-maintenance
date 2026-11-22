@extends('layouts.app')

@section('title', 'Buku Paspor Servis')

@section('greeting')
<div class="flex items-center space-x-2 text-sm sm:text-base">
    <a href="{{ route('garage') }}" class="text-slate-400 hover:text-brand-main dark:hover:text-brand-accent transition flex items-center space-x-1">
        <i data-lucide="bike" class="w-4 h-4"></i>
        <span>Garasi Kendaraan</span>
    </a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-600"></i>
    <span class="font-bold text-slate-800 dark:text-white">Riwayat</span>
</div>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'service_logs', // 'service_logs' atau 'ai_scans'
    addLogModal: false,
    filterYear: 'all',
    selectedItem: 'all'
}">

    <!-- Flash Message Notification -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(!$activeVehicle)
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-12 text-center text-slate-400 space-y-3 border border-slate-100 dark:border-slate-800">
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
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6 transition-colors">
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
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pengeluaran</span>
                    <span class="text-sm sm:text-base font-black text-slate-800 dark:text-white mt-0.5 block">
                        Rp {{ number_format($totalCost, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Frekuensi Servis -->
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Catatan Servis</span>
                    <span class="text-sm sm:text-base font-black text-slate-800 dark:text-white mt-0.5 block">
                        {{ $totalServices }} Log Selesai
                    </span>
                </div>

                <!-- Riwayat Diagnosa AI -->
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800 col-span-2 sm:col-span-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Diagnosa AI</span>
                    <span class="text-sm sm:text-base font-black text-slate-800 dark:text-white mt-0.5 block">
                        {{ $totalAiScans }} Riwayat Scan
                    </span>
                </div>
            </div>

            <!-- Right Buttons: Print PDF & Add Service Log -->
            <div class="flex items-center space-x-2 lg:flex-col lg:space-x-0 lg:space-y-2 justify-end">
                <button @click="addLogModal = true" 
                        class="flex-1 lg:w-full flex items-center justify-center space-x-1.5 px-4 py-2.5 rounded-2xl bg-brand-main hover:bg-brand-dark text-white text-xs font-bold transition shadow-md shadow-brand-main/25">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Catat Servis</span>
                </button>

                <a href="{{ route('history.passport', $activeVehicle->id) }}" target="_blank"
                   class="flex-1 lg:w-full flex items-center justify-center space-x-1.5 px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4 text-brand-main dark:text-brand-accent"></i>
                    <span>Cetak Paspor</span>
                </a>
            </div>
        </div>

        <!-- ==================== 2. TABS & FILTER CONTROLS ==================== -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Tab Selector -->
            <div class="flex items-center space-x-1.5 bg-slate-100 dark:bg-slate-900/80 p-1 rounded-2xl border border-slate-200/60 dark:border-slate-800">
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
                     class="bg-white dark:bg-[#1E293B] rounded-3xl p-5 sm:p-6 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:border-slate-200 dark:hover:border-slate-700">
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent flex-shrink-0 mt-0.5">
                            <i data-lucide="wrench" class="w-5 h-5"></i>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <h3 class="font-extrabold text-slate-800 dark:text-white text-base">
                                    {{ $log->part_names->isNotEmpty() ? 'Servis & Penggantian Suku Cadang' : 'Servis Berkala / Pemeriksaan Umum' }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ $log->workshop_name ?: 'Bengkel Mandiri' }}
                                </span>
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
                                <p class="text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 mt-2">
                                    {{ $log->notes }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Cost & Verification Badge -->
                    <div class="flex md:flex-col items-center md:items-end justify-between border-t md:border-t-0 pt-3 md:pt-0 border-slate-100 dark:border-slate-800">
                        <span class="text-base font-black text-brand-main dark:text-brand-accent">
                            Rp {{ number_format($log->actual_cost, 0, ',', '.') }}
                        </span>
                        <span class="inline-flex items-center space-x-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>Terverifikasi Pemilik</span>
                        </span>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-12 text-center text-slate-400 space-y-3 border border-slate-100 dark:border-slate-800">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                        <i data-lucide="clipboard" class="w-8 h-8"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Belum Ada Log Servis</h4>
                    <p class="text-xs max-w-sm mx-auto">Catat riwayat servis pertama motor ini untuk mulai membangun Paspor Servis digital resmi.</p>
                    <button @click="addLogModal = true" class="px-4 py-2 rounded-xl bg-brand-main text-white text-xs font-bold shadow-md shadow-brand-main/20">
                        Catat Servis Sekarang
                    </button>
                </div>
            @endforelse
        </div>

        <!-- ==================== 4. TAB 2: RIWAYAT DIAGNOSA AI ==================== -->
        <div x-show="activeTab === 'ai_scans'" class="space-y-4">
            @forelse($aiScans as $scan)
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-5 sm:p-6 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all">
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
                            <p class="text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 mt-2">
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
                    <div class="flex md:flex-col items-center md:items-end justify-between border-t md:border-t-0 pt-3 md:pt-0 border-slate-100 dark:border-slate-800">
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
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-12 text-center text-slate-400 space-y-3 border border-slate-100 dark:border-slate-800">
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

    <!-- ==================== 5. MODAL CATAT SERVIS BARU ==================== -->
    <div x-show="addLogModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="addLogModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700">
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="clipboard-pen" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base">Catat Servis Baru</h3>
                        <p class="text-[11px] text-slate-400">{{ $activeVehicle ? $activeVehicle->model_name : '' }}</p>
                    </div>
                </div>
                <button @click="addLogModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Catat Servis -->
            <form action="{{ route('history.storeLog') }}" method="POST" class="space-y-3 text-left">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $activeVehicle ? $activeVehicle->id : '' }}">

                <!-- Pilih Komponen / Sparepart (Multi-Checklist) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300">
                            Komponen yang Diganti / Diservis
                        </label>
                        <span class="text-[10px] text-slate-400">Bisa pilih lebih dari 1</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/80 max-h-40 overflow-y-auto no-scrollbar">
                        @foreach($maintenanceItems as $item)
                            <label class="flex items-center space-x-2 text-xs text-slate-700 dark:text-slate-300 hover:text-brand-main dark:hover:text-brand-accent cursor-pointer p-1 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition">
                                <input type="checkbox" name="maintenance_item_ids[]" value="{{ $item->id }}"
                                       class="rounded border-slate-300 dark:border-slate-700 text-brand-main focus:ring-brand-main/20">
                                <span class="truncate">{{ $item->item_name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1 italic">*Kosongkan jika hanya servis rutin / periksa umum tanpa ganti part.</p>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <!-- Tanggal Servis -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Tanggal Servis</label>
                        <input type="date" name="service_date" value="{{ date('Y-m-d') }}" required
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    </div>
                    <!-- Odometer saat Servis -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Odometer (KM)</label>
                        <input type="number" name="odometer_at_service" value="{{ $activeVehicle ? $activeVehicle->current_odometer : 0 }}" required
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <!-- Total Biaya Aktual -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Total Biaya (Rp)</label>
                        <input type="number" name="actual_cost" placeholder="Contoh: 65000" required
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    </div>
                    <!-- Nama Bengkel -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Bengkel</label>
                        <input type="text" name="workshop_name" placeholder="Misal: AHASS, Bengkel Mandiri"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    </div>
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Catatan mekanik, keluhan motor, atau no. garansi..."
                              class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3"></textarea>
                </div>

                <div class="pt-2 flex space-x-2">
                    <button type="button" @click="addLogModal = false" 
                            class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-brand-main hover:bg-brand-dark text-xs font-bold text-white transition shadow-md shadow-brand-main/30">
                        Simpan ke Paspor
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
