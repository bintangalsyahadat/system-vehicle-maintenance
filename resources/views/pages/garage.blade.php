@extends('layouts.app')

@section('title', 'Garasi Kendaraan')
@section('greeting', 'Garasi Kendaraan')

@section('content')
<div class="space-y-6" x-data="{ 
    searchQuery: '',
    editVehicleModal: false,
    recalibrateModal: false,
    deleteVehicleModal: false,
    deletingVehicle: {
        id: null,
        model_name: ''
    },
    editingVehicle: {
        id: null,
        brand: '',
        model_name: '',
        license_plate: '',
        manufacture_year: ''
    },
    calibratingVehicle: {
        id: null,
        model_name: '',
        current_odometer: 0,
        daily_average_km: 20
    },
    openEditModal(veh) {
        this.editingVehicle = { 
            id: veh.id,
            brand: veh.brand,
            model_name: veh.model_name,
            license_plate: veh.license_plate,
            manufacture_year: veh.manufacture_year
        };
        this.editVehicleModal = true;
    },
    openRecalibrateModal(veh) {
        this.calibratingVehicle = {
            id: veh.id,
            model_name: veh.model_name,
            current_odometer: veh.current_odometer,
            daily_average_km: veh.daily_average_km
        };
        this.recalibrateModal = true;
    },
    openDeleteModal(veh) {
        this.deletingVehicle = {
            id: veh.id,
            model_name: veh.model_name
        };
        this.deleteVehicleModal = true;
    }
}">

    <!-- Flash Message Notification -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent">
                <i data-lucide="bike" class="w-7 h-7"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white">Garasi Kendaraan Saya</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola data kendaraan, kalibrasi angka kilometer, dan pilih kendaraan aktif.</p>
            </div>
        </div>

        <button @click="addVehicleModal = true" 
                class="w-full sm:w-auto flex items-center justify-center space-x-2 px-5 py-3 rounded-2xl bg-brand-main hover:bg-brand-dark text-white text-xs font-bold transition shadow-md shadow-brand-main/25">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Kendaraan Baru</span>
        </button>
    </div>

    <!-- Search Bar & Summary Filter -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari berdasarkan nama motor, merk, atau plat nomor..." 
                   class="w-full pl-10 pr-9 py-2.5 text-xs rounded-2xl border-slate-200 dark:border-slate-800 bg-white dark:bg-[#1E293B] text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-brand-main focus:ring focus:ring-brand-main/20 shadow-sm transition">
            <button x-show="searchQuery.length > 0" 
                    @click="searchQuery = ''" 
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 px-1">
            Total {{ $vehicles->count() }} Kendaraan Terdaftar
        </div>
    </div>

    <!-- Grid Kartu Kendaraan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($vehicles as $vehicle)
            @php
                $isActive = session('active_vehicle_id') == $vehicle->id || (!session('active_vehicle_id') && $loop->first);
                $searchableText = strtolower($vehicle->model_name . ' ' . $vehicle->brand . ' ' . ($vehicle->license_plate ?? ''));
            @endphp
            <div x-show="searchQuery === '' || '{{ $searchableText }}'.includes(searchQuery.toLowerCase())"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="rounded-3xl p-6 border transition-all duration-200 flex flex-col justify-between {{ $isActive ? 'border-brand-main bg-white dark:bg-[#1E293B] ring-2 ring-brand-main/30 shadow-lg shadow-brand-main/5' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-[#1E293B] hover:border-slate-300 dark:hover:border-slate-700 shadow-sm' }}">
                <div class="space-y-4">
                    <!-- Card Top Header -->
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent">
                            <i data-lucide="bike" class="w-6 h-6"></i>
                        </div>
                        @if($isActive)
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-brand-main text-white shadow-sm flex items-center space-x-1">
                                <i data-lucide="check" class="w-3 h-3"></i>
                                <span>Aktif Dipakai</span>
                            </span>
                        @else
                            <form action="{{ route('vehicles.switch', $vehicle->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-[10px] font-bold text-slate-500 dark:text-slate-400 hover:text-brand-main dark:hover:text-brand-accent hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition">
                                    Pilih Kendaraan
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Vehicle Name & Brand -->
                    <div>
                        <h3 class="font-extrabold text-slate-800 dark:text-white text-lg">{{ $vehicle->model_name }}</h3>
                        <div class="flex items-center space-x-2 text-xs text-slate-400 mt-0.5">
                            <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $vehicle->brand }}</span>
                            <span>&bull;</span>
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-mono text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                                {{ $vehicle->license_plate ?: 'Tanpa Plat' }}
                            </span>
                        </div>
                    </div>

                    <!-- Metrics Stats Card with Recalibrate Icon Button -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80">
                        <div class="bg-slate-50 dark:bg-slate-900/40 p-3 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div class="grid grid-cols-2 gap-3 flex-1 text-xs pr-2">
                                <div>
                                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">Odometer</span>
                                    <span class="font-black text-slate-800 dark:text-slate-100 text-sm">
                                        {{ number_format($vehicle->current_odometer, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-400">KM</span>
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">Rata-rata/hari</span>
                                    <span class="font-black text-slate-800 dark:text-slate-100 text-sm">
                                        {{ $vehicle->daily_average_km }} <span class="text-[10px] font-normal text-slate-400">KM</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Button Icon Kalibrasi Ulang (Affordance tombol yang jelas + icon refresh-cw) -->
                            <button type="button" 
                                    @click="openRecalibrateModal({
                                        id: {{ $vehicle->id }},
                                        model_name: '{{ addslashes($vehicle->model_name) }}',
                                        current_odometer: {{ $vehicle->current_odometer }},
                                        daily_average_km: {{ $vehicle->daily_average_km }}
                                    })"
                                    class="p-2.5 rounded-xl bg-brand-main text-white hover:bg-brand-dark transition-all duration-200 shadow-md shadow-brand-main/25 hover:shadow-brand-main/40 hover:scale-105 active:scale-95 flex-shrink-0" 
                                    title="Kalibrasi Ulang">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ==================== KONDISI 5 SPAREPART VITAL (CIRCULAR DONUT GAUGE) ==================== -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 flex items-center space-x-1.5">
                                <i data-lucide="activity" class="w-3.5 h-3.5 text-brand-main dark:text-brand-accent"></i>
                                <span>Kondisi Sparepart Vital</span>
                            </span>
                            <span class="text-[10px] text-slate-400">Geser &rarr;</span>
                        </div>

                        <!-- Horizontal Scrollable Container -->
                        <div class="flex items-center space-x-2.5 overflow-x-auto no-scrollbar pb-1 pt-1 -mx-1 px-1">
                            @foreach($vehicle->parts_health as $part)
                                @php
                                    // Hitung stroke-dashoffset untuk SVG circle (lingkaran keliling = 2 * pi * 18 = 113.1)
                                    $radius = 16;
                                    $circumference = 2 * 3.14159 * $radius; // ~100.53
                                    $dashoffset = $circumference - ($circumference * $part->percentage / 100);
                                @endphp
                                <div class="flex flex-col items-center flex-shrink-0 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800/80 w-[78px] hover:border-slate-300 dark:hover:border-slate-700 transition group"
                                     title="{{ $part->name }}: sisa {{ number_format($part->km_remaining, 0, ',', '.') }} KM ({{ $part->percentage }}%)">
                                    
                                    <!-- Donut Chart SVG -->
                                    <div class="relative w-11 h-11 flex items-center justify-center">
                                        <svg class="w-11 h-11 transform -rotate-90" viewBox="0 0 40 40">
                                            <!-- Background Ring -->
                                            <circle cx="20" cy="20" r="{{ $radius }}" 
                                                    stroke="currentColor" stroke-width="3.5" 
                                                    class="text-slate-200 dark:text-slate-800 fill-none" />
                                            <!-- Progress Value Ring -->
                                            <circle cx="20" cy="20" r="{{ $radius }}" 
                                                    stroke="{{ $part->stroke_color }}" stroke-width="3.5" 
                                                    stroke-dasharray="{{ $circumference }}" 
                                                    stroke-dashoffset="{{ $dashoffset }}" 
                                                    stroke-linecap="round" 
                                                    class="fill-none transition-all duration-500 ease-out" />
                                        </svg>
                                        <!-- Percentage in Center -->
                                        <span class="absolute text-[10px] font-black {{ $part->color }} font-mono">
                                            {{ $part->percentage }}<span class="text-[8px]">%</span>
                                        </span>
                                    </div>

                                    <!-- Part Short Label -->
                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200 mt-1.5 truncate max-w-[70px] text-center" title="{{ $part->name }}">
                                        {{ $part->short_name }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">Tahun: {{ $vehicle->manufacture_year ?: '-' }}</span>
                    <div class="flex items-center space-x-1.5">
                        <!-- Button Lihat Riwayat Servis -->
                        <a href="{{ route('history', ['vehicle_id' => $vehicle->id]) }}" 
                           class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-brand-secondary/40 dark:bg-slate-800 hover:bg-brand-secondary/70 dark:hover:bg-slate-700 text-brand-main dark:text-brand-accent text-xs font-bold transition"
                           title="Lihat Riwayat Servis {{ $vehicle->model_name }}">
                            <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                            <span class="text-[11px]">Riwayat</span>
                        </a>

                        <!-- Button Edit Kendaraan -->
                        <button type="button" 
                                @click="openEditModal({
                                    id: {{ $vehicle->id }},
                                    brand: '{{ addslashes($vehicle->brand) }}',
                                    model_name: '{{ addslashes($vehicle->model_name) }}',
                                    license_plate: '{{ addslashes($vehicle->license_plate ?? '') }}',
                                    manufacture_year: '{{ $vehicle->manufacture_year ?? '' }}'
                                })"
                                class="p-2 rounded-xl text-slate-400 hover:text-brand-main hover:bg-brand-secondary/30 dark:hover:bg-slate-800 transition" 
                                title="Edit Spesifikasi Kendaraan">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>

                        @if($vehicles->count() > 1)
                            <button type="button" 
                                    @click="openDeleteModal({
                                        id: {{ $vehicle->id }},
                                        model_name: '{{ addslashes($vehicle->model_name) }}'
                                    })"
                                    class="p-2 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition" 
                                    title="Hapus Kendaraan">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400 space-y-3 bg-white dark:bg-[#1E293B] rounded-3xl p-8 border border-slate-100 dark:border-slate-800">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                    <i data-lucide="bike" class="w-8 h-8"></i>
                </div>
                <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Garasi Anda Masih Kosong</h4>
                <p class="text-xs max-w-sm mx-auto text-slate-400">Tambahkan kendaraan pertama Anda untuk mulai memantau odometer dan jadwal servis secara otomatis.</p>
                <button @click="addVehicleModal = true" class="px-4 py-2 rounded-xl bg-brand-main text-white text-xs font-bold transition shadow-sm">
                    Tambah Kendaraan Sekarang
                </button>
            </div>
        @endforelse

        <!-- Empty State Saat Hasil Pencarian Tidak Ditemukan -->
        @if($vehicles->count() > 0)
            <div x-cloak 
                 x-show="searchQuery !== '' && !Array.from($el.parentElement.children).some(el => el !== $el && el.style.display !== 'none')" 
                 class="col-span-full py-12 text-center text-slate-400 space-y-3 bg-white dark:bg-[#1E293B] rounded-3xl p-8 border border-slate-100 dark:border-slate-800">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                    <i data-lucide="search-x" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Kendaraan Tidak Ditemukan</h4>
                <p class="text-xs text-slate-400">Tidak ada kendaraan dengan kata kunci "<span class="font-semibold text-brand-main" x-text="searchQuery"></span>".</p>
                <button @click="searchQuery = ''" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Reset Pencarian
                </button>
            </div>
        @endif
    </div>

    <!-- ==================== 1. MODAL EDIT KENDARAAN (Merk, Model, Plat, Tahun) ==================== -->
    <div x-show="editVehicleModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="editVehicleModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700">
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Edit Spesifikasi Kendaraan</h3>
                </div>
                <button @click="editVehicleModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Edit Kendaraan -->
            <form :action="'{{ url('/vehicles') }}/' + editingVehicle.id" method="POST" class="space-y-3 text-left">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Merk Kendaraan</label>
                    <input type="text" name="brand" x-model="editingVehicle.brand" required
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Model / Nama Kendaraan</label>
                    <input type="text" name="model_name" x-model="editingVehicle.model_name" required
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Plat Nomor</label>
                        <input type="text" name="license_plate" x-model="editingVehicle.license_plate" placeholder="B 1234 ABC"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Tahun Pembuatan</label>
                        <input type="number" name="manufacture_year" x-model="editingVehicle.manufacture_year" placeholder="2023"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                    </div>
                </div>

                <div class="pt-3 flex space-x-2">
                    <button type="button" @click="editVehicleModal = false" 
                            class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-brand-main hover:bg-brand-dark text-xs font-bold text-white transition shadow-md shadow-brand-main/30">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== 2. MODAL KALIBRASI ULANG ODOMETER ==================== -->
    <div x-show="recalibrateModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="recalibrateModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700">
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base">Kalibrasi Odometer</h3>
                        <p class="text-[11px] text-slate-400" x-text="calibratingVehicle.model_name"></p>
                    </div>
                </div>
                <button @click="recalibrateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'{{ url('/vehicles') }}/' + calibratingVehicle.id + '/recalibrate'" method="POST" class="space-y-3 text-left">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        Angka Odometer Saat Ini (KM)
                    </label>
                    <input type="number" name="current_odometer_km" x-model="calibratingVehicle.current_odometer" required
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    <span class="text-[10px] text-slate-400">Sesuaikan dengan angka yang tertera di speedometer fisik kendaraan Anda hari ini.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        Estimasi Rata-rata Pemakaian (KM / Hari)
                    </label>
                    <input type="number" step="0.1" name="daily_average_km" x-model="calibratingVehicle.daily_average_km" required
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    <span class="text-[10px] text-slate-400">Digunakan oleh sistem untuk memprediksi penambahan kilometer harian.</span>
                </div>

                <div class="pt-3 flex space-x-2">
                    <button type="button" @click="recalibrateModal = false" 
                            class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-brand-main hover:bg-brand-dark text-xs font-bold text-white transition shadow-md shadow-brand-main/30">
                        Simpan Kalibrasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== 3. MODAL KONFIRMASI HAPUS KENDARAAN (CUSTOM MODAL) ==================== -->
    <div x-show="deleteVehicleModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="deleteVehicleModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700">
            
            <div class="flex items-center space-x-3 text-red-600">
                <div class="w-10 h-10 rounded-2xl bg-red-100 dark:bg-red-950/40 flex items-center justify-center text-red-600 dark:text-red-400 flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Hapus Kendaraan</h3>
                    <p class="text-xs text-slate-400">Konfirmasi penghapusan unit motor</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300">
                Apakah Anda yakin ingin menghapus <strong class="text-slate-900 dark:text-white" x-text="deletingVehicle.model_name"></strong> dari garasi Anda? Seluruh riwayat dan data terkait akan dihapus.
            </p>

            <form :action="'{{ url('/vehicles') }}/' + deletingVehicle.id" method="POST" class="pt-2">
                @csrf
                @method('DELETE')

                <div class="flex space-x-2">
                    <button type="button" @click="deleteVehicleModal = false" 
                            class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-xs font-bold text-white transition shadow-md shadow-red-600/30">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
