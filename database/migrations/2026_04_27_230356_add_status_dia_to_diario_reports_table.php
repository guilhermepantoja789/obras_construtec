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
        Schema::table('diario_reports', function (Blueprint $table) {
            $table->string('status_dia')->default('trabalhado')->after('data_relatorio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diario_reports', function (Blueprint $table) {
            $table->dropColumn('status_dia');
        });
    }
};
