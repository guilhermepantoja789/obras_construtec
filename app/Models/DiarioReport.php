<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiarioReport extends Model
{
    protected $fillable = [
        'obra_id',
        'user_id',
        'data_relatorio',
        'clima_horario',
        'mao_de_obra',
        'maquinario',
        'servicos_iniciados',
        'servicos_execucao',
        'servicos_concluidos',
        'materiais_recebidos',
        'ocorrencias',
        'observacoes',
        'motivo_paralisacao',
        'dia_improdutivo',
        'editado_em',
        'editado_por',
    ];

    protected $casts = [
        'data_relatorio' => 'date',
        'clima_horario'  => 'array',
        'mao_de_obra'    => 'array',
        'maquinario'     => 'array',
        'dia_improdutivo'=> 'boolean',
        'editado_em'     => 'datetime',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editado_por');
    }

    public function foiEditado(): bool
    {
        return !is_null($this->editado_em);
    }
}
