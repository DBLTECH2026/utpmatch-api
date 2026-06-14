<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DemoUserSeeder — Datos de demostración para la presentación.
 *
 * Crea:
 *  - María Quispe (alumno) con perfil 360 + skills (la persona del prototipo).
 *  - Un asesor y un admin para mostrar RBAC.
 *
 * Idempotente (updateOrCreate por email).
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // --- Alumna demo: María Quispe (coincide con el prototipo/Figma) ---
        $maria = User::updateOrCreate(
            ['email' => 'maria@utp.edu.pe'],
            [
                'name'       => 'María Quispe',
                'password'   => Hash::make('Utp+Match2026!'),
                'codigo_utp' => 'U20231234',
                'carrera'    => 'Ing. de Sistemas',
                'ciclo'      => 8,
                'rol'        => 'alumno',
            ]
        );

        // Sin rol_objetivo a propósito: al entrar verá el módulo de Bienvenida
        // para elegir su meta (ese es el flujo real).
        $profile = $maria->profile()->updateOrCreate([], [
            'rol_objetivo'        => null,
            'headline'            => 'Estudiante UTP de Ing. de Sistemas',
            'about'               => 'Base sólida en lógica, Git y bases de datos.',
            'empleabilidad_score' => 68,
        ]);

        // Cursos importados de UTP (data demo).
        $cursos = [
            ['Algoritmos y Estructuras de Datos', 'aprobado', 16.0, 4],
            ['Base de Datos I', 'aprobado', 17.0, 5],
            ['Programación Web', 'aprobado', 15.0, 6],
            ['Ingeniería de Software', 'aprobado', 16.0, 7],
            ['Desarrollo de Aplicaciones Web', 'en_curso', null, 8],
        ];
        foreach ($cursos as [$nombre, $estado, $nota, $ciclo]) {
            $maria->courses()->updateOrCreate(
                ['nombre' => $nombre],
                ['estado' => $estado, 'nota' => $nota, 'ciclo' => $ciclo]
            );
        }

        // Skills que "ya tiene" (origen UTP/GitHub, verificadas).
        $base = ['Python' => 'utp', 'Git' => 'github', 'SQL' => 'utp', 'HTML/CSS' => 'utp', 'Lógica de programación' => 'utp'];
        $sync = [];
        foreach ($base as $nombre => $origen) {
            $skill = Skill::where('nombre', $nombre)->first();
            if ($skill) {
                $sync[$skill->id] = ['nivel' => 4, 'origen' => $origen, 'verificado' => true];
            }
        }
        $profile->skills()->sync($sync);

        // Nudge proactivo del copiloto (demo).
        $maria->nudges()->updateOrCreate(
            ['mensaje' => 'Te falta 1 curso para calificar a 3 vacantes nuevas de Frontend. ¿Te armo la ruta?'],
            ['tipo' => 'sugerencia', 'cta_label' => 'Armar mi ruta', 'cta_route' => '/dashboard/ruta', 'leido' => false]
        );

        // --- Asesor y Admin (para demostrar RBAC) ---
        User::updateOrCreate(
            ['email' => 'asesor@utp.edu.pe'],
            ['name' => 'Jorge Ramos', 'password' => Hash::make('Utp+Match2026!'), 'rol' => 'asesor']
        );

        User::updateOrCreate(
            ['email' => 'admin@utp.edu.pe'],
            ['name' => 'Admin UTP+Match', 'password' => Hash::make('Utp+Match2026!'), 'rol' => 'admin']
        );
    }
}
