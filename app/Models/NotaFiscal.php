<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaFiscal extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_id',
        'numero_nota',
        'descricao',
        'valor',
        'quem_recebeu',
        'arquivo_path',
        'observacao',
        'data_recebimento',
    ];

    protected $casts = [
        'data_recebimento' => 'date',
        'valor' => 'decimal:2',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
