<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full" 
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
          colorTheme: localStorage.getItem('colorTheme') || 'theme-pink',
          mobileMenuOpen: false
      }" 
      :class="{ 'dark': darkMode, 'theme-pink': colorTheme === 'theme-pink', 'theme-blue': colorTheme === 'theme-blue' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SERVISA') }} - Perawatan Kendaraan Cerdas</title>

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

    <!-- Favicon SERVISA -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">

    <!-- Fonts: Figtree -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Assets (Tailwind CSS v3 + Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* Scroll-Reveal Smooth Transitions (Show from Left & Right) */
        .reveal-left {
            opacity: 0;
            transform: translateX(-2.5rem);
            transition: opacity 700ms cubic-bezier(0.22, 1, 0.36, 1), transform 700ms cubic-bezier(0.22, 1, 0.36, 1);
            will-change: opacity, transform;
        }

        .reveal-right {
            opacity: 0;
            transform: translateX(2.5rem);
            transition: opacity 700ms cubic-bezier(0.22, 1, 0.36, 1), transform 700ms cubic-bezier(0.22, 1, 0.36, 1);
            will-change: opacity, transform;
        }

        .reveal-left.revealed,
        .reveal-right.revealed {
            opacity: 1;
            transform: translateX(0);
        }

        /* Stagger Delays */
        .delay-100 { transition-delay: 100ms; }
        .delay-150 { transition-delay: 150ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }

        /* Accessibility: motion-reduce */
        @media (prefers-reduced-motion: reduce) {
            .reveal-left, .reveal-right {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
            .animate-subtle-float, .animate-subtle-float-delayed {
                animation: none !important;
            }
        }

        /* Subtle Floating Keyframes for Floating Badges */
        @keyframes subtleFloat {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        .animate-subtle-float {
            animation: subtleFloat 4s ease-in-out infinite;
        }

        .animate-subtle-float-delayed {
            animation: subtleFloat 4.6s ease-in-out 1.6s infinite;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col overflow-x-hidden transition-colors duration-200">

    <!-- ==================== 1. NAVBAR (Sticky, bg-brand-main/95 backdrop-blur-md text-white) ==================== -->
    <header class="sticky top-0 z-50 bg-brand-main/95 backdrop-blur-md text-white shadow-md transition-colors duration-200 border-b border-white/10 w-full">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12 h-16 sm:h-20 flex items-center justify-between">
            <!-- Left: Logo lingkaran putih + teks SERVISA -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group select-none">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white flex items-center justify-center text-brand-main shadow-sm flex-shrink-0 transition-transform duration-300 group-hover:scale-105">
                    <!-- Clean minimal SERVISA 3-node branch icon matching Landing Page.jpg -->
                    <svg viewBox="0 0 24 24" class="w-5 h-5 fill-current" stroke="currentColor" stroke-width="0.5">
                        <circle cx="7" cy="12" r="2.8" />
                        <circle cx="17" cy="6.5" r="2.8" />
                        <circle cx="17" cy="17.5" r="2.8" />
                        <line x1="7" y1="12" x2="17" y2="6.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
                        <line x1="7" y1="12" x2="17" y2="17.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-black tracking-wider text-white">SERVISA</span>
            </a>

            <!-- Center: Anchor links smooth-scroll (Desktop) -->
            <nav class="hidden md:flex items-center space-x-8 lg:space-x-10 text-sm font-semibold tracking-wide">
                <a href="#fitur" class="text-white/90 hover:text-white transition-colors duration-150 py-1">Fitur</a>
                <a href="#hemat-biaya" class="text-white/90 hover:text-white transition-colors duration-150 py-1">Hemat Biaya</a>
                <a href="#cara-kerja" class="text-white/90 hover:text-white transition-colors duration-150 py-1">Cara Kerja</a>
            </nav>

            <!-- Right: Toggle Dual Theme & Dark Mode + Tombol Pill Putih Masuk -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Toggle Dual Theme (theme-pink / theme-blue) -->
                <button type="button" 
                        @click="colorTheme = (colorTheme === 'theme-pink' ? 'theme-blue' : 'theme-pink'); localStorage.setItem('colorTheme', colorTheme); document.documentElement.classList.remove('theme-pink', 'theme-blue'); document.documentElement.classList.add(colorTheme); $nextTick(() => { if (window.lucide) lucide.createIcons(); });" 
                        class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center text-white transition-all duration-200 shadow-sm"
                        :title="colorTheme === 'theme-pink' ? 'Ganti ke Tema Biru' : 'Ganti ke Tema Pink'">
                    <i data-lucide="palette" class="w-4 h-4"></i>
                </button>

                <!-- Toggle Dark Mode -->
                <button type="button" 
                        @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light'); if (darkMode) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); } $nextTick(() => { if (window.lucide) lucide.createIcons(); });" 
                        class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center text-white transition-all duration-200 shadow-sm"
                        title="Ganti Mode Terang / Gelap">
                    <!-- Sun Icon (Tampil saat Dark Mode) -->
                    <svg x-show="darkMode" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2"/><path d="M12 20v2"/>
                        <path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/>
                        <path d="M2 12h2"/><path d="M20 12h2"/>
                        <path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                    </svg>
                    <!-- Moon Icon (Tampil saat Light Mode) -->
                    <svg x-show="!darkMode" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3a6 6 0 0 0 9 9 9 0 1 1-9-9Z"/>
                    </svg>
                </button>

                <!-- Tombol Pill Putih 'Masuk' (Fallback ke Dashboard jika Login route belum ada) -->
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="hidden sm:inline-flex items-center justify-center px-6 py-2 rounded-full bg-white hover:bg-white/90 text-brand-main font-bold text-sm shadow-sm transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="hidden sm:inline-flex items-center justify-center px-6 py-2 rounded-full bg-white hover:bg-white/90 text-brand-main font-bold text-sm shadow-sm transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                            Masuk
                        </a>
                    @endauth
                @else
                    <a href="{{ route('dashboard') }}" 
                       class="hidden sm:inline-flex items-center justify-center px-6 py-2 rounded-full bg-white hover:bg-white/90 text-brand-main font-bold text-sm shadow-sm transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                        Masuk
                    </a>
                @endif

                <!-- Hamburger Menu Button (Mobile) -->
                <button type="button" 
                        @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden p-2 rounded-xl text-white hover:bg-white/15 focus:outline-none transition-colors"
                        aria-label="Toggle Navigation">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation Menu (Alpine.js) -->
        <div x-show="mobileMenuOpen" 
             x-cloak 
             @click.outside="mobileMenuOpen = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden bg-brand-main border-t border-white/20 px-6 pt-3 pb-6 space-y-3">
            <a href="#fitur" @click="mobileMenuOpen = false" class="block py-2 text-base font-semibold text-white/95 hover:text-white">Fitur</a>
            <a href="#hemat-biaya" @click="mobileMenuOpen = false" class="block py-2 text-base font-semibold text-white/95 hover:text-white">Hemat Biaya</a>
            <a href="#cara-kerja" @click="mobileMenuOpen = false" class="block py-2 text-base font-semibold text-white/95 hover:text-white">Cara Kerja</a>
            <div class="pt-2 border-t border-white/20">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center py-2.5 rounded-full bg-white text-brand-main font-bold text-sm shadow-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center py-2.5 rounded-full bg-white text-brand-main font-bold text-sm shadow-sm">
                            Masuk
                        </a>
                    @endauth
                @else
                    <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center py-2.5 rounded-full bg-white text-brand-main font-bold text-sm shadow-sm">
                        Masuk
                    </a>
                @endif
            </div>
        </div>
    </header>


    <!-- ==================== 2. HERO SECTION ==================== -->
    <section class="relative bg-white dark:bg-slate-900 overflow-hidden transition-colors duration-200 w-full">
        <!-- Aksen bentuk bulat lembut minimalis di sisi kanan (bg-brand-secondary/35 dark:bg-brand-main/15) -->
        <div class="absolute -top-12 -right-20 sm:-top-24 sm:-right-24 w-80 h-80 sm:w-[500px] sm:h-[500px] lg:w-[650px] lg:h-[650px] bg-brand-secondary/35 dark:bg-brand-main/15 rounded-full pointer-events-none -z-0"></div>
        <div class="absolute -bottom-28 -left-20 w-72 h-72 sm:w-96 sm:h-96 bg-brand-secondary/25 dark:bg-brand-main/10 rounded-full blur-3xl pointer-events-none -z-0"></div>

        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12 pt-10 sm:pt-14 lg:pt-20 pb-16 sm:pb-20 lg:pb-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10 items-center">
                <!-- Kolom Kiri: Ukuran Diperbesar Signifikan (Animasi: Show from Left) -->
                <div class="lg:col-span-6 text-left reveal-left">
                    <h1 class="text-4xl sm:text-5xl lg:text-[56px] xl:text-[64px] font-black text-slate-900 dark:text-white tracking-tight leading-[1.12]">
                        Servis tepat waktu,<br>
                        <span class="text-brand-main dark:text-brand-accent">biaya lebih hemat.</span>
                    </h1>

                    <p class="mt-6 text-base sm:text-lg lg:text-xl text-slate-500 dark:text-slate-400 leading-relaxed max-w-xl">
                        SERVISA memprediksi jadwal servis motor dari pemakaian harian Anda, lalu mengingatkan sebelum terlambat.
                    </p>

                    <div class="mt-8 sm:mt-10 flex flex-wrap items-center gap-3.5 sm:gap-4">
                        <!-- Mulai Sekarang Pill -->
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center justify-center px-8 py-3.5 rounded-full bg-brand-main hover:bg-brand-dark text-white font-bold text-sm sm:text-base shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0">
                            Mulai Sekarang
                        </a>

                        <!-- Lihat Fitur Pill -->
                        <a href="#fitur" 
                           class="inline-flex items-center justify-center px-8 py-3.5 rounded-full border border-brand-main text-brand-main hover:bg-brand-secondary/30 dark:hover:bg-brand-main/20 font-bold text-sm sm:text-base transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0">
                            Lihat Fitur
                        </a>
                    </div>
                </div>

                <!-- Kolom Kanan (Animasi: Show from Right) -->
                <div class="lg:col-span-6 flex justify-center lg:justify-end reveal-right">
                    <div class="relative w-full max-w-md sm:max-w-lg lg:max-w-xl">
                        <!-- Floating Badge Kiri Atas: Kampas Rem - Perlu diganti (Delay reveal + subtle float) -->
                        <div class="absolute -top-3 -left-2 sm:-top-5 sm:-left-4 z-20 bg-white dark:bg-slate-800 rounded-2xl px-4 py-2.5 sm:px-5 sm:py-3 shadow-lg shadow-slate-200/50 dark:shadow-black/40 border border-slate-100 dark:border-slate-700/80 animate-subtle-float">
                            <span class="block text-[11px] font-medium text-slate-400 dark:text-slate-400">Kampas Rem</span>
                            <span class="block text-xs sm:text-sm font-bold text-brand-main dark:text-brand-accent">Perlu diganti</span>
                        </div>

                        <!-- Kartu Bersih Wadah Ilustrasi dengan Highlight Radial di Dark Mode -->
                        <div class="relative bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-6 sm:p-10 shadow-lg shadow-slate-200/60 dark:shadow-black/40 border border-slate-100 dark:border-slate-700/80 flex items-center justify-center transition-all duration-300 hover:shadow-xl overflow-hidden">
                            <!-- Subtle Radial Glow / Highlight di Belakang Motor (Khusus Dark Mode agar Siluet Tegas & Kontras) -->
                            <div class="absolute w-72 h-48 bg-brand-secondary/30 dark:bg-brand-main/20 rounded-full blur-2xl pointer-events-none -z-0"></div>

                            <!-- Ilustrasi Motor Matic Minimalis Inline SVG (Adaptif Light & Dark Mode) -->
                            <svg class="w-full h-48 sm:h-64 select-none drop-shadow-sm relative z-10" viewBox="0 0 320 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Bayangan Tanah Lembut (Adaptif) -->
                                <ellipse cx="160" cy="180" rx="115" ry="7" class="fill-slate-200/80 dark:fill-slate-700/80 transition-colors" />

                                <!-- Roda Belakang (Ban luar adaptif: slate-900 di Light, slate-200 di Dark; Velg dalam: white di Light, slate-800 di Dark) -->
                                <circle cx="80" cy="150" r="26" class="fill-slate-900 dark:fill-slate-200 transition-colors" />
                                <circle cx="80" cy="150" r="13" class="fill-white dark:fill-slate-800 transition-colors" />
                                <circle cx="80" cy="150" r="4" class="fill-slate-500 dark:fill-slate-400 transition-colors" />

                                <!-- Roda Depan (Ban luar adaptif: slate-900 di Light, slate-200 di Dark; Velg dalam: white di Light, slate-800 di Dark) -->
                                <circle cx="240" cy="150" r="26" class="fill-slate-900 dark:fill-slate-200 transition-colors" />
                                <circle cx="240" cy="150" r="13" class="fill-white dark:fill-slate-800 transition-colors" />
                                <circle cx="240" cy="150" r="4" class="fill-slate-500 dark:fill-slate-400 transition-colors" />

                                <!-- Front Fork / Suspensi Depan (Adaptif: kontras tinggi di Light & Dark) -->
                                <line x1="240" y1="150" x2="225" y2="88" stroke="currentColor" class="text-slate-900 dark:text-slate-200 transition-colors" stroke-width="10" stroke-linecap="round" />

                                <!-- Slebor / Fender Depan -->
                                <path d="M228 142 C232 130 248 130 252 142" stroke="currentColor" class="text-brand-main dark:text-brand-accent transition-colors" stroke-width="5" stroke-linecap="round" />

                                <!-- Bodi Utama Skuter Minimalis (Fill Brand yang Dinamis Berganti Mengikuti Tema) -->
                                <path d="M 88 150 
                                         C 88 126, 104 114, 126 114 
                                         L 158 114 
                                         C 174 114, 185 120, 195 132 
                                         L 205 144 
                                         C 212 149, 222 149, 230 144 
                                         L 230 150 
                                         C 218 156, 198 156, 188 144 
                                         L 180 134 
                                         C 172 126, 164 124, 150 124 
                                         L 126 124 
                                         C 108 124, 98 134, 96 150 
                                         Z" 
                                      class="fill-brand-secondary dark:fill-brand-secondary/80 transition-colors" />

                                <!-- Bodi Dek Atas / Sadel Bawah -->
                                <path d="M 104 124 
                                         C 104 112, 116 102, 134 102 
                                         L 186 102 
                                         C 204 102, 218 114, 216 130 
                                         L 214 136 
                                         C 208 130, 198 126, 184 126 
                                         L 132 126 
                                         C 118 126, 108 126, 104 124 
                                         Z" 
                                      class="fill-brand-main dark:fill-brand-main transition-colors" opacity="0.9" />

                                <!-- Jok / Sadel Motor Ergonomis Horizontal (Adaptif: slate-900 di Light, slate-200 di Dark) -->
                                <rect x="115" y="92" width="76" height="15" rx="7.5" class="fill-slate-900 dark:fill-slate-200 transition-colors" />

                                <!-- Stang Kemudi & Panel Headlamp -->
                                <path d="M 223 88 L 244 74" stroke="currentColor" class="text-brand-secondary dark:text-brand-accent transition-colors" stroke-width="8" stroke-linecap="round" />
                                <circle cx="225" cy="86" r="5" class="fill-slate-900 dark:fill-slate-200 transition-colors" />

                                <!-- Lampu Headlamp Cincin Kuning/Amber Minimalis -->
                                <circle cx="250" cy="80" r="6" stroke="#F59E0B" class="dark:stroke-amber-400" stroke-width="3" fill="none" />
                            </svg>
                        </div>

                        <!-- Floating Badge Kanan Bawah: Servis oli berikutnya - 13 hari lagi (Delay reveal + subtle float) -->
                        <div class="absolute -bottom-3 -right-2 sm:-bottom-5 sm:-right-4 z-20 bg-brand-main text-white rounded-2xl px-4 py-2.5 sm:px-5 sm:py-3 shadow-xl animate-subtle-float-delayed">
                            <span class="block text-[11px] font-medium text-white/90">Servis oli berikutnya</span>
                            <span class="block text-xs sm:text-sm font-black text-white mt-0.5">13 hari lagi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 3. SECTION FITUR (id="fitur", full-width bg-brand-main text-white, Konsep Mengintip dari Bawah) ==================== -->
    <section id="fitur" class="w-full bg-brand-main text-white pt-16 sm:pt-20 lg:pt-24 pb-16 sm:pb-20 lg:py-24 transition-colors duration-200 rounded-t-[2.5rem] sm:rounded-t-[3.5rem] lg:rounded-t-[4rem] shadow-2xl relative z-20 -mt-6 sm:-mt-8 lg:-mt-10">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12">
            <!-- Eyebrow & Heading (Animasi: Show from Left) -->
            <div class="text-left mb-10 sm:mb-14 reveal-left">
                <span class="block text-xs sm:text-sm font-semibold text-white/80 uppercase tracking-wider mb-2">Fitur</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight max-w-2xl leading-tight">
                    Semua yang Anda butuhkan untuk merawat motor
                </h2>
            </div>

            <!-- Grid 4 Kartu Fitur (Kartu 1 & 2: Show from Left, Kartu 3 & 4: Show from Right) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Kartu 1: Kesehatan Kendaraan (Show from Left, delay 100ms) -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-7 shadow-lg shadow-black/5 flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-xl reveal-left delay-100">
                    <div class="w-11 h-11 rounded-xl bg-brand-main text-white flex items-center justify-center mb-5 shadow-sm flex-shrink-0">
                        <i data-lucide="gauge" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2.5">
                        Kesehatan Kendaraan
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                        Pantau kondisi oli, busi, aki, dan komponen motor Anda lainnya dengan status yang jelas.
                    </p>
                </div>

                <!-- Kartu 2: Kalender Servis (Show from Left, delay 200ms) -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-7 shadow-lg shadow-black/5 flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-xl reveal-left delay-200">
                    <div class="w-11 h-11 rounded-xl bg-brand-main text-white flex items-center justify-center mb-5 shadow-sm flex-shrink-0">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2.5">
                        Kalender Servis
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                        Tanggal servis diprediksi dari KM harian Anda dan bisa disimpan ke kalender HP.
                    </p>
                </div>

                <!-- Kartu 3: Kalkulator Kerugian (Show from Right, delay 200ms) -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-7 shadow-lg shadow-black/5 flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-xl reveal-right delay-200">
                    <div class="w-11 h-11 rounded-xl bg-brand-main text-white flex items-center justify-center mb-5 shadow-sm flex-shrink-0">
                        <i data-lucide="calculator" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2.5">
                        Kalkulator Kerugian
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                        Lihat selisih biaya servis tepat waktu dengan kerusakan akibat ditunda.
                    </p>
                </div>

                <!-- Kartu 4: Diagnosa Darurat (Show from Right, delay 300ms) -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-7 shadow-lg shadow-black/5 flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-xl reveal-right delay-300">
                    <div class="w-11 h-11 rounded-xl bg-brand-main text-white flex items-center justify-center mb-5 shadow-sm flex-shrink-0">
                        <i data-lucide="stethoscope" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2.5">
                        Diagnosa Darurat
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                        Kenali gejala dan ikuti langkah awal yang aman saat motor bermasalah.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 4. SECTION HEMAT BIAYA (id="hemat-biaya", bg-white dark:bg-slate-900) ==================== -->
    <section id="hemat-biaya" class="w-full bg-white dark:bg-slate-900 py-16 sm:py-20 lg:py-24 transition-colors duration-200">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Kiri (Animasi: Show from Left) -->
                <div class="lg:col-span-6 text-left reveal-left">
                    <span class="block text-xs sm:text-sm font-bold text-brand-main dark:text-brand-accent uppercase tracking-wider mb-2">Hemat Biaya</span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
                        Menunda servis membuat biaya berlipat
                    </h2>
                    <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed max-w-lg">
                        Contoh penggantian oli mesin. Perawatan rutin jauh lebih murah dibanding memperbaiki kerusakan mesin.
                    </p>
                </div>

                <!-- Kanan (Animasi: Show from Right) -->
                <div class="lg:col-span-6 reveal-right">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Kartu 1: Servis tepat waktu (Outline Hijau / Emerald, Ukuran Dipertegas) -->
                        <div class="bg-white dark:bg-slate-800 border-2 border-emerald-300 dark:border-emerald-700 rounded-2xl p-6 sm:p-8 shadow-sm flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <span class="block text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                Servis tepat waktu
                            </span>
                            <span class="block text-3xl sm:text-4xl lg:text-[40px] font-black text-slate-900 dark:text-white tracking-tight">
                                Rp55.000
                            </span>
                        </div>

                        <!-- Kartu 2: Jika diabaikan (bg-brand-main text-white, Ukuran Dipertegas) -->
                        <div class="bg-brand-main text-white rounded-2xl p-6 sm:p-8 shadow-lg shadow-brand-main/20 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                            <span class="block text-xs sm:text-sm font-medium text-white/90 mb-2">
                                Jika diabaikan
                            </span>
                            <span class="block text-3xl sm:text-4xl lg:text-[40px] font-black text-white tracking-tight">
                                Rp1.200.000
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 5. SECTION CARA KERJA (id="cara-kerja", bg-white dark:bg-slate-900) ==================== -->
    <section id="cara-kerja" class="w-full bg-white dark:bg-slate-900 py-16 sm:py-20 transition-colors duration-200">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12">
            <!-- Eyebrow & Heading (Animasi: Show from Left) -->
            <div class="text-left mb-10 sm:mb-12 reveal-left">
                <span class="block text-xs sm:text-sm font-bold text-brand-main dark:text-brand-accent uppercase tracking-wider mb-2">Cara kerja</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Tiga langkah sederhana
                </h2>
            </div>

            <!-- Grid 3 Kartu Langkah Sederhana -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kartu 1: Isi data kendaraan (Show from Left) -->
                <div class="bg-slate-50 dark:bg-slate-800/70 rounded-2xl p-6 sm:p-7 border border-slate-100/80 dark:border-slate-700/60 shadow-sm flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-md reveal-left">
                    <span class="block text-4xl sm:text-5xl font-bold text-brand-main/25 dark:text-brand-accent/30 mb-4 select-none">
                        1
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2">
                        Isi data kendaraan
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Masukkan tipe motor, odometer, dan estimasi KM harian.
                    </p>
                </div>

                <!-- Kartu 2: Dapatkan prediksi (Show from Left, delay 150ms) -->
                <div class="bg-slate-50 dark:bg-slate-800/70 rounded-2xl p-6 sm:p-7 border border-slate-100/80 dark:border-slate-700/60 shadow-sm flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-md reveal-left delay-150">
                    <span class="block text-4xl sm:text-5xl font-bold text-brand-main/25 dark:text-brand-accent/30 mb-4 select-none">
                        2
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2">
                        Dapatkan prediksi
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        SERVISA menghitung kapan tiap komponen perlu diservis.
                    </p>
                </div>

                <!-- Kartu 3: Servis tepat waktu (Show from Right, delay 200ms) -->
                <div class="bg-slate-50 dark:bg-slate-800/70 rounded-2xl p-6 sm:p-7 border border-slate-100/80 dark:border-slate-700/60 shadow-sm flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-md reveal-right delay-200">
                    <span class="block text-4xl sm:text-5xl font-bold text-brand-main/25 dark:text-brand-accent/30 mb-4 select-none">
                        3
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2">
                        Servis tepat waktu
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Terima peringatan, tandai sudah diservis, dan jadwal diperbarui otomatis.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 6. BOTTOM CTA & FOOTER (bg-brand-main text-white) ==================== -->
    <footer class="w-full bg-brand-main text-white transition-colors duration-200 mt-auto">
        <!-- Baris Atas: Siap merawat motor Anda? + Tombol Mulai Sekarang -->
        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12 py-12 sm:py-16">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 text-left">
                <!-- Kiri (Animasi: Show from Left) -->
                <div class="reveal-left">
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Siap merawat motor Anda?
                    </h2>
                    <p class="text-sm sm:text-base text-white/80 mt-1">
                        Masuk dan lihat jadwal servis berikutnya.
                    </p>
                </div>

                <!-- Kanan (Animasi: Show from Right) -->
                <div class="reveal-right">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-white hover:bg-white/90 text-brand-main font-bold text-sm sm:text-base shadow-lg transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 whitespace-nowrap">
                        Mulai Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- Garis Pemisah Tipis -->
        <div class="w-full border-t border-white/20"></div>

        <!-- Baris Bawah: Teks Footer Rata Tengah -->
        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12 py-8 text-center">
            <p class="text-xs sm:text-sm text-white/85 font-medium">
                SERVISA. Perawatan kendaraan yang lebih tenang.
            </p>
        </div>
    </footer>

    <!-- Inisialisasi Ikon Lucide & Scroll-Reveal IntersectionObserver -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. Inisialisasi Lucide Icons
            if (window.lucide) {
                lucide.createIcons();
            }

            // 2. Sistem Animasi Scroll-Reveal (IntersectionObserver)
            const observerOptions = {
                root: null,
                rootMargin: '0px 0px -40px 0px',
                threshold: 0.15
            };

            const revealObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal-left, .reveal-right').forEach(function (el) {
                revealObserver.observe(el);
            });
        });
    </script>
</body>
</html>
