<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * skills — Catálogo de habilidades (técnicas y blandas).
 * Tabla maestra referenciada por profile_skills, vacancy_skills, etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->enum('categoria', ['tecnica', 'blanda'])->default('tecnica')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
