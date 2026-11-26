<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" 
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
          colorTheme: localStorage.getItem('colorTheme') || 'theme-pink',
          toggleDarkMode() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }" 
      :class="{ 'dark': darkMode, 'theme-pink': colorTheme === 'theme-pink', 'theme-blue': colorTheme === 'theme-blue' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SERVISA') }} - @yield('title', 'Autentikasi')</title>

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

    <!-- Fonts Figtree -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Scripts & Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased min-h-screen bg-brand-light dark:bg-[#0B1120] text-slate-800 dark:text-slate-100 flex flex-col justify-between transition-colors duration-200">

    <!-- Header Navbar Atas Sesuai Desain Mockup -->
    <header class="w-full max-w-7xl mx-auto px-6 pt-6 sm:pt-8 flex items-center justify-between">
        <!-- Logo SERVISA -->
        <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
            <div class="w-10 h-10 rounded-full bg-brand-main text-white flex items-center justify-center shadow-sm transition group-hover:scale-105">
                <i data-lucide="gauge" class="w-5 h-5"></i>
            </div>
            <span class="text-xl font-extrabold tracking-wider text-slate-900 dark:text-white">SERVISA</span>
        </a>

        <!-- Switcher Mode Gelap / Terang (Sesuai Tombol Bulat di Mockup) -->
        <div class="flex items-center space-x-3">
            <button type="button" 
                    @click="toggleDarkMode()" 
                    class="w-10 h-10 rounded-full border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/80 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-brand-main dark:hover:text-brand-accent shadow-xs transition" 
                    title="Ganti Mode Gelap / Terang">
                <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                <i x-show="darkMode" data-lucide="sun" class="w-5 h-5" style="display: none;"></i>
            </button>
        </div>
    </header>

    <!-- Konten Utama (Form Login / Register Split-Screen) -->
    <main class="flex-1 flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="w-full max-w-6xl">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer Bersih -->
    <footer class="py-4 text-center text-xs text-slate-400 dark:text-slate-600">
        SERVISA. Perawatan kendaraan yang lebih tenang.
    </footer>

    <!-- Inisialisasi Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
