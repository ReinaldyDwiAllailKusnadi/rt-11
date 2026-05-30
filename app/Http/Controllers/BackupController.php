<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resident;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    public function index()
    {
        return view('backup.index');
    }

    public function export()
    {
        $backupData = [
            'version' => '1.0',
            'exportDate' => now()->toIso8601String(),
            'data' => [
                'residents' => Resident::all()->toArray(),
                'payments' => Payment::all()->toArray(),
                'settings' => Setting::all()->toArray(),
            ]
        ];

        $json = json_encode($backupData, JSON_PRETTY_PRINT);
        $fileName = 'rt011_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $fileContent = file_get_contents($request->file('backup_file')->getRealPath());
        $backup = json_decode($fileContent, true);

        if (!$backup || !isset($backup['data']) || (!isset($backup['data']['residents']) && !isset($backup['data']['payments']))) {
            return back()->withErrors(['backup_file' => 'Format file backup JSON tidak valid.']);
        }

        $residentsData = $backup['data']['residents'] ?? [];
        $paymentsData = $backup['data']['payments'] ?? [];

        // 1. Deep validate residents format
        if (!is_array($residentsData)) {
            return back()->withErrors(['backup_file' => 'Data warga (residents) harus berupa list array.']);
        }
        foreach ($residentsData as $index => $res) {
            if (!is_array($res) || empty($res['name']) || (!isset($res['noRumah']) && !isset($res['no_rumah']))) {
                return back()->withErrors(['backup_file' => "Format data warga indeks ke-{$index} tidak lengkap (harus ada 'name' dan nomor rumah)."]);
            }
        }

        // 2. Deep validate payments format
        if (!is_array($paymentsData)) {
            return back()->withErrors(['backup_file' => 'Data transaksi (payments) harus berupa list array.']);
        }
        foreach ($paymentsData as $index => $pay) {
            if (!is_array($pay) || empty($pay['type']) || !isset($pay['amount']) || !is_numeric($pay['amount']) || empty($pay['date'])) {
                return back()->withErrors(['backup_file' => "Format data transaksi indeks ke-{$index} tidak lengkap (harus ada 'type', 'amount', dan 'date')."]);
            }
        }

        try {
            DB::transaction(function() use ($backup) {
                Schema::disableForeignKeyConstraints();
                Payment::truncate();
                Resident::truncate();
                Setting::truncate();
                Schema::enableForeignKeyConstraints();

                $residentsData = $backup['data']['residents'] ?? [];
                $paymentsData = $backup['data']['payments'] ?? [];
                $settingsData = $backup['data']['settings'] ?? [];
                $saldoAwal = $backup['saldoAwal'] ?? null; // from old HTML format backup

                $idMap = [];

                // 1. Restore residents
                foreach ($residentsData as $res) {
                    $oldId = $res['id'] ?? null;
                    
                    $newResident = Resident::create([
                        'name' => strtoupper($res['name']),
                        'no_rumah' => $res['noRumah'] ?? $res['no_rumah'] ?? '',
                    ]);

                    if ($oldId) {
                        $idMap[$oldId] = $newResident->id;
                    }
                }

                // 2. Restore settings
                if ($saldoAwal) {
                    Setting::updateOrCreate(['key' => 'saldo_awal_kas'], ['value' => $saldoAwal['kas'] ?? 0]);
                    Setting::updateOrCreate(['key' => 'saldo_awal_keamanan'], ['value' => $saldoAwal['keamanan'] ?? 0]);
                } else {
                    foreach ($settingsData as $set) {
                        Setting::updateOrCreate(['key' => $set['key']], ['value' => $set['value']]);
                    }
                }

                // 3. Restore payments
                foreach ($paymentsData as $pay) {
                    $oldWargaId = $pay['wargaId'] ?? $pay['resident_id'] ?? null;
                    $newResidentId = null;

                    if ($oldWargaId && isset($idMap[$oldWargaId])) {
                        $newResidentId = $idMap[$oldWargaId];
                    } elseif ($oldWargaId && is_numeric($oldWargaId)) {
                        $newResidentId = $oldWargaId;
                    }

                    Payment::create([
                        'resident_id' => $newResidentId,
                        'type' => $pay['type'],
                        'amount' => $pay['amount'],
                        'date' => substr($pay['date'], 0, 10),
                        'keterangan' => $pay['keterangan'] ?? null,
                        'nama_satpam' => $pay['namaSatpam'] ?? $pay['nama_satpam'] ?? null,
                        'bulan_list' => $pay['bulanList'] ?? $pay['bulan_list'] ?? null,
                    ]);
                }
            });

            return redirect()->route('admin.dashboard')->with('success', 'Backup berhasil di-restore. Semua data telah diperbarui.');

        } catch (\Exception $e) {
            return back()->withErrors(['backup_file' => 'Gagal merestore backup: ' . $e->getMessage()]);
        }
    }
}
