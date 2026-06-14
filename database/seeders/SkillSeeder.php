<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

/**
 * SkillSeeder — Catálogo base de habilidades (técnicas y blandas).
 * Idempotente: usa updateOrCreate para no duplicar al re-ejecutar.
 */
class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $tecnicas = [
            'Python', 'JavaScript', 'TypeScript', 'React', 'Vue', 'PHP', 'Laravel',
            'SQL', 'Git', 'HTML/CSS', 'CSS avanzado', 'Node.js', 'Docker',
            'Testing', 'Cypress', 'Selenium', 'Lógica de programación',
        ];

        $blandas = [
            'Comunicación', 'Trabajo en equipo', 'Liderazgo',
            'Inglés técnico', 'Resolución de problemas', 'Adaptabilidad',
        ];

        foreach ($tecnicas as $nombre) {
            Skill::updateOrCreate(['nombre' => $nombre], ['categoria' => 'tecnica']);
        }

        foreach ($blandas as $nombre) {
            Skill::updateOrCreate(['nombre' => $nombre], ['categoria' => 'blanda']);
        }
    }
}
