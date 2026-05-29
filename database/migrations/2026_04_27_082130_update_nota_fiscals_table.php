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
        Schema::table('nota_fiscals', function (Blueprint $table) {
            $table->string('descricao')->nullable()->after('numero_nota');
            $table->decimal('valor', 15, 2)->default(0)->after('descricao');
            $table->string('quem_recebeu')->nullable()->after('valor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_fiscals', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'valor', 'quem_recebeu']);
        });
    }
};
