<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empreiteiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained()->onDelete('cascade');
            $table->string('nome');
            $table->decimal('valor_acordado', 15, 2);
            $table->string('servico')->nullable();
            $table->string('telefone')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empreiteiras');
    }
};
