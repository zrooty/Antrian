<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCounterValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_petugas_requires_counter_id()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test Petugas',
                'email' => 'test@petugas.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'petugas',
                'counter_id' => '', // Empty
            ]);

        $response->assertSessionHasErrors('counter_id');
    }

    public function test_petugas_with_valid_counter_is_saved()
    {
        $counter = Counter::create(['name' => 'Loket A', 'code' => 'A', 'status' => 'active']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test Petugas',
                'email' => 'test@petugas.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'petugas',
                'counter_id' => $counter->id,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@petugas.com',
            'counter_id' => $counter->id,
        ]);
    }

    public function test_non_petugas_role_sets_counter_id_to_null()
    {
        $counter = Counter::create(['name' => 'Loket A', 'code' => 'A', 'status' => 'active']);

        // Even if counter_id is provided, if role is admin, it should be null
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test Admin',
                'email' => 'other@admin.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'admin',
                'counter_id' => $counter->id,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'other@admin.com',
            'counter_id' => null,
        ]);
    }
}
