<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\MaintenanceItem;
use App\Models\ServiceLog;
use App\Models\ServiceReminder;
use App\Models\AiScanHistory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Servisa
        $admin = User::firstOrCreate(
            ['email' => 'admin@servisa.com'],
            [
                'name' => 'Administrator Servisa',
                'password' => Hash::make('password123'),
                'phone_whatsapp' => '081122334455',
            ]
        );

        // 2. Akun User Demo Servisa
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@servisa.com'],
            [
                'name' => 'Demo Rider',
                'password' => Hash::make('password123'),
                'phone_whatsapp' => '081234567890',
            ]
        );

        // 3. Data Master 5 Suku Cadang Pabrikan (Astra Honda / Yamaha)
        $items = [
            [
                'vehicle_type' => 'motor',
                'item_name' => 'Oli Mesin SPX2 (Full Synthetic)',
                'interval_km' => 2500,
                'interval_months' => 2,
                'routine_cost' => 65000,
                'neglect_cost' => 1850000,
                'neglect_impact_desc' => 'Oli basi/kering memicu gesekan ekstrem piston dan blok silinder baret fatal hingga turun mesin.',
            ],
            [
                'vehicle_type' => 'motor',
                'item_name' => 'Oli Gardan / Gear Oil',
                'interval_km' => 8000,
                'interval_months' => 8,
                'routine_cost' => 25000,
                'neglect_cost' => 450000,
                'neglect_impact_desc' => 'Gigi rasio transmisi aus dan bunyi mendengung kasar saat motor berakselerasi.',
            ],
            [
                'vehicle_type' => 'motor',
                'item_name' => 'Busi NGK CPR9EA-9',
                'interval_km' => 8000,
                'interval_months' => 8,
                'routine_cost' => 35000,
                'neglect_cost' => 250000,
                'neglect_impact_desc' => 'Elektroda aus memicu pembakaran tidak sempurna, mesin tersendat, boros BBM, dan emisi hidrokarbon kotor.',
            ],
            [
                'vehicle_type' => 'motor',
                'item_name' => 'Kampas Rem Depan (Brake Pad)',
                'interval_km' => 12000,
                'interval_months' => 12,
                'routine_cost' => 60000,
                'neglect_cost' => 380000,
                'neglect_impact_desc' => 'Besi kampas mengikis piringan cakram hingga tipis & rem berisiko blong mendadak.',
            ],
            [
                'vehicle_type' => 'motor',
                'item_name' => 'V-Belt & Roller Kit CVT',
                'interval_km' => 24000,
                'interval_months' => 24,
                'routine_cost' => 145000,
                'neglect_cost' => 650000,
                'neglect_impact_desc' => 'Karet retak berisiko putus di tengah kecepatan tinggi, mengunci transmisi roda dan membahayakan keselamatan.',
            ],
            [
                'vehicle_type' => 'motor',
                'item_name' => 'Filter Udara (Air Cleaner)',
                'interval_km' => 16000,
                'interval_months' => 16,
                'routine_cost' => 55000,
                'neglect_cost' => 350000,
                'neglect_impact_desc' => 'Filter udara tersumbat debu pekat membuat pasokan udara mesin tercekik, tarikan berat, dan konsumsi bensin boros.',
            ],
        ];

        $createdItems = [];
        foreach ($items as $item) {
            $createdItems[] = MaintenanceItem::firstOrCreate(
                ['item_name' => $item['item_name']],
                $item
            );
        }

        // 4. Kendaraan Demo Milik demo@servisa.com (Honda Vario 160)
        $vehicle = Vehicle::firstOrCreate(
            ['license_plate' => 'B 4120 KXZ'],
            [
                'user_id' => $demoUser->id,
                'vehicle_type' => 'motor',
                'brand' => 'Honda',
                'model_name' => 'Vario 160 ABS',
                'manufacture_year' => 2023,
                'initial_odometer_km' => 12000,
                'daily_average_km' => 25.4,
                'last_recalibrate_km' => 14750,
                'last_recalibrate_date' => now()->subDays(4)->toDateString(),
            ]
        );

        // 5. Riwayat Servis Kendaraan Demo (Paspor Servis Digital & Bukti Nota Terverifikasi)
        // Kunjungan 1: Servis Berkala Lengkap (Oli Mesin + Filter Udara) dengan Bukti Nota Resmi AHASS
        $receiptA = 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80';
        ServiceLog::updateOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => $createdItems[0]->id, // Oli Mesin
                'odometer_at_service' => 14700,
            ],
            [
                'service_date' => now()->subDays(6)->toDateString(),
                'actual_cost' => 65000,
                'workshop_name' => 'AHASS Astra Motor Purwokerto',
                'notes' => 'Ganti Oli Mesin SPX2 (Full Synthetic) & Reset Sensor MIL O2.',
                'receipt_image_url' => $receiptA,
            ]
        );

        ServiceLog::updateOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => $createdItems[5]->id, // Filter Udara
                'odometer_at_service' => 14700,
            ],
            [
                'service_date' => now()->subDays(6)->toDateString(),
                'actual_cost' => 55000,
                'workshop_name' => 'AHASS Astra Motor Purwokerto',
                'notes' => 'Ganti Filter Udara (Air Cleaner) Baru.',
                'receipt_image_url' => $receiptA,
            ]
        );

        // Kunjungan 2: Penggantian Kampas Rem & Busi
        $receiptB = 'https://images.unsplash.com/photo-1607344645866-009c320c5ab8?auto=format&fit=crop&w=800&q=80';
        ServiceLog::updateOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => $createdItems[3]->id, // Kampas Rem
                'odometer_at_service' => 12200,
            ],
            [
                'service_date' => now()->subMonths(2)->toDateString(),
                'actual_cost' => 60000,
                'workshop_name' => 'Bengkel Resmi Astra Honda',
                'notes' => 'Penggantian Kampas Rem Depan Original (Brake Pad).',
                'receipt_image_url' => $receiptB,
            ]
        );

        ServiceLog::updateOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => $createdItems[2]->id, // Busi
                'odometer_at_service' => 12200,
            ],
            [
                'service_date' => now()->subMonths(2)->toDateString(),
                'actual_cost' => 35000,
                'workshop_name' => 'Bengkel Resmi Astra Honda',
                'notes' => 'Ganti Busi NGK CPR9EA-9 Standar Pabrikan.',
                'receipt_image_url' => $receiptB,
            ]
        );

        // Kunjungan 3: Servis Perawatan Transmisi CVT (Oli Gardan & V-Belt)
        ServiceLog::updateOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => $createdItems[1]->id, // Oli Gardan
                'odometer_at_service' => 9500,
            ],
            [
                'service_date' => now()->subMonths(5)->toDateString(),
                'actual_cost' => 25000,
                'workshop_name' => 'Bengkel Mandiri Jaya Motor',
                'notes' => 'Penggantian Cairan Pelumas Oli Gardan Matic.',
                'receipt_image_url' => null,
            ]
        );

        // 6. Service Reminder Otomatis
        ServiceReminder::firstOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'maintenance_item_id' => $createdItems[0]->id,
            ],
            [
                'scheduled_date' => now()->addDays(12)->toDateString(),
                'custom_km' => 15000,
                'is_snoozed' => false,
                'status' => 'pending',
                'channel' => 'whatsapp',
                'last_notified_at' => now()->subDay(),
            ]
        );

        // 7. Riwayat Diagnosa AI Scan Terdahulu
        AiScanHistory::firstOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'detected_indicator' => 'MIL Check Engine (12 Kedipan)',
            ],
            [
                'image_url' => 'presets/demo_check_engine.jpg',
                'severity_level' => 'CRITICAL',
                'emergency_action' => "1. Kurangi kecepatan bertahap dan tepikan motor.\n2. Matikan kontak motor (posisi OFF).\n3. Jangan paksa putaran gas tinggi jika mesin tersendat.\n4. Kunjungi AHASS terdekat untuk reset ECM / periksa soket injektor.",
                'estimated_cost_min' => 45000,
                'estimated_cost_max' => 180000,
                'is_resolved' => true,
            ]
        );
    }
}
