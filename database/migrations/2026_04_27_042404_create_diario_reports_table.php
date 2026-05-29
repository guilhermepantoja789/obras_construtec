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
        Schema::create('diario_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('data_relatorio');
            $table->string('clima_manha')->nullable();
            $table->string('clima_tarde')->nullable();
            $table->string('clima_noite')->nullable();
            $table->integer('efetivo_total')->default(0);
            $table->integer('equipamentos_total')->default(0);
            $table->text('servicos_iniciados')->nullable();
            $table->text('servicos_execucao')->nullable();
            $table->text('servicos_concluidos')->nullable();
            $table->text('materiais_recebidos')->nullable();
            $table->text('ocorrencias')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('motivo_paralisacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diario_reports');
    }
};
