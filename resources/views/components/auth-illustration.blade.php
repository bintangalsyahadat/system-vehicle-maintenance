<div class="relative w-full max-w-lg flex flex-col items-center select-none">
    <!-- Background Curved Pastel Shape (Dekoratif Melengkung Sesuai Tema) -->
    <div class="absolute -top-12 -right-8 w-80 h-80 sm:w-96 sm:h-96 bg-brand-secondary/50 dark:bg-brand-secondary/20 rounded-full -z-10 blur-2xl transform scale-110"></div>
    
    <!-- Kontainer Ilustrasi Motor Skuter (Sama Persis dengan Dashboard, Lengkap dengan Animasi Melaju & Roda Berputar) -->
    <div class="relative w-full h-72 sm:h-80 flex items-center justify-center">
        <!-- Glow Efek di Belakang Skuter -->
        <div class="absolute inset-0 bg-brand-secondary/40 dark:bg-brand-main/15 rounded-full blur-3xl transform scale-125"></div>

        <!-- Animasi CSS Khusus Motor Melaju -->
        <style>
            @keyframes bikeRide {
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
            @keyframes shadowPulse {
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
            @keyframes speedDash {
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
            .animate-bike-ride {
                animation: bikeRide 1.6s ease-in-out infinite;
                transform-origin: center bottom;
            }
            .animate-shadow-pulse {
                animation: shadowPulse 1.6s ease-in-out infinite;
                transform-origin: center;
            }
            .animate-speed-dash-1 {
                animation: speedDash 1s ease-out infinite;
            }
            .animate-speed-dash-2 {
                animation: speedDash 1.2s ease-out 0.3s infinite;
            }
            .animate-speed-dash-3 {
                animation: speedDash 0.9s ease-out 0.6s infinite;
            }
        </style>

        <!-- Vector Graphic Scooter -->
        <svg class="w-full h-full drop-shadow-md select-none" viewBox="15 45 270 145" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Bayangan Tanah Dinamis -->
            <ellipse cx="160" cy="180" rx="105" ry="8" fill="currentColor" class="text-slate-200 dark:text-slate-800/80 animate-shadow-pulse" />

            <!-- Efek Garis Angin Kecepatan di Belakang Motor -->
            <g>
                <line x1="60" y1="150" x2="35" y2="150" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-speed-dash-1" stroke-width="2.5" stroke-linecap="round" />
                <line x1="50" y1="130" x2="20" y2="130" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-speed-dash-2" stroke-width="2" stroke-linecap="round" />
                <line x1="70" y1="110" x2="45" y2="110" stroke="currentColor" class="text-slate-300 dark:text-slate-600 animate-speed-dash-3" stroke-width="2" stroke-linecap="round" />
            </g>

            <!-- GROUP MOTOR DENGAN BOUNCING SUSPENSI -->
            <g class="animate-bike-ride">
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

    <!-- Teks Headline & Tagline Edukatif Sesuai UI Mockup -->
    <div class="mt-6 text-center max-w-sm">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
            Rawat kendaraan, hemat biaya
        </h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-2.5 leading-relaxed">
            Pantau jadwal servis, hitung kerugian, dan diagnosa masalah dalam satu tempat.
        </p>
    </div>
</div>
