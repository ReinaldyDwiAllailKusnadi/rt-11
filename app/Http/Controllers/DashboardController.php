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
        $saldoKas = $this->finance->getSaldoKas();
        $saldoKeamanan = $this->finance->getSaldoKeamanan();
        $saldoBersih = $this->finance->getSaldoBersih();
        $saldoAwalKas = $this->finance->getSaldoAwalKas();
        $saldoAwalKeamanan = $this->finance->getSaldoAwalKeamanan();

        $totalKas = $this->finance->getPemasukanKasTotal();
        $totalKeamanan = $this->finance->getPemasukanKeamananTotal();
        $totalPengeluaranKas = $this->finance->getPengeluaranKasTotal();
        $totalPengeluaranKeamanan = $this->finance->getPengeluaranKeamananTotal();
        $totalPengeluaranGabungan = $totalPengeluaranKas + $totalPengeluaranKeamanan;

        return view('admin.dashboard', compact(
            'saldoKas', 'saldoKeamanan', 'saldoBersih', 'saldoAwalKas', 'saldoAwalKeamanan',
            'totalKas', 'totalKeamanan', 'totalPengeluaranKas', 'totalPengeluaranKeamanan', 'totalPengeluaranGabungan'
        ));
    }
}
