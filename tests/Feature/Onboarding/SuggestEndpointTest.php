<?php

namespace Tests\Feature\Onboarding;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SuggestEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_roles_sugeridos_de_la_carrera_del_usuario(): void
    {
        $user = User::factory()->create(['carrera' => 'Ing. Industrial']);
        Sanctum::actingAs($user);

        $res = $this->postJson('/api/v1/onboarding/suggest', [
            'fortalezas' => ['Mejora de procesos', 'Lean Manufacturing'],
            'intereses'  => ['Datos y números'],
        ]);

        $res->assertOk()
            ->assertJsonPath('data.0.rol', 'Analista de Procesos')
            ->assertJsonStructure(['data' => [['rol', 'puntaje', 'skills_coinciden']]]);
    }

    public function test_requiere_autenticacion(): void
    {
        $this->postJson('/api/v1/onboarding/suggest', [])->assertUnauthorized();
    }
}
