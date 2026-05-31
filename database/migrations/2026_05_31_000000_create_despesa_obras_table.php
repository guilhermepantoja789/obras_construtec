<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('despesa_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained()->onDelete('cascade');
            $table->decimal('valor', 15, 2);
            $table->date('data');
            $table->string('descricao');
            $table->string('fornecedor')->nullable();
            $table->string('categoria')->nullable();
            $table->enum('status', ['pago', 'pendente'])->default('pago');
            $table->string('forma_pagamento')->nullable();
            $table->string('comprovante_path')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('despesa_obras');
    }
};
