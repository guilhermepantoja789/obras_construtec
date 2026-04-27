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
        Schema::create('proposta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposta_id')->constrained()->onDelete('cascade');
            $table->string('descricao');
            $table->string('unidade')->nullable();
            $table->decimal('quantidade', 15, 3)->default(1);
            $table->decimal('valor_unitario', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->boolean('is_etapa')->default(false);
            $table->integer('ordem')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposta_items');
    }
};
