<x-guest-layout>
    @section('title', 'Masuk ke Akun')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
        <!-- Sisi Kiri: Form Login Interaktif -->
        <div class="lg:col-span-6 w-full max-w-md mx-auto">
            <!-- Tombol Kembali ke Beranda Sesuai Mockup -->
            <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-brand-main dark:hover:text-brand-accent bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 px-4 py-2 rounded-xl shadow-xs transition mb-8 group">
                <i data-lucide="chevron-left" class="w-4 h-4 transition group-hover:-translate-x-0.5"></i>
                <span>Kembali ke beranda</span>
            </a>

            <!-- Judul & Subtitle Sesuai Mockup -->
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Selamat datang kembali
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm sm:text-base leading-relaxed">
                Masuk untuk memantau kondisi kendaraan Anda.
            </p>

            <!-- Status Sesi (Jika ada flash message) -->
            <x-auth-session-status class="mt-4 mb-2" :status="session('status')" />

            <!-- Form Autentikasi Login -->
            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           placeholder="nama@email.com" 
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-main focus:border-brand-main transition text-sm shadow-xs" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500" />
                </div>

                <!-- Input Kata Sandi -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Kata Sandi
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password" 
                           placeholder="Minimal 6 karakter" 
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-main focus:border-brand-main transition text-sm shadow-xs" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-500" />
                </div>

                <!-- Tombol Submit Masuk -->
                <button type="submit" 
                        class="w-full mt-3 py-3.5 px-6 rounded-xl bg-brand-main text-white font-semibold text-sm sm:text-base shadow-sm hover:opacity-90 active:scale-[0.99] transition duration-150 flex items-center justify-center">
                    Masuk
                </button>
            </form>

            <!-- Tautan Registrasi Akun Baru -->
            <p class="mt-8 text-sm text-center text-slate-600 dark:text-slate-400">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-semibold text-brand-main dark:text-brand-accent hover:underline">
                    Daftar
                </a>
            </p>
        </div>

        <!-- Sisi Kanan: Ilustrasi Motor Skuter & Tagline Edukatif (Layar Desktop) -->
        <div class="hidden lg:flex lg:col-span-6 justify-center">
            <x-auth-illustration />
        </div>
    </div>
</x-guest-layout>
