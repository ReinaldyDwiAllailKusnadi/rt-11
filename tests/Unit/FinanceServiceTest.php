<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Resident;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\FinanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $financeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->financeService = new FinanceService();
    }

    public function test_get_saldo_awal_defaults_to_zero()
    {
        $this->assertEquals(0, $this->financeService->getSaldoAwalKas());
        $this->assertEquals(0, $this->financeService->getSaldoAwalKeamanan());
    }

    public function test_get_saldo_awal_with_settings()
    {
        Setting::create(['key' => 'saldo_awal_kas', 'value' => 500000]);
        Setting::create(['key' => 'saldo_awal_keamanan', 'value' => 1000000]);

        $this->assertEquals(500000, $this->financeService->getSaldoAwalKas());
        $this->assertEquals(1000000, $this->financeService->getSaldoAwalKeamanan());
    }

    public function test_get_saldo_kas_and_keamanan_calculations()
    {
        // Setup initial balances
        Setting::create(['key' => 'saldo_awal_kas', 'value' => 1000000]);
        Setting::create(['key' => 'saldo_awal_keamanan', 'value' => 2000000]);

        // Add resident
        $resident = Resident::create(['name' => 'Budi', 'no_rumah' => 'A.1']);

        // Add income: kas & keamanan
        Payment::create(['resident_id' => $resident->id, 'type' => 'kas', 'amount' => 20000, 'date' => '2026-01-01']);
        Payment::create(['resident_id' => $resident->id, 'type' => 'keamanan', 'amount' => 55000, 'date' => '2026-01-01']);

        // Add expense: kas (sakit) & keamanan (bayarSATPAM)
        Payment::create(['type' => 'sakit', 'amount' => 100000, 'date' => '2026-01-02']);
        Payment::create(['type' => 'bayarSATPAM', 'amount' => 500000, 'date' => '2026-01-02']);

        // Assert totals
        $this->assertEquals(20000, $this->financeService->getPemasukanKasTotal());
        $this->assertEquals(55000, $this->financeService->getPemasukanKeamananTotal());
        $this->assertEquals(100000, $this->financeService->getPengeluaranKasTotal());
        $this->assertEquals(500000, $this->financeService->getPengeluaranKeamananTotal());

        // Assert balances
        // Saldo Kas = 1.000.000 + 20.000 - 100.000 = 920.000
        $this->assertEquals(920000, $this->financeService->getSaldoKas());
        // Saldo Keamanan = 2.000.000 + 55.000 - 500.000 = 1.555.000
        $this->assertEquals(1555000, $this->financeService->getSaldoKeamanan());
        // Saldo Bersih = 920.000 + 1.555.000 = 2.475.000
        $this->assertEquals(2475000, $this->financeService->getSaldoBersih());
    }

    public function test_cek_saldo_cukup()
    {
        Setting::create(['key' => 'saldo_awal_kas', 'value' => 50000]);
        Setting::create(['key' => 'saldo_awal_keamanan', 'value' => 100000]);

        $this->assertTrue($this->financeService->cekSaldoCukup('sakit', 40000));
        $this->assertFalse($this->financeService->cekSaldoCukup('sakit', 60000));

        $this->assertTrue($this->financeService->cekSaldoCukup('bayarSATPAM', 90000));
        $this->assertFalse($this->financeService->cekSaldoCukup('bayarSATPAM', 110000));
    }
}
