<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('despesa_obra_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('despesa_obra_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->string('nome_original')->nullable();
            $table->string('mime')->nullable();
            $table->timestamps();
        });

        foreach (DB::table('despesa_obras')->whereNotNull('comprovante_path')->orderBy('id')->get() as $despesa) {
            DB::table('despesa_obra_anexos')->insert([
                'despesa_obra_id' => $despesa->id,
                'path' => $despesa->comprovante_path,
                'nome_original' => basename($despesa->comprovante_path),
                'mime' => null,
                'created_at' => $despesa->created_at ?? now(),
                'updated_at' => $despesa->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('despesa_obra_anexos');
    }
};
