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

test('cliente nao acessa gestao de usuarios', function () {
    $cliente = User::factory()->create(['role' => 'cliente']);

    $this->actingAs($cliente)
        ->get(route('users.index'))
        ->assertForbidden();
});
