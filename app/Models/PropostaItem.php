<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropostaItem extends Model
{
    protected $fillable = [
        'proposta_id',
        'descricao',
        'unidade',
        'quantidade',
        'valor_unitario',
        'subtotal',
        'is_etapa',
        'ordem',
    ];

    public function proposta()
    {
        return $this->belongsTo(Proposta::class);
    }
}
