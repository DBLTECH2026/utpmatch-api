<?php

namespace Tests\Feature\Route;

use App\Models\RoleSkill;
use App\Models\Skill;
use App\Models\User;
use App\Services\GapRouteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomTargetTest extends TestCase
{
    use RefreshDatabase;

    public function test_meta_manual_se_mapea_a_rol_del_catalogo_y_genera_ruta(): void
    {
        $user = User::factory()->create(['carrera' => 'Ing. Industrial']);
        $profile = $user->profile()->create([]);

        // Sembrar demanda para el rol del catálogo "Analista de Procesos".
        $skill = Skill::create(['nombre' => 'Mejora de procesos', 'categoria' => 'tecnica']);
        RoleSkill::create(['rol_objetivo' => 'Analista de Procesos', 'skill_id' => $skill->id, 'demanda_pct' => 85]);

        $engine = app(GapRouteService::class);
        // Texto libre que NO es exacto pero mapea por substring.
        $route = $engine->buildRoute($profile->fresh(), 'analista de procesos junior');

        $this->assertNotEmpty($route->items, 'La ruta no debe quedar vacía para una meta manual mapeable.');
    }
}
