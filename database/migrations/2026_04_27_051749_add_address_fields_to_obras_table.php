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
        Schema::table('obras', function (Blueprint $table) {
            $table->string('cep')->nullable()->after('localizacao');
            $table->string('logradouro')->nullable()->after('cep');
            $table->string('bairro')->nullable()->after('logradouro');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado')->nullable()->after('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn(['cep', 'logradouro', 'bairro', 'cidade', 'estado']);
        });
    }
};
