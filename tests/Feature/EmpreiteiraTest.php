<?php

use App\Models\DespesaObra;
use App\Models\Empreiteira;
use App\Models\Obra;
use App\Models\User;

test('chefe cadastra empreiteira com valor acordado', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Teste', 'status' => 'em_andamento']);
    $chefe->obras()->attach($obra->id);

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('empreiteiras.store'), [
            'nome' => 'João Pedreiro',
            'valor_acordado' => 15000.00,
            'servico' => 'Alvenaria',
            'telefone' => '(11) 99999-9999',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('empreiteiras', [
        'obra_id' => $obra->id,
        'nome' => 'João Pedreiro',
        'valor_acordado' => 15000.00,
        'servico' => 'Alvenaria',
    ]);
});

test('chefe vincula despesa paga a empreiteira e progresso atualiza', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Teste', 'status' => 'em_andamento']);
    $chefe->obras()->attach($obra->id);

    $empreiteira = Empreiteira::create([
        'obra_id' => $obra->id,
        'nome' => 'Maria Eletricista',
        'valor_acordado' => 10000.00,
    ]);

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('despesas.store'), [
            'valor' => 2500.00,
            'data' => now()->toDateString(),
            'descricao' => 'Primeira medição',
            'status' => 'pago',
            'empreiteira_id' => $empreiteira->id,
        ])
        ->assertRedirect();

    $empreiteira->refresh();
    expect($empreiteira->valor_pago)->toBe(2500.0);
    expect($empreiteira->percentual_pago)->toBe(25.0);
    expect($empreiteira->saldo_restante)->toBe(7500.0);
    expect($empreiteira->status_pagamento)->toBe('em_andamento');

    $this->assertDatabaseHas('despesa_obras', [
        'obra_id' => $obra->id,
        'empreiteira_id' => $empreiteira->id,
        'valor' => 2500.00,
        'status' => 'pago',
    ]);
});

test('despesa pendente nao conta no progresso da empreiteira', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Teste', 'status' => 'em_andamento']);
    $chefe->obras()->attach($obra->id);

    $empreiteira = Empreiteira::create([
        'obra_id' => $obra->id,
        'nome' => 'Pedreiro',
        'valor_acordado' => 5000.00,
    ]);

    DespesaObra::create([
        'obra_id' => $obra->id,
        'empreiteira_id' => $empreiteira->id,
        'valor' => 1000.00,
        'data' => now(),
        'descricao' => 'Pendente',
        'status' => 'pendente',
    ]);

    $empreiteira->refresh();
    expect($empreiteira->valor_pago)->toBe(0.0);
    expect($empreiteira->valor_pendente)->toBe(1000.0);
});

test('chefe acessa listagem e detalhe de empreiteiras', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Empreiteiras', 'status' => 'em_andamento']);
    $chefe->obras()->attach($obra->id);

    $empreiteira = Empreiteira::create([
        'obra_id' => $obra->id,
        'nome' => 'Empreiteira A',
        'valor_acordado' => 8000.00,
    ]);

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->get(route('empreiteiras.index'))
        ->assertOk()
        ->assertSee('Empreiteira A');

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->get(route('empreiteiras.show', $empreiteira))
        ->assertOk()
        ->assertSee('Progresso de pagamento')
        ->assertSee('R$ 8.000,00');
});

test('nao vincula despesa a empreiteira de outra obra', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra1 = Obra::create(['nome' => 'Obra 1', 'status' => 'em_andamento']);
    $obra2 = Obra::create(['nome' => 'Obra 2', 'status' => 'em_andamento']);
    $chefe->obras()->attach([$obra1->id, $obra2->id]);

    $empreiteiraOutraObra = Empreiteira::create([
        'obra_id' => $obra2->id,
        'nome' => 'Outra Obra',
        'valor_acordado' => 3000.00,
    ]);

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra1->id])
        ->post(route('despesas.store'), [
            'valor' => 500.00,
            'data' => now()->toDateString(),
            'descricao' => 'Tentativa inválida',
            'status' => 'pago',
            'empreiteira_id' => $empreiteiraOutraObra->id,
        ])
        ->assertForbidden();
});

test('operador nao acessa empreiteiras', function () {
    $operador = User::factory()->create(['role' => 'operador']);
    $obra = Obra::create(['nome' => 'Obra Teste', 'status' => 'em_andamento']);
    $operador->obras()->attach($obra->id);

    $this->actingAs($operador)
        ->withSession(['active_obra_id' => $obra->id])
        ->get(route('empreiteiras.index'))
        ->assertForbidden();
});
