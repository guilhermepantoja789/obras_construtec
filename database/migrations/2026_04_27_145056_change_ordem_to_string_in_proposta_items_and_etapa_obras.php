<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposta_items', function (Blueprint $table) {
            $table->string('ordem')->nullable()->change();
        });

        Schema::table('etapa_obras', function (Blueprint $table) {
            $table->string('ordem')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('proposta_items', function (Blueprint $table) {
            $table->integer('ordem')->nullable()->change();
        });

        Schema::table('etapa_obras', function (Blueprint $table) {
            $table->integer('ordem')->nullable()->change();
        });
    }
};
