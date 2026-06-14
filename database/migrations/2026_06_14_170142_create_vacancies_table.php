<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** vacancies — Vacantes (data propia UTP + agregadores JSearch/Jooble). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('utp'); // utp|jsearch|jooble
            $table->string('external_id')->nullable();
            $table->string('titulo');
            $table->string('empresa');
            $table->string('ubicacion')->nullable();
            $table->string('modalidad')->nullable(); // remoto|presencial|hibrido
            $table->string('rol_objetivo')->nullable()->index(); // rol al que mapea
            $table->string('salario')->nullable();
            $table->text('descripcion_raw')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
