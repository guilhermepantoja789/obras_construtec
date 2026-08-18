<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obra_user', function (Blueprint $table) {
            $table->unique(['obra_id', 'user_id']);
        });

        Schema::table('diario_reports', function (Blueprint $table) {
            $table->unique(['obra_id', 'data_relatorio']);
        });

        Schema::table('diario_posts', function (Blueprint $table) {
            $table->index(['obra_id', 'data_postagem']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });

        Schema::table('etapa_obras', function (Blueprint $table) {
            $table->index(['obra_id', 'status']);
        });

        Schema::table('despesa_obras', function (Blueprint $table) {
            $table->index(['obra_id', 'data']);
            $table->index(['obra_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('obra_user', function (Blueprint $table) {
            $table->dropUnique(['obra_id', 'user_id']);
        });

        Schema::table('diario_reports', function (Blueprint $table) {
            $table->dropUnique(['obra_id', 'data_relatorio']);
        });

        Schema::table('diario_posts', function (Blueprint $table) {
            $table->dropIndex(['obra_id', 'data_postagem']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('etapa_obras', function (Blueprint $table) {
            $table->dropIndex(['obra_id', 'status']);
        });

        Schema::table('despesa_obras', function (Blueprint $table) {
            $table->dropIndex(['obra_id', 'data']);
            $table->dropIndex(['obra_id', 'status']);
        });
    }
};
