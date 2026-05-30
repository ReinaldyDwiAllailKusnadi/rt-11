<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Models\Resident;
use App\Models\Payment;
use App\Services\FinanceService;

class LaporanRTExport implements WithMultipleSheets
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function sheets(): array
    {
        return [
            new RingkasanSheet($this->finance),
            new RekapBulananWargaSheet(),
            new StatusWargaSheet($this->finance),
            new DaftarTransaksiSheet(),
        ];
    }
}

// 1. RINGKASAN SHEET
class RingkasanSheet implements FromArray, WithTitle, WithColumnWidths
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 20,
        ];
    }

    public function array(): array
    {
        $totalKas = $this->finance->totalByType('kas');
        $totalKeamanan = $this->finance->totalByType('keamanan');
        $totalKemalangan = $this->finance->totalByType('kemalangan');
        $totalSakit = $this->finance->totalByType('sakit');
        $totalBayarSATPAM = $this->finance->totalByType('bayarSATPAM');
        $totalKonsumsi = $this->finance->totalByType('konsumsiRAPAT');
        $totalLain = $this->finance->totalByType('lainLAIN');

        $totalPemasukan = $totalKas + $totalKeamanan;
        $totalPengeluaran = $totalKemalangan + $totalSakit + $totalBayarSATPAM + $totalKonsumsi + $totalLain;

        return [
            ['LAPORAN KEUANGAN RT.011', ''],
            ['Tanggal Cetak: ' . now()->format('d/m/Y'), ''],
            ['', ''],
            ['RINGKASAN KEUANGAN', ''],
            ['A. SALDO AWAL', ''],
            ['Saldo Awal Kas RT', 'Rp ' . number_format($this->finance->getSaldoAwalKas(), 0, ',', '.')],
            ['Saldo Awal Keamanan', 'Rp ' . number_format($this->finance->getSaldoAwalKeamanan(), 0, ',', '.')],
            ['', ''],
            ['B. PEMASUKAN', ''],
            ['Kas RT (Rp20.000/bulan)', 'Rp ' . number_format($totalKas, 0, ',', '.')],
            ['Keamanan (Rp55.000/bulan)', 'Rp ' . number_format($totalKeamanan, 0, ',', '.')],
            ['TOTAL PEMASUKAN', 'Rp ' . number_format($totalPemasukan, 0, ',', '.')],
            ['', ''],
            ['C. PENGELUARAN', ''],
            ['Kemalangan', 'Rp ' . number_format($totalKemalangan, 0, ',', '.')],
            ['Sakit', 'Rp ' . number_format($totalSakit, 0, ',', '.')],
            ['Bayar Satpam', 'Rp ' . number_format($totalBayarSATPAM, 0, ',', '.')],
            ['Konsumsi Rapat', 'Rp ' . number_format($totalKonsumsi, 0, ',', '.')],
            ['Lain-lain', 'Rp ' . number_format($totalLain, 0, ',', '.')],
            ['TOTAL PENGELUARAN', 'Rp ' . number_format($totalPengeluaran, 0, ',', '.')],
            ['', ''],
            ['D. SALDO AKHIR', ''],
            ['Saldo Kas RT', 'Rp ' . number_format($this->finance->getSaldoKas(), 0, ',', '.')],
            ['Saldo Keamanan', 'Rp ' . number_format($this->finance->getSaldoKeamanan(), 0, ',', '.')],
            ['Saldo Bersih', 'Rp ' . number_format($this->finance->getSaldoBersih(), 0, ',', '.')],
            ['', ''],
            ['STATISTIK', ''],
            ['Total Warga', Resident::count()],
            ['Total Transaksi', Payment::count()]
        ];
    }
}

// 2. REKAP BULANAN WARGA SHEET
class RekapBulananWargaSheet implements FromArray, WithTitle, WithHeadings, WithColumnWidths
{
    public function title(): string
    {
        return 'Rekap_Bulanan_Warga';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 12,
            'C' => 25,
            'D' => 12,
            'E' => 18,
            'F' => 15,
            'G' => 30,
        ];
    }

    public function headings(): array
    {
        return ['No', 'Bulan', 'Nama Warga', 'No Rumah', 'Jenis Iuran', 'Total Bayar (Rp)', 'Keterangan'];
    }

    public function array(): array
    {
        $payments = Payment::with('resident')->whereIn('type', ['kas', 'keamanan'])->get();
        $rekapData = [];
        $no = 1;

        // Grouping & sorting logic similar to original JS
        $rekapMap = [];
        foreach ($payments as $p) {
            if (!$p->resident) continue;
            
            $bulanList = $p->bulan_list;
            if (!$bulanList) {
                $bulanList = [substr($p->date instanceof \Carbon\Carbon ? $p->date->format('Y-m-d') : (string)$p->date, 0, 7)];
            }

            foreach ($bulanList as $bln) {
                // Determine monthly portion
                $monthlyPortion = $p->amount / count($bulanList);
                $key = $p->resident_id . '|' . $bln . '|' . $p->type;

                if (!isset($rekapMap[$key])) {
                    $rekapMap[$key] = [
                        'namaWarga' => $p->resident->name,
                        'noRumah' => $p->resident->no_rumah,
                        'bulan' => $bln,
                        'jenis' => $p->type === 'kas' ? 'Kas RT' : 'Keamanan',
                        'total' => 0,
                        'keterangan' => $p->keterangan ?? ''
                    ];
                }
                $rekapMap[$key]['total'] += $monthlyPortion;
            }
        }

        // Sort by house number then month
        usort($rekapMap, function($a, $b) {
            $partsA = explode('.', $a['noRumah']);
            $partsB = explode('.', $b['noRumah']);
            $prefA = $partsA[0] ?? '';
            $prefB = $partsB[0] ?? '';
            
            $prefComp = strcmp($prefA, $prefB);
            if ($prefComp !== 0) return $prefComp;
            
            $numA = isset($partsA[1]) ? (int)$partsA[1] : 0;
            $numB = isset($partsB[1]) ? (int)$partsB[1] : 0;
            if ($numA !== $numB) return $numA <=> $numB;

            return strcmp($a['bulan'], $b['bulan']);
        });

        foreach ($rekapMap as $item) {
            $rekapData[] = [
                $no++,
                $item['bulan'],
                $item['namaWarga'],
                $item['noRumah'],
                $item['jenis'],
                'Rp ' . number_format($item['total'], 0, ',', '.'),
                $item['keterangan']
            ];
        }

        return $rekapData;
    }
}

// 3. STATUS WARGA SHEET
class StatusWargaSheet implements FromArray, WithTitle, WithHeadings, WithColumnWidths
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function title(): string
    {
        return 'Status_Warga';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 25,
            'C' => 12,
            'D' => 15,
            'E' => 12,
            'F' => 15,
            'G' => 14,
            'H' => 15,
        ];
    }

    public function headings(): array
    {
        return ['No', 'Nama Warga', 'No Rumah', 'Kas RT (Rp)', 'Status Kas', 'Keamanan (Rp)', 'Status Keamanan', 'Total Bayar (Rp)'];
    }

    public function array(): array
    {
        $residents = Resident::all()->sortBy(function($resident) {
            $parts = explode('.', $resident->no_rumah);
            $prefix = $parts[0] ?? '';
            $num = isset($parts[1]) ? (int)$parts[1] : 0;
            return [$prefix, $num];
        });

        $statusData = [];
        $no = 1;
        $jumlahBulan = $this->finance->getJumlahBulanBerjalan();
        $targetKas = FinanceService::TARGET_KAS_PER_BULAN * $jumlahBulan;
        $targetKeam = FinanceService::TARGET_KEAMANAN_PER_BULAN * $jumlahBulan;

        foreach ($residents as $warga) {
            $status = $this->finance->getResidentPaymentStatus($warga->id);
            $kasStatus = $status['total_kas'] >= $targetKas ? 'Lunas' : ($status['total_kas'] > 0 ? 'Sebagian' : 'Belum');
            $keamStatus = $status['total_keamanan'] >= $targetKeam ? 'Lunas' : ($status['total_keamanan'] > 0 ? 'Sebagian' : 'Belum');

            $statusData[] = [
                $no++,
                $warga->name,
                $warga->no_rumah,
                'Rp ' . number_format($status['total_kas'], 0, ',', '.'),
                $kasStatus,
                'Rp ' . number_format($status['total_keamanan'], 0, ',', '.'),
                $keamStatus,
                'Rp ' . number_format($status['total_kas'] + $status['total_keamanan'], 0, ',', '.')
            ];
        }

        return $statusData;
    }
}

// 4. DAFTAR TRANSAKSI SHEET
class DaftarTransaksiSheet implements FromArray, WithTitle, WithHeadings, WithColumnWidths
{
    public function title(): string
    {
        return 'Daftar_Transaksi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 12,
            'C' => 18,
            'D' => 25,
            'E' => 12,
            'F' => 15,
            'G' => 30,
        ];
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Jenis Transaksi', 'Nama Warga/SATPAM', 'No Rumah', 'Nominal (Rp)', 'Keterangan'];
    }

    public function array(): array
    {
        $payments = Payment::with('resident')->latest('date')->get();
        $transaksiData = [];
        $no = 1;

        $jenisLabels = [
            'kas' => 'Kas RT',
            'keamanan' => 'Keamanan',
            'kemalangan' => 'Kemalangan',
            'sakit' => 'Sakit',
            'bayarSATPAM' => 'Bayar Satpam',
            'konsumsiRAPAT' => 'Konsumsi Rapat',
            'lainLAIN' => 'Lain-lain'
        ];

        foreach ($payments as $p) {
            $nama = '';
            $noRumah = '-';
            
            if ($p->resident) {
                $nama = $p->resident->name;
                $noRumah = $p->resident->no_rumah;
            } elseif ($p->nama_satpam) {
                $nama = $p->nama_satpam;
            } else {
                $nama = 'Umum';
            }

            $label = $jenisLabels[$p->type] ?? $p->type;

            $transaksiData[] = [
                $no++,
                $p->date instanceof \Carbon\Carbon ? $p->date->format('Y-m-d') : (string)$p->date,
                $label,
                $nama,
                $noRumah,
                'Rp ' . number_format($p->amount, 0, ',', '.'),
                $p->keterangan ?? ''
            ];
        }

        return $transaksiData;
    }
}
