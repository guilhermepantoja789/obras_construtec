<?php

namespace App\Support;

use Illuminate\Support\Collection;

class OrdemHelper
{
    public static function normalize(?string $ordem): string
    {
        if ($ordem === null || trim((string) $ordem) === '') {
            return '999999';
        }

        $parts = array_map(
            fn ($segment) => (string) (int) $segment,
            explode('.', trim((string) $ordem))
        );

        return implode('.', $parts);
    }

    public static function compare(?string $a, ?string $b): int
    {
        $partsA = explode('.', self::normalize($a));
        $partsB = explode('.', self::normalize($b));
        $max = max(count($partsA), count($partsB));

        for ($i = 0; $i < $max; $i++) {
            $segA = isset($partsA[$i]) ? (int) $partsA[$i] : 0;
            $segB = isset($partsB[$i]) ? (int) $partsB[$i] : 0;

            if ($segA !== $segB) {
                return $segA <=> $segB;
            }
        }

        return 0;
    }

    public static function depth(?string $ordem): int
    {
        if ($ordem === null || trim((string) $ordem) === '') {
            return 0;
        }

        return substr_count(trim((string) $ordem), '.') + 1;
    }

    public static function isTopLevel(?string $ordem): bool
    {
        return self::depth($ordem) <= 1;
    }

    public static function sortCollection(Collection $collection, string $key = 'ordem'): Collection
    {
        return $collection
            ->sort(fn ($a, $b) => self::compare($a->{$key} ?? null, $b->{$key} ?? null))
            ->values();
    }

    /**
     * @return array<int, array{etapa: mixed, children: array<int, mixed>}>
     */
    public static function groupEtapas(Collection $etapas): array
    {
        $sorted = self::sortCollection($etapas);
        $groups = [];

        foreach ($sorted as $etapa) {
            if (self::isTopLevel($etapa->ordem) || empty($groups)) {
                $groups[] = ['etapa' => $etapa, 'children' => []];
                continue;
            }

            $groups[count($groups) - 1]['children'][] = $etapa;
        }

        return $groups;
    }

    /**
     * @return array<int, array{etapa: mixed|null, items: array<int, mixed>}>
     */
    public static function groupPropostaItems(Collection $items): array
    {
        $sorted = self::sortCollection($items, 'ordem');
        $groups = [];
        $current = null;

        foreach ($sorted as $item) {
            if ($item->is_etapa) {
                $current = ['etapa' => $item, 'items' => []];
                $groups[] = $current;
                continue;
            }

            if ($current === null) {
                $groups[] = ['etapa' => null, 'items' => [$item]];
            } else {
                $groups[count($groups) - 1]['items'][] = $item;
            }
        }

        return $groups;
    }
}
