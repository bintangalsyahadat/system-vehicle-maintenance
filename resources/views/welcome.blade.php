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

        /* Di layar mobile (< 640px) gunakan translateY (geser naik vertikal) agar efek muncul tetap ada dan tidak memicu overflow horizontal */
        @media (max-width: 639px) {
            .reveal-left, .reveal-right {
                transform: translateY(1.5rem);
            }
            .reveal-left.revealed, .reveal-right.revealed {
                transform: translateY(0);
                opacity: 1;
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
<body class="font-sans antialiased bg-white dark:bg-[#0B1120] text-slate-800 dark:text-slate-100 min-h-screen flex flex-col overflow-x-hidden transition-colors duration-200">

    <!-- ==================== 1. NAVBAR (Sticky, bg-brand-main/95 dark:bg-[#0B1120]/90 backdrop-blur-md text-white) ==================== -->
    <header class="sticky top-0 z-50 bg-brand-main/95 dark:bg-[#0B1120]/90 backdrop-blur-md text-white shadow-md transition-colors duration-200 border-b border-white/10 dark:border-slate-800/80 w-full">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-8 lg:px-12 h-16 sm:h-20 flex items-center justify-between">
            <!-- Left: Logo lingkaran putih + teks SERVISA -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2.5 sm:space-x-3 group select-none">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white flex items-center justify-center text-brand-main shadow-sm flex-shrink-0 transition-transform duration-300 group-hover:scale-105">
                    <i data-lucide="gauge" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </div>
                <span class="text-lg sm:text-2xl font-black tracking-wider text-white">SERVISA</span>
            </a>

            <!-- Center: Anchor links smooth-scroll (Desktop) -->
            <nav class="hidden md:flex items-center space-x-8 lg:space-x-10 text-sm font-semibold tracking-wide">
                <a href="#fitur" class="text-white/90 hover:text-white transition-colors duration-150 py-1">Fitur</a>
                <a href="#cara-kerja" class="text-white/90 hover:text-white transition-colors duration-150 py-1">Cara Kerja</a>
                <a href="#hemat-biaya" class="text-white/90 hover:text-white transition-colors duration-150 py-1">Simulasi</a>
                <a href="#faq" class="text-white/90 hover:text-white transition-colors duration-150 py-1">FAQ</a>
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
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
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
            <a href="#cara-kerja" @click="mobileMenuOpen = false" class="block py-2 text-base font-semibold text-white/95 hover:text-white">Cara Kerja</a>
            <a href="#hemat-biaya" @click="mobileMenuOpen = false" class="block py-2 text-base font-semibold text-white/95 hover:text-white">Simulasi</a>
            <a href="#faq" @click="mobileMenuOpen = false" class="block py-2 text-base font-semibold text-white/95 hover:text-white">FAQ</a>
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
    <section class="relative bg-white dark:bg-[#0B1120] overflow-hidden transition-colors duration-200 w-full min-h-[auto] lg:min-h-[calc(100vh-5rem)] flex items-center">
        <!-- Aksen bentuk bulat lembut minimalis di sisi kanan (bg-brand-secondary/35 dark:bg-brand-main/15) -->
        <div class="absolute -top-12 -right-20 sm:-top-24 sm:-right-24 w-80 h-80 sm:w-[500px] sm:h-[500px] lg:w-[650px] lg:h-[650px] bg-brand-secondary/35 dark:bg-brand-main/10 rounded-full pointer-events-none -z-0"></div>
        <div class="absolute -bottom-28 -left-20 w-72 h-72 sm:w-96 sm:h-96 bg-brand-secondary/25 dark:bg-brand-main/5 rounded-full blur-3xl pointer-events-none -z-0"></div>

        <div class="max-w-[1400px] w-full mx-auto px-4 sm:px-8 lg:px-12 pt-8 sm:pt-16 lg:pt-24 pb-20 sm:pb-24 lg:pb-32 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12 lg:gap-10 items-center">
                <!-- Kolom Kiri: Ukuran Diperbesar Signifikan (Animasi: Show from Left) -->
                <div class="lg:col-span-6 text-left reveal-left">
                    <h1 class="text-3xl sm:text-5xl lg:text-[56px] xl:text-[64px] font-black text-slate-900 dark:text-white tracking-tight leading-[1.15] sm:leading-[1.12]">
                        Servis tepat waktu,<br>
                        <span class="text-brand-main dark:text-brand-accent">biaya lebih hemat.</span>
                    </h1>

                    <p class="mt-4 sm:mt-6 text-sm sm:text-lg lg:text-xl text-slate-500 dark:text-slate-400 leading-relaxed max-w-xl">
                        SERVISA memprediksi jadwal servis motor dari pemakaian harian Anda, mendeteksi masalah via AI Scanner, lalu mengingatkan sebelum terlambat.
                    </p>

                    <div class="mt-6 sm:mt-10 flex flex-wrap items-center gap-3 sm:gap-4">
                        <!-- Mulai Sekarang Pill -->
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-3.5 rounded-full bg-brand-main hover:bg-brand-dark text-white font-bold text-sm sm:text-base shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0">
                                Buka Dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-3.5 rounded-full bg-brand-main hover:bg-brand-dark text-white font-bold text-sm sm:text-base shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0">
                                    Mulai Sekarang
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-3.5 rounded-full bg-brand-main hover:bg-brand-dark text-white font-bold text-sm sm:text-base shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0">
                                    Mulai Sekarang
                                </a>
                            @endif
                        @endauth

                        <!-- Lihat Fitur Pill -->
                        <a href="#fitur" 
                           class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-3.5 rounded-full border border-brand-main text-brand-main dark:text-brand-accent dark:border-brand-accent/50 hover:bg-brand-secondary/30 dark:hover:bg-brand-accent/10 font-bold text-sm sm:text-base transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0">
                            Lihat Fitur
                        </a>
                    </div>

                    <!-- Trust Stats Bar (Mengisi ruang Hero agar padat dan berbobot) -->
                    <div class="mt-8 sm:mt-10 pt-6 sm:pt-8 border-t border-slate-100 dark:border-slate-800/80 grid grid-cols-3 gap-2 sm:gap-6">
                        <div>
                            <div class="text-xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">6 Part</div>
                            <div class="text-[11px] sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Komponen Vital</div>
                        </div>
                        <div>
                            <div class="text-xl sm:text-3xl font-black text-brand-main dark:text-brand-accent tracking-tight">AI Vision</div>
                            <div class="text-[11px] sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">OCR Nota & ODO</div>
                        </div>
                        <div>
                            <div class="text-xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">Hemat</div>
                            <div class="text-[11px] sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">Cegah Kerusakan</div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan (Animasi: Show from Right) -->
                <div class="lg:col-span-6 flex justify-center lg:justify-end reveal-right mt-2 sm:mt-0">
                    <div class="relative w-full max-w-sm sm:max-w-lg lg:max-w-xl">
                        <!-- Floating Badge Kiri Atas: Kampas Rem - Perlu diganti -->
                        <div class="absolute -top-3 left-2 sm:-top-5 sm:-left-4 z-20 bg-white dark:bg-[#1E293B] rounded-xl sm:rounded-2xl px-3 py-1.5 sm:px-5 sm:py-3 shadow-md sm:shadow-lg shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-slate-700/60 animate-subtle-float">
                            <span class="block text-[10px] sm:text-[11px] font-medium text-slate-400 dark:text-slate-400">Kampas Rem</span>
                            <span class="block text-xs sm:text-sm font-bold text-brand-main dark:text-brand-accent">Perlu diganti</span>
                        </div>

                        <!-- Floating Badge Kiri Bawah: Nota Terverifikasi AI -->
                        <div class="absolute -bottom-3 left-2 sm:-bottom-4 sm:-left-4 z-20 bg-white dark:bg-[#1E293B] rounded-xl sm:rounded-2xl px-2.5 py-1.5 sm:px-4 sm:py-2.5 shadow-md sm:shadow-lg shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-slate-700/60 animate-subtle-float-delayed flex items-center gap-1.5 sm:gap-2.5">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            </div>
                            <div>
                                <span class="block text-[9px] sm:text-[10px] text-slate-400 leading-tight">Nota Fisik</span>
                                <span class="block text-[11px] sm:text-xs font-bold text-slate-800 dark:text-white leading-tight">Terverifikasi AI</span>
                            </div>
                        </div>

                        <!-- Kartu Bersih Wadah Ilustrasi dengan Highlight Radial di Dark Mode -->
                        <div class="relative bg-white dark:bg-[#1E293B] rounded-2xl sm:rounded-3xl p-5 sm:p-10 shadow-lg shadow-slate-200/60 dark:shadow-black/50 border border-slate-100 dark:border-slate-700/60 flex items-center justify-center transition-all duration-300 hover:shadow-xl overflow-hidden">
                            <!-- Subtle Radial Glow / Highlight di Belakang Motor (Khusus Dark Mode agar Siluet Tegas & Kontras) -->
                            <div class="absolute w-72 h-48 bg-brand-secondary/30 dark:bg-brand-main/15 rounded-full blur-2xl pointer-events-none -z-0"></div>

                            <!-- Animasi CSS Khusus Motor Melaju Hero -->
                            <style>
                                @keyframes heroBikeRide {
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
                                @keyframes heroShadowPulse {
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
                                @keyframes heroSpeedDash {
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
                                .animate-hero-bike {
                                    animation: heroBikeRide 1.6s ease-in-out infinite;
                                    transform-origin: center bottom;
                                }
                                .animate-hero-shadow {
                                    animation: heroShadowPulse 1.6s ease-in-out infinite;
                                    transform-origin: center;
                                }
                                .animate-hero-dash-1 {
                                    animation: heroSpeedDash 1s ease-out infinite;
                                }
                                .animate-hero-dash-2 {
                                    animation: heroSpeedDash 1.2s ease-out 0.3s infinite;
                                }
                                .animate-hero-dash-3 {
                                    animation: heroSpeedDash 0.9s ease-out 0.6s infinite;
                                }
                            </style>

                            <!-- Ilustrasi Motor Skuter Bergerak Dinamis (Sinkron dengan Dashboard & Login) -->
                            <svg class="w-full h-44 sm:h-64 select-none drop-shadow-sm relative z-10" viewBox="15 45 270 145" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Bayangan Tanah Dinamis -->
                                <ellipse cx="160" cy="180" rx="105" ry="8" fill="currentColor" class="text-slate-200 dark:text-slate-700/80 animate-hero-shadow" />

                                <!-- Efek Garis Angin Kecepatan di Belakang Motor -->
                                <g>
                                    <line x1="60" y1="150" x2="35" y2="150" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-hero-dash-1" stroke-width="2.5" stroke-linecap="round" />
                                    <line x1="50" y1="130" x2="20" y2="130" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-hero-dash-2" stroke-width="2" stroke-linecap="round" />
                                    <line x1="70" y1="110" x2="45" y2="110" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-hero-dash-3" stroke-width="2" stroke-linecap="round" />
                                </g>

                                <!-- GROUP MOTOR DENGAN BOUNCING SUSPENSI -->
                                <g class="animate-hero-bike">
                                    <!-- Roda Belakang Berputar 360 Derajat -->
                                    <g>
                                        <!-- Ban Luar Hitam -->
                                        <circle cx="85" cy="148" r="28" fill="#1E293B" stroke="#0F172A" stroke-width="6" />
                                        <circle cx="85" cy="148" r="14" fill="#94A3B8" />

                                        <!-- Velg Spokes Berputar (Native SVG Rotation Transform) -->
                                        <g>
                                            <animateTransform 
                                                attributeName="transform" 
                                                type="rotate" 
                                                from="0 85 148" 
                                                to="360 85 148" 
                                                dur="1.2s" 
                                                repeatCount="indefinite" />
                                            
                                            <line x1="85" y1="123" x2="85" y2="173" stroke="#334155" stroke-width="3" stroke-linecap="round" />
                                            <line x1="60" y1="148" x2="110" y2="148" stroke="#334155" stroke-width="3" stroke-linecap="round" />
                                            <line x1="67" y1="130" x2="103" y2="166" stroke="#475569" stroke-width="2.5" stroke-linecap="round" />
                                            <line x1="67" y1="166" x2="103" y2="130" stroke="#475569" stroke-width="2.5" stroke-linecap="round" />
                                            <circle cx="85" cy="138" r="3" fill="#F8FAFC" />
                                        </g>

                                        <!-- Poros Hub Roda Belakang -->
                                        <circle cx="85" cy="148" r="6" fill="#F8FAFC" stroke="#0F172A" stroke-width="1.5" />
                                    </g>

                                    <!-- Roda Depan Berputar 360 Derajat -->
                                    <g>
                                        <!-- Ban Luar Hitam -->
                                        <circle cx="235" cy="148" r="28" fill="#1E293B" stroke="#0F172A" stroke-width="6" />
                                        <circle cx="235" cy="148" r="14" fill="#94A3B8" />

                                        <!-- Velg Spokes Berputar (Native SVG Rotation Transform) -->
                                        <g>
                                            <animateTransform 
                                                attributeName="transform" 
                                                type="rotate" 
                                                from="0 235 148" 
                                                to="360 235 148" 
                                                dur="1.2s" 
                                                repeatCount="indefinite" />

                                            <line x1="235" y1="123" x2="235" y2="173" stroke="#334155" stroke-width="3" stroke-linecap="round" />
                                            <line x1="210" y1="148" x2="260" y2="148" stroke="#334155" stroke-width="3" stroke-linecap="round" />
                                            <line x1="217" y1="130" x2="253" y2="166" stroke="#475569" stroke-width="2.5" stroke-linecap="round" />
                                            <line x1="217" y1="166" x2="253" y2="130" stroke="#475569" stroke-width="2.5" stroke-linecap="round" />
                                            <circle cx="235" cy="138" r="3" fill="#F8FAFC" />
                                        </g>

                                        <!-- Poros Hub Roda Depan -->
                                        <circle cx="235" cy="148" r="6" fill="#F8FAFC" stroke="#0F172A" stroke-width="1.5" />
                                    </g>

                                    <!-- Body Frame Bawah -->
                                    <path d="M90 148 L145 148 L170 120 L220 120 L235 148" stroke="currentColor" class="text-slate-400 dark:text-slate-600" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" />

                                    <!-- Main Body Motor Skuter -->
                                    <path d="M100 135 C100 115 115 95 140 95 L190 95 C205 95 215 105 210 125 L195 140 C175 145 120 145 100 135 Z" 
                                          class="fill-brand-main" />

                                    <!-- Jok Motor Ergonomis -->
                                    <path d="M115 93 C115 88 122 84 135 84 L175 84 C185 84 190 88 188 93 Z" fill="#0F172A" />

                                    <!-- Footstep / Dek Pijakan Kaki -->
                                    <path d="M150 140 L195 140" stroke="#334155" stroke-width="6" stroke-linecap="round" />

                                    <!-- Stang & Spion -->
                                    <path d="M210 120 L235 70" stroke="currentColor" class="text-slate-500 dark:text-slate-400" stroke-width="6" stroke-linecap="round" />
                                    <path d="M225 70 L245 70" stroke="#0F172A" stroke-width="8" stroke-linecap="round" />
                                    <circle cx="240" cy="55" r="7" fill="currentColor" class="text-slate-300 dark:text-slate-600" stroke="#0F172A" stroke-width="2" />
                                    <path d="M238 62 L237 70" stroke="#0F172A" stroke-width="3" />

                                    <!-- Lampu Headlamp & Indikator Amber -->
                                    <path d="M242 82 C248 85 248 95 242 98 Z" fill="#FBBF24" />
                                    <circle cx="241" cy="68" r="3.5" fill="#F59E0B" />

                                    <!-- Sorotan Cahaya Headlamp Halus -->
                                    <polygon points="247,84 275,76 275,104 247,96" fill="#FDE047" opacity="0.25" />

                                    <!-- Slebor Depan -->
                                    <path d="M220 125 C225 115 245 115 250 125" stroke="currentColor" class="text-brand-main" stroke-width="6" stroke-linecap="round" />

                                    <!-- Knalpot Belakang -->
                                    <rect x="55" y="148" width="35" height="10" rx="5" fill="#475569" stroke="#1E293B" stroke-width="2" />
                                </g>
                            </svg>
                        </div>

                        <!-- Floating Badge Kanan Bawah: Servis oli berikutnya - 13 hari lagi -->
                        <div class="absolute -bottom-3 right-2 sm:-bottom-5 sm:-right-4 z-20 bg-brand-main text-white rounded-xl sm:rounded-2xl px-3 py-1.5 sm:px-5 sm:py-3 shadow-md sm:shadow-xl animate-subtle-float-delayed">
                            <span class="block text-[10px] sm:text-[11px] font-medium text-white/90">Servis berikutnya</span>
                            <span class="block text-xs sm:text-sm font-black text-white mt-0.5">13 hari lagi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 3. SECTION FITUR (id="fitur", full-width bg-brand-main dark:bg-[#111827] text-white, Konsep Mengintip dari Bawah) ==================== -->
    <section id="fitur" class="w-full bg-brand-main dark:bg-[#111827] dark:border-t dark:border-slate-700/60 text-white pt-16 sm:pt-20 lg:pt-24 pb-14 sm:pb-20 lg:py-24 transition-colors duration-200 rounded-t-[2.5rem] sm:rounded-t-[3.5rem] lg:rounded-t-[4rem] shadow-2xl relative z-20 mt-4 sm:-mt-8 lg:-mt-10">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-8 lg:px-12">
            <!-- Eyebrow & Heading (Animasi: Show from Left) -->
            <div class="text-left mb-8 sm:mb-14 reveal-left">
                <span class="block text-xs sm:text-sm font-semibold text-white/80 uppercase tracking-wider mb-1.5 sm:mb-2">Fitur</span>
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight max-w-2xl leading-tight">
                    Semua yang Anda butuhkan untuk merawat motor
                </h2>
            </div>

            <!-- Grid 4 Kartu Fitur (Kartu 1 & 2: Show from Left, Kartu 3 & 4: Show from Right) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Kartu 1: Smart Odometer & 6 Part Vital (Show from Left, delay 100ms) -->
                <div class="bg-white dark:bg-[#1E293B] rounded-2xl p-6 sm:p-7 shadow-lg shadow-black/5 dark:shadow-black/30 border border-transparent dark:border-slate-700/60 flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-xl reveal-left delay-100">
                    <div class="w-11 h-11 rounded-xl bg-brand-main text-white flex items-center justify-center mb-5 shadow-sm flex-shrink-0">
                        <i data-lucide="gauge" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2.5">
                        Smart Odometer
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                        Prediksi keausan 6 komponen vital motor secara real-time dari rata-rata kilometer harian Anda.
                    </p>
                </div>

                <!-- Kartu 2: Scan Nota Servis AI (Show from Left, delay 200ms) -->
                <div class="bg-white dark:bg-[#1E293B] rounded-2xl p-6 sm:p-7 shadow-lg shadow-black/5 dark:shadow-black/30 border border-transparent dark:border-slate-700/60 flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-xl reveal-left delay-200">
                    <div class="w-11 h-11 rounded-xl bg-brand-main text-white flex items-center justify-center mb-5 shadow-sm flex-shrink-0">
                        <i data-lucide="receipt" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2.5">
                        Scan Nota Servis AI
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                        Ekstraksi otomatis bukti fisik nota bengkel via Gemini Vision ke Paspor Servis ber-badge Terverifikasi.
                    </p>
                </div>

                <!-- Kartu 3: Diagnosa AI Vision (Show from Right, delay 200ms) -->
                <div class="bg-white dark:bg-[#1E293B] rounded-2xl p-6 sm:p-7 shadow-lg shadow-black/5 dark:shadow-black/30 border border-transparent dark:border-slate-700/60 flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-xl reveal-right delay-200">
                    <div class="w-11 h-11 rounded-xl bg-brand-main text-white flex items-center justify-center mb-5 shadow-sm flex-shrink-0">
                        <i data-lucide="scan" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2.5">
                        Diagnosa AI Vision
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                        Foto panel speedometer untuk deteksi indikator MIL, sinkronisasi ODO, serta protokol darurat kendala motor.
                    </p>
                </div>

                <!-- Kartu 4: Kalkulator Kerugian (Show from Right, delay 300ms) -->
                <div class="bg-white dark:bg-[#1E293B] rounded-2xl p-6 sm:p-7 shadow-lg shadow-black/5 dark:shadow-black/30 border border-transparent dark:border-slate-700/60 flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-xl reveal-right delay-300">
                    <div class="w-11 h-11 rounded-xl bg-brand-main text-white flex items-center justify-center mb-5 shadow-sm flex-shrink-0">
                        <i data-lucide="calculator" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2.5">
                        Kalkulator Kerugian
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                        Simulasi interaktif efek domino keterlambatan servis dan estimasi pembengkakan biaya perbaikan.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 3.5 SECTION SHOWCASE / PREVIEW ANTARMUKA (id="showcase", Interaktif Alpine.js) ==================== -->
    <section id="showcase" class="w-full bg-slate-50 dark:bg-[#070D18] py-12 sm:py-20 lg:py-24 border-b border-slate-200/60 dark:border-slate-800/80 transition-colors duration-200" x-data="{ activeTab: 'dashboard' }">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-8 lg:px-12">
            <!-- Header Section -->
            <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-14 reveal-left">
                <span class="block text-xs sm:text-sm font-bold text-brand-main dark:text-brand-accent uppercase tracking-wider mb-2">Showcase Antarmuka</span>
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Pengalaman Digital Terintegrasi & Modern
                </h2>
                <p class="mt-2.5 sm:mt-3 text-xs sm:text-base text-slate-500 dark:text-slate-400">
                    Lihat bagaimana SERVISA menyajikan data perawatan kendaraan secara presisi, visual, dan mudah dipahami.
                </p>

                <!-- Segmented Tabs Switcher (Scroll horizontal di mobile agar tidak terpotong) -->
                <div class="mt-6 sm:mt-8 flex justify-center">
                    <div class="inline-flex max-w-full overflow-x-auto p-1 sm:p-1.5 rounded-xl sm:rounded-2xl bg-white dark:bg-[#1E293B] border border-slate-200/80 dark:border-slate-700/60 shadow-xs gap-1 sm:gap-2 no-scrollbar">
                        <button type="button" 
                                @click="activeTab = 'dashboard'" 
                                :class="activeTab === 'dashboard' ? 'bg-brand-main text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                                class="px-3 sm:px-6 py-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-1.5 sm:gap-2 whitespace-nowrap flex-shrink-0">
                            <i data-lucide="layout-grid" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            <span>Dashboard</span>
                        </button>
                        <button type="button" 
                                @click="activeTab = 'ocr'" 
                                :class="activeTab === 'ocr' ? 'bg-brand-main text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                                class="px-3 sm:px-6 py-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-1.5 sm:gap-2 whitespace-nowrap flex-shrink-0">
                            <i data-lucide="receipt" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            <span>Scan Nota AI</span>
                        </button>
                        <button type="button" 
                                @click="activeTab = 'kalkulator'" 
                                :class="activeTab === 'kalkulator' ? 'bg-brand-main text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                                class="px-3 sm:px-6 py-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-1.5 sm:gap-2 whitespace-nowrap flex-shrink-0">
                            <i data-lucide="calculator" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            <span>Kalkulator</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Content Mockup Card -->
            <div class="bg-white dark:bg-[#1E293B] rounded-2xl sm:rounded-3xl p-4 sm:p-10 border border-slate-200/80 dark:border-slate-700/60 shadow-xl max-w-5xl mx-auto transition-all duration-300">
                <!-- 1. Tab Dashboard Vital -->
                <div x-show="activeTab === 'dashboard'" x-transition class="space-y-4 sm:space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-4 sm:pb-6 border-b border-slate-100 dark:border-slate-700/60">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Decay Engine Aktif
                            </span>
                            <h3 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white mt-1">Honda Vario 160 ABS</h3>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="text-xs text-slate-400">Odometer Terkini</span>
                            <div class="text-xl sm:text-2xl font-black text-brand-main dark:text-brand-accent">14.850 KM</div>
                        </div>
                    </div>

                    <!-- Mini Part Vital Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0F172A]/70 border border-slate-100 dark:border-slate-700/50">
                            <span class="text-[11px] sm:text-xs font-medium text-slate-400">Oli Mesin</span>
                            <div class="text-sm sm:text-base font-bold text-slate-800 dark:text-white mt-0.5 sm:mt-1">Sisa 650 KM</div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 sm:h-2 rounded-full mt-2 sm:mt-2.5 overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full" style="width: 78%;"></div>
                            </div>
                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold mt-1 block">Waspada - 13 Hari lagi</span>
                        </div>
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0F172A]/70 border border-slate-100 dark:border-slate-700/50">
                            <span class="text-[11px] sm:text-xs font-medium text-slate-400">Oli Gardan</span>
                            <div class="text-sm sm:text-base font-bold text-slate-800 dark:text-white mt-0.5 sm:mt-1">Sisa 3.200 KM</div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 sm:h-2 rounded-full mt-2 sm:mt-2.5 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: 35%;"></div>
                            </div>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 block">Kondisi Aman</span>
                        </div>
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0F172A]/70 border border-slate-100 dark:border-slate-700/50">
                            <span class="text-[11px] sm:text-xs font-medium text-slate-400">Kampas Rem</span>
                            <div class="text-sm sm:text-base font-bold text-slate-800 dark:text-white mt-0.5 sm:mt-1">Sisa 120 KM</div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 sm:h-2 rounded-full mt-2 sm:mt-2.5 overflow-hidden">
                                <div class="bg-rose-500 h-full rounded-full" style="width: 95%;"></div>
                            </div>
                            <span class="text-[10px] text-rose-600 dark:text-rose-400 font-semibold mt-1 block">Kritis - Segera Ganti</span>
                        </div>
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0F172A]/70 border border-slate-100 dark:border-slate-700/50">
                            <span class="text-[11px] sm:text-xs font-medium text-slate-400">Busi</span>
                            <div class="text-sm sm:text-base font-bold text-slate-800 dark:text-white mt-0.5 sm:mt-1">Sisa 4.100 KM</div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 sm:h-2 rounded-full mt-2 sm:mt-2.5 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: 25%;"></div>
                            </div>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 block">Kondisi Aman</span>
                        </div>
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0F172A]/70 border border-slate-100 dark:border-slate-700/50">
                            <span class="text-[11px] sm:text-xs font-medium text-slate-400">V-Belt & Roller</span>
                            <div class="text-sm sm:text-base font-bold text-slate-800 dark:text-white mt-0.5 sm:mt-1">Sisa 8.500 KM</div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 sm:h-2 rounded-full mt-2 sm:mt-2.5 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: 45%;"></div>
                            </div>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 block">Kondisi Aman</span>
                        </div>
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0F172A]/70 border border-slate-100 dark:border-slate-700/50">
                            <span class="text-[11px] sm:text-xs font-medium text-slate-400">Filter Udara</span>
                            <div class="text-sm sm:text-base font-bold text-slate-800 dark:text-white mt-0.5 sm:mt-1">Sisa 2.800 KM</div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 sm:h-2 rounded-full mt-2 sm:mt-2.5 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: 30%;"></div>
                            </div>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 block">Kondisi Aman</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Tab OCR Scan Nota -->
                <div x-show="activeTab === 'ocr'" x-cloak x-transition class="space-y-4 sm:space-y-6">
                    <div class="flex items-center justify-between pb-4 sm:pb-6 border-b border-slate-100 dark:border-slate-700/60">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-brand-secondary/30 dark:bg-brand-main/20 text-brand-main dark:text-brand-accent">
                                <i data-lucide="sparkles" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                                Vision AI Sukses
                            </span>
                            <h3 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white mt-1">AHASS Pratama</h3>
                        </div>
                        <div class="px-2.5 py-1 sm:px-3.5 sm:py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[11px] sm:text-xs font-bold flex items-center gap-1 sm:gap-1.5">
                            <i data-lucide="check" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            Terverifikasi
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0F172A]/70 border border-slate-100 dark:border-slate-700/50 space-y-2.5 sm:space-y-3">
                            <span class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Rincian Pembacaan AI</span>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-slate-500 dark:text-slate-300">Tanggal Servis:</span>
                                <span class="font-bold text-slate-800 dark:text-white">20 Nov 2026</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-slate-500 dark:text-slate-300">Odometer:</span>
                                <span class="font-bold text-slate-800 dark:text-white">14.200 KM</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-slate-500 dark:text-slate-300">Total Biaya:</span>
                                <span class="font-extrabold text-brand-main dark:text-brand-accent">Rp185.000</span>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0F172A]/70 border border-slate-100 dark:border-slate-700/50 space-y-2">
                            <span class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Suku Cadang Terdeteksi</span>
                            <div class="flex items-center justify-between text-xs py-1 border-b border-slate-200/50 dark:border-slate-700/50">
                                <span class="text-slate-700 dark:text-slate-200">AHM Oil SPX2 0.8L</span>
                                <span class="font-semibold text-slate-900 dark:text-white">Rp65.000</span>
                            </div>
                            <div class="flex items-center justify-between text-xs py-1 border-b border-slate-200/50 dark:border-slate-700/50">
                                <span class="text-slate-700 dark:text-slate-200">Kampas Rem Depan</span>
                                <span class="font-semibold text-slate-900 dark:text-white">Rp75.000</span>
                            </div>
                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-700 dark:text-slate-200">Jasa Servis Lengkap</span>
                                <span class="font-semibold text-slate-900 dark:text-white">Rp45.000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Tab Kalkulator Kerugian -->
                <div x-show="activeTab === 'kalkulator'" x-cloak x-transition class="space-y-4 sm:space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4 pb-4 sm:pb-6 border-b border-slate-100 dark:border-slate-700/60">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-brand-secondary/30 dark:bg-brand-main/20 text-brand-main dark:text-brand-accent">
                                Analisis Finansial & Risiko
                            </span>
                            <h3 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white mt-1">Simulasi Keterlambatan</h3>
                        </div>
                        <span class="self-start sm:self-auto px-2.5 py-1 rounded-xl bg-rose-500/10 text-rose-600 font-bold text-[11px] sm:text-xs">Telat 3 Bulan</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div class="p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/40">
                            <span class="text-[11px] sm:text-xs font-bold text-emerald-700 dark:text-emerald-400 block mb-1">Rutin Servis Tepat Waktu</span>
                            <div class="text-xl sm:text-2xl font-black text-emerald-800 dark:text-emerald-300">Rp55.000</div>
                            <p class="text-[10px] sm:text-[11px] text-emerald-600 dark:text-emerald-400/80 mt-1.5 sm:mt-2">Mesin awet, gesekan minim, performa motor stabil.</p>
                        </div>
                        <div class="p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-rose-50 dark:bg-rose-950/20 border border-rose-200/60 dark:border-rose-800/40 sm:col-span-2">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[11px] sm:text-xs font-bold text-rose-700 dark:text-rose-400 block mb-1">Jika Diabaikan (Turun Mesin)</span>
                                    <div class="text-2xl sm:text-3xl font-black text-rose-800 dark:text-rose-300">Rp1.560.000</div>
                                </div>
                                <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-rose-600 text-white text-[10px] sm:text-xs font-black">28x Lebih Mahal</span>
                            </div>
                            <p class="text-[11px] sm:text-xs text-rose-600 dark:text-rose-400/80 mt-2">Piston aus dan silinder baret memicu kerusakan berantai dan pembengkakan biaya perbaikan berat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
            </div>
        </div>
    </section>


    <!-- ==================== 4. SECTION HEMAT BIAYA (id="hemat-biaya", bg-white dark:bg-[#0B1120]) ==================== -->
    <section id="hemat-biaya" class="w-full bg-white dark:bg-[#0B1120] py-12 sm:py-20 lg:py-24 transition-colors duration-200">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-8 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Kiri (Animasi: Show from Left) -->
                <div class="lg:col-span-6 text-left reveal-left">
                    <span class="block text-xs sm:text-sm font-bold text-brand-main dark:text-brand-accent uppercase tracking-wider mb-2">Simulasi Biaya</span>
                    <h2 class="text-xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight mb-3 sm:mb-4">
                        Menunda servis membuat biaya berlipat
                    </h2>
                    <p class="text-xs sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed max-w-lg">
                        Contoh penggantian oli mesin. Perawatan rutin jauh lebih murah dibanding memperbaiki kerusakan mesin.
                    </p>
                </div>

                <!-- Kanan (Animasi: Show from Right) -->
                <div class="lg:col-span-6 reveal-right">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-6">
                        <!-- Kartu 1: Servis tepat waktu -->
                        <div class="bg-white dark:bg-[#1E293B] border-2 border-emerald-300 dark:border-emerald-600/50 rounded-2xl p-5 sm:p-8 shadow-xs flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <span class="block text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400 mb-1.5 sm:mb-2">
                                Servis tepat waktu
                            </span>
                            <span class="block text-2xl sm:text-4xl lg:text-[40px] font-black text-slate-900 dark:text-white tracking-tight">
                                Rp55.000
                            </span>
                        </div>

                        <!-- Kartu 2: Jika diabaikan -->
                        <div class="bg-brand-main text-white rounded-2xl p-5 sm:p-8 shadow-lg shadow-brand-main/20 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                            <span class="block text-xs sm:text-sm font-medium text-white/90 mb-1.5 sm:mb-2">
                                Jika diabaikan
                            </span>
                            <span class="block text-2xl sm:text-4xl lg:text-[40px] font-black text-white tracking-tight">
                                Rp1.200.000
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 5. SECTION CARA KERJA (id="cara-kerja", bg-white dark:bg-[#0B1120]) ==================== -->
    <section id="cara-kerja" class="w-full bg-white dark:bg-[#0B1120] py-12 sm:py-20 transition-colors duration-200">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-8 lg:px-12">
            <!-- Eyebrow & Heading (Animasi: Show from Left) -->
            <div class="text-left mb-8 sm:mb-12 reveal-left">
                <span class="block text-xs sm:text-sm font-bold text-brand-main dark:text-brand-accent uppercase tracking-wider mb-1.5 sm:mb-2">Cara kerja</span>
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Tiga langkah sederhana
                </h2>
            </div>

            <!-- Grid 3 Kartu Langkah Sederhana -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <!-- Kartu 1: Isi data kendaraan (Show from Left) -->
                <div class="bg-slate-50 dark:bg-[#1E293B] rounded-2xl p-5 sm:p-7 border border-slate-100/80 dark:border-slate-700/60 shadow-xs flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-md reveal-left">
                    <span class="block text-3xl sm:text-5xl font-black text-brand-main/25 dark:text-brand-accent/30 mb-2 sm:mb-4 select-none">
                        1
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-1.5 sm:mb-2">
                        Isi data kendaraan
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Masukkan tipe motor, odometer, dan estimasi KM harian.
                    </p>
                </div>

                <!-- Kartu 2: Dapatkan prediksi (Show from Left, delay 150ms) -->
                <div class="bg-slate-50 dark:bg-[#1E293B] rounded-2xl p-5 sm:p-7 border border-slate-100/80 dark:border-slate-700/60 shadow-xs flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-md reveal-left delay-150">
                    <span class="block text-3xl sm:text-5xl font-black text-brand-main/25 dark:text-brand-accent/30 mb-2 sm:mb-4 select-none">
                        2
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-1.5 sm:mb-2">
                        Dapatkan prediksi
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        SERVISA menghitung kapan tiap komponen perlu diservis.
                    </p>
                </div>

                <!-- Kartu 3: Servis tepat waktu (Show from Right, delay 200ms) -->
                <div class="bg-slate-50 dark:bg-[#1E293B] rounded-2xl p-5 sm:p-7 border border-slate-100/80 dark:border-slate-700/60 shadow-xs flex flex-col justify-start text-left transition-all duration-300 hover:-translate-y-1 hover:shadow-md reveal-right delay-200">
                    <span class="block text-3xl sm:text-5xl font-black text-brand-main/25 dark:text-brand-accent/30 mb-2 sm:mb-4 select-none">
                        3
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-1.5 sm:mb-2">
                        Servis tepat waktu
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Terima peringatan, tandai sudah diservis, dan jadwal diperbarui otomatis.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 5.5 SECTION FAQ (Accordion Interaktif Alpine.js) ==================== -->
    <section id="faq" class="w-full bg-slate-50 dark:bg-[#070D18] py-12 sm:py-20 border-t border-slate-200/60 dark:border-slate-800/80 transition-colors duration-200" x-data="{ activeFaq: null }">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-8 lg:px-12">
            <div class="text-center mb-8 sm:mb-12 reveal-left">
                <span class="block text-xs sm:text-sm font-bold text-brand-main dark:text-brand-accent uppercase tracking-wider mb-1.5 sm:mb-2">FAQ</span>
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="mt-2 sm:mt-3 text-xs sm:text-base text-slate-500 dark:text-slate-400 max-w-xl mx-auto">
                    Pelajari bagaimana SERVISA memanfaatkan AI untuk memudahkan perawatan kendaraan bermotor Anda.
                </p>
            </div>

            <!-- Accordion Items -->
            <div class="space-y-3 sm:space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-white dark:bg-[#1E293B] rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs overflow-hidden transition-all duration-200">
                    <button type="button" 
                            @click="activeFaq = (activeFaq === 1 ? null : 1)" 
                            class="w-full px-4 sm:px-6 py-4 sm:py-5 text-left flex items-center justify-between font-bold text-xs sm:text-base text-slate-900 dark:text-white focus:outline-none">
                        <span class="pr-2">Bagaimana AI mengekstrak data dari nota servis bengkel?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-brand-main': activeFaq === 1 }"></i>
                    </button>
                    <div x-show="activeFaq === 1" x-cloak x-transition.opacity.duration.200ms class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-700/50">
                        SERVISA menggunakan model multimodal Google Gemini Vision untuk memindai dokumen nota bengkel (berupa foto kamera maupun berkas PDF). Sistem secara otomatis mengidentifikasi nama bengkel, tanggal servis, angka odometer, daftar suku cadang yang diganti, serta rincian biaya. Anda dapat meninjau dan mengoreksi data tersebut sebelum disimpan ke Paspor Servis resmi.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white dark:bg-[#1E293B] rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs overflow-hidden transition-all duration-200">
                    <button type="button" 
                            @click="activeFaq = (activeFaq === 2 ? null : 2)" 
                            class="w-full px-4 sm:px-6 py-4 sm:py-5 text-left flex items-center justify-between font-bold text-xs sm:text-base text-slate-900 dark:text-white focus:outline-none">
                        <span class="pr-2">Apakah angka odometer harus diisi manual setiap hari?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-brand-main': activeFaq === 2 }"></i>
                    </button>
                    <div x-show="activeFaq === 2" x-cloak x-transition.opacity.duration.200ms class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-700/50">
                        Tidak perlu! SERVISA dilengkapi dengan <em>Smart Odometer Decay Engine</em> yang secara cerdas memperkirakan penambahan jarak tempuh harian Anda secara otomatis. Anda juga dapat memperbarui odometer secara instan hanya dengan memotret speedometer motor Anda menggunakan fitur AI Speedometer Scanner.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white dark:bg-[#1E293B] rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs overflow-hidden transition-all duration-200">
                    <button type="button" 
                            @click="activeFaq = (activeFaq === 3 ? null : 3)" 
                            class="w-full px-4 sm:px-6 py-4 sm:py-5 text-left flex items-center justify-between font-bold text-xs sm:text-base text-slate-900 dark:text-white focus:outline-none">
                        <span class="pr-2">Apa itu Paspor Servis Terverifikasi dan bagaimana cara mengunduhnya?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-brand-main': activeFaq === 3 }"></i>
                    </button>
                    <div x-show="activeFaq === 3" x-cloak x-transition.opacity.duration.200ms class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-700/50">
                        Paspor Servis adalah buku rekam medis digital untuk motor Anda yang mencatat seluruh histori servis, pergantian oli, dan suku cadang. Catatan yang disertai bukti nota fisik akan mendapatkan badge hijau <em>Terverifikasi</em>. Dokumen ini dapat diunduh dalam format PDF resmi, sangat berguna untuk meningkatkan nilai jual kembali motor Anda.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white dark:bg-[#1E293B] rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs overflow-hidden transition-all duration-200">
                    <button type="button" 
                            @click="activeFaq = (activeFaq === 4 ? null : 4)" 
                            class="w-full px-4 sm:px-6 py-4 sm:py-5 text-left flex items-center justify-between font-bold text-xs sm:text-base text-slate-900 dark:text-white focus:outline-none">
                        <span class="pr-2">Bagaimana SERVISA menghitung estimasi kerugian jika servis ditunda?</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-brand-main': activeFaq === 4 }"></i>
                    </button>
                    <div x-show="activeFaq === 4" x-cloak x-transition.opacity.duration.200ms class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-700/50">
                        Melalui fitur Kalkulator Kerugian, sistem mensimulasikan efek domino dari kelalaian perawatan tiap komponen vital. Contohnya, menunda penggantian oli mesin akan mengikis dinding silinder dan piston, yang dapat memicu turun mesin berbiaya jutaan rupiah dibanding biaya servis berkala yang hanya puluhan ribu rupiah.
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================== 6. BOTTOM CTA & FOOTER (bg-brand-main dark:bg-[#0B1120] text-white) ==================== -->
    <footer class="w-full bg-brand-main dark:bg-[#0B1120] text-white transition-colors duration-200 mt-auto border-t border-transparent dark:border-slate-800/80">
        <!-- Baris Atas: Siap merawat motor Anda? + Tombol Mulai Sekarang -->
        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12 py-12 sm:py-16">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 text-left">
                <!-- Kiri (Animasi: Show from Left) -->
                <div class="reveal-left">
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Siap merawat motor Anda?
                    </h2>
                    <p class="text-sm sm:text-base text-white/80 dark:text-slate-300 mt-1">
                        Masuk dan lihat jadwal servis berikutnya.
                    </p>
                </div>

                <!-- Kanan (Animasi: Show from Right) -->
                <div class="reveal-right">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-white hover:bg-white/90 dark:bg-brand-main dark:hover:bg-brand-main/90 text-brand-main dark:text-white font-bold text-sm sm:text-base shadow-lg transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 whitespace-nowrap">
                        Mulai Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- Garis Pemisah Tipis -->
        <div class="w-full border-t border-white/20 dark:border-slate-800/80"></div>

        <!-- Baris Bawah: Teks Footer Rata Tengah -->
        <div class="max-w-[1400px] mx-auto px-6 sm:px-8 lg:px-12 py-8 text-center">
            <p class="text-xs sm:text-sm text-white/85 dark:text-slate-400 font-medium">
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
