<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega los campos capturados por el wizard de onboarding al perfil:
 *  - autopercepcion / intereses / fortalezas (respuestas del alumno)
 *  - onboarding_visto (flag para no volver a forzar el wizard)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->json('autopercepcion')->nullable()->after('about');
            $table->json('intereses')->nullable()->after('autopercepcion');
            $table->json('fortalezas')->nullable()->after('intereses');
            $table->boolean('onboarding_visto')->default(false)->after('fortalezas');
        });

        // Backfill: quien ya eligió una meta no debe pasar de nuevo por el wizard.
        DB::table('profiles')->whereNotNull('rol_objetivo')->update(['onboarding_visto' => true]);
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['autopercepcion', 'intereses', 'fortalezas', 'onboarding_visto']);
        });
    }
};
