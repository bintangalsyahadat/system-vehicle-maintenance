<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceItem;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalculatorController extends Controller
{
    /**
     * Tampilkan halaman Kalkulator Kerugian Servis (Cost of Neglect & SDG 12)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $vehicles = $user ? $user->vehicles : collect();

        $activeVehicleId = session('active_vehicle_id');
        $activeVehicle = $vehicles->firstWhere('id', $activeVehicleId) ?: $vehicles->first();

        // Ambil 5 suku cadang dari database
        $dbItems = MaintenanceItem::where('vehicle_type', $activeVehicle->vehicle_type ?? 'motor')->get();
        if ($dbItems->isEmpty()) {
            $dbItems = MaintenanceItem::all();
        }

        // Mapping data kalkulator dan analisis lingkungan SDG 12
        $calculatorItems = $dbItems->map(function ($item) {
            // Deskripsi dan indikator lingkungan khusus per komponen
            $envData = match ($item->id) {
                1 => [
                    'short_name' => 'Oli Mesin',
                    'routine_cost' => 55000,
                    'neglect_cost' => 1200000,
                    'routine_desc' => 'Perawatan rutin membantu menjaga performa mesin dan suhu tetap stabil.',
                    'neglect_desc' => 'Kerusakan komponen dapat menimbulkan biaya perbaikan besar hingga turun mesin.',
                    'environmental_desc' => 'Oli bekas yang terlambat diganti mempercepat keausan piston dan dinding silinder, menghasilkan limbah logam serta oli terkontaminasi yang berbahaya bagi tanah dan air.',
                    'metal_waste' => 'Meningkat',
                    'hazardous_substance' => 'Berisiko',
                ],
                2 => [
                    'short_name' => 'Oli Gardan',
                    'routine_cost' => 25000,
                    'neglect_cost' => 450000,
                    'routine_desc' => 'Pelumasan roda gigi transmisi menjaga gesekan rasio tetap halus dan senyap.',
                    'neglect_desc' => 'Gigi rasio aus, bunyi mendengung kasar, dan memicu penggantian satu set girboks.',
                    'environmental_desc' => 'Oli transmisi yang hangus menghasilkan residu gram besi pekat yang sulit diurai serta meningkatkan polusi kebisingan mekanik di jalan.',
                    'metal_waste' => 'Signifikan',
                    'hazardous_substance' => 'Beracun',
                ],
                3 => [
                    'short_name' => 'Busi',
                    'routine_cost' => 35000,
                    'neglect_cost' => 250000,
                    'routine_desc' => 'Pengapian optimal menjamin efisiensi pembakaran bensin secara sempurna.',
                    'neglect_desc' => 'Mesin tersendat, motor mogok mendadak, dan merusak koil serta sensor injeksi.',
                    'environmental_desc' => 'Busi aus menyebabkan pembakaran tidak tuntas, memboroskan bahan bakar fosil dan melepaskan emisi gas buang kotor hidrokarbon (HC) berbahaya ke udara.',
                    'metal_waste' => 'Meningkat',
                    'hazardous_substance' => 'Tinggi',
                ],
                4 => [
                    'short_name' => 'Kampas Rem',
                    'routine_cost' => 60000,
                    'neglect_cost' => 380000,
                    'routine_desc' => 'Pengereman pakem menjaga keselamatan pengendara dan melindungi piringan cakram.',
                    'neglect_desc' => 'Besi kampas mengikis piringan cakram hingga tipis dan rem berisiko blong mendadak.',
                    'environmental_desc' => 'Gesekan besi kampas rem yang habis mengikis piringan menghasilkan partikulat serbuk besi mikroskopis yang mencemari udara pernapasan kota.',
                    'metal_waste' => 'Kritis',
                    'hazardous_substance' => 'Berisiko',
                ],
                5 => [
                    'short_name' => 'V-Belt & Roller',
                    'routine_cost' => 145000,
                    'neglect_cost' => 650000,
                    'routine_desc' => 'Transmisi tenaga mesin ke roda belakang responsif dan bebas slip akselerasi.',
                    'neglect_desc' => 'V-Belt putus di jalan mengunci roda belakang, merusak rumah roller, dan pulley CVT.',
                    'environmental_desc' => 'Karet belt yang hangus dan putus paksa menghasilkan limbah mikroplastik polimer karet sintetis dan serat nilon yang tidak dapat terurai secara alami.',
                    'metal_waste' => 'Tinggi',
                    'hazardous_substance' => 'Berbahaya',
                ],
                default => [
                    'short_name' => $item->item_name,
                    'routine_cost' => (int) ($item->routine_cost ?: 50000),
                    'neglect_cost' => (int) ($item->neglect_cost ?: 500000),
                    'routine_desc' => 'Servis berkala mencegah kerusakan fatal dan menjaga efisiensi motor.',
                    'neglect_desc' => 'Kelalaian servis menyebabkan kerusakan komponen sekunder bernilai tinggi.',
                    'environmental_desc' => $item->neglect_impact_desc ?: 'Kerusakan komponen memicu limbah logam dan emisi berlebih.',
                    'metal_waste' => 'Meningkat',
                    'hazardous_substance' => 'Berisiko',
                ]
            };

            return [
                'id' => $item->id,
                'name' => $item->item_name,
                'short_name' => $envData['short_name'],
                'routine_cost' => $envData['routine_cost'],
                'neglect_cost' => $envData['neglect_cost'],
                'routine_desc' => $envData['routine_desc'],
                'neglect_desc' => $envData['neglect_desc'],
                'environmental_desc' => $envData['environmental_desc'],
                'metal_waste' => $envData['metal_waste'],
                'hazardous_substance' => $envData['hazardous_substance'],
            ];
        });

        return view('pages.calculator', compact('calculatorItems', 'activeVehicle'));
    }
}
