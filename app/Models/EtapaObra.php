<?php

namespace App\Models;

use App\Support\OrdemHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EtapaObra extends Model
{
    protected $table = 'etapa_obras';

    protected $fillable = [
        'obra_id',
        'proposta_item_id',
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
        'valor' => 'decimal:2',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function propostaItem()
    {
        return $this->belongsTo(PropostaItem::class);
    }

    public function posts()
    {
        return $this->hasMany(DiarioPost::class, 'etapa_obra_id');
    }

    public function ordemDepth(): int
    {
        return OrdemHelper::depth($this->ordem);
    }

    public function isFromProposta(): bool
    {
        return $this->proposta_item_id !== null
            || $this->descricao === 'Gerado automaticamente via Proposta';
    }

    public function applyAutoStatus(array $attributes): array
    {
        if (($attributes['status'] ?? $this->status) === 'concluida') {
            return $attributes;
        }

        $fim = $attributes['data_fim_prevista'] ?? $this->data_fim_prevista;

        if ($fim && Carbon::parse($fim)->startOfDay()->lt(now()->startOfDay())) {
            $attributes['status'] = 'atrasada';
        }

        return $attributes;
    }
}
