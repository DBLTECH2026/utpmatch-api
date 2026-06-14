<?php

namespace Tests\Unit;

use App\Support\CareerCatalog;
use PHPUnit\Framework\TestCase;

class CareerCatalogResolveTest extends TestCase
{
    public function test_match_exacto(): void
    {
        $this->assertSame(
            'Analista de Procesos',
            CareerCatalog::resolveRole('Ing. Industrial', 'Analista de Procesos')
        );
    }

    public function test_match_por_substring_e_ignora_acentos_mayusculas(): void
    {
        $this->assertSame(
            'Analista de Logística',
            CareerCatalog::resolveRole('Ing. Industrial', 'analista de logistica senior')
        );
    }

    public function test_sin_match_devuelve_null(): void
    {
        $this->assertNull(
            CareerCatalog::resolveRole('Ing. Industrial', 'Astronauta')
        );
    }

    public function test_carrera_desconocida_devuelve_null(): void
    {
        $this->assertNull(CareerCatalog::resolveRole(null, 'lo que sea'));
    }
}
