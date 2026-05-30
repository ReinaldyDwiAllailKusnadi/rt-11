<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resident;
use App\Services\FinanceService;

class PublicController extends Controller
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function dashboard(Request $request)
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

        // Fetch paginated residents for public payment info
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        
        $query = Resident::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('no_rumah', 'like', "%{$search}%");
            });
        }
        
        // Sorting no_rumah properly (alphanumeric sort like J.1, J.2, J.10)
        $residents = $query->get()->sortBy(function($resident) {
            // Sort by street prefix then house number numerically
            $parts = explode('.', $resident->no_rumah);
            $prefix = $parts[0] ?? '';
            $num = isset($parts[1]) ? (int)$parts[1] : 0;
            return [$prefix, $num];
        });

        // Paginate manually since we sorted in memory or keep simple pagination
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $col = collect($residents);
        $slice = $col->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedResidents = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice, 
            $col->count(), 
            $perPage, 
            $currentPage, 
            ['path' => route('public.dashboard'), 'query' => $request->query()]
        );

        $paginatedResidents->each(function($resident) {
            $status = $this->finance->getResidentPaymentStatus($resident->id);
            $resident->total_kas = $status['total_kas'];
            $resident->total_keamanan = $status['total_keamanan'];
            $resident->bulan_kas_list = $status['bulan_kas_list'];
            $resident->bulan_keamanan_list = $status['bulan_keamanan_list'];
            $resident->status_pembayaran = $status['status'];
            $resident->tunggakan = $status['tunggakan'];
            $resident->show_warning = $status['show_warning'];
        });

        $showInfoModal = $request->has('show_info') || $request->has('page') || $request->has('search');

        return view('public.dashboard', compact(
            'summary',
            'paginatedResidents', 'showInfoModal', 'search'
        ));
    }
}
