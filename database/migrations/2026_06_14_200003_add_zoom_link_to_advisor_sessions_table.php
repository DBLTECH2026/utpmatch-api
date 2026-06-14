<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advisor_sessions', function (Blueprint $table) {
            $table->string('zoom_link')->nullable()->after('notas');
        });
    }

    public function down(): void
    {
        Schema::table('advisor_sessions', function (Blueprint $table) {
            $table->dropColumn('zoom_link');
        });
    }
};
