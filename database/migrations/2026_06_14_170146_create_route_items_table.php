<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** route_items — Pasos priorizados de una ruta, con taller asociado. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('orden');
            $table->foreignId('taller_id')->nullable()->constrained('talleres')->nullOnDelete();
            $table->enum('estado', ['pendiente', 'en_curso', 'completado'])->default('pendiente');
            $table->unsignedTinyInteger('demanda_pct')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_items');
    }
};
