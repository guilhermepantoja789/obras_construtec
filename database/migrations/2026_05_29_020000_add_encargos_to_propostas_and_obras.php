<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propostas', function (Blueprint $table) {
            $table->decimal('subtotal_itens', 15, 2)->default(0)->after('valor_total');
            $table->json('encargos')->nullable()->after('subtotal_itens');
        });

        Schema::table('obras', function (Blueprint $table) {
            $table->json('encargos_padrao')->nullable()->after('prazo_dias');
        });
    }

    public function down(): void
    {
        Schema::table('propostas', function (Blueprint $table) {
            $table->dropColumn(['subtotal_itens', 'encargos']);
        });

        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn('encargos_padrao');
        });
    }
};
