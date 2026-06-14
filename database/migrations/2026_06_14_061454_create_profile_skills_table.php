<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * profile_skills — Pivote N:M entre profiles y skills.
 * Incluye metadatos: nivel, origen (de dónde salió la skill) y verificación.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('nivel')->default(1); // 1..5
            // origen: cómo se obtuvo la skill (trazabilidad de datos)
            $table->enum('origen', ['utp', 'github', 'linkedin', 'manual'])->default('manual');
            $table->boolean('verificado')->default(false);

            $table->timestamps();

            $table->unique(['profile_id', 'skill_id']); // sin duplicados
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_skills');
    }
};
