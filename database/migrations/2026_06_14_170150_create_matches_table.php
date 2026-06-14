<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** matches — Resultado del match engine: perfil ↔ vacante con % y faltantes. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('match_pct'); // 0..100
            $table->json('faltantes_json')->nullable(); // skills que faltan
            $table->timestamps();

            $table->unique(['profile_id', 'vacancy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
