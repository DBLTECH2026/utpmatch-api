<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** advisor_sessions — Solicitudes de sesión alumno ↔ asesor. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advisor_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained()->cascadeOnDelete();
            $table->enum('estado', ['solicitada', 'confirmada', 'completada', 'cancelada'])->default('solicitada');
            $table->timestamp('fecha')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisor_sessions');
    }
};
