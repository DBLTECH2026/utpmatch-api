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

        $profile = $maria->profile()->updateOrCreate([], [
            'rol_objetivo'        => 'Frontend Jr.',
            'headline'            => 'Estudiante UTP en ruta a Frontend',
            'about'               => 'Base sólida en lógica, Git y bases de datos. Cerrando brechas hacia Frontend.',
            'empleabilidad_score' => 68,
        ]);

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
