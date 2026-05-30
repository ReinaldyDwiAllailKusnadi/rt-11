<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resident;
use App\Services\FinanceService;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function show(Resident $resident)
    {
        $status = $this->finance->getResidentPaymentStatus($resident->id);
        $jumlahBulan = $this->finance->getJumlahBulanBerjalan();
        
        $targetKasTotal = FinanceService::TARGET_KAS_PER_BULAN * $jumlahBulan;
        $targetKeamTotal = FinanceService::TARGET_KEAMANAN_PER_BULAN * $jumlahBulan;

        $sisaKas = max(0, $targetKasTotal - $status['total_kas']);
        $sisaKeam = max(0, $targetKeamTotal - $status['total_keamanan']);
        
        $lunas = $status['status'] === 'LUNAS';
        $tglCetak = Carbon::now()->locale('id')->isoFormat('D MMMM YYYY');
        
        $ketuaRT = $this->finance->getNamaKetuaRT();
        $receiptNo = rand(1000, 9999) . '/RT.011/K/' . date('Y');

        return view('receipts.show', compact(
            'resident', 'status', 'jumlahBulan', 
            'targetKasTotal', 'targetKeamTotal', 'sisaKas', 'sisaKeam', 
            'lunas', 'tglCetak', 'ketuaRT', 'receiptNo'
        ));
    }
}
