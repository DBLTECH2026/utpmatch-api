<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_persiste_campos_de_onboarding_con_casts(): void
    {
        $user = User::factory()->create(['carrera' => 'Ing. Industrial']);
        $profile = $user->profile()->create([
            'autopercepcion' => ['Analítica', 'Organizada'],
            'intereses'      => ['Mejorar procesos'],
            'fortalezas'     => ['Excel avanzado', 'Lean Manufacturing'],
        ]);

        $fresh = $profile->fresh();
        $this->assertSame(['Analítica', 'Organizada'], $fresh->autopercepcion);
        $this->assertSame(['Excel avanzado', 'Lean Manufacturing'], $fresh->fortalezas);
        $this->assertFalse($fresh->onboarding_visto); // default
    }
}
