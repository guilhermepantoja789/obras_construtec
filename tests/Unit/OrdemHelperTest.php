<?php

use App\Support\OrdemHelper;

test('ordem helper sorts hierarchical numbers correctly', function () {
    $items = collect([
        (object) ['ordem' => '1.10'],
        (object) ['ordem' => '1.2'],
        (object) ['ordem' => '1.1.2'],
        (object) ['ordem' => '1.1'],
        (object) ['ordem' => '2'],
        (object) ['ordem' => '10'],
    ]);

    $sorted = OrdemHelper::sortCollection($items)->pluck('ordem')->all();

    expect($sorted)->toBe(['1.1', '1.1.2', '1.2', '1.10', '2', '10']);
});

test('ordem helper groups etapas under top level parents', function () {
    $etapas = collect([
        (object) ['ordem' => '1', 'nome' => 'Fase 1'],
        (object) ['ordem' => '1.1', 'nome' => 'Sub 1'],
        (object) ['ordem' => '2', 'nome' => 'Fase 2'],
    ]);

    $groups = OrdemHelper::groupEtapas($etapas);

    expect($groups)->toHaveCount(2);
    expect($groups[0]['children'])->toHaveCount(1);
    expect($groups[0]['children'][0]->nome)->toBe('Sub 1');
});
