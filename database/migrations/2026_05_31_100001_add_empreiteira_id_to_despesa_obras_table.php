<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('despesa_obras', function (Blueprint $table) {
            $table->foreignId('empreiteira_id')
                ->nullable()
                ->after('obra_id')
                ->constrained('empreiteiras')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('despesa_obras', function (Blueprint $table) {
            $table->dropConstrainedForeignId('empreiteira_id');
        });
    }
};
