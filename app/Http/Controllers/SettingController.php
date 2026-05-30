<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function showSaldoAwal()
    {
        $saldoAwalKas = Setting::find('saldo_awal_kas')?->value ?? 0;
        $saldoAwalKeamanan = Setting::find('saldo_awal_keamanan')?->value ?? 0;

        return view('settings.saldo_awal', compact('saldoAwalKas', 'saldoAwalKeamanan'));
    }

    public function storeSaldoAwal(Request $request)
    {
        $request->validate([
            'saldo_awal_kas' => 'required|integer|min:0',
            'saldo_awal_keamanan' => 'required|integer|min:0',
        ]);

        Setting::updateOrCreate(['key' => 'saldo_awal_kas'], ['value' => $request->saldo_awal_kas]);
        Setting::updateOrCreate(['key' => 'saldo_awal_keamanan'], ['value' => $request->saldo_awal_keamanan]);

        return redirect()->route('admin.dashboard')->with('success', 'Saldo awal berhasil dikonfigurasi.');
    }
}
