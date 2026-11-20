<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SERVISA') }} - @yield('title', 'Perawatan Kendaraan Cerdas')</title>

    <!-- Favicon SERVISA (v2 Centered) -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Scripts & Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="font-sans antialiased h-full bg-[#FDF7FA] text-slate-800 flex" x-data="{ addVehicleModal: false }">

    <!-- ==================== 1. SIDEBAR DESKTOP (Warna Utama: #B83A74) ==================== -->
    <aside class="hidden lg:flex flex-col w-64 bg-[#B83A74] text-white flex-shrink-0 transition-all duration-300">
        <!-- Logo SERVISA -->
        <div class="h-20 flex items-center px-6 space-x-3">
            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#B83A74] shadow-sm">
                <i data-lucide="gauge" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xl font-extrabold tracking-wider text-white">SERVISA</span>
                <span class="block text-[10px] uppercase tracking-widest text-[#F5DBE8] font-medium">Auto Tracker</span>
            </div>
        </div>

        <!-- 4 Menu Navigasi Utama -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <!-- 1. Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-2xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white text-[#B83A74] shadow-md shadow-black/5' : 'text-[#F5DBE8] hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="layout-grid" class="w-5 h-5 mr-3"></i>
                <span>Dashboard</span>
            </a>

            <!-- 2. Kalkulator -->
            <a href="{{ route('calculator') }}" 
               class="flex items-center px-4 py-3 rounded-2xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('calculator') ? 'bg-white text-[#B83A74] shadow-md shadow-black/5' : 'text-[#F5DBE8] hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="calculator" class="w-5 h-5 mr-3"></i>
                <span>Kalkulator</span>
            </a>

            <!-- 3. Diagnosa -->
            <a href="{{ route('diagnosa') }}" 
               class="flex items-center px-4 py-3 rounded-2xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('diagnosa') ? 'bg-white text-[#B83A74] shadow-md shadow-black/5' : 'text-[#F5DBE8] hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="stethoscope" class="w-5 h-5 mr-3"></i>
                <span>Diagnosa</span>
            </a>

            <!-- 4. Riwayat -->
            <a href="{{ route('history') }}" 
               class="flex items-center px-4 py-3 rounded-2xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('history') ? 'bg-white text-[#B83A74] shadow-md shadow-black/5' : 'text-[#F5DBE8] hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="clipboard-list" class="w-5 h-5 mr-3"></i>
                <span>Riwayat</span>
            </a>
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-4 text-center">
            <span class="text-[11px] text-[#F5DBE8]/70">SERVISA v1.0 &copy; 2026</span>
        </div>
    </aside>

    <!-- ==================== 2. MAIN CONTENT WRAPPER ==================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto no-scrollbar">

        <!-- ==================== TOPBAR / NAVBAR ==================== -->
        <header class="h-auto lg:h-20 bg-transparent flex flex-col lg:flex-row lg:items-center justify-between px-4 sm:px-6 lg:px-10 pt-4 lg:pt-0 gap-3 lg:gap-0 flex-shrink-0">
            <!-- Left Header: Greeting / Page Title -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-slate-800">
                        @yield('greeting', 'Selamat datang, ' . (Auth::user() ? explode(' ', Auth::user()->name)[0] : 'Rider') . '!')
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">Kendaraan sehat, perjalanan lebih tenang.</p>
                </div>

                <!-- Logo Mobile/Tablet Only (Muncul saat sidebar disembunyikan di < lg) -->
                <div class="flex lg:hidden items-center space-x-1.5 bg-[#B83A74] text-white px-2.5 py-1 rounded-xl shadow-sm">
                    <i data-lucide="gauge" class="w-3.5 h-3.5 text-white"></i>
                    <span class="text-xs font-black tracking-wider">SERVISA</span>
                </div>
            </div>

            <!-- Right Header: Vehicle Switcher, Notification, Profile -->
            <div class="flex items-center justify-end space-x-2 lg:space-x-3">

                <!-- Vehicle Switcher Dropdown (Opsi 2) -->
                @php
                    $vehicles = Auth::user() ? Auth::user()->vehicles : collect();
                    $activeVehicleId = session('active_vehicle_id');
                    $activeVehicle = $vehicles->firstWhere('id', $activeVehicleId) ?: $vehicles->first();
                @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center space-x-1.5 sm:space-x-2 bg-white hover:bg-slate-50 transition px-3 py-2 rounded-2xl border border-slate-200 shadow-sm text-xs font-semibold text-slate-700">
                        <i data-lucide="bike" class="w-4 h-4 text-[#B83A74] flex-shrink-0"></i>
                        <span class="truncate max-w-[110px] sm:max-w-[160px]">{{ $activeVehicle ? $activeVehicle->model_name : 'Pilih Motor' }}</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="open" 
                         @click.outside="open = false" 
                         x-cloak 
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                        <div class="px-3 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Garasi Saya</div>
                        
                        @forelse($vehicles as $veh)
                            <form action="{{ route('vehicles.switch', $veh->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-700 hover:bg-[#FDF7FA] hover:text-[#B83A74] transition text-left">
                                    <span class="flex items-center truncate mr-2">
                                        <i data-lucide="bike" class="w-3.5 h-3.5 mr-2 text-slate-400 flex-shrink-0"></i>
                                        <span class="truncate">{{ $veh->model_name }}</span>
                                    </span>
                                    @if($activeVehicle && $activeVehicle->id === $veh->id)
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-[#B83A74] flex-shrink-0"></i>
                                    @endif
                                </button>
                            </form>
                        @empty
                            <div class="px-3 py-2 text-xs text-slate-400">Belum ada kendaraan</div>
                        @endforelse

                        <div class="border-t border-slate-100 my-1"></div>

                        <!-- Button Trigger Modal Tambah Kendaraan -->
                        <button @click="open = false; addVehicleModal = true" 
                                class="w-full text-left flex items-center px-3 py-2 text-xs font-bold text-[#B83A74] hover:bg-[#F5DBE8]/50 transition">
                            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                            Tambah Kendaraan Baru
                        </button>
                    </div>
                </div>

                <!-- Notifikasi Bell -->
                <button class="relative p-2.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 shadow-sm text-slate-600 transition flex-shrink-0">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-[#B83A74] rounded-full"></span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative flex-shrink-0" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" 
                            class="flex items-center space-x-1.5 bg-white hover:bg-slate-50 p-1.5 lg:pr-3 rounded-2xl border border-slate-200 shadow-sm transition">
                        <div class="w-8 h-8 rounded-xl bg-[#F5DBE8] flex items-center justify-center text-[#B83A74] font-bold text-xs">
                            {{ Auth::user() ? substr(Auth::user()->name, 0, 2) : 'DR' }}
                        </div>
                        <span class="text-xs font-bold text-slate-700 hidden xl:inline-block">
                            {{ Auth::user() ? Auth::user()->name : 'User' }}
                        </span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>

                    <!-- Dropdown Profil -->
                    <div x-show="profileOpen" 
                         @click.outside="profileOpen = false" 
                         x-cloak 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-xs font-medium text-slate-700 hover:bg-[#FDF7FA] hover:text-[#B83A74] transition">
                            <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                            Profil & Garasi
                        </a>
                        
                        <div class="border-t border-slate-100 my-1"></div>
                        
                        <!-- Form Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition">
                                <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
                                Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        <!-- ==================== BODY CONTENT INJECTION ==================== -->
        <main class="flex-1 px-4 sm:px-6 lg:px-10 pt-4 lg:pt-2 pb-24 lg:pb-10">
            @yield('content')
        </main>
    </div>

    <!-- ==================== 3. BOTTOM NAVIGATION MOBILE & TABLET (4 MENU) ==================== -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-3 py-2 flex justify-around shadow-lg">
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center py-1 {{ request()->routeIs('dashboard') ? 'text-[#B83A74] font-bold' : 'text-slate-400' }}">
            <i data-lucide="layout-grid" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Dashboard</span>
        </a>

        <a href="{{ route('calculator') }}" 
           class="flex flex-col items-center py-1 {{ request()->routeIs('calculator') ? 'text-[#B83A74] font-bold' : 'text-slate-400' }}">
            <i data-lucide="calculator" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Kalkulator</span>
        </a>

        <a href="{{ route('diagnosa') }}" 
           class="flex flex-col items-center py-1 {{ request()->routeIs('diagnosa') ? 'text-[#B83A74] font-bold' : 'text-slate-400' }}">
            <i data-lucide="stethoscope" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Diagnosa</span>
        </a>

        <a href="{{ route('history') }}" 
           class="flex flex-col items-center py-1 {{ request()->routeIs('history') ? 'text-[#B83A74] font-bold' : 'text-slate-400' }}">
            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Riwayat</span>
        </a>
    </nav>

    <!-- ==================== 4. MODAL TAMBAH KENDARAAN (GLOBAL) ==================== -->
    <div x-show="addVehicleModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="addVehicleModal = false" 
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-[#F5DBE8] flex items-center justify-center text-[#B83A74]">
                        <i data-lucide="bike" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-base">Tambah Kendaraan Baru</h3>
                </div>
                <button @click="addVehicleModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Tambah Motor -->
            <form action="{{ route('vehicles.store') }}" method="POST" class="space-y-3 text-left">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Merk Kendaraan</label>
                    <input type="text" name="brand" placeholder="Misal: Honda, Yamaha" required
                           class="w-full text-xs rounded-xl border-slate-200 focus:border-[#B83A74] focus:ring focus:ring-[#B83A74]/20 py-2 px-3">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Model / Nama Motor</label>
                    <input type="text" name="model_name" placeholder="Misal: Vario 160, NMAX 155" required
                           class="w-full text-xs rounded-xl border-slate-200 focus:border-[#B83A74] focus:ring focus:ring-[#B83A74]/20 py-2 px-3">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Plat Nomor</label>
                        <input type="text" name="license_plate" placeholder="B 4120 KXZ"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-[#B83A74] focus:ring focus:ring-[#B83A74]/20 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun Pembuatan</label>
                        <input type="number" name="manufacture_year" placeholder="2023"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-[#B83A74] focus:ring focus:ring-[#B83A74]/20 py-2 px-3">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Odometer Awal (KM)</label>
                        <input type="number" name="initial_odometer_km" placeholder="12000" required
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-[#B83A74] focus:ring focus:ring-[#B83A74]/20 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Rata-rata KM / Hari</label>
                        <input type="number" step="0.1" name="daily_average_km" placeholder="20" required
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-[#B83A74] focus:ring focus:ring-[#B83A74]/20 py-2 px-3">
                    </div>
                </div>

                <div class="pt-3 flex space-x-2">
                    <button type="button" @click="addVehicleModal = false" 
                            class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-[#B83A74] hover:bg-[#8D2455] text-xs font-bold text-white transition shadow-md shadow-[#B83A74]/30">
                        Simpan Motor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Inisialisasi Ikon Lucide -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>
