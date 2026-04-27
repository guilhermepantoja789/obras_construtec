<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diario_reports', function (Blueprint $table) {
            $table->timestamp('editado_em')->nullable()->after('updated_at');
            $table->foreignId('editado_por')->nullable()->constrained('users')->nullOnDelete()->after('editado_em');
        });
    }

    public function down(): void
    {
        Schema::table('diario_reports', function (Blueprint $table) {
            $table->dropColumn(['editado_em', 'editado_por']);
        });
    }
};
