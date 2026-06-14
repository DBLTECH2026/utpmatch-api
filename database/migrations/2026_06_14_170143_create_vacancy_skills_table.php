<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** vacancy_skills — Skills requeridas por vacante (salida de la normalización IA). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancy_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('demanda_pct')->default(50);
            $table->timestamps();

            $table->unique(['vacancy_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_skills');
    }
};
