<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resident;
use App\Services\FinanceService;
use Carbon\Carbon;

class LetterController extends Controller
{
    protected $finance;

    public function __construct(FinanceService $finance)
    {
        $this->finance = $finance;
    }

    public function index()
    {
        $residents = Resident::all()->sortBy(function($resident) {
            $parts = explode('.', $resident->no_rumah);
            $prefix = $parts[0] ?? '';
            $num = isset($parts[1]) ? (int)$parts[1] : 0;
            return [$prefix, $num];
        });
        $today = Carbon::now()->format('Y-m-d');
        return view('letters.index', compact('residents', 'today'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'type' => 'required|in:domisili,usaha,keterangan',
            'nomor' => 'required|string',
            'date' => 'required|date',
            'keterangan_tambahan' => 'nullable|string',
        ]);

        $resident = Resident::find($request->resident_id);
        $type = $request->type;
        $nomor = $request->nomor;
        $date = Carbon::parse($request->date);
        $tglForm = $date->locale('id')->isoFormat('D MMMM YYYY');
        $tambah = $request->keterangan_tambahan;

        $typeLabel = '';
        if ($type === 'domisili') {
            $typeLabel = 'KETERANGAN DOMISILI';
        } elseif ($type === 'usaha') {
            $typeLabel = 'KETERANGAN USAHA';
        } else {
            $typeLabel = 'KETERANGAN';
        }

        $ketuaRT = $this->finance->getNamaKetuaRT();

        // Build letter text
        $isi = "SURAT KETERANGAN {$typeLabel}\n";
        $isi .= "Nomor: {$nomor}\n\n";
        $isi .= "Yang bertanda tangan di bawah ini, Ketua RT.011 / RW.003 Karanggintung, menerangkan dengan sebenarnya bahwa:\n\n";
        $isi .= "Nama              : " . strtoupper($resident->name) . "\n";
        $isi .= "Alamat/No. Rumah  : " . $resident->no_rumah . "\n\n";
        
        if ($tambah) {
            $isi .= "Keterangan Tambahan:\n{$tambah}\n\n";
        }
        
        $isi .= "Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.\n\n\n";
        $isi .= "                                                 Purwokerto, {$tglForm}\n";
        $isi .= "                                                 Ketua RT.011\n\n\n\n";
        $isi .= "                                                 ( {$ketuaRT} )";

        return view('letters.preview', compact('isi', 'type', 'nomor'));
    }

    public function exportWord(Request $request)
    {
        $text = $request->input('text');
        $fileName = 'Surat_RT011_' . now()->format('Y-m-d') . '.doc';

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Surat RT.011</title>
            <style>
                body { font-family: "Times New Roman", serif; margin: 40px; line-height: 1.6; }
                pre { white-space: pre-wrap; font-family: "Times New Roman", serif; font-size: 12pt; }
            </style>
        </head>
        <body>
            <pre>' . e($text) . '</pre>
        </body>
        </html>';

        return response($html, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
