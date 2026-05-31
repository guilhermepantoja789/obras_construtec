<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespesaObraAnexo extends Model
{
    protected $fillable = [
        'despesa_obra_id',
        'path',
        'nome_original',
        'mime',
    ];

    public function despesa()
    {
        return $this->belongsTo(DespesaObra::class, 'despesa_obra_id');
    }
}
