<?php

namespace App\Support;

/**
 * CareerCatalog — Catálogo cerrado de carreras → roles objetivo.
 *
 * Fuente única de verdad usada por:
 *  - el endpoint /careers (el front arma los selectores)
 *  - el CareerSeeder (siembra role_skills coherentes con esto)
 *
 * El alumno elige su CARRERA al registrarse; el "rol objetivo" se limita a
 * los roles de su carrera para que el match siempre tenga demanda definida.
 */
final class CareerCatalog
{
    /**
     * carrera => [ 'Rol' => ['Skill' => demanda_pct, ...], ... ]
     * Skills y demanda alimentan role_skills (gap/match engine).
     */
    public const CARRERAS = [
        'Ing. de Sistemas' => [
            'Frontend Jr.' => [
                'HTML/CSS' => 60, 'JavaScript' => 80, 'React' => 88, 'TypeScript' => 72,
                'CSS avanzado' => 65, 'Git' => 55, 'Trabajo en equipo' => 50,
            ],
            'Backend Jr.' => [
                'PHP' => 70, 'Laravel' => 72, 'SQL' => 82, 'Python' => 55,
                'Git' => 60, 'Testing' => 55, 'APIs REST' => 75,
            ],
            'Data Analyst Jr.' => [
                'SQL' => 85, 'Python' => 78, 'Excel avanzado' => 70, 'Power BI' => 72,
                'Estadística' => 60, 'Storytelling de datos' => 55,
            ],
            'QA / Testing Jr.' => [
                'Testing' => 85, 'Cypress' => 65, 'Selenium' => 60,
                'Lógica de programación' => 60, 'SQL' => 50, 'Atención al detalle' => 65,
            ],
        ],

        'Ing. Industrial' => [
            'Analista de Procesos' => [
                'Mejora de procesos' => 85, 'BPM' => 65, 'Lean Manufacturing' => 70,
                'Excel avanzado' => 75, 'Power BI' => 55, 'Resolución de problemas' => 60,
            ],
            'Analista de Logística' => [
                'Gestión de inventarios' => 80, 'Cadena de suministro' => 82, 'SAP' => 65,
                'Excel avanzado' => 75, 'Planeamiento' => 60, 'Negociación' => 50,
            ],
            'Analista de Calidad' => [
                'Control de calidad' => 85, 'ISO 9001' => 70, 'Six Sigma' => 60,
                'Auditoría' => 55, 'Excel avanzado' => 65, 'Atención al detalle' => 65,
            ],
            'Trainee de Operaciones' => [
                'Planeamiento' => 70, 'Lean Manufacturing' => 65, 'Excel avanzado' => 70,
                'Liderazgo' => 60, 'Trabajo en equipo' => 60, 'Indicadores KPI' => 65,
            ],
        ],

        'Derecho' => [
            'Asistente Legal' => [
                'Redacción jurídica' => 80, 'Investigación legal' => 75, 'Derecho civil' => 70,
                'Gestión documental' => 65, 'Atención al detalle' => 70, 'Comunicación' => 55,
            ],
            'Abogado Jr. Corporativo' => [
                'Derecho corporativo' => 85, 'Contratos' => 80, 'Derecho comercial' => 75,
                'Inglés legal' => 60, 'Negociación' => 60, 'Redacción jurídica' => 70,
            ],
            'Analista Legal' => [
                'Investigación legal' => 80, 'Cumplimiento normativo' => 75, 'Derecho administrativo' => 70,
                'Análisis de riesgos' => 60, 'Redacción jurídica' => 70, 'Atención al detalle' => 65,
            ],
            'Asistente Laboral/Tributario' => [
                'Derecho laboral' => 80, 'Derecho tributario' => 78, 'Planillas/PLAME' => 60,
                'Normativa SUNAT' => 65, 'Redacción jurídica' => 60, 'Excel avanzado' => 50,
            ],
        ],

        'Contabilidad' => [
            'Asistente Contable' => [
                'Registro contable' => 85, 'PLAME' => 65, 'Tributación' => 70,
                'Excel avanzado' => 75, 'Conciliaciones' => 65, 'Atención al detalle' => 65,
            ],
            'Analista Tributario' => [
                'Tributación' => 88, 'Normativa SUNAT' => 80, 'Declaraciones juradas' => 75,
                'Excel avanzado' => 70, 'Análisis de riesgos' => 55, 'Legislación laboral' => 50,
            ],
            'Auditor Jr.' => [
                'Auditoría' => 85, 'NIIF' => 75, 'Control interno' => 72,
                'Excel avanzado' => 70, 'Análisis de riesgos' => 65, 'Atención al detalle' => 70,
            ],
            'Analista de Costos' => [
                'Contabilidad de costos' => 85, 'Presupuestos' => 75, 'Excel avanzado' => 80,
                'Power BI' => 55, 'Indicadores KPI' => 60, 'Análisis financiero' => 65,
            ],
        ],
    ];

    /** Lista de nombres de carrera. */
    public static function carreras(): array
    {
        return array_keys(self::CARRERAS);
    }

    /** Roles (solo nombres) de una carrera. */
    public static function roles(string $carrera): array
    {
        return array_keys(self::CARRERAS[$carrera] ?? []);
    }

    /** Estructura para el front: [{carrera, roles:[...]}, ...]. */
    public static function forApi(): array
    {
        return array_map(
            fn ($carrera) => ['carrera' => $carrera, 'roles' => self::roles($carrera)],
            self::carreras()
        );
    }
}
