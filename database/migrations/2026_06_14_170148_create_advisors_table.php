<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** advisors — Asesores/mentores externos. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advisors', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('especialidad')->nullable();
            $table->string('empresa')->nullable();
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->string('contacto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisors');
    }
};
