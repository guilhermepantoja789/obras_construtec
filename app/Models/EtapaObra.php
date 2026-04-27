<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtapaObra extends Model
{
    protected $table = 'etapa_obras';

    protected $fillable = [
        'obra_id',
        'nome',
        'valor',
        'descricao',
        'data_inicio_prevista',
        'data_fim_prevista',
        'data_inicio_real',
        'data_fim_real',
        'percentual_concluido',
        'status',
        'ordem',
    ];

    protected $casts = [
        'data_inicio_prevista' => 'date',
        'data_fim_prevista' => 'date',
        'data_inicio_real' => 'date',
        'data_fim_real' => 'date',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function posts()
    {
        return $this->hasMany(DiarioPost::class, 'etapa_obra_id');
    }
}
