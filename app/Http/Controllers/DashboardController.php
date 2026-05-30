<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FinanceService;
use App\Models\Resident;
use App\Models\Payment;

class DashboardController extends Controller
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function index()
    {
        $summary = [
            'saldo_awal_kas' => $this->finance->getSaldoAwalKas(),
            'saldo_awal_keamanan' => $this->finance->getSaldoAwalKeamanan(),
            'kas_rt' => $this->finance->getPemasukanKasTotal(),
            'keamanan' => $this->finance->getPemasukanKeamananTotal(),
            'kemalangan' => $this->finance->totalByType('kemalangan'),
            'sakit' => $this->finance->totalByType('sakit'),
            'bayar_satpam' => $this->finance->totalByType('bayarSATPAM'),
            'konsumsi_rapat' => $this->finance->totalByType('konsumsiRAPAT'),
            'lain_lain' => $this->finance->totalByType('lainLAIN'),
            'saldo_kas' => $this->finance->getSaldoKas(),
            'saldo_keamanan' => $this->finance->getSaldoKeamanan(),
            'saldo_bersih' => $this->finance->getSaldoBersih(),
        ];

        return view('admin.dashboard', compact('summary'));
    }
}
