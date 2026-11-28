<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" 
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
          colorTheme: localStorage.getItem('colorTheme') || 'theme-pink'
      }" 
      :class="{ 'dark': darkMode, 'theme-pink': colorTheme === 'theme-pink', 'theme-blue': colorTheme === 'theme-blue' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SERVISA') }} - @yield('title', 'Perawatan Kendaraan Cerdas')</title>

    <!-- Script Anti Flash Dark Mode & Color Theme -->
    <script>
        const isDark = localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        const savedTheme = localStorage.getItem('colorTheme') || 'theme-pink';
        document.documentElement.classList.add(savedTheme);
    </script>

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
<body class="font-sans antialiased h-full bg-brand-light dark:bg-[#0B1120] text-slate-800 dark:text-slate-100 flex transition-colors duration-200" x-data="{ addVehicleModal: false }">

    <!-- ==================== 1. SIDEBAR DESKTOP (Dinamis: Pink / Blue / Dark) ==================== -->
    <aside class="hidden lg:flex flex-col w-64 bg-brand-sidebar text-white flex-shrink-0 transition-all duration-300 border-r border-transparent dark:border-slate-800/80">
        <!-- Logo SERVISA -->
        <div class="h-20 flex items-center px-6 space-x-3">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-brand-main flex items-center justify-center text-brand-main dark:text-white shadow-sm">
                <i data-lucide="gauge" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xl font-extrabold tracking-wider text-white">SERVISA</span>
                <span class="block text-[10px] uppercase tracking-widest text-brand-secondary dark:text-brand-accent font-medium">Auto Tracker</span>
            </div>
        </div>

        <!-- 4 Menu Navigasi Utama -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <!-- 1. Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-2xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white dark:bg-brand-active text-brand-main dark:text-white shadow-md shadow-black/5' : 'text-brand-secondary dark:text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="layout-grid" class="w-5 h-5 mr-3"></i>
                <span>Dashboard</span>
            </a>

            <!-- 2. Kalkulator -->
            <a href="{{ route('calculator') }}" 
               class="flex items-center px-4 py-3 rounded-2xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('calculator') ? 'bg-white dark:bg-brand-active text-brand-main dark:text-white shadow-md shadow-black/5' : 'text-brand-secondary dark:text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="calculator" class="w-5 h-5 mr-3"></i>
                <span>Kalkulator</span>
            </a>

            <!-- 3. Diagnosa -->
            <a href="{{ route('diagnosa') }}" 
               class="flex items-center px-4 py-3 rounded-2xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('diagnosa') ? 'bg-white dark:bg-brand-active text-brand-main dark:text-white shadow-md shadow-black/5' : 'text-brand-secondary dark:text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="stethoscope" class="w-5 h-5 mr-3"></i>
                <span>Diagnosa</span>
            </a>

            <!-- 4. Garasi Kendaraan (tetap aktif jika di halaman riwayat / history) -->
            <a href="{{ route('garage') }}" 
               class="flex items-center px-4 py-3 rounded-2xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('garage', 'history*') ? 'bg-white dark:bg-brand-active text-brand-main dark:text-white shadow-md shadow-black/5' : 'text-brand-secondary dark:text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="bike" class="w-5 h-5 mr-3"></i>
                <span>Garasi Kendaraan</span>
            </a>
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-4 text-center">
            <span class="text-[11px] text-brand-secondary/70 dark:text-slate-400">SERVISA v1.0 &copy; 2026</span>
        </div>
    </aside>

    <!-- ==================== 2. MAIN CONTENT WRAPPER ==================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto no-scrollbar">

        <!-- ==================== TOPBAR / NAVBAR ==================== -->
        <header class="h-auto lg:h-20 bg-transparent flex flex-col lg:flex-row lg:items-center justify-between px-4 sm:px-6 lg:px-10 pt-4 lg:pt-0 gap-3 lg:gap-0 flex-shrink-0">
            <!-- Left Header: Greeting / Page Title -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-slate-800 dark:text-white">
                        @yield('greeting', 'Selamat datang, ' . (Auth::user() ? explode(' ', Auth::user()->name)[0] : 'Rider') . '!')
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kendaraan sehat, perjalanan lebih tenang.</p>
                </div>

                <!-- Logo Mobile/Tablet Only -->
                <div class="flex lg:hidden items-center space-x-1.5 bg-brand-main text-white px-2.5 py-1 rounded-xl shadow-sm">
                    <i data-lucide="gauge" class="w-3.5 h-3.5 text-white"></i>
                    <span class="text-xs font-black tracking-wider">SERVISA</span>
                </div>
            </div>

            <!-- Right Header: Vehicle Switcher, Darkmode, Notification, Profile -->
            <div class="flex items-center justify-end space-x-2 lg:space-x-3">

                <!-- Vehicle Switcher Dropdown (Opsi 2) -->
                @php
                    $vehicles = Auth::user() ? Auth::user()->vehicles : collect();
                    $activeVehicleId = session('active_vehicle_id');
                    $activeVehicle = $vehicles->firstWhere('id', $activeVehicleId) ?: $vehicles->first();
                @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center space-x-1.5 sm:space-x-2 bg-white dark:bg-[#1E293B] hover:bg-slate-50 dark:hover:bg-slate-800 transition px-3 py-2 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm text-xs font-semibold text-slate-700 dark:text-slate-200">
                        <i data-lucide="bike" class="w-4 h-4 text-brand-main dark:text-brand-accent flex-shrink-0"></i>
                        <span class="truncate max-w-[110px] sm:max-w-[160px]">{{ $activeVehicle ? $activeVehicle->model_name : 'Pilih Kendaraan' }}</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="open" 
                         @click.outside="open = false" 
                         x-cloak 
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-[#1E293B] rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-2 z-50">
                        <div class="px-3 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Garasi Saya</div>
                        
                        @forelse($vehicles as $veh)
                            <form action="{{ route('vehicles.switch', $veh->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-light dark:hover:bg-slate-800 hover:text-brand-main dark:hover:text-brand-accent transition text-left">
                                    <span class="flex items-center truncate mr-2">
                                        <i data-lucide="bike" class="w-3.5 h-3.5 mr-2 text-slate-400 flex-shrink-0"></i>
                                        <span class="truncate">{{ $veh->model_name }}</span>
                                    </span>
                                    @if($activeVehicle && $activeVehicle->id === $veh->id)
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-brand-main dark:text-brand-accent flex-shrink-0"></i>
                                    @endif
                                </button>
                            </form>
                        @empty
                            <div class="px-3 py-2 text-xs text-slate-400">Belum ada kendaraan</div>
                        @endforelse

                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>

                        <!-- Button Trigger Modal Tambah Kendaraan -->
                        <button @click="open = false; addVehicleModal = true" 
                                class="w-full text-left flex items-center px-3 py-2 text-xs font-bold text-brand-main dark:text-brand-accent hover:bg-brand-secondary/40 dark:hover:bg-slate-800 transition">
                            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                            Tambah Kendaraan Baru
                        </button>
                    </div>
                </div>

                <!-- ==================== DARK MODE TOGGLE BUTTON ==================== -->
                <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light'); if (darkMode) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }"
                        class="p-2.5 rounded-2xl bg-white dark:bg-[#1E293B] hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm transition flex-shrink-0"
                        title="Ganti Mode Terang / Gelap">
                    <!-- Icon Sun SVG (Tampil saat Dark Mode) -->
                    <svg x-show="darkMode" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2"/><path d="M12 20v2"/>
                        <path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/>
                        <path d="M2 12h2"/><path d="M20 12h2"/>
                        <path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                    </svg>

                    <!-- Icon Moon SVG (Tampil saat Light Mode) -->
                    <svg x-show="!darkMode" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                    </svg>
                </button>

                <!-- Notifikasi Bell (Web In-App) -->
                <div class="relative flex-shrink-0" x-data="{ notifOpen: false }">
                    <button type="button"
                            @click="notifOpen = !notifOpen"
                            class="relative p-2.5 rounded-2xl bg-white dark:bg-[#1E293B] hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm text-slate-600 dark:text-slate-300 transition flex-shrink-0"
                            title="Notifikasi & Pengingat Servis">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        @if(($unreadNotificationCount ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-brand-main text-white text-[10px] font-black flex items-center justify-center shadow-sm">
                                {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel Notifikasi -->
                    <div x-show="notifOpen"
                         @click.outside="notifOpen = false"
                         x-cloak
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-[#1E293B] rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700/80 overflow-hidden z-50">
                        <!-- Header Panel -->
                        <div class="px-4 py-3.5 border-b border-slate-100 dark:border-slate-700/80 flex items-center justify-between bg-slate-50/70 dark:bg-slate-900/50">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-800 dark:text-white">Notifikasi & Pengingat</h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                    @if(Auth::user()?->canReceiveWhatsAppNotifications())
                                        Notif WA: <span class="font-semibold text-emerald-600 dark:text-emerald-400">Aktif ({{ Auth::user()->phone_whatsapp }})</span>
                                    @else
                                        <a href="{{ route('profile.edit') }}" class="text-brand-main dark:text-brand-accent font-semibold hover:underline">Aktifkan & Verifikasi WA di Profil</a>
                                    @endif
                                </p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-secondary/40 dark:bg-brand-main/25 text-brand-main dark:text-brand-accent">
                                {{ $unreadNotificationCount ?? 0 }} Aktif
                            </span>
                        </div>

                        <!-- Daftar Notifikasi -->
                        <div class="max-h-72 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700/60">
                            @forelse(($webNotifications ?? collect()) as $notif)
                                <div class="p-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-start justify-between gap-2.5">
                                    <a href="{{ $notif->url }}" class="flex items-start space-x-2.5 flex-1 min-w-0">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 {{ $notif->level === 'critical' ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-500' : 'bg-amber-50 dark:bg-amber-950/50 text-amber-500' }}">
                                            <i data-lucide="{{ $notif->type === 'ai_scan' ? 'scan' : ($notif->level === 'critical' ? 'alert-triangle' : 'clock') }}" class="w-4 h-4"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $notif->title }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">{{ $notif->message }}</p>
                                            <span class="inline-block mt-1 text-[10px] font-semibold {{ $notif->level === 'critical' ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }}">
                                                {{ $notif->time_label }}
                                            </span>
                                        </div>
                                    </a>
                                    @if($notif->can_snooze && $notif->reminder_id)
                                        <form action="{{ route('notifications.snooze', $notif->reminder_id) }}" method="POST" class="flex-shrink-0">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2 py-1 rounded-lg text-[10px] font-semibold text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                                                    title="Tunda pengingat ini">
                                                Tunda
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="p-6 text-center space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center mx-auto">
                                        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Semua Kondisi Prima</p>
                                    <p class="text-[11px] text-slate-400">Belum ada pengingat servis yang mendesak saat ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative flex-shrink-0" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" 
                            class="flex items-center space-x-1.5 bg-white dark:bg-[#1E293B] hover:bg-slate-50 dark:hover:bg-slate-800 p-1.5 lg:pr-3 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition">
                        <div class="w-8 h-8 rounded-xl bg-brand-main/50 text-white flex items-center justify-center font-bold text-xs">
                            {{ Auth::user() ? substr(Auth::user()->name, 0, 2) : 'DR' }}
                        </div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 hidden xl:inline-block">
                            {{ Auth::user() ? Auth::user()->name : 'User' }}
                        </span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>

                    <!-- Dropdown Profil -->
                    <div x-show="profileOpen" 
                         @click.outside="profileOpen = false" 
                         x-cloak 
                         class="absolute right-0 mt-2 w-52 bg-white dark:bg-[#1E293B] rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-2 z-50">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-light dark:hover:bg-slate-800 hover:text-brand-main transition">
                            <i data-lucide="user" class="w-4 h-4 mr-2 text-slate-400"></i>
                            Profil
                        </a>
                        
                        <div class="border-t border-slate-100 dark:border-slate-700 my-1.5"></div>

                        <!-- Color Swatch Theme Switcher -->
                        <div class="px-4 py-2">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tema Warna</span>
                            <div class="flex items-center space-x-2.5">
                                <!-- Option Pink -->
                                <button type="button" 
                                        @click="colorTheme = 'theme-pink'; localStorage.setItem('colorTheme', 'theme-pink'); document.documentElement.classList.remove('theme-blue'); document.documentElement.classList.add('theme-pink');"
                                        class="flex items-center space-x-1.5 px-2.5 py-1.5 rounded-xl border text-[11px] font-bold transition-all shadow-sm"
                                        :class="colorTheme === 'theme-pink' ? 'border-[#B83A74] bg-[#F5DBE8]/60 dark:bg-[#B83A74]/20 text-[#B83A74] dark:text-[#F5DBE8] ring-1 ring-[#B83A74]/40' : 'border-slate-200 dark:border-slate-700/80 bg-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#B83A74] ring-2 ring-white/40 dark:ring-slate-900"></span>
                                    <span>Pink</span>
                                </button>

                                <!-- Option Blue -->
                                <button type="button" 
                                        @click="colorTheme = 'theme-blue'; localStorage.setItem('colorTheme', 'theme-blue'); document.documentElement.classList.remove('theme-pink'); document.documentElement.classList.add('theme-blue');"
                                        class="flex items-center space-x-1.5 px-2.5 py-1.5 rounded-xl border text-[11px] font-bold transition-all shadow-sm"
                                        :class="colorTheme === 'theme-blue' ? 'border-[#1D5FE0] bg-[#D3E2FF]/60 dark:bg-[#1D5FE0]/25 text-[#0B2A78] dark:text-[#93C5FD] ring-1 ring-[#1D5FE0]/40' : 'border-slate-200 dark:border-slate-700/80 bg-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#1D5FE0] ring-2 ring-white/40 dark:ring-slate-900"></span>
                                    <span>Blue</span>
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 dark:border-slate-700 my-1.5"></div>
                        
                        <!-- Form Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
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
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-[#1E293B]/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-700/80 px-3 py-2 flex justify-around shadow-lg transition-colors">
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center py-1 {{ request()->routeIs('dashboard') ? 'text-brand-main dark:text-brand-accent font-bold' : 'text-slate-400 dark:text-slate-500' }}">
            <i data-lucide="layout-grid" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Dashboard</span>
        </a>

        <a href="{{ route('calculator') }}" 
           class="flex flex-col items-center py-1 {{ request()->routeIs('calculator') ? 'text-brand-main dark:text-brand-accent font-bold' : 'text-slate-400 dark:text-slate-500' }}">
            <i data-lucide="calculator" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Kalkulator</span>
        </a>

        <a href="{{ route('diagnosa') }}" 
           class="flex flex-col items-center py-1 {{ request()->routeIs('diagnosa') ? 'text-brand-main dark:text-brand-accent font-bold' : 'text-slate-400 dark:text-slate-500' }}">
            <i data-lucide="stethoscope" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Diagnosa</span>
        </a>

        <a href="{{ route('garage') }}" 
           class="flex flex-col items-center py-1 {{ request()->routeIs('garage', 'history*') ? 'text-brand-main dark:text-brand-accent font-bold' : 'text-slate-400 dark:text-slate-500' }}">
            <i data-lucide="bike" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Garasi</span>
        </a>
    </nav>

    <!-- ==================== 4. MODAL TAMBAH KENDARAAN (GLOBAL) ==================== -->
    <div x-show="addVehicleModal" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="addVehicleModal = false" 
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700">
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-secondary/40 dark:bg-brand-main/30 flex items-center justify-center text-brand-main dark:text-brand-accent">
                        <i data-lucide="bike" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Tambah Kendaraan Baru</h3>
                </div>
                <button @click="addVehicleModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Tambah Kendaraan -->
            <form action="{{ route('vehicles.store') }}" method="POST" class="space-y-3 text-left">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Merk Kendaraan</label>
                    <input type="text" name="brand" placeholder="Misal: Honda, Yamaha" required
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Model / Nama Kendaraan</label>
                    <input type="text" name="model_name" placeholder="Misal: Vario 160, NMAX 155" required
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Plat Nomor</label>
                        <input type="text" name="license_plate" placeholder="B 4120 KXZ"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Tahun Pembuatan</label>
                        <input type="number" name="manufacture_year" placeholder="2023"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Odometer Awal (KM)</label>
                        <input type="number" name="initial_odometer_km" placeholder="12000" required
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Rata-rata KM / Hari</label>
                        <input type="number" step="0.1" name="daily_average_km" placeholder="20" required
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2 px-3">
                    </div>
                </div>

                <div class="pt-3 flex space-x-2">
                    <button type="button" @click="addVehicleModal = false" 
                            class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-brand-main hover:bg-brand-dark text-xs font-bold text-white transition shadow-md shadow-brand-main/30">
                        Simpan Kendaraan
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
