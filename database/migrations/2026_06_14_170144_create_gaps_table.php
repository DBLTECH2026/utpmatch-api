<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** gaps — Brechas del alumno: ya tiene / le falta cada skill para el rol. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->string('rol_objetivo')->index();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->enum('estado', ['tiene', 'falta']);
            $table->unsignedTinyInteger('demanda_pct')->default(0);
            $table->timestamps();

            $table->unique(['profile_id', 'rol_objetivo', 'skill_id'], 'gaps_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaps');
    }
};
