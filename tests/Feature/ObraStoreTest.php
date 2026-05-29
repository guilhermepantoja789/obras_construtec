<?php

use App\Models\Obra;
use App\Models\User;

test('chefe can create obra without expected end date', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);

    $response = $this->actingAs($chefe)->post(route('obras.store'), [
        'nome' => 'Obra Sem Prazo',
        'status' => 'planejamento',
        'data_inicio' => '2026-01-15',
        'data_fim_prevista' => '',
        'prazo_dias' => '',
    ]);

    $response->assertRedirect(route('obras.index'));
    $response->assertSessionHas('success');

    $obra = Obra::first();
    expect($obra->nome)->toBe('Obra Sem Prazo');
    expect($obra->data_inicio?->format('Y-m-d'))->toBe('2026-01-15');
    expect($obra->data_fim_prevista)->toBeNull();
    expect($obra->prazo_dias)->toBe(0);
});

test('chefe cannot set end date before start date', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);

    $response = $this->actingAs($chefe)->post(route('obras.store'), [
        'nome' => 'Obra Datas Invalidas',
        'status' => 'planejamento',
        'data_inicio' => '2026-06-01',
        'data_fim_prevista' => '2026-01-01',
    ]);

    $response->assertSessionHasErrors('data_fim_prevista');
    expect(Obra::count())->toBe(0);
});

test('chefe can create obra with both dates and auto prazo', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);

    $response = $this->actingAs($chefe)->post(route('obras.store'), [
        'nome' => 'Obra Com Prazo',
        'status' => 'em_andamento',
        'data_inicio' => '2026-01-01',
        'data_fim_prevista' => '2026-07-01',
    ]);

    $response->assertRedirect(route('obras.index'));

    $obra = Obra::first();
    expect($obra->data_fim_prevista?->format('Y-m-d'))->toBe('2026-07-01');
    expect($obra->prazo_dias)->toBe(181);
});
