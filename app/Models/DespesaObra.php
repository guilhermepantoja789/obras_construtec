<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespesaObra extends Model
{
    protected $fillable = [
        'obra_id',
        'empreiteira_id',
        'valor',
        'data',
        'descricao',
        'fornecedor',
        'categoria',
        'status',
        'forma_pagamento',
        'comprovante_path',
        'observacao',
    ];

    protected $casts = [
        'data' => 'date',
        'valor' => 'decimal:2',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function empreiteira()
    {
        return $this->belongsTo(Empreiteira::class);
    }

    public function anexos()
    {
        return $this->hasMany(DespesaObraAnexo::class);
    }
}
