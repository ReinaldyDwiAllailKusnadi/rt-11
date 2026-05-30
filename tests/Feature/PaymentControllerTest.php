<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Resident;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $resident;

    protected function setUp(): void
    {
        parent::setUp();
        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin RT.011',
            'username' => 'admin',
            'password' => bcrypt('@rt011'),
        ]);

        // Create resident
        $this->resident = Resident::create([
            'name' => 'John Doe',
            'no_rumah' => 'A.10',
        ]);

        // Setup default initial balances
        Setting::create(['key' => 'saldo_awal_kas', 'value' => 1000000]);
        Setting::create(['key' => 'saldo_awal_keamanan', 'value' => 2000000]);
    }

    public function test_guests_cannot_access_payment_routes()
    {
        $this->get(route('payments.index'))->assertRedirect(route('login'));
        $this->get(route('payments.create'))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_payment_routes()
    {
        $this->actingAs($this->admin)
            ->get(route('payments.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_store_payment_successfully()
    {
        $this->actingAs($this->admin)
            ->post(route('payments.store'), [
                'type' => 'kas',
                'resident_id' => $this->resident->id,
                'amount_per_month' => 20000,
                'months' => [1, 2], // Jan & Feb
                'date' => '2026-01-01',
                'tahun' => 2026,
            ])
            ->assertRedirect(route('payments.create', ['type' => 'kas']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'resident_id' => $this->resident->id,
            'type' => 'kas',
            'amount' => 40000,
        ]);
    }

    public function test_store_validates_duplicate_payment()
    {
        // First payment already paid for Jan 2026
        Payment::create([
            'resident_id' => $this->resident->id,
            'type' => 'kas',
            'amount' => 20000,
            'date' => '2026-01-01',
            'bulan_list' => ['2026-01'],
        ]);

        // Attempting to pay again for Jan 2026 should trigger duplicate warning
        $this->actingAs($this->admin)
            ->from(route('payments.create', ['type' => 'kas']))
            ->post(route('payments.store'), [
                'type' => 'kas',
                'resident_id' => $this->resident->id,
                'amount_per_month' => 20000,
                'months' => [1],
                'date' => '2026-01-02',
                'tahun' => 2026,
            ])
            ->assertRedirect(route('payments.create', ['type' => 'kas']))
            ->assertSessionHas('duplicate_warning');
    }

    public function test_store_expense_validates_balance_limit()
    {
        // Attempt to spend more than kas balance (kas balance is 1,000,000)
        $this->actingAs($this->admin)
            ->from(route('payments.create', ['type' => 'sakit']))
            ->post(route('payments.store'), [
                'type' => 'sakit',
                'amount' => 1500000, // greater than 1,000,000
                'date' => '2026-01-02',
                'keterangan' => 'Sakit warga',
            ])
            ->assertRedirect(route('payments.create', ['type' => 'sakit']))
            ->assertSessionHasErrors(['amount']);
    }

    public function test_prevent_editing_income_to_cause_negative_balance()
    {
        // Add expense of 900,000 (Kas RT has 1,000,000, so remaining is 100,000)
        Payment::create(['type' => 'sakit', 'amount' => 900000, 'date' => '2026-01-02']);

        // Create an income payment of 100,000
        $payment = Payment::create([
            'resident_id' => $this->resident->id,
            'type' => 'kas',
            'amount' => 100000,
            'date' => '2026-01-01',
        ]);

        // Attempt to reduce that income payment to 10,000 (reduction is 90,000).
        // Remaining kas balance is 100,000 (without reduction), reduction is 90,000 which leaves 10,000 (positive).
        $this->actingAs($this->admin)
            ->put(route('payments.update', $payment->id), [
                'amount' => 10000, // Reduction of 90,000
                'date' => '2026-01-01',
            ])
            ->assertRedirect(route('payments.index', ['type' => 'kas']));

        // Add another expense of 105,000 (Kas balance is 1,000,000 + 10,000 - 900,000 = 110,000).
        // Remaining balance is 5,000.
        Payment::create(['type' => 'sakit', 'amount' => 105000, 'date' => '2026-01-03']);

        // Attempt to reduce payment from 10,000 to 4,000 (reduction of 6,000).
        // This reduction is greater than current balance of 5,000, so it should be blocked.
        $this->actingAs($this->admin)
            ->from(route('payments.edit', $payment->id))
            ->put(route('payments.update', $payment->id), [
                'amount' => 4000, // Reduction of 6,000
                'date' => '2026-01-01',
            ])
            ->assertRedirect(route('payments.edit', $payment->id))
            ->assertSessionHasErrors(['amount']);
    }

    public function test_prevent_deleting_income_to_cause_negative_balance()
    {
        // Kas balance is 1,000,000.
        // Add expense of 950,000. Remaining balance is 50,000.
        Payment::create(['type' => 'sakit', 'amount' => 950000, 'date' => '2026-01-02']);

        // Create an income payment of 100,000. Kas balance is now 1,100,000, remaining is 150,000.
        $payment = Payment::create([
            'resident_id' => $this->resident->id,
            'type' => 'kas',
            'amount' => 100000,
            'date' => '2026-01-01',
        ]);

        // Add another expense of 100,000. Kas balance is 1,100,000 - 1,050,000 = 50,000.
        Payment::create(['type' => 'sakit', 'amount' => 100000, 'date' => '2026-01-03']);

        // Attempt to delete the 100,000 income payment.
        // Deleting this would make Kas balance become -50,000. Should be blocked.
        $this->actingAs($this->admin)
            ->from(route('payments.index', ['type' => 'kas']))
            ->delete(route('payments.destroy', $payment->id))
            ->assertRedirect(route('payments.index', ['type' => 'kas']))
            ->assertSessionHas('error');
    }
}
