<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** copilot_nudges — Avisos proactivos del copiloto al alumno. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copilot_nudges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tipo')->default('sugerencia'); // sugerencia|logro|alerta
            $table->text('mensaje');
            $table->string('cta_label')->nullable();
            $table->string('cta_route')->nullable();
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copilot_nudges');
    }
};
