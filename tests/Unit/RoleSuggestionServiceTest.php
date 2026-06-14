<?php

namespace Tests\Unit;

use App\Services\RoleSuggestionService;
use PHPUnit\Framework\TestCase;

class RoleSuggestionServiceTest extends TestCase
{
    public function test_ordena_roles_por_overlap_de_demanda(): void
    {
        $svc = new RoleSuggestionService();
        // Fortalezas que pesan en "Analista de Procesos" (Mejora de procesos 85, Lean 70).
        $out = $svc->suggest('Ing. Industrial', ['Mejora de procesos', 'Lean Manufacturing', 'Excel avanzado']);

        $this->assertNotEmpty($out);
        $this->assertSame('Analista de Procesos', $out[0]['rol']);
        $this->assertGreaterThan(0, $out[0]['puntaje']);
        $this->assertContains('Mejora de procesos', $out[0]['skills_coinciden']);
    }

    public function test_carrera_desconocida_devuelve_vacio(): void
    {
        $this->assertSame([], (new RoleSuggestionService())->suggest(null, ['x']));
    }
}
