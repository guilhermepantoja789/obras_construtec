<?php

namespace App\Services;

class PropostaCalculoService
{
    public static function templateEncargos(?array $saved = null): array
    {
        $template = config('proposta_encargos', []);
        $savedByKey = collect($saved ?? [])->keyBy('key');

        return collect($template)->map(function (array $item) use ($savedByKey) {
            $savedItem = $savedByKey->get($item['key']);

            return [
                'key' => $item['key'],
                'label' => $item['label'],
                'descricao' => $item['descricao'] ?? '',
                'ativo' => $savedItem['ativo'] ?? $item['default_ativo'] ?? false,
                'percent' => (float) ($savedItem['percent'] ?? $item['default_percent'] ?? 0),
                'subtrai' => $item['subtrai'] ?? false,
            ];
        })->values()->all();
    }

    public static function calcularSubtotalItens(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $total += ((float) ($item['quantidade'] ?? 0)) * ((float) ($item['valor_unitario'] ?? 0));
        }

        return round($total, 2);
    }

    /**
     * @return array{subtotal_itens: float, encargos: array, valor_total: float}
     */
    public static function calcularTotais(array $items, array $encargos): array
    {
        $subtotal = self::calcularSubtotalItens($items);
        $encargosCalculados = [];
        $ajuste = 0;

        foreach ($encargos as $encargo) {
            if (empty($encargo['ativo'])) {
                continue;
            }

            $percent = (float) ($encargo['percent'] ?? 0);
            $valor = round($subtotal * ($percent / 100), 2);

            $encargosCalculados[] = array_merge($encargo, [
                'valor' => $valor,
            ]);

            if (! empty($encargo['subtrai'])) {
                $ajuste -= $valor;
            } else {
                $ajuste += $valor;
            }
        }

        return [
            'subtotal_itens' => $subtotal,
            'encargos' => $encargosCalculados,
            'valor_total' => round($subtotal + $ajuste, 2),
        ];
    }

    public static function normalizarEncargosRequest(array $encargosInput): array
    {
        $template = self::templateEncargos();
        $normalizados = [];

        foreach ($template as $item) {
            $key = $item['key'];
            $input = $encargosInput[$key] ?? [];

            $normalizados[] = [
                'key' => $key,
                'label' => $item['label'],
                'descricao' => $item['descricao'],
                'ativo' => isset($input['ativo']) && $input['ativo'] == '1',
                'percent' => (float) ($input['percent'] ?? $item['percent']),
                'subtrai' => $item['subtrai'] ?? false,
            ];
        }

        return $normalizados;
    }
}
