<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $resident->name }}</title>
    <meta name="robots" content="noindex, nofollow">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6fa;
            font-family: 'Inter', sans-serif;
            padding: 20px;
        }

        .receipt-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            border: 2px solid #e0e0e0;
            position: relative;
        }

        .receipt-header {
            border-bottom: 2px dashed #ccc;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .receipt-title {
            color: #1b3a53;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            width: 180px;
        }

        .receipt-body .row {
            margin-bottom: 10px;
        }

        .stamp-lunas {
            position: absolute;
            top: 40px;
            right: 40px;
            border: 3px double #20B2AA;
            color: #20B2AA;
            font-size: 1.5rem;
            font-weight: 800;
            padding: 8px 15px;
            border-radius: 8px;
            transform: rotate(-10deg);
            opacity: 0.85;
            text-transform: uppercase;
        }

        .stamp-tunggak {
            position: absolute;
            top: 40px;
            right: 40px;
            border: 3px double #dc3545;
            color: #dc3545;
            font-size: 1.3rem;
            font-weight: 800;
            padding: 8px 15px;
            border-radius: 8px;
            transform: rotate(-10deg);
            opacity: 0.85;
            text-transform: uppercase;
        }

        .control-panel {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            body {
                padding: 10px !important;
            }
            .receipt-container {
                padding: 20px !important;
            }
            .stamp-lunas, .stamp-tunggak {
                position: static !important;
                display: inline-block !important;
                margin-top: 10px !important;
                transform: none !important;
                font-size: 1rem !important;
            }
            .receipt-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 15px !important;
            }
            .receipt-header .text-end {
                text-align: left !important;
            }
            .info-label {
                width: 120px !important;
                min-width: 120px !important;
            }
            .control-panel {
                flex-direction: column !important;
                gap: 10px !important;
                align-items: flex-start !important;
            }
            .control-panel div {
                width: 100% !important;
                justify-content: space-between !important;
            }
        }

        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
            }
            .control-panel {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                padding: 30px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <!-- CONTROL PANEL FOR ACTIONS -->
    <div class="control-panel d-flex justify-content-between align-items-center no-print">
        <span class="fw-bold text-muted small"><i class="bi bi-printer me-1"></i>CETAK KWITANSI RT.011</span>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-printer me-1"></i> Cetak Kwitansi
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary btn-sm px-3">Tutup</button>
        </div>
    </div>

    <!-- MAIN RECEIPT CONTAINER -->
    <div class="receipt-container">
        
        <!-- Status Stamp -->
        @if($lunas)
            <div class="stamp-lunas">Lunas 2026</div>
        @else
            <div class="stamp-tunggak">Menunggak {{ $status['tunggakan'] }} Bln</div>
        @endif

        <!-- Receipt Header -->
        <div class="receipt-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="receipt-title mb-1"><i class="bi bi-shield-check text-success me-2"></i>KUITANSI PEMBAYARAN</h4>
                <p class="text-muted small mb-0">No: {{ $receiptNo }}</p>
            </div>
            <div class="text-end">
                <h6 class="fw-bold mb-0">RT.011 / RW.003</h6>
                <p class="small text-muted mb-0">Perumahan Karanggintung</p>
            </div>
        </div>

        <!-- Receipt Body Info -->
        <div class="receipt-body">
            <div class="d-flex mb-3">
                <div class="info-label">Telah Diterima Dari</div>
                <div class="flex-grow-1 border-bottom pb-1 fw-bold text-uppercase">: {{ $resident->name }}</div>
            </div>
            
            <div class="d-flex mb-3">
                <div class="info-label">No. Rumah / Alamat</div>
                <div class="flex-grow-1 border-bottom pb-1">: {{ $resident->no_rumah }} / Perumahan Karanggintung</div>
            </div>

            <!-- Setoran Iuran Details -->
            <div class="table-responsive my-4">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis Iuran</th>
                            <th>Bulan yang Dibayar (Tahun 2026)</th>
                            <th class="text-end" style="width: 180px;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Kas RT</td>
                            <td class="small">{{ $status['bulan_kas_list'] ?: '-' }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($status['total_kas'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Keamanan</td>
                            <td class="small">{{ $status['bulan_keamanan_list'] ?: '-' }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($status['total_keamanan'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="2" class="text-end fw-bold">TOTAL DITERIMA</td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($status['total_kas'] + $status['total_keamanan'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Arrears / Sisa Tagihan Info -->
            <div class="p-3 bg-light rounded-3 mb-4">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1"></i>Catatan Tunggakan s/d Bulan Ini ({{ $jumlahBulan }} bln)</h6>
                <div class="row small g-2">
                    <div class="col-6">Sisa Tagihan Kas RT : Rp {{ number_format($sisaKas, 0, ',', '.') }}</div>
                    <div class="col-6">Sisa Tagihan Keamanan : Rp {{ number_format($sisaKeam, 0, ',', '.') }}</div>
                    <div class="col-12 border-top pt-1 mt-1 fw-bold text-danger">
                        Total Tunggakan: Rp {{ number_format($sisaKas + $sisaKeam, 0, ',', '.') }} ({{ $status['tunggakan'] }} Bulan)
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="row mt-5">
                <div class="col-6 text-center">
                    <p class="small mb-5">Penerima/Pembayar,</p>
                    <p class="fw-bold mb-0 border-bottom d-inline-block px-3" style="min-width: 150px;">{{ $resident->name }}</p>
                </div>
                <div class="col-6 text-center">
                    <p class="small mb-0">Purwokerto, {{ $tglCetak }}</p>
                    <p class="small mb-5">Ketua RT.011,</p>
                    <p class="fw-bold mb-0 border-bottom d-inline-block px-3" style="min-width: 150px;">{{ $ketuaRT }}</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
