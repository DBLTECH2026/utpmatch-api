<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmptyStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_sin_meta_devuelve_sin_meta_true(): void
    {
        $user = User::factory()->create(['carrera' => 'Ing. Industrial']);
        $user->profile()->create([]); // sin rol_objetivo
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('data.sin_meta', true);
    }
}
