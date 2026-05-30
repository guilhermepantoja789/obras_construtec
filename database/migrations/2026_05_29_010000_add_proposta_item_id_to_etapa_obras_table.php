<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etapa_obras', function (Blueprint $table) {
            $table->foreignId('proposta_item_id')
                ->nullable()
                ->after('obra_id')
                ->constrained('proposta_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('etapa_obras', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proposta_item_id');
        });
    }
};
