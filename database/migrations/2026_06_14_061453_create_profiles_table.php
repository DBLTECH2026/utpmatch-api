<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * profiles — Perfil 360 del alumno (1:1 con users).
 * Separa los datos de empleabilidad de la identidad/credenciales.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('rol_objetivo')->nullable();      // meta: ej. "Frontend Jr."
            $table->string('headline')->nullable();          // titular del perfil
            $table->text('about')->nullable();               // resumen / sobre mí
            $table->unsignedTinyInteger('empleabilidad_score')->default(0); // 0..100

            $table->timestamps();

            $table->unique('user_id'); // 1:1 garantizado a nivel de BD
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
