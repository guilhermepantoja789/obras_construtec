<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    protected $fillable = [
        'nome',
        'localizacao',
        'cep',
        'logradouro',
        'bairro',
        'cidade',
        'estado',
        'data_inicio',
        'data_fim_prevista',
        'status',
        'contratante',
        'cnpj_contratante',
        'empresa_contratada',
        'cnpj_empresa_contratada',
        'engenheiro_responsavel',
        'prazo_dias',
        'encargos_padrao',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim_prevista' => 'date',
        'encargos_padrao' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function diarioPosts()
    {
        return $this->hasMany(DiarioPost::class);
    }

    public function diarioReports()
    {
        return $this->hasMany(DiarioReport::class);
    }

    public function propostas()
    {
        return $this->hasMany(Proposta::class);
    }

    public function notasFiscais()
    {
        return $this->hasMany(NotaFiscal::class);
    }

    public function etapas()
    {
        return $this->hasMany(EtapaObra::class);
    }

    public function contrato()
    {
        return $this->hasOne(Contrato::class);
    }

    public function getLocalizacaoExibicaoAttribute()
    {
        if ($this->localizacao) {
            return $this->localizacao;
        }

        $partes = [];
        if ($this->bairro) $partes[] = $this->bairro;
        if ($this->cidade) $partes[] = $this->cidade;
        
        $local = implode(', ', $partes);
        
        if ($this->estado) {
            $local = $local ? "{$local} - {$this->estado}" : $this->estado;
        }

        return $local ?: 'Sem local';
    }
}
