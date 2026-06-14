<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\RoleSkill;
use App\Models\Route;
use Illuminate\Support\Facades\DB;

/**
 * GapRouteService — Motor de Brechas y Ruta (el corazón del producto).
 *
 * Capa: Service. Lógica pura de dominio, sin HTTP.
 *  - computeGaps(): compara las skills del perfil contra la demanda del rol
 *    (role_skills) y produce "ya tiene / te falta" con % de demanda.
 *  - buildRoute(): prioriza lo que falta por demanda y arma route_items con
 *    su taller UTP sugerido, y calcula match actual vs match meta.
 *
 * El "match %" se define como: demanda cubierta / demanda total del rol.
 */
class GapRouteService
{
    /**
     * Calcula y persiste las brechas del perfil hacia un rol objetivo.
     *
     * @return array{tiene: array, falta: array, match_actual: int}
     */
    public function computeGaps(Profile $profile, string $rol): array
    {
        // Mapea metas escritas a mano al rol del catálogo más parecido (la
        // demanda vive por nombre de rol del catálogo). Si no hay match, usa el
        // texto tal cual (probablemente sin demanda → ruta vacía manejada abajo).
        $rolDemanda = \App\Support\CareerCatalog::resolveRole($profile->user?->carrera, $rol) ?? $rol;

        // Demanda del rol: skill_id => demanda_pct
        $demanda = RoleSkill::where('rol_objetivo', $rolDemanda)
            ->with('skill')
            ->get();

        if ($demanda->isEmpty()) {
            return ['tiene' => [], 'falta' => [], 'match_actual' => 0];
        }

        // Skills que el alumno ya posee (ids).
        $mias = $profile->skills()->pluck('skills.id')->all();

        $tiene = [];
        $falta = [];
        $totalDemanda = 0;
        $cubierta = 0;

        // Limpia brechas previas de este rol (recálculo idempotente).
        $profile->gaps()->where('rol_objetivo', $rol)->delete();

        foreach ($demanda as $rs) {
            $totalDemanda += $rs->demanda_pct;
            $loTiene = in_array($rs->skill_id, $mias, true);
            if ($loTiene) {
                $cubierta += $rs->demanda_pct;
            }

            $estado = $loTiene ? 'tiene' : 'falta';
            $profile->gaps()->create([
                'rol_objetivo' => $rol,
                'skill_id'     => $rs->skill_id,
                'estado'       => $estado,
                'demanda_pct'  => $rs->demanda_pct,
            ]);

            $row = [
                'skill_id'    => $rs->skill_id,
                'nombre'      => $rs->skill->nombre,
                'demanda_pct' => $rs->demanda_pct,
            ];
            $loTiene ? $tiene[] = $row : $falta[] = $row;
        }

        // Ordena lo que falta por mayor demanda (prioridad).
        usort($falta, fn ($a, $b) => $b['demanda_pct'] <=> $a['demanda_pct']);

        $matchActual = $totalDemanda > 0 ? (int) round(($cubierta / $totalDemanda) * 100) : 0;

        return ['tiene' => $tiene, 'falta' => $falta, 'match_actual' => $matchActual];
    }

    /**
     * Construye (o regenera) la ruta priorizada hacia el rol, con talleres.
     */
    public function buildRoute(Profile $profile, string $rol): Route
    {
        $gaps = $this->computeGaps($profile, $rol);

        return DB::transaction(function () use ($profile, $rol, $gaps) {
            // Una ruta vigente por (perfil, rol).
            $profile->routes()->where('rol_objetivo', $rol)->delete();

            // Match meta: si cierra todo lo que falta, cubre el 100% alcanzable.
            $matchMeta = min(100, $gaps['match_actual'] + array_sum(array_map(
                fn ($f) => 0, $gaps['falta'] // el meta real lo aproximamos abajo
            )));
            // Meta = 100% de la demanda (cerrando todas las brechas).
            $matchMeta = 100;

            $route = $profile->routes()->create([
                'rol_objetivo' => $rol,
                'match_actual' => $gaps['match_actual'],
                'match_meta'   => $matchMeta,
            ]);

            // Cada skill faltante = un paso, con taller UTP si existe.
            foreach (array_values($gaps['falta']) as $i => $f) {
                $taller = \App\Models\Taller::where('skill_id', $f['skill_id'])->first();
                $route->items()->create([
                    'skill_id'    => $f['skill_id'],
                    'orden'       => $i + 1,
                    'taller_id'   => $taller?->id,
                    'estado'      => $i === 0 ? 'en_curso' : 'pendiente',
                    'demanda_pct' => $f['demanda_pct'],
                ]);
            }

            return $route->load('items.skill', 'items.taller');
        });
    }
}
