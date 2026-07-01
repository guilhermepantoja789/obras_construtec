<?php

use App\Models\NotaFiscal;
use App\Models\Obra;
use App\Models\User;

function operadorComObraAtiva(): array
{
    $operador = User::factory()->create(['role' => 'operador']);
    $obra = Obra::create(['nome' => 'Obra NF', 'status' => 'em_andamento']);

    return [$operador, $obra];
}

test('operador exporta pdf de notas fiscais do periodo', function () {
    [$operador, $obra] = operadorComObraAtiva();

    NotaFiscal::create([
        'obra_id' => $obra->id,
        'numero_nota' => '12345',
        'descricao' => 'Material de construção',
        'valor' => 1500.50,
        'quem_recebeu' => 'João Silva',
        'data_recebimento' => now()->toDateString(),
        'observacao' => 'Entrega parcial',
    ]);

    $response = $this->actingAs($operador)
        ->withSession(['active_obra_id' => $obra->id])
        ->get(route('nota-fiscals.pdf', ['periodo' => 'este_mes']));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('cliente nao acessa exportacao pdf de notas fiscais', function () {
    $cliente = User::factory()->create(['role' => 'cliente']);
    $obra = Obra::create(['nome' => 'Obra Cliente', 'status' => 'em_andamento']);
    $cliente->obras()->attach($obra->id);

    $this->actingAs($cliente)
        ->withSession(['active_obra_id' => $obra->id])
        ->get(route('nota-fiscals.pdf'))
        ->assertForbidden();
});

test('exportacao pdf redireciona sem obra ativa', function () {
    $operador = User::factory()->create(['role' => 'operador']);

    $this->actingAs($operador)
        ->get(route('nota-fiscals.pdf'))
        ->assertRedirect(route('obras.index'));
});
