<?php

namespace Database\Seeders;

use App\Models\Advisor;
use App\Models\RoleSkill;
use App\Models\Skill;
use App\Models\Taller;
use App\Models\Vacancy;
use App\Support\CareerCatalog;
use Illuminate\Database\Seeder;

/**
 * CatalogSeeder — Siembra el dominio del match a partir de CareerCatalog.
 *
 * Para cada carrera → rol → skills:
 *  - crea la skill si no existe (clasifica técnica/blanda por heurística),
 *  - registra role_skills (demanda por rol),
 *  - genera un taller UTP para cada skill (cierra la brecha).
 * Además: vacantes demo (con skills) y asesores.
 *
 * Idempotente.
 */
class CatalogSeeder extends Seeder
{
    /** Palabras que delatan una soft skill. */
    private array $blandas = [
        'Trabajo en equipo', 'Liderazgo', 'Comunicación', 'Negociación',
        'Resolución de problemas', 'Atención al detalle', 'Storytelling de datos',
    ];

    public function run(): void
    {
        // 1) Skills + role_skills desde el catálogo.
        foreach (CareerCatalog::CARRERAS as $roles) {
            foreach ($roles as $rol => $skills) {
                foreach ($skills as $nombre => $demanda) {
                    $skill = Skill::firstOrCreate(
                        ['nombre' => $nombre],
                        ['categoria' => in_array($nombre, $this->blandas, true) ? 'blanda' : 'tecnica']
                    );

                    RoleSkill::updateOrCreate(
                        ['rol_objetivo' => $rol, 'skill_id' => $skill->id],
                        ['demanda_pct' => $demanda]
                    );

                    // Taller UTP para skills técnicas (las blandas tienen sus propios talleres).
                    Taller::firstOrCreate(
                        ['nombre' => "Taller UTP: {$nombre}"],
                        ['area' => 'UTP', 'skill_id' => $skill->id]
                    );
                }
            }
        }

        // 2) Vacantes demo con skills (una por algunos roles representativos).
        $this->seedVacancies();

        // 3) Asesores.
        $this->seedAdvisors();
    }

    private function seedVacancies(): void
    {
        $skillId = fn (string $n) => Skill::where('nombre', $n)->value('id');

        $vacantes = [
            // [titulo, empresa, ubic, modalidad, rol, salario, [skill=>dem]]
            ['Practicante Frontend', 'Culqi', 'Lima', 'Remoto', 'Frontend Jr.', 'S/ 1,200',
             ['HTML/CSS' => 60, 'JavaScript' => 70, 'Git' => 50, 'React' => 88, 'CSS avanzado' => 60, 'Trabajo en equipo' => 50]],
            ['Dev Junior PHP/Laravel', 'Crehana', 'Lima', 'Híbrido', 'Backend Jr.', 'S/ 1,800',
             ['PHP' => 80, 'Laravel' => 75, 'SQL' => 70, 'Git' => 55, 'Testing' => 50, 'APIs REST' => 70]],
            ['Analista de Procesos Jr.', 'Alicorp', 'Lima', 'Presencial', 'Analista de Procesos', 'S/ 2,000',
             ['Mejora de procesos' => 85, 'Lean Manufacturing' => 70, 'Excel avanzado' => 75, 'Resolución de problemas' => 60]],
            ['Asistente Legal Corporativo', 'Estudio Rodrigo', 'Lima', 'Presencial', 'Asistente Legal', 'S/ 1,600',
             ['Redacción jurídica' => 80, 'Investigación legal' => 75, 'Derecho civil' => 70, 'Atención al detalle' => 70]],
            ['Asistente Contable', 'PwC Perú', 'Lima', 'Híbrido', 'Asistente Contable', 'S/ 1,700',
             ['Registro contable' => 85, 'Tributación' => 70, 'Excel avanzado' => 75, 'Conciliaciones' => 65]],
            ['Analista Tributario Jr.', 'EY Perú', 'Lima', 'Híbrido', 'Analista Tributario', 'S/ 2,200',
             ['Tributación' => 88, 'Normativa SUNAT' => 80, 'Declaraciones juradas' => 75, 'Excel avanzado' => 70]],
        ];

        foreach ($vacantes as [$titulo, $empresa, $ubic, $modal, $rol, $sal, $skills]) {
            $vac = Vacancy::updateOrCreate(
                ['titulo' => $titulo, 'empresa' => $empresa],
                ['source' => 'utp', 'ubicacion' => $ubic, 'modalidad' => $modal,
                 'rol_objetivo' => $rol, 'salario' => $sal, 'fetched_at' => now()]
            );
            $sync = [];
            foreach ($skills as $nombre => $pct) {
                if ($id = $skillId($nombre)) $sync[$id] = ['demanda_pct' => $pct];
            }
            $vac->skills()->sync($sync);
        }
    }

    private function seedAdvisors(): void
    {
        $advisors = [
            [
                'nombre'       => 'Jaime Madrid',
                'especialidad' => 'Entrevistas técnicas · Ciberseguridad',
                'empresa'      => 'BanBif',
                'rating'       => 4.9,
                'bio'          => 'Ingeniero de Sistemas por la UNFV y MBA Gerencial por CENTRUM PUCP. Más de 15 años de experiencia en banca digital y seguridad de sistemas. Actualmente lidera proyectos de transformación digital en BanBif, donde fue reconocido con el Premio al Desempeño Excepcional (top 3% de la empresa). Apasionado por preparar a la próxima generación de ingenieros para el mercado laboral tech.',
                'linkedin_url' => 'https://www.linkedin.com/in/jaime-madrid-780671106/',
            ],
            [
                'nombre'       => 'María Paz',
                'especialidad' => 'CV y marca personal',
                'empresa'      => 'Yape',
                'rating'       => 4.8,
                'bio'          => 'Especialista en marca personal y empleabilidad con más de 8 años ayudando a jóvenes profesionales a destacar en el mercado laboral. Trabaja en Yape donde lidera iniciativas de talento y cultura.',
                'linkedin_url' => null,
            ],
            [
                'nombre'       => 'Luis Andrade',
                'especialidad' => 'Primeros pasos · Coach de carrera',
                'empresa'      => 'Coach de carrera',
                'rating'       => 5.0,
                'bio'          => 'Coach certificado con más de 10 años acompañando a estudiantes universitarios en su transición al mundo profesional. Especializado en autoconocimiento, definición de objetivos de carrera y preparación para primeras entrevistas.',
                'linkedin_url' => null,
            ],
            [
                'nombre'       => 'Carla Núñez',
                'especialidad' => 'Carrera en consultoría',
                'empresa'      => 'EY Perú',
                'rating'       => 4.7,
                'bio'          => 'Senior Manager en EY Perú con especialización en consultoría de negocios y transformación organizacional. Mentora activa de practicantes y jóvenes profesionales que buscan ingresar a las Big Four.',
                'linkedin_url' => null,
            ],
        ];

        foreach ($advisors as $data) {
            Advisor::updateOrCreate(['nombre' => $data['nombre']], $data);
        }
    }
}
