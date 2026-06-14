<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_put_profile_guarda_y_devuelve_campos_de_onboarding(): void
    {
        $user = User::factory()->create(['carrera' => 'Ing. Industrial']);
        $user->profile()->create([]);
        Sanctum::actingAs($user);

        $res = $this->putJson('/api/v1/profile', [
            'autopercepcion'   => ['Analítica', 'Líder'],
            'intereses'        => ['Mejorar procesos', 'Datos y números'],
            'fortalezas'       => ['Excel avanzado'],
            'onboarding_visto' => true,
        ]);

        $res->assertOk()
            ->assertJsonPath('data.autopercepcion', ['Analítica', 'Líder'])
            ->assertJsonPath('data.fortalezas', ['Excel avanzado'])
            ->assertJsonPath('data.onboarding_visto', true);
    }
}
