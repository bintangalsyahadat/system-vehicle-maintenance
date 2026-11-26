<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paspor Servis Digital - {{ $vehicle->model_name }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 20px; font-size: 12px; }
            .no-print { display: none !important; }
            @page { margin: 15mm; size: A4; }
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
            line-height: 1.5;
            background-color: #f8fafc;
            padding: 30px;
        }
        .container {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .logo-title {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .tagline {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .badge {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: bold;
        }
        .vehicle-specs {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .spec-item .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .spec-item .value {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f1f5f9;
            text-align: left;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            color: #475569;
            font-weight: bold;
            border-bottom: 2px solid #cbd5e1;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            vertical-align: top;
        }
        .text-right { text-align: right; }
        .tag-verified {
            display: inline-block;
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 3px;
        }
        .footer-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #e2e8f0;
            padding-top: 20px;
        }
        .footer-note {
            font-size: 11px;
            color: #64748b;
            max-width: 480px;
        }
        .print-btn {
            background-color: #0f172a;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 820px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 13px; color: #64748b;">Pratinjau Cetak Buku Paspor Servis Resmi</span>
        <button class="print-btn" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <div class="logo-title">SERVISA</div>
                <div class="tagline">Smart Vehicle Maintenance Passport & Verified History</div>
            </div>
            <div>
                <span class="badge">✓ Terverifikasi Digital</span>
            </div>
        </div>

        <!-- Spesifikasi Kendaraan -->
        <div class="vehicle-specs">
            <div class="spec-item">
                <div class="label">Kendaraan</div>
                <div class="value">{{ $vehicle->model_name }}</div>
            </div>
            <div class="spec-item">
                <div class="label">Merk & Tahun</div>
                <div class="value">{{ $vehicle->brand }} ({{ $vehicle->manufacture_year ?: '-' }})</div>
            </div>
            <div class="spec-item">
                <div class="label">Plat Nomor</div>
                <div class="value">{{ $vehicle->license_plate ?: 'Tanpa Plat' }}</div>
            </div>
            <div class="spec-item">
                <div class="label">Odometer Terkini</div>
                <div class="value">{{ number_format($vehicle->current_odometer, 0, ',', '.') }} KM</div>
            </div>
        </div>

        <!-- Tabel Catatan Servis -->
        <h3 style="font-size: 14px; font-weight: 800; margin-bottom: 10px; color: #0f172a;">Catatan Rekam Jejak Servis Bengkel & Verifikasi Nota</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Tanggal</th>
                    <th>Komponen & Item Servis</th>
                    <th>Bengkel Pelaksana</th>
                    <th>Odometer</th>
                    <th>Status Nota</th>
                    <th class="text-right">Biaya (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groupedLogs as $index => $log)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->service_date)->translatedFormat('d/m/Y') }}</td>
                        <td>
                            @if(!empty($log->part_names) && count($log->part_names) > 0)
                                <strong>{{ implode(', ', $log->part_names->toArray()) }}</strong>
                            @else
                                <strong>Servis Berkala / Pemeriksaan Umum</strong>
                            @endif
                            @if($log->notes)
                                <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $log->notes }}</div>
                            @endif
                        </td>
                        <td>{{ $log->workshop_name ?: 'Bengkel Mandiri' }}</td>
                        <td style="font-family: monospace;">{{ number_format($log->odometer_at_service, 0, ',', '.') }} KM</td>
                        <td>
                            @if(!empty($log->is_verified))
                                <span class="tag-verified">📸 Nota Asli</span>
                            @else
                                <span style="font-size: 10px; color: #94a3b8;">Input Manual</span>
                            @endif
                        </td>
                        <td class="text-right" style="font-weight: bold;">
                            {{ number_format($log->actual_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; padding: 25px;">Belum ada catatan servis tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer Ringkasan -->
        <div class="footer-summary">
            <div class="footer-note">
                Dokumen ini dicetak secara sah dari sistem <strong>SERVISA</strong> sebagai bukti rekam jejak pemeliharaan preventif kendaraan bermotor (SDGs 12 & 3).
            </div>
            <div style="text-align: right;">
                <div style="font-size: 11px; color: #64748b; text-transform: uppercase;">Total Akumulasi Biaya</div>
                <div style="font-size: 18px; font-weight: 900; color: #0f172a; margin-top: 2px;">
                    Rp {{ number_format($totalCost, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

</body>
</html>
