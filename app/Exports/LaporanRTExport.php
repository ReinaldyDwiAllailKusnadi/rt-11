<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Resident;
use App\Models\Payment;
use App\Services\FinanceService;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// HELPER TRAIT FOR STYLING
trait ExportStylingHelper
{
    protected function styleTitle(Worksheet $sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '13294B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);
    }

    protected function styleSubtitle(Worksheet $sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '64748B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
    }

    protected function styleSectionHeader(Worksheet $sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '13294B'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'indent' => 1,
            ],
        ]);
        $row = explode(':', $range)[0];
        $rowNum = preg_replace('/[^0-9]/', '', $row);
        if ($rowNum) {
            $sheet->getRowDimension($rowNum)->setRowHeight(24);
        }
    }

    protected function styleTableHeader(Worksheet $sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '13294B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $row = explode(':', $range)[0];
        $rowNum = preg_replace('/[^0-9]/', '', $row);
        if ($rowNum) {
            $sheet->getRowDimension($rowNum)->setRowHeight(28);
        }
    }

    protected function applyBorders(Worksheet $sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);
    }

    protected function applyCurrencyFormat(Worksheet $sheet, string $range)
    {
        $sheet->getStyle($range)->getNumberFormat()->setFormatCode('"Rp" #,##0;[Red]-"Rp" #,##0');
    }

    protected function applyZebraRows(Worksheet $sheet, int $startRow, int $endRow, string $endCol)
    {
        for ($row = $startRow; $row <= $endRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$endCol}{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ],
                ]);
            }
        }
    }
}

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
class RingkasanSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    use ExportStylingHelper;

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
            'A' => 35,
            'B' => 18,
            'C' => 18,
            'D' => 18,
        ];
    }

    public function array(): array
    {
        $totalKas = (int)$this->finance->totalByType('kas');
        $totalKeamanan = (int)$this->finance->totalByType('keamanan');
        $totalKemalangan = (int)$this->finance->totalByType('kemalangan');
        $totalSakit = (int)$this->finance->totalByType('sakit');
        $totalBayarSATPAM = (int)$this->finance->totalByType('bayarSATPAM');
        $totalKonsumsi = (int)$this->finance->totalByType('konsumsiRAPAT');
        $totalLain = (int)$this->finance->totalByType('lainLAIN');

        $totalPemasukan = $totalKas + $totalKeamanan;
        $totalPengeluaran = $totalKemalangan + $totalSakit + $totalBayarSATPAM + $totalKonsumsi + $totalLain;

        return [
            ['LAPORAN KEUANGAN RT.011', '', '', ''],
            ['RW.003 Karanggintung - Perum Duta Graha Golden Wisata', '', '', ''],
            ['Tanggal Cetak: ' . now()->format('d/m/Y H:i'), '', '', ''],
            ['', '', '', ''],
            ['RINGKASAN KEUANGAN', '', '', ''],
            ['A. SALDO AWAL', '', '', ''],
            ['Saldo Awal Kas RT', (int)$this->finance->getSaldoAwalKas(), '', ''],
            ['Saldo Awal Keamanan', (int)$this->finance->getSaldoAwalKeamanan(), '', ''],
            ['', '', '', ''],
            ['B. PEMASUKAN', '', '', ''],
            ['Kas RT (Rp20.000/bulan)', $totalKas, '', ''],
            ['Keamanan (Rp55.000/bulan)', $totalKeamanan, '', ''],
            ['TOTAL PEMASUKAN', $totalPemasukan, '', ''],
            ['', '', '', ''],
            ['C. PENGELUARAN', '', '', ''],
            ['Kemalangan', -$totalKemalangan, '', ''],
            ['Sakit', -$totalSakit, '', ''],
            ['Bayar Satpam', -$totalBayarSATPAM, '', ''],
            ['Konsumsi Rapat', -$totalKonsumsi, '', ''],
            ['Lain-lain', -$totalLain, '', ''],
            ['TOTAL PENGELUARAN', -$totalPengeluaran, '', ''],
            ['', '', '', ''],
            ['D. SALDO AKHIR', '', '', ''],
            ['Saldo Kas RT', (int)$this->finance->getSaldoKas(), '', ''],
            ['Saldo Keamanan', (int)$this->finance->getSaldoKeamanan(), '', ''],
            ['Saldo Bersih', (int)$this->finance->getSaldoBersih(), '', ''],
            ['', '', '', ''],
            ['STATISTIK', '', '', ''],
            ['Total Warga', (int)Resident::count(), '', ''],
            ['Total Transaksi', (int)Payment::count(), '', '']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge Title & Headers
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');
                $sheet->mergeCells('A5:D5');
                $sheet->mergeCells('A6:D6');
                $sheet->mergeCells('A10:D10');
                $sheet->mergeCells('A15:D15');
                $sheet->mergeCells('A23:D23');
                $sheet->mergeCells('A28:D28');

                // Apply Title Styles
                $this->styleTitle($sheet, 'A1:D1');
                $this->styleSubtitle($sheet, 'A2:D2');
                
                // Style date row
                $sheet->getStyle('A3:D3')->applyFromArray([
                    'font' => ['size' => 9, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Section Headers
                $this->styleSectionHeader($sheet, 'A5:D5');
                $this->styleSectionHeader($sheet, 'A6:D6');
                $this->styleSectionHeader($sheet, 'A10:D10');
                $this->styleSectionHeader($sheet, 'A15:D15');
                $this->styleSectionHeader($sheet, 'A23:D23');
                $this->styleSectionHeader($sheet, 'A28:D28');

                // Currency Formats
                $this->applyCurrencyFormat($sheet, 'B7:B8');
                $this->applyCurrencyFormat($sheet, 'B11:B13');
                $this->applyCurrencyFormat($sheet, 'B16:B21');
                $this->applyCurrencyFormat($sheet, 'B24:B26');

                // Bold elements
                $sheet->getStyle('A13:B13')->getFont()->setBold(true);
                $sheet->getStyle('A21:B21')->getFont()->setBold(true);
                
                // Saldo Akhir / Saldo Bersih color styling
                $sheet->getStyle('A26:D26')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '20B2AA'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);
                $sheet->getRowDimension(26)->setRowHeight(24);

                // Borders
                $this->applyBorders($sheet, 'A7:B8');
                $this->applyBorders($sheet, 'A11:B13');
                $this->applyBorders($sheet, 'A16:B21');
                $this->applyBorders($sheet, 'A24:B26');
                $this->applyBorders($sheet, 'A29:B30');

                // Alignments
                $sheet->getStyle('B7:B30')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Page setup
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getHeaderFooter()->setOddFooter('&C &"Arial,Regular" RT.011 Karanggintung');
            }
        ];
    }
}

// 2. REKAP BULANAN WARGA SHEET
class RekapBulananWargaSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    use ExportStylingHelper;

    public function title(): string
    {
        return 'Rekap_Bulanan_Warga';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 25,
            'D' => 12,
            'E' => 16,
            'F' => 18,
            'G' => 30,
        ];
    }

    public function array(): array
    {
        $payments = Payment::with('resident')->whereIn('type', ['kas', 'keamanan'])->get();
        $rekapData = [];
        $no = 1;

        $rekapMap = [];
        foreach ($payments as $p) {
            if (!$p->resident) continue;
            
            $bulanList = $p->bulan_list;
            if (!$bulanList) {
                $bulanList = [substr($p->date instanceof \Carbon\Carbon ? $p->date->format('Y-m-d') : (string)$p->date, 0, 7)];
            }

            foreach ($bulanList as $bln) {
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
                (int)$item['total'],
                $item['keterangan']
            ];
        }

        return array_merge([
            ['LAPORAN REKAP BULANAN IURAN WARGA RT.011', '', '', '', '', '', ''],
            ['RW.003 Karanggintung - Perum Duta Graha Golden Wisata', '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            ['No', 'Bulan', 'Nama Warga', 'No Rumah', 'Jenis Iuran', 'Total Bayar', 'Keterangan']
        ], $rekapData);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Merge titles
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');

                // Title Styles
                $this->styleTitle($sheet, 'A1:G1');
                $this->styleSubtitle($sheet, 'A2:G2');

                // Header Styles
                $this->styleTableHeader($sheet, 'A4:G4');

                // Freeze Pane at Row 5
                $sheet->freezePane('A5');

                // Auto Filter
                $sheet->setAutoFilter("A4:G{$highestRow}");

                // Borders
                $this->applyBorders($sheet, "A4:G{$highestRow}");

                // Currency formatting
                $this->applyCurrencyFormat($sheet, "F5:F{$highestRow}");

                // Zebra Rows
                $this->applyZebraRows($sheet, 5, $highestRow, 'G');

                // Alignments & Wrap text
                $sheet->getStyle("A5:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B5:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D5:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E5:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Wrap text for Name and Keterangan
                $sheet->getStyle("C5:C{$highestRow}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("G5:G{$highestRow}")->getAlignment()->setWrapText(true);

                // Page setup
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getHeaderFooter()->setOddFooter('&C &"Arial,Regular" RT.011 Karanggintung');
            }
        ];
    }
}

// 3. STATUS WARGA SHEET
class StatusWargaSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    use ExportStylingHelper;

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
            'A' => 6,
            'B' => 25,
            'C' => 12,
            'D' => 16,
            'E' => 14,
            'F' => 16,
            'G' => 18,
            'H' => 18,
        ];
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
                (int)$status['total_kas'],
                $kasStatus,
                (int)$status['total_keamanan'],
                $keamStatus,
                (int)($status['total_kas'] + $status['total_keamanan'])
            ];
        }

        return array_merge([
            ['STATUS IURAN & KEUANGAN WARGA RT.011', '', '', '', '', '', '', ''],
            ['RW.003 Karanggintung - Perum Duta Graha Golden Wisata', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['No', 'Nama Warga', 'No Rumah', 'Kas RT', 'Status Kas', 'Keamanan', 'Status Keamanan', 'Total Bayar']
        ], $statusData);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Merge titles
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');

                // Title Styles
                $this->styleTitle($sheet, 'A1:H1');
                $this->styleSubtitle($sheet, 'A2:H2');

                // Header Styles
                $this->styleTableHeader($sheet, 'A4:H4');

                // Freeze Pane
                $sheet->freezePane('A5');

                // Auto Filter
                $sheet->setAutoFilter("A4:H{$highestRow}");

                // Borders
                $this->applyBorders($sheet, "A4:H{$highestRow}");

                // Currency Formats
                $this->applyCurrencyFormat($sheet, "D5:D{$highestRow}");
                $this->applyCurrencyFormat($sheet, "F5:F{$highestRow}");
                $this->applyCurrencyFormat($sheet, "H5:H{$highestRow}");

                // Zebra Rows
                $this->applyZebraRows($sheet, 5, $highestRow, 'H');

                // Alignments
                $sheet->getStyle("A5:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C5:C{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E5:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G5:G{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->getStyle("B5:B{$highestRow}")->getAlignment()->setWrapText(true);

                // Highlight Status cells
                for ($row = 5; $row <= $highestRow; $row++) {
                    foreach (['E', 'G'] as $col) {
                        $val = $sheet->getCell("{$col}{$row}")->getValue();
                        if ($val === 'Lunas') {
                            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                    'color' => ['rgb' => '155724'],
                                ],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'D4EDDA'],
                                ]
                            ]);
                        } elseif ($val === 'Sebagian') {
                            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                    'color' => ['rgb' => '856404'],
                                ],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FFF3CD'],
                                ]
                            ]);
                        } elseif ($val === 'Belum') {
                            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                    'color' => ['rgb' => '721C24'],
                                ],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F8D7DA'],
                                ]
                            ]);
                        }
                    }
                }

                // Page Setup
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getHeaderFooter()->setOddFooter('&C &"Arial,Regular" RT.011 Karanggintung');
            }
        ];
    }
}

// 4. DAFTAR TRANSAKSI SHEET
class DaftarTransaksiSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    use ExportStylingHelper;

    public function title(): string
    {
        return 'Daftar_Transaksi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 18,
            'D' => 25,
            'E' => 12,
            'F' => 18,
            'G' => 30,
        ];
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

            $isExpenditure = in_array($p->type, ['kemalangan', 'sakit', 'bayarSATPAM', 'konsumsiRAPAT', 'lainLAIN']);
            $valAmount = (int)$p->amount;
            if ($isExpenditure) {
                $valAmount = -$valAmount;
            }

            $dateStr = $p->date instanceof \Carbon\Carbon ? $p->date->format('Y-m-d') : (string)$p->date;

            $transaksiData[] = [
                $no++,
                $dateStr,
                $label,
                $nama,
                $noRumah,
                $valAmount,
                $p->keterangan ?? '',
                $p->type
            ];
        }

        return array_merge([
            ['DAFTAR TRANSAKSI KEUANGAN RT.011', '', '', '', '', '', '', ''],
            ['RW.003 Karanggintung - Perum Duta Graha Golden Wisata', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['No', 'Tanggal', 'Jenis Transaksi', 'Nama Warga/SATPAM', 'No Rumah', 'Nominal', 'Keterangan', 'OriginalType']
        ], $transaksiData);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Merge titles
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');

                // Title Styles
                $this->styleTitle($sheet, 'A1:G1');
                $this->styleSubtitle($sheet, 'A2:G2');

                // Header Styles
                $this->styleTableHeader($sheet, 'A4:G4');

                // Freeze Pane
                $sheet->freezePane('A5');

                // Auto Filter
                $sheet->setAutoFilter("A4:G{$highestRow}");

                // Borders
                $this->applyBorders($sheet, "A4:G{$highestRow}");

                // Format Tanggal
                $sheet->getStyle("B5:B{$highestRow}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');

                // Currency formatting
                $this->applyCurrencyFormat($sheet, "F5:F{$highestRow}");

                // Zebra Rows
                $this->applyZebraRows($sheet, 5, $highestRow, 'G');

                // Alignments
                $sheet->getStyle("A5:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B5:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C5:C{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E5:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->getStyle("D5:D{$highestRow}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("G5:G{$highestRow}")->getAlignment()->setWrapText(true);

                // Style transaction amounts
                for ($row = 5; $row <= $highestRow; $row++) {
                    $type = $sheet->getCell("H{$row}")->getValue();
                    $isExpenditure = in_array($type, ['kemalangan', 'sakit', 'bayarSATPAM', 'konsumsiRAPAT', 'lainLAIN']);
                    
                    if ($isExpenditure) {
                        $sheet->getStyle("F{$row}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => 'C62828'], // Red
                            ]
                        ]);
                    } else {
                        $sheet->getStyle("F{$row}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => '2E7D32'], // Green
                            ]
                        ]);
                    }
                }

                // Hide OriginalType Column
                $sheet->getColumnDimension('H')->setVisible(false);

                // Page Setup
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getHeaderFooter()->setOddFooter('&C &"Arial,Regular" RT.011 Karanggintung');
            }
        ];
    }
}
