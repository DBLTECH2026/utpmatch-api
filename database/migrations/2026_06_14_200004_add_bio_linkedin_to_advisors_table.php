<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advisors', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('contacto');
            $table->string('linkedin_url')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('advisors', function (Blueprint $table) {
            $table->dropColumn(['bio', 'linkedin_url']);
        });
    }
};
