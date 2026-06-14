<?php

namespace Database\Seeders;

use App\Models\Advisor;
use App\Models\RoleSkill;
use App\Models\Skill;
use App\Models\Taller;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

/**
 * CatalogSeeder — Datos demo del dominio (role_skills, talleres, vacantes, asesores).
 * Idempotente. Se ejecuta después de SkillSeeder.
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $skill = fn (string $n) => Skill::where('nombre', $n)->value('id');

        // --- Demanda por rol (gap engine) ---
        $demanda = [
            'Frontend Jr.' => [
                'React' => 88, 'TypeScript' => 72, 'CSS avanzado' => 65, 'HTML/CSS' => 60,
                'JavaScript' => 80, 'Git' => 55, 'Trabajo en equipo' => 50,
            ],
            'Backend Jr.' => [
                'PHP' => 78, 'Laravel' => 75, 'SQL' => 80, 'Git' => 60, 'Python' => 50, 'Testing' => 55,
            ],
        ];
        foreach ($demanda as $rol => $skills) {
            foreach ($skills as $nombre => $pct) {
                if ($id = $skill($nombre)) {
                    RoleSkill::updateOrCreate(
                        ['rol_objetivo' => $rol, 'skill_id' => $id],
                        ['demanda_pct' => $pct]
                    );
                }
            }
        }

        // --- Talleres UTP que cierran skills ---
        $talleres = [
            ['React desde cero', 'Frontend', 'React'],
            ['TypeScript para React', 'Frontend', 'TypeScript'],
            ['Flexbox & Grid', 'Frontend', 'CSS avanzado'],
            ['Taller de Oratoria', 'Soft skills', 'Comunicación'],
            ['Inglés técnico UTP', 'Idiomas', 'Inglés técnico'],
            ['Liderazgo y equipos', 'Soft skills', 'Liderazgo'],
            ['Laravel práctico', 'Backend', 'Laravel'],
        ];
        foreach ($talleres as [$nombre, $area, $skillNombre]) {
            Taller::updateOrCreate(
                ['nombre' => $nombre],
                ['area' => $area, 'skill_id' => $skill($skillNombre)]
            );
        }

        // --- Vacantes demo + sus skills requeridas ---
        $vacantes = [
            ['Practicante Frontend', 'Culqi', 'Lima', 'Remoto', 'Frontend Jr.', 'S/ 1,200',
             ['HTML/CSS' => 60, 'JavaScript' => 70, 'Git' => 50, 'React' => 88, 'CSS avanzado' => 60, 'Trabajo en equipo' => 50]],
            ['Dev Junior PHP/Laravel', 'Crehana', 'Lima', 'Híbrido', 'Backend Jr.', 'S/ 1,800',
             ['PHP' => 80, 'Laravel' => 75, 'SQL' => 70, 'Git' => 55, 'Testing' => 50]],
            ['QA Trainee', 'Yape', 'Lima', 'Presencial', 'Backend Jr.', 'S/ 1,000',
             ['Testing' => 80, 'Cypress' => 70, 'Selenium' => 60, 'Lógica de programación' => 50]],
        ];
        foreach ($vacantes as [$titulo, $empresa, $ubic, $modal, $rol, $sal, $skills]) {
            $vac = Vacancy::updateOrCreate(
                ['titulo' => $titulo, 'empresa' => $empresa],
                ['source' => 'utp', 'ubicacion' => $ubic, 'modalidad' => $modal,
                 'rol_objetivo' => $rol, 'salario' => $sal, 'fetched_at' => now()]
            );
            $sync = [];
            foreach ($skills as $nombre => $pct) {
                if ($id = $skill($nombre)) $sync[$id] = ['demanda_pct' => $pct];
            }
            $vac->skills()->sync($sync);
        }

        // --- Asesores ---
        $advisors = [
            ['Jorge Ramos', 'Entrevistas técnicas', 'Culqi', 4.9],
            ['María Paz', 'CV y marca personal', 'Yape', 4.8],
            ['Luis Andrade', 'Primeros pasos', 'Coach de carrera', 5.0],
        ];
        foreach ($advisors as [$n, $esp, $emp, $rat]) {
            Advisor::updateOrCreate(['nombre' => $n], ['especialidad' => $esp, 'empresa' => $emp, 'rating' => $rat]);
        }
    }
}
