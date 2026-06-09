<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResidentControllerTest extends TestCase
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
            'name' => 'JOHN DOE',
            'no_rumah' => 'A. 10',
        ]);
    }

    public function test_guests_cannot_access_resident_routes()
    {
        $this->get(route('residents.index'))->assertRedirect(route('login'));
        $this->post(route('residents.store'), [])->assertRedirect(route('login'));
        $this->put(route('residents.update', $this->resident->id), [])->assertRedirect(route('login'));
        $this->delete(route('residents.destroy', $this->resident->id))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_resident_index()
    {
        $this->actingAs($this->admin)
            ->get(route('residents.index'))
            ->assertStatus(200)
            ->assertSee('JOHN DOE')
            ->assertSee('A. 10');
    }

    public function test_admin_can_store_resident()
    {
        $this->actingAs($this->admin)
            ->post(route('residents.store'), [
                'name' => 'Jane Smith',
                'no_rumah' => 'B. 12',
            ])
            ->assertRedirect(route('residents.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('residents', [
            'name' => 'JANE SMITH',
            'no_rumah' => 'B. 12',
        ]);
    }

    public function test_admin_can_update_resident()
    {
        $this->actingAs($this->admin)
            ->put(route('residents.update', $this->resident->id), [
                'name' => 'John Doe Updated',
                'no_rumah' => 'A. 15',
            ])
            ->assertRedirect(route('residents.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('residents', [
            'id' => $this->resident->id,
            'name' => 'JOHN DOE UPDATED',
            'no_rumah' => 'A. 15',
        ]);
    }

    public function test_admin_cannot_update_resident_with_existing_name()
    {
        $otherResident = Resident::create([
            'name' => 'ANOTHER RESIDENT',
            'no_rumah' => 'C. 20',
        ]);

        $this->actingAs($this->admin)
            ->from(route('residents.index'))
            ->put(route('residents.update', $this->resident->id), [
                'name' => 'Another Resident',
                'no_rumah' => 'A. 10',
            ])
            ->assertRedirect(route('residents.index'))
            ->assertSessionHasErrors(['name']);
    }

    public function test_admin_can_delete_resident()
    {
        $this->actingAs($this->admin)
            ->delete(route('residents.destroy', $this->resident->id))
            ->assertRedirect(route('residents.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('residents', [
            'id' => $this->resident->id,
        ]);
    }
}
