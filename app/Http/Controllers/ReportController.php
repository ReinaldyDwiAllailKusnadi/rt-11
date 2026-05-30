<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LaporanRTExport;
use App\Services\FinanceService;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function index()
    {
        return view('reports.index');
    }

    public function exportExcel()
    {
        $fileName = 'Laporan_RT011_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new LaporanRTExport($this->finance), $fileName);
    }
}
