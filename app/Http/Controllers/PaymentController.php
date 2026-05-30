<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Resident;
use App\Services\FinanceService;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function index(Request $request)
    {
        $type = $request->input('type', '');
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        $query = Payment::query()->with('resident');

        if ($type) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhere('nama_satpam', 'like', "%{$search}%")
                  ->orWhereHas('resident', function($qr) use ($search) {
                      $qr->where('name', 'like', "%{$search}%")
                        ->orWhere('no_rumah', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest('date')->paginate($perPage);

        return view('payments.index', compact('payments', 'type', 'search', 'perPage'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'kas');
        $residents = Resident::all()->sortBy(function($resident) {
            $parts = explode('.', $resident->no_rumah);
            $prefix = $parts[0] ?? '';
            $num = isset($parts[1]) ? (int)$parts[1] : 0;
            return [$prefix, $num];
        });

        // Default inputs
        $today = Carbon::now()->format('Y-m-d');
        $targetKas = FinanceService::TARGET_KAS_PER_BULAN;
        $targetKeamanan = FinanceService::TARGET_KEAMANAN_PER_BULAN;

        // Recent payments
        $recentPayments = Payment::with('resident')->latest()->limit(5)->get();

        return view('payments.create', compact('residents', 'type', 'today', 'targetKas', 'targetKeamanan', 'recentPayments'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        // Validation based on type
        if (in_array($type, ['kas', 'keamanan'])) {
            $request->validate([
                'resident_id' => 'required|exists:residents,id',
                'amount_per_month' => 'required|integer|min:1',
                'months' => 'required|array|min:1',
                'date' => 'required|date',
                'tahun' => 'required|integer',
                'keterangan' => 'nullable|string',
            ]);

            $residentId = $request->resident_id;
            $amountPerMonth = (int)$request->amount_per_month;
            $months = $request->months;
            $year = $request->tahun;
            $totalAmount = $amountPerMonth * count($months);

            // 1. Validate duplicates
            $duplicateMonths = [];
            $bulanNama = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            foreach ($months as $m) {
                $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                $searchBulan = "{$year}-{$monthStr}";

                // Check in payments database
                $exists = Payment::where('resident_id', $residentId)
                    ->where('type', $type)
                    ->where(function($q) use ($searchBulan) {
                        $q->whereJsonContains('bulan_list', $searchBulan)
                          ->orWhere('date', 'like', "{$searchBulan}%");
                    })->exists();

                if ($exists) {
                    $duplicateMonths[] = $bulanNama[$m - 1] . ' ' . $year;
                }
            }

            if (!empty($duplicateMonths) && !$request->has('force_save')) {
                $resident = Resident::find($residentId);
                $typeLabel = $type === 'kas' ? 'Kas RT' : 'Keamanan';
                $msg = "Warga {$resident->name} ({$resident->no_rumah}) sudah membayar iuran {$typeLabel} untuk bulan: " . implode(', ', $duplicateMonths) . ".";
                return back()->withInput()->with('duplicate_warning', $msg);
            }

            // Create payment
            $bulanList = [];
            foreach ($months as $m) {
                $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                $bulanList[] = "{$year}-{$monthStr}";
            }

            Payment::create([
                'resident_id' => $residentId,
                'type' => $type,
                'amount' => $totalAmount,
                'date' => $request->date,
                'keterangan' => $request->keterangan ?? ('Bayar iuran ' . ($type === 'kas' ? 'Kas RT' : 'Keamanan') . ' untuk bulan: ' . implode(', ', array_map(fn($m) => $bulanNama[$m-1] . ' ' . $year, $months)) . ' (' . count($months) . ' bulan)'),
                'bulan_list' => $bulanList
            ]);

            return redirect()->route('payments.create', ['type' => $type])->with('success', 'Transaksi berhasil disimpan sebesar Rp ' . number_format($totalAmount, 0, ',', '.'));

        } else {
            // Expenses or other categories
            $request->validate([
                'type' => 'required|in:kemalangan,sakit,bayarSATPAM,konsumsiRAPAT,lainLAIN',
                'resident_id' => 'nullable|exists:residents,id',
                'nama_satpam' => 'required_if:type,bayarSATPAM|nullable|string',
                'amount' => 'required|integer|min:1',
                'date' => 'required|date',
                'keterangan' => 'nullable|string',
            ]);

            $amount = (int)$request->amount;

            // 2. Validate expense limits
            if (!$this->finance->cekSaldoCukup($type, $amount)) {
                $source = $type === 'bayarSATPAM' ? 'Dana Keamanan' : 'Dana Kas RT';
                return back()->withInput()->withErrors(['amount' => "Saldo tidak mencukupi. Pengeluaran {$type} melebihi saldo {$source}."]);
            }

            Payment::create([
                'resident_id' => $request->resident_id,
                'type' => $type,
                'amount' => $amount,
                'date' => $request->date,
                'nama_satpam' => $request->type === 'bayarSATPAM' ? $request->nama_satpam : null,
                'keterangan' => $request->keterangan
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Transaksi pengeluaran berhasil disimpan.');
        }
    }

    public function edit(Payment $payment)
    {
        $residents = Resident::all()->sortBy(function($resident) {
            $parts = explode('.', $resident->no_rumah);
            $prefix = $parts[0] ?? '';
            $num = isset($parts[1]) ? (int)$parts[1] : 0;
            return [$prefix, $num];
        });
        return view('payments.edit', compact('payment', 'residents'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'date' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $newAmount = (int)$request->amount;
        $diff = $newAmount - $payment->amount;

        // If it's an expense, validate if editing exceeds balance limits
        if (in_array($payment->type, ['kemalangan', 'sakit', 'bayarSATPAM', 'konsumsiRAPAT', 'lainLAIN'])) {
            if ($diff > 0 && !$this->finance->cekSaldoCukup($payment->type, $diff)) {
                return back()->withErrors(['amount' => 'Saldo tidak mencukupi untuk kenaikan jumlah pengeluaran ini.']);
            }
        } else {
            // For income types (kas, keamanan), reduction in income cannot exceed current balance
            if ($diff < 0) {
                $currentBalance = $payment->type === 'kas' ? $this->finance->getSaldoKas() : $this->finance->getSaldoKeamanan();
                if (abs($diff) > $currentBalance) {
                    return back()->withErrors(['amount' => 'Saldo tidak mencukupi untuk melakukan pengurangan nominal pemasukan ini.']);
                }
            }
        }

        $payment->update([
            'amount' => $newAmount,
            'date' => $request->date,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('payments.index', ['type' => $payment->type])->with('success', 'Transaksi berhasil diupdate.');
    }

    public function destroy(Payment $payment)
    {
        $type = $payment->type;

        // Verify if deleting income type leads to negative balance
        if (in_array($type, ['kas', 'keamanan'])) {
            $currentBalance = $type === 'kas' ? $this->finance->getSaldoKas() : $this->finance->getSaldoKeamanan();
            if ($payment->amount > $currentBalance) {
                return back()->with('error', 'Transaksi tidak bisa dihapus karena saldo akan menjadi minus.');
            }
        }

        $payment->delete();
        return redirect()->route('payments.index', ['type' => $type])->with('success', 'Transaksi berhasil dihapus.');
    }

    public function statusBayar()
    {
        $jumlahBulan = $this->finance->getJumlahBulanBerjalan();
        $targetKasTotal = FinanceService::TARGET_KAS_PER_BULAN * $jumlahBulan;
        $targetKeamTotal = FinanceService::TARGET_KEAMANAN_PER_BULAN * $jumlahBulan;

        $residents = Resident::all()->sortBy(function($resident) {
            $parts = explode('.', $resident->no_rumah);
            $prefix = $parts[0] ?? '';
            $num = isset($parts[1]) ? (int)$parts[1] : 0;
            return [$prefix, $num];
        });

        $residents->each(function($resident) {
            $status = $this->finance->getResidentPaymentStatus($resident->id);
            $resident->total_kas = $status['total_kas'];
            $resident->total_keamanan = $status['total_keamanan'];
            $resident->status_pembayaran = $status['status'];
            $resident->tunggakan = $status['tunggakan'];
            $resident->show_warning = $status['show_warning'];
        });

        return view('payments.status_bayar', compact('residents', 'jumlahBulan', 'targetKasTotal', 'targetKeamTotal'));
    }

    public function riwayatWarga(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $residents = Resident::all()->sortBy(function($resident) {
            $parts = explode('.', $resident->no_rumah);
            $prefix = $parts[0] ?? '';
            $num = isset($parts[1]) ? (int)$parts[1] : 0;
            return [$prefix, $num];
        });

        $residents->each(function($resident) {
            $resident->total_kas = Payment::where('resident_id', $resident->id)->where('type', 'kas')->sum('amount');
            $resident->total_keamanan = Payment::where('resident_id', $resident->id)->where('type', 'keamanan')->sum('amount');
        });

        // Paginate manually
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $col = collect($residents);
        
        if ($perPage === 'all') {
            $paginatedResidents = new \Illuminate\Pagination\LengthAwarePaginator(
                $col->values(),
                $col->count(),
                $col->count() ?: 1,
                1,
                ['path' => route('payments.riwayat')]
            );
        } else {
            $slice = $col->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $paginatedResidents = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice,
                $col->count(),
                $perPage,
                $currentPage,
                ['path' => route('payments.riwayat'), 'query' => $request->query()]
            );
        }

        return view('payments.riwayat', compact('paginatedResidents', 'perPage'));
    }
}
