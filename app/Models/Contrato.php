<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $fillable = [
        'obra_id',
        'conteudo',
        'status',
        'assinado_em',
    ];

    protected $casts = [
        'assinado_em' => 'datetime',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
