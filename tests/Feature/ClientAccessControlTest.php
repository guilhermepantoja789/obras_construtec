<?php

use App\Models\DiarioReport;
use App\Models\Obra;
use App\Models\User;

test('cliente nao acessa relatorio de obra sem vinculo', function () {
    $cliente = User::factory()->create(['role' => 'cliente']);
    $autor = User::factory()->create(['role' => 'operador']);

    $obraComAcesso = Obra::create(['nome' => 'Obra A', 'status' => 'em_andamento']);
    $obraSemAcesso = Obra::create(['nome' => 'Obra B', 'status' => 'em_andamento']);

    $cliente->obras()->attach($obraComAcesso->id);

    $report = DiarioReport::create([
        'obra_id' => $obraSemAcesso->id,
        'user_id' => $autor->id,
        'data_relatorio' => now()->toDateString(),
        'status_dia' => 'trabalhado',
    ]);

    $this->actingAs($cliente)
        ->get(route('diario-reports.show', $report))
        ->assertForbidden();

    $this->actingAs($cliente)
        ->get(route('diario-reports.pdf', $report))
        ->assertForbidden();
});

test('cliente acessa relatorio da propria obra', function () {
    $cliente = User::factory()->create(['role' => 'cliente']);
    $autor = User::factory()->create(['role' => 'operador']);

    $obra = Obra::create(['nome' => 'Obra Cliente', 'status' => 'em_andamento']);
    $cliente->obras()->attach($obra->id);

    $report = DiarioReport::create([
        'obra_id' => $obra->id,
        'user_id' => $autor->id,
        'data_relatorio' => now()->toDateString(),
        'status_dia' => 'trabalhado',
    ]);

    $this->actingAs($cliente)
        ->get(route('diario-reports.show', $report))
        ->assertOk();
});

test('cliente com multiplas obras acessa listagem de obras', function () {
    $cliente = User::factory()->create(['role' => 'cliente']);
    $obra1 = Obra::create(['nome' => 'Obra 1', 'status' => 'em_andamento']);
    $obra2 = Obra::create(['nome' => 'Obra 2', 'status' => 'em_andamento']);
    $obraSemVinculo = Obra::create(['nome' => 'Obra X', 'status' => 'em_andamento']);

    $cliente->obras()->attach([$obra1->id, $obra2->id]);

    $this->actingAs($cliente)
        ->get(route('obras.index'))
        ->assertOk()
        ->assertSee('Obra 1')
        ->assertSee('Obra 2')
        ->assertDontSee('Obra X');
});

test('cliente acessa detalhe da propria obra', function () {
    $cliente = User::factory()->create(['role' => 'cliente']);
    $obra = Obra::create(['nome' => 'Obra Cliente', 'status' => 'em_andamento']);
    $cliente->obras()->attach($obra->id);

    $this->actingAs($cliente)
        ->get(route('obras.show', $obra))
        ->assertOk();
});

test('cliente nao acessa detalhe de obra sem vinculo', function () {
    $cliente = User::factory()->create(['role' => 'cliente']);
    $obra = Obra::create(['nome' => 'Obra Alheia', 'status' => 'em_andamento']);

    $this->actingAs($cliente)
        ->get(route('obras.show', $obra))
        ->assertForbidden();
});

test('cliente nao acessa gestao de usuarios', function () {
    $cliente = User::factory()->create(['role' => 'cliente']);

    $this->actingAs($cliente)
        ->get(route('users.index'))
        ->assertForbidden();
});
