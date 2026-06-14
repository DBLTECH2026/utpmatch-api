<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * connections — Cuentas externas vinculadas (LinkedIn, GitHub, …).
 *
 * SEGURIDAD (OWASP A02 Cryptographic Failures):
 *  - access_token_enc se almacena CIFRADO (cast 'encrypted' en el modelo).
 *  - Nunca se expone en las API Resources.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('provider', ['linkedin', 'github', 'behance', 'figma']);
            $table->string('external_id')->nullable();   // id del usuario en el proveedor
            $table->text('access_token_enc')->nullable(); // token OAuth CIFRADO en reposo
            $table->timestamp('last_sync_at')->nullable();
            $table->enum('status', ['conectado', 'desconectado', 'error'])->default('conectado');

            $table->timestamps();

            $table->unique(['user_id', 'provider']); // una conexión por proveedor
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
