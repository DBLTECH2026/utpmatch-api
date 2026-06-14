<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** courses — Cursos del alumno (data importada de UTP). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->enum('estado', ['aprobado', 'en_curso', 'matriculado'])->default('aprobado');
            $table->decimal('nota', 4, 1)->nullable();
            $table->unsignedTinyInteger('ciclo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
