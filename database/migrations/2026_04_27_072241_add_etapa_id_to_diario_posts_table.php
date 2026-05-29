<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('diario_posts', function (Blueprint $table) {
            $table->foreignId('etapa_obra_id')->nullable()->after('obra_id')->constrained('etapa_obras')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diario_posts', function (Blueprint $table) {
            $table->dropForeign(['etapa_obra_id']);
            $table->dropColumn('etapa_obra_id');
        });
    }
};
