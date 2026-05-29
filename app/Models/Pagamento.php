<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $fillable = [
        'proposta_id',
        'valor_pago',
        'data_pagamento',
        'comprovante_path',
        'observacao',
    ];

    protected $casts = [
        'data_pagamento' => 'date',
    ];

    public function proposta()
    {
        return $this->belongsTo(Proposta::class);
    }
}
