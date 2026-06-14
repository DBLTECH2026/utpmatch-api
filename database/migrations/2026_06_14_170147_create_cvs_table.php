<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** cvs — CV generado sobre plantilla (json_data) + score ATS. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->string('rol_objetivo');
            $table->string('plantilla')->default('estandar');
            $table->json('json_data');                  // estructura RF-CV-02
            $table->unsignedTinyInteger('ats_score')->default(0);
            $table->json('sugerencias')->nullable();     // mejoras ATS
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
