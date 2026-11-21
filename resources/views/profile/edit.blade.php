@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('greeting', 'Profil Pengguna')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Flash Message Notification -->
    @if (session('status') === 'profile-updated')
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>Informasi profil berhasil diperbarui!</span>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>Kata sandi Anda berhasil diperbarui!</span>
        </div>
    @endif

    <!-- ==================== 1. HERO PROFILE SUMMARY CARD ==================== -->
    <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 transition-colors">
        <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-5 text-center sm:text-left">
            <!-- Avatar Initials -->
            <div class="w-20 h-20 rounded-3xl bg-brand-main/50 text-white flex items-center justify-center font-extrabold text-2xl shadow-lg shadow-brand-main/20 flex-shrink-0">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <div>
                <div class="flex items-center justify-center sm:justify-start space-x-2">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white">{{ Auth::user()->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-secondary/50 dark:bg-brand-main/20 text-brand-main dark:text-brand-accent">
                        Rider
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-center sm:justify-start space-x-1.5">
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                    <span>{{ Auth::user()->email }}</span>
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center justify-center sm:justify-start space-x-1.5">
                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                    <span>{{ Auth::user()->phone_whatsapp ?: 'Nomor WhatsApp belum diisi' }}</span>
                </p>
            </div>
        </div>

        <!-- Garasi Quick Link Button -->
        <a href="{{ route('garage') }}" class="flex items-center space-x-3 bg-brand-secondary/30 dark:bg-slate-900/60 hover:bg-brand-secondary/50 dark:hover:bg-slate-800/80 p-4 rounded-2xl border border-brand-main/20 dark:border-slate-800 transition">
            <div class="w-10 h-10 rounded-xl bg-brand-main text-white flex items-center justify-center">
                <i data-lucide="bike" class="w-5 h-5"></i>
            </div>
            <div class="text-left">
                <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Garasi Saya</span>
                <span class="text-xs font-bold text-brand-main dark:text-brand-accent flex items-center">
                    <span>Lihat {{ Auth::user()->vehicles->count() }} Kendaraan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                </span>
            </div>
        </a>
    </div>

    <!-- ==================== 2. EDIT PROFILE & SECURITY FORMS ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Form 1: Informasi Akun & WhatsApp -->
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-sm space-y-5 transition-colors">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center space-x-2">
                    <i data-lucide="user" class="w-4 h-4 text-brand-main dark:text-brand-accent"></i>
                    <span>Informasi Akun</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui nama, email, dan nomor WhatsApp notifikasi servis.</p>
            </div>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('patch')

                <!-- Nama -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    @error('name')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    @error('email')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        Nomor WhatsApp (Pengingat Servis)
                    </label>
                    <input type="text" name="phone_whatsapp" value="{{ old('phone_whatsapp', Auth::user()->phone_whatsapp) }}" placeholder="Contoh: 081234567890"
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    <span class="text-[10px] text-slate-400">Digunakan untuk integrasi jadwal pengingat servis otomatis.</span>
                    @error('phone_whatsapp')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-main hover:bg-brand-dark text-white text-xs font-bold transition shadow-md shadow-brand-main/25">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Form 2: Keamanan / Ganti Password -->
        <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-sm space-y-5 transition-colors">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center space-x-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-brand-main dark:text-brand-accent"></i>
                    <span>Ubah Kata Sandi</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pastikan akun menggunakan kata sandi yang kuat dan aman.</p>
            </div>

            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('put')

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" autocomplete="current-password"
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    @error('current_password', 'updatePassword')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" autocomplete="new-password"
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    @error('password', 'updatePassword')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-brand-main focus:ring focus:ring-brand-main/20 py-2.5 px-3">
                    @error('password_confirmation', 'updatePassword')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-bold transition shadow-sm">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- ==================== 3. DANGER ZONE (HAPUS AKUN) ==================== -->
    <div class="bg-red-50/50 dark:bg-red-950/20 rounded-3xl p-6 sm:p-8 border border-red-100 dark:border-red-900/40 space-y-4" x-data="{ confirmDelete: false }">
        <div>
            <h3 class="text-base font-bold text-red-700 dark:text-red-400 flex items-center space-x-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
                <span>Hapus Akun</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Setelah akun Anda dihapus, seluruh data kendaraan, riwayat servis, dan catatan diagnosa AI akan dihapus secara permanen.
            </p>
        </div>

        <button @click="confirmDelete = true" 
                class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition shadow-sm">
            Hapus Akun Saya
        </button>

        <!-- Modal Konfirmasi Hapus Akun -->
        <div x-show="confirmDelete" 
             x-cloak 
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.outside="confirmDelete = false" 
                 class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-transparent dark:border-slate-700">
                <div class="flex items-center space-x-3 text-red-600">
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-base">Konfirmasi Hapus Akun</h4>
                        <p class="text-xs text-slate-400">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 text-left">
                    @csrf
                    @method('delete')

                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        Masukkan kata sandi Anda untuk mengonfirmasi penghapusan permanen akun ini:
                    </p>

                    <div>
                        <input type="password" name="password" placeholder="Kata sandi akun" required
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-red-500 focus:ring focus:ring-red-500/20 py-2.5 px-3">
                        @error('password', 'userDeletion')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex space-x-2 pt-2">
                        <button type="button" @click="confirmDelete = false" 
                                class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="w-1/2 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-xs font-bold text-white transition shadow-md shadow-red-600/30">
                            Ya, Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
