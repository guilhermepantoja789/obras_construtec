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
        Schema::table('etapa_obras', function (Blueprint $table) {
            $table->string('nome')->after('obra_id');
            $table->date('data_inicio_prevista')->nullable()->after('descricao');
            $table->date('data_fim_prevista')->nullable()->after('data_inicio_prevista');
            $table->date('data_inicio_real')->nullable()->after('data_fim_prevista');
            $table->date('data_fim_real')->nullable()->after('data_inicio_real');
            $table->enum('status', ['pendente', 'em_progresso', 'concluida', 'atrasada'])->default('pendente')->after('percentual_concluido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etapa_obras', function (Blueprint $table) {
            $table->dropColumn(['nome', 'data_inicio_prevista', 'data_fim_prevista', 'data_inicio_real', 'data_fim_real', 'status']);
        });
    }
};
