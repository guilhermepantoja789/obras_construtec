<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empreiteira extends Model
{
    protected $fillable = [
        'obra_id',
        'nome',
        'valor_acordado',
        'servico',
        'telefone',
        'observacao',
    ];

    protected $casts = [
        'valor_acordado' => 'decimal:2',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function despesas()
    {
        return $this->hasMany(DespesaObra::class);
    }

    public function getValorPagoAttribute(): float
    {
        return (float) $this->despesas()->where('status', 'pago')->sum('valor');
    }

    public function getValorPendenteAttribute(): float
    {
        return (float) $this->despesas()->where('status', 'pendente')->sum('valor');
    }

    public function getSaldoRestanteAttribute(): float
    {
        return max(0, (float) $this->valor_acordado - $this->valor_pago);
    }

    public function getPercentualPagoAttribute(): float
    {
        if ((float) $this->valor_acordado <= 0) {
            return 0;
        }

        return min(100, round(($this->valor_pago / (float) $this->valor_acordado) * 100, 1));
    }

    public function getStatusPagamentoAttribute(): string
    {
        if ($this->valor_pago >= (float) $this->valor_acordado) {
            return 'concluido';
        }

        if ($this->valor_pago > 0) {
            return 'em_andamento';
        }

        return 'nao_iniciado';
    }
}
