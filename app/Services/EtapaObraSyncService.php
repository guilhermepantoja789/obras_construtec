<?php

namespace App\Services;

use App\Models\EtapaObra;
use App\Models\Proposta;
use App\Models\PropostaItem;
use App\Support\OrdemHelper;
use Illuminate\Support\Collection;

class EtapaObraSyncService
{
    public static function syncFromProposta(Proposta $proposta): array
    {
        if ($proposta->status !== 'aceita') {
            return ['criadas' => 0, 'atualizadas' => 0, 'removidas' => 0];
        }

        $proposta->load('items');
        $obraId = $proposta->obra_id;
        $etapaItems = OrdemHelper::sortCollection($proposta->items)->where('is_etapa', true);
        $usedOrdens = [];
        $criadas = 0;
        $atualizadas = 0;

        foreach ($etapaItems as $item) {
            $ordem = (string) ($item->ordem ?? '');
            $usedOrdens[] = $ordem;

            $data = [
                'nome' => $item->descricao,
                'valor' => $item->subtotal,
                'ordem' => $ordem,
                'proposta_item_id' => $item->id,
                'descricao' => 'Gerado automaticamente via Proposta',
            ];

            $existing = self::findExistingPropostaEtapa($obraId, $item, $ordem);

            if ($existing) {
                $existing->update(array_merge($data, [
                    'percentual_concluido' => $existing->percentual_concluido,
                    'status' => $existing->status,
                    'data_inicio_prevista' => $existing->data_inicio_prevista,
                    'data_fim_prevista' => $existing->data_fim_prevista,
                    'data_inicio_real' => $existing->data_inicio_real,
                    'data_fim_real' => $existing->data_fim_real,
                ]));
                $atualizadas++;
                continue;
            }

            EtapaObra::create(array_merge($data, [
                'obra_id' => $obraId,
                'status' => 'pendente',
                'percentual_concluido' => 0,
            ]));
            $criadas++;
        }

        $removidas = self::removeOrphanPropostaEtapas($obraId, $usedOrdens);

        return compact('criadas', 'atualizadas', 'removidas');
    }

    public static function regenerateFromProposta(Proposta $proposta): array
    {
        $obraId = $proposta->obra_id;

        $removidas = EtapaObra::where('obra_id', $obraId)
            ->where(function ($query) {
                $query->whereNotNull('proposta_item_id')
                    ->orWhere('descricao', 'Gerado automaticamente via Proposta');
            })
            ->delete();

        if ($proposta->status !== 'aceita') {
            return ['removidas' => $removidas, 'criadas' => 0, 'atualizadas' => 0];
        }

        $sync = self::syncFromProposta($proposta);

        return [
            'removidas' => $removidas,
            'criadas' => $sync['criadas'],
            'atualizadas' => $sync['atualizadas'],
        ];
    }

    private static function findExistingPropostaEtapa(int $obraId, PropostaItem $item, string $ordem): ?EtapaObra
    {
        if ($item->id) {
            $byItem = EtapaObra::where('obra_id', $obraId)
                ->where('proposta_item_id', $item->id)
                ->first();

            if ($byItem) {
                return $byItem;
            }
        }

        return EtapaObra::where('obra_id', $obraId)
            ->where('ordem', $ordem)
            ->where(function ($query) {
                $query->whereNotNull('proposta_item_id')
                    ->orWhere('descricao', 'Gerado automaticamente via Proposta');
            })
            ->first();
    }

    private static function removeOrphanPropostaEtapas(int $obraId, array $usedOrdens): int
    {
        $query = EtapaObra::where('obra_id', $obraId)
            ->where(function ($q) {
                $q->whereNotNull('proposta_item_id')
                    ->orWhere('descricao', 'Gerado automaticamente via Proposta');
            });

        if (empty($usedOrdens)) {
            return $query->delete();
        }

        return $query->whereNotIn('ordem', $usedOrdens)->delete();
    }

    public static function calcularProgressoPonderado(Collection $etapas): int
    {
        if ($etapas->isEmpty()) {
            return 0;
        }

        $totalValor = $etapas->sum('valor');

        if ($totalValor > 0) {
            $ponderado = $etapas->sum(fn (EtapaObra $etapa) => $etapa->valor * $etapa->percentual_concluido);

            return (int) round($ponderado / $totalValor);
        }

        return (int) round($etapas->avg('percentual_concluido') ?: 0);
    }
}
