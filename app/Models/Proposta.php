<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    protected $fillable = [
        'obra_id',
        'titulo',
        'escopo',
        'data_proposta',
        'valor_total',
        'status',
        'arquivo_path',
    ];

    protected $casts = [
        'data_proposta' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PropostaItem::class);
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }
}
