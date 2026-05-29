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
            $table->string('contratante')->nullable();
            $table->string('empresa_contratada')->nullable();
            $table->string('engenheiro_responsavel')->nullable();
            $table->integer('prazo_dias')->default(0);
        });

        Schema::table('diario_reports', function (Blueprint $table) {
            // Remove old climate/count fields
            $table->dropColumn(['clima_manha', 'clima_tarde', 'clima_noite', 'efetivo_total', 'equipamentos_total']);
            
            // Add JSON fields for detailed data
            $table->json('clima_horario')->nullable(); // { "07:00": "sol", ... }
            $table->json('mao_de_obra')->nullable();   // [ { "funcao": "Pedreiro", "quantidade": 2 }, ... ]
            $table->json('maquinario')->nullable();    // [ { "item": "Betoneira", "quantidade": 1 }, ... ]
            $table->boolean('dia_improdutivo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn(['contratante', 'empresa_contratada', 'engenheiro_responsavel', 'prazo_dias']);
        });

        Schema::table('diario_reports', function (Blueprint $table) {
            $table->dropColumn(['clima_horario', 'mao_de_obra', 'maquinario', 'dia_improdutivo']);
            $table->string('clima_manha')->nullable();
            $table->string('clima_tarde')->nullable();
            $table->string('clima_noite')->nullable();
            $table->integer('efetivo_total')->default(0);
            $table->integer('equipamentos_total')->default(0);
        });
    }
};
