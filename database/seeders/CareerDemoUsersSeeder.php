<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * CareerDemoUsersSeeder — Un alumno demo por cada carrera del catálogo.
 *
 * Sirve para que el equipo pruebe el flujo completo (Inicio → elegir rol →
 * match) con datos coherentes de cada carrera. Todos comparten la contraseña
 * demo y arrancan SIN rol_objetivo (para ver el módulo de bienvenida).
 *
 * Idempotente (updateOrCreate por email).
 */
class CareerDemoUsersSeeder extends Seeder
{
    private const PASSWORD = 'Utp+Match2026!';

    public function run(): void
    {
        $alumnos = [
            [
                'name' => 'Diego Torres', 'email' => 'diego.sistemas@utp.edu.pe',
                'codigo' => 'U20231001', 'carrera' => 'Ing. de Sistemas', 'ciclo' => 7,
                'skills' => ['Python' => 'utp', 'Git' => 'github', 'SQL' => 'utp', 'JavaScript' => 'manual', 'HTML/CSS' => 'utp'],
                'cursos' => [
                    ['Algoritmos y Estructuras de Datos', 'aprobado', 16, 4],
                    ['Base de Datos I', 'aprobado', 15, 5],
                    ['Programación Web', 'en_curso', null, 7],
                ],
            ],
            [
                'name' => 'Lucía Vargas', 'email' => 'lucia.industrial@utp.edu.pe',
                'codigo' => 'U20231002', 'carrera' => 'Ing. Industrial', 'ciclo' => 8,
                'skills' => ['Excel avanzado' => 'utp', 'Lean Manufacturing' => 'utp', 'Trabajo en equipo' => 'manual', 'Resolución de problemas' => 'manual'],
                'cursos' => [
                    ['Investigación de Operaciones', 'aprobado', 17, 6],
                    ['Gestión de la Producción', 'aprobado', 16, 7],
                    ['Logística y Cadena de Suministro', 'en_curso', null, 8],
                ],
            ],
            [
                'name' => 'Andrés Salazar', 'email' => 'andres.derecho@utp.edu.pe',
                'codigo' => 'U20231003', 'carrera' => 'Derecho', 'ciclo' => 9,
                'skills' => ['Redacción jurídica' => 'utp', 'Investigación legal' => 'utp', 'Derecho civil' => 'utp', 'Comunicación' => 'manual'],
                'cursos' => [
                    ['Derecho Civil I', 'aprobado', 16, 5],
                    ['Derecho Procesal', 'aprobado', 15, 7],
                    ['Derecho Corporativo', 'en_curso', null, 9],
                ],
            ],
            [
                'name' => 'Camila Flores', 'email' => 'camila.contabilidad@utp.edu.pe',
                'codigo' => 'U20231004', 'carrera' => 'Contabilidad', 'ciclo' => 6,
                'skills' => ['Registro contable' => 'utp', 'Excel avanzado' => 'utp', 'Tributación' => 'utp', 'Atención al detalle' => 'manual'],
                'cursos' => [
                    ['Contabilidad General', 'aprobado', 17, 3],
                    ['Tributación I', 'aprobado', 16, 5],
                    ['Costos y Presupuestos', 'en_curso', null, 6],
                ],
            ],
        ];

        foreach ($alumnos as $a) {
            $user = User::updateOrCreate(
                ['email' => $a['email']],
                [
                    'name'       => $a['name'],
                    'password'   => Hash::make(self::PASSWORD),
                    'codigo_utp' => $a['codigo'],
                    'carrera'    => $a['carrera'],
                    'ciclo'      => $a['ciclo'],
                    'rol'        => 'alumno',
                ]
            );

            // Perfil sin rol_objetivo (verá el módulo Inicio para elegir meta).
            $profile = $user->profile()->updateOrCreate([], [
                'rol_objetivo'        => null,
                'headline'            => "Estudiante UTP de {$a['carrera']}",
                'empleabilidad_score' => 60,
            ]);

            // Skills base de su carrera.
            $sync = [];
            foreach ($a['skills'] as $nombre => $origen) {
                if ($id = Skill::where('nombre', $nombre)->value('id')) {
                    $sync[$id] = ['nivel' => 3, 'origen' => $origen, 'verificado' => $origen !== 'manual'];
                }
            }
            $profile->skills()->sync($sync);

            // Cursos importados.
            foreach ($a['cursos'] as [$nombre, $estado, $nota, $ciclo]) {
                $user->courses()->updateOrCreate(
                    ['nombre' => $nombre],
                    ['estado' => $estado, 'nota' => $nota, 'ciclo' => $ciclo]
                );
            }
        }
    }
}
