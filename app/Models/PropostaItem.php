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

    protected $casts = [
        'is_etapa' => 'boolean',
        'quantidade' => 'decimal:3',
        'valor_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function proposta()
    {
        return $this->belongsTo(Proposta::class);
    }

    public function etapaObra()
    {
        return $this->hasOne(EtapaObra::class);
    }
}
