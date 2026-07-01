<?php

use App\Models\Obra;
use App\Models\User;

function obraPayload(array $overrides = []): array
{
    return array_merge([
        'nome' => 'Obra Teste',
        'status' => 'em_andamento',
        'data_inicio' => '2026-01-01',
        'data_fim_prevista' => '2026-07-01',
        'prazo_dias' => '',
    ], $overrides);
}

test('chefe can update obra prazo manualmente', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create([
        'nome' => 'Obra Original',
        'status' => 'em_andamento',
        'data_inicio' => '2026-01-01',
        'data_fim_prevista' => '2026-07-01',
        'prazo_dias' => 181,
    ]);

    $response = $this->actingAs($chefe)->patch(route('obras.update', $obra), obraPayload([
        'nome' => 'Obra Atualizada',
        'prazo_dias' => '200',
    ]));

    $response->assertRedirect(route('obras.index'));
    $response->assertSessionHas('success');

    $obra->refresh();
    expect($obra->nome)->toBe('Obra Atualizada');
    expect($obra->prazo_dias)->toBe(200);
});

test('chefe can clear prazo and keep zero when obra has no dates', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create([
        'nome' => 'Obra Sem Datas',
        'status' => 'planejamento',
        'prazo_dias' => 120,
    ]);

    $response = $this->actingAs($chefe)->patch(route('obras.update', $obra), obraPayload([
        'nome' => 'Obra Sem Datas',
        'data_inicio' => '',
        'data_fim_prevista' => '',
        'prazo_dias' => '',
    ]));

    $response->assertRedirect(route('obras.index'));

    $obra->refresh();
    expect($obra->data_inicio)->toBeNull();
    expect($obra->data_fim_prevista)->toBeNull();
    expect($obra->prazo_dias)->toBe(0);
});

test('chefe can update obra recalculating prazo from dates', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create([
        'nome' => 'Obra Com Datas',
        'status' => 'em_andamento',
        'data_inicio' => '2026-01-01',
        'data_fim_prevista' => '2026-03-01',
        'prazo_dias' => 59,
    ]);

    $response = $this->actingAs($chefe)->patch(route('obras.update', $obra), obraPayload([
        'data_inicio' => '2026-01-01',
        'data_fim_prevista' => '2026-07-01',
        'prazo_dias' => '',
    ]));

    $response->assertRedirect(route('obras.index'));

    $obra->refresh();
    expect($obra->data_fim_prevista?->format('Y-m-d'))->toBe('2026-07-01');
    expect($obra->prazo_dias)->toBe(181);
});
