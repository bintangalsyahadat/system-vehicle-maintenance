@extends('layouts.app')

@section('title', 'Kalkulator Kerugian')

@section('greeting', 'Kalkulator Kerugian')

@section('content')
<div class="space-y-6" x-data="calculatorManager(@js($calculatorItems))">

    <!-- Layout 2 Kolom Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- ==================== KOLOM KIRI (PILIH KOMPONEN, KOMPARASI, DAMPAK FINANSIAL) ==================== -->
        <div class="lg:col-span-7 space-y-6">

            <!-- KARTU 1: PILIH KOMPONEN KENDARAAN -->
            <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm transition-colors">
                <span class="block text-sm font-bold text-slate-800 dark:text-white mb-3">
                    Pilih Komponen Kendaraan
                </span>
                
                <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar pb-1">
                    <template x-for="item in items" :key="item.id">
                        <button type="button" 
                                @click="selectedId = item.id"
                                :class="selectedId == item.id ? 'bg-brand-main text-white shadow-md shadow-brand-main/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition"
                                x-text="item.short_name">
                        </button>
                    </template>
                </div>

                <!-- Slider Simulasi Keterlambatan Servis (Fitur Tambahan Interaktif Sesuai AGENTS.md) -->
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">
                        <span class="flex items-center space-x-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>Simulasi Keterlambatan:</span>
                        </span>
                        <span class="font-extrabold text-brand-main dark:text-brand-accent px-2 py-0.5 rounded-full bg-brand-secondary/30 dark:bg-brand-main/20" 
                              x-text="delayMonths === 1 ? '1 Bulan (Standar)' : delayMonths + ' Bulan'">
                        </span>
                    </div>
                    <input type="range" 
                           min="1" 
                           max="6" 
                           step="1" 
                           x-model.number="delayMonths" 
                           class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-brand-main">
                    <div class="flex justify-between text-[10px] text-slate-400 mt-1 font-medium">
                        <span>1 Bln</span>
                        <span>2 Bln</span>
                        <span>3 Bln</span>
                        <span>4 Bln</span>
                        <span>5 Bln</span>
                        <span>6 Bln</span>
                    </div>
                </div>
            </div>

            <!-- KARTU 2: DUA BOX KOMPARASI BIAYA (HIJAU VS MERAH PERSIS FOTO) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- BOX KIRI (HIJAU: SERVIS TEPAT WAKTU) -->
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm transition-colors flex flex-col justify-between min-h-[170px]">
                    <div>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 tracking-wide block">
                            Servis Tepat Waktu
                        </span>
                        <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white mt-1.5 tracking-tight" 
                             x-text="formatRupiah(routineCost)">
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-4 leading-relaxed" 
                       x-text="currentItem ? currentItem.routine_desc : 'Perawatan rutin membantu menjaga performa mesin.'">
                    </p>
                </div>

                <!-- BOX KANAN (MERAH: JIKA DIABAIKAN) -->
                <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm transition-colors flex flex-col justify-between min-h-[170px]">
                    <div>
                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400 tracking-wide block">
                            Jika Diabaikan
                        </span>
                        <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white mt-1.5 tracking-tight" 
                             x-text="formatRupiah(neglectCost)">
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-4 leading-relaxed" 
                       x-text="currentItem ? currentItem.neglect_desc : 'Kerusakan komponen dapat menimbulkan biaya perbaikan besar.'">
                    </p>
                </div>

            </div>

            <!-- KARTU 3: DAMPAK FINANSIAL & GRAFIK BATANG (BAR CHART) -->
            <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-7 border border-slate-100 dark:border-slate-800 shadow-sm transition-colors space-y-4">
                
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white">Dampak Finansial</h3>
                    <span class="text-xs text-slate-400 font-medium block mt-1">Estimasi biaya tambahan</span>
                    
                    <div class="text-3xl sm:text-4xl font-black text-brand-main dark:text-brand-accent tracking-tight mt-1.5" 
                         x-text="'+' + formatRupiah(additionalCost)">
                    </div>
                    
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">
                        Anda berpotensi membayar <span class="font-extrabold text-slate-800 dark:text-white" x-text="costMultiplier + 'x lebih mahal.'"></span>
                    </p>
                </div>

                <!-- Wadah Visualisasi Grafik Batang (Persis Desain Foto) -->
                <div class="bg-slate-50/80 dark:bg-slate-800/40 rounded-2xl p-6 border border-slate-100 dark:border-slate-800/80 mt-4">
                    <div class="flex items-end justify-center space-x-12 sm:space-x-16 min-h-[160px] pt-6 pb-2">
                        
                        <!-- Batang 1: Tepat Waktu (Hijau) -->
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 leading-none" 
                                  x-text="formatRupiah(routineCost)">
                            </span>
                            <div class="w-20 sm:w-24 bg-emerald-500 rounded-t-xl transition-all duration-500 shadow-sm" 
                                 :style="'height: ' + barHeightRoutine + 'px; min-height: 14px;'">
                            </div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-2.5">
                                Tepat waktu
                            </span>
                        </div>

                        <!-- Batang 2: Diabaikan (Merah / Brand) -->
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 leading-none" 
                                  x-text="formatRupiah(neglectCost)">
                            </span>
                            <div class="w-20 sm:w-24 bg-brand-main rounded-t-xl transition-all duration-500 shadow-md shadow-brand-main/20" 
                                 :style="'height: ' + barHeightNeglect + 'px; min-height: 50px;'">
                            </div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-2.5">
                                Diabaikan
                            </span>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <!-- ==================== KOLOM KANAN (LINGKUNGAN & SDG 12) ==================== -->
        <div class="lg:col-span-5">
            <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-6 sm:p-7 border border-slate-100 dark:border-slate-800 shadow-sm transition-colors space-y-6">
                
                <!-- Header Card: Tag Lingkungan & SDG 12 Badge -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lingkungan</span>
                    <span class="bg-slate-900 dark:bg-slate-800 text-white text-[11px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                        SDG 12
                    </span>
                </div>

                <!-- Judul & Deskripsi Lingkungan -->
                <div class="space-y-2">
                    <h2 class="text-lg font-black text-slate-800 dark:text-white">
                        Dampak Terhadap Lingkungan
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed" 
                       x-text="currentItem ? currentItem.environmental_desc : ''">
                    </p>
                </div>

                <!-- 2 Metrik Status (Limbah Logam & Zat Berbahaya) -->
                <div class="grid grid-cols-2 gap-4 py-4 border-y border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-xs text-slate-400 font-medium block">Limbah logam</span>
                        <span class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-white mt-0.5 block" 
                              x-text="currentItem ? currentItem.metal_waste : 'Meningkat'">
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 font-medium block">Zat berbahaya</span>
                        <span class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-white mt-0.5 block" 
                              x-text="currentItem ? currentItem.hazardous_substance : 'Berisiko'">
                        </span>
                    </div>
                </div>

                <!-- Quote Box / Kotak Edukasi SDG 12 di Bagian Bawah -->
                <div class="bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-5 transition-colors">
                    <p class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 leading-relaxed">
                        Merawat kendaraan tepat waktu bukan hanya menghemat biaya, tetapi juga membantu mengurangi limbah.
                    </p>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- Alpine Component Script Definition -->
<script>
    function calculatorManager(itemsData) {
        return {
            items: itemsData || [],
            selectedId: itemsData && itemsData.length > 0 ? itemsData[0].id : 1,
            delayMonths: 1,

            get currentItem() {
                const found = this.items.find(i => Number(i.id) === Number(this.selectedId));
                return found || (this.items.length > 0 ? this.items[0] : null);
            },

            get routineCost() {
                return this.currentItem ? Number(this.currentItem.routine_cost) : 55000;
            },

            get neglectCost() {
                if (!this.currentItem) return 1200000;
                const base = Number(this.currentItem.neglect_cost);
                // Skala eskalasi keterlambatan (1 bulan = 1.0x, tiap bulan tambahan bertambah 15%)
                const multiplier = 1 + ((this.delayMonths - 1) * 0.15);
                return Math.round(base * multiplier);
            },

            get additionalCost() {
                return Math.max(0, this.neglectCost - this.routineCost);
            },

            get costMultiplier() {
                if (this.routineCost <= 0) return 1;
                return Math.round(this.neglectCost / this.routineCost);
            },

            get barHeightRoutine() {
                return 18;
            },

            get barHeightNeglect() {
                // Biarkan faktor delayMonths memberi kontribusi visual tinggi bar
                const baseHeight = 55;
                const growth = (this.delayMonths - 1) * 14;
                return Math.min(140, baseHeight + growth);
            },

            formatRupiah(val) {
                return 'Rp' + Number(val).toLocaleString('id-ID');
            }
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endsection
