<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** routes — Ruta de aprendizaje generada hacia un rol objetivo. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->string('rol_objetivo');
            $table->unsignedTinyInteger('match_actual')->default(0); // % al crear
            $table->unsignedTinyInteger('match_meta')->default(0);   // % al completar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
