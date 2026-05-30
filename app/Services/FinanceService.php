<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Payment;
use App\Models\Resident;
use Carbon\Carbon;

class FinanceService
{
    public const TARGET_KAS_PER_BULAN = 20000;
    public const TARGET_KEAMANAN_PER_BULAN = 55000;
    public const START_YEAR = 2026;
    public const START_MONTH = 1; // January

    public function getSaldoAwalKas(): int
    {
        $setting = Setting::find('saldo_awal_kas');
        return $setting ? (int)$setting->value : 0;
    }

    public function getSaldoAwalKeamanan(): int
    {
        $setting = Setting::find('saldo_awal_keamanan');
        return $setting ? (int)$setting->value : 0;
    }

    public function getNamaKetuaRT(): string
    {
        $setting = Setting::find('nama_ketua_rt');
        return $setting ? $setting->value : 'MUHAMMAD NASIKUN';
    }

    public function totalByType(string $type): int
    {
        return Payment::where('type', $type)->sum('amount');
    }

    public function getPemasukanKasTotal(): int
    {
        return $this->totalByType('kas');
    }

    public function getPemasukanKeamananTotal(): int
    {
        return $this->totalByType('keamanan');
    }

    public function getPengeluaranKasTotal(): int
    {
        return $this->totalByType('sakit') +
               $this->totalByType('kemalangan') +
               $this->totalByType('konsumsiRAPAT') +
               $this->totalByType('lainLAIN');
    }

    public function getPengeluaranKeamananTotal(): int
    {
        return $this->totalByType('bayarSATPAM');
    }

    public function getSaldoKas(): int
    {
        return $this->getSaldoAwalKas() + $this->getPemasukanKasTotal() - $this->getPengeluaranKasTotal();
    }

    public function getSaldoKeamanan(): int
    {
        return $this->getSaldoAwalKeamanan() + $this->getPemasukanKeamananTotal() - $this->getPengeluaranKeamananTotal();
    }

    public function getSaldoBersih(): int
    {
        return $this->getSaldoKas() + $this->getSaldoKeamanan();
    }

    public function cekSaldoCukup(string $type, int $amount): bool
    {
        if ($type === 'bayarSATPAM') {
            return $amount <= $this->getSaldoKeamanan();
        }
        return $amount <= $this->getSaldoKas();
    }

    public function getJumlahBulanBerjalan(): int
    {
        $now = Carbon::now();
        $totalBulan = ($now->year - self::START_YEAR) * 12 + ($now->month - self::START_MONTH) + 1;
        return $totalBulan < 1 ? 1 : $totalBulan;
    }

    public function getBulanDibayarList2026(int $residentId, string $type): string
    {
        $payments = Payment::where('resident_id', $residentId)
            ->where('type', $type)
            ->get();

        $bulanSet = [];
        foreach ($payments as $p) {
            if ($p->bulan_list && is_array($p->bulan_list)) {
                foreach ($p->bulan_list as $bln) {
                    $parts = explode('-', $bln);
                    if (count($parts) === 2 && $parts[0] == self::START_YEAR) {
                        $bulanSet[] = (int)$parts[1];
                    }
                }
            } else {
                $dateStr = $p->date instanceof Carbon ? $p->date->format('Y-m-d') : (string)$p->date;
                $parts = explode('-', substr($dateStr, 0, 7));
                if (count($parts) === 2 && $parts[0] == self::START_YEAR) {
                    $bulanSet[] = (int)$parts[1];
                }
            }
        }

        $bulanSet = array_unique($bulanSet);
        sort($bulanSet);

        $bulanPendek = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $result = [];
        foreach ($bulanSet as $b) {
            if ($b >= 1 && $b <= 12) {
                $result[] = $bulanPendek[$b - 1] . ' ' . self::START_YEAR;
            }
        }
        return implode(', ', $result);
    }

    public function getTunggakanBulanCount(int $residentId): int
    {
        $jumlahBulan = $this->getJumlahBulanBerjalan();
        $payments = Payment::where('resident_id', $residentId)
            ->whereIn('type', ['kas', 'keamanan'])
            ->get();

        $bulanKas = [];
        $bulanKeam = [];

        foreach ($payments as $p) {
            $bulanList = $p->bulan_list;
            if (!$bulanList) {
                $dateStr = $p->date instanceof Carbon ? $p->date->format('Y-m-d') : (string)$p->date;
                $bulanList = [substr($dateStr, 0, 7)];
            }

            foreach ($bulanList as $bln) {
                $parts = explode('-', $bln);
                if (count($parts) === 2 && $parts[0] == self::START_YEAR) {
                    $monthVal = (int)$parts[1];
                    if ($p->type === 'kas') {
                        $bulanKas[] = $monthVal;
                    } elseif ($p->type === 'keamanan') {
                        $bulanKeam[] = $monthVal;
                    }
                }
            }
        }

        $bulanKas = array_unique($bulanKas);
        $bulanKeam = array_unique($bulanKeam);

        $belumLunas = 0;
        for ($i = 1; $i <= $jumlahBulan; $i++) {
            if (!in_array($i, $bulanKas) || !in_array($i, $bulanKeam)) {
                $belumLunas++;
            }
        }
        return $belumLunas;
    }

    public function cekLunasBulanBerjalan(int $residentId): bool
    {
        return $this->getTunggakanBulanCount($residentId) === 0;
    }

    public function getResidentPaymentStatus(int $residentId): array
    {
        $totalKas = Payment::where('resident_id', $residentId)->where('type', 'kas')->sum('amount');
        $totalKeam = Payment::where('resident_id', $residentId)->where('type', 'keamanan')->sum('amount');
        $jumlahBulan = $this->getJumlahBulanBerjalan();
        $tunggakan = $this->getTunggakanBulanCount($residentId);
        $lunas = $tunggakan === 0;

        return [
            'total_kas' => $totalKas,
            'total_keamanan' => $totalKeam,
            'bulan_kas_list' => $this->getBulanDibayarList2026($residentId, 'kas'),
            'bulan_keamanan_list' => $this->getBulanDibayarList2026($residentId, 'keamanan'),
            'status' => $lunas ? 'LUNAS' : 'BELUM LUNAS',
            'tunggakan' => $tunggakan,
            'show_warning' => $tunggakan > 3
        ];
    }
}
