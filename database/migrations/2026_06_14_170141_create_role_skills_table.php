<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * role_skills — Demanda agregada de skills por rol objetivo.
 * Base del gap engine: cuánto se pide cada skill para un rol (0..100%).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_skills', function (Blueprint $table) {
            $table->id();
            $table->string('rol_objetivo')->index();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('demanda_pct'); // 0..100
            $table->timestamps();

            $table->unique(['rol_objetivo', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_skills');
    }
};
