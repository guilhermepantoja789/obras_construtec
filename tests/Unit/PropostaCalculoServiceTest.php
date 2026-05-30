<?php

use App\Services\PropostaCalculoService;

test('calculates total with active encargos', function () {
    $items = [
        ['quantidade' => 10, 'valor_unitario' => 100],
    ];

    $encargos = [
        ['key' => 'bdi', 'label' => 'BDI', 'ativo' => true, 'percent' => 25, 'subtrai' => false],
    ];

    $totais = PropostaCalculoService::calcularTotais($items, $encargos);

    expect($totais['subtotal_itens'])->toBe(1000.0);
    expect($totais['valor_total'])->toBe(1250.0);
});

test('discount encargo subtracts from total', function () {
    $items = [['quantidade' => 1, 'valor_unitario' => 1000]];

    $encargos = [
        ['key' => 'desconto', 'label' => 'Desconto', 'ativo' => true, 'percent' => 10, 'subtrai' => true],
    ];

    $totais = PropostaCalculoService::calcularTotais($items, $encargos);

    expect($totais['valor_total'])->toBe(900.0);
});
