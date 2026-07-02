<?php

use App\Models\NotaFiscal;
use App\Models\Obra;
use App\Models\User;

function operadorComObraAtivaParaNotaFiscal(): array
{
    $operador = User::factory()->create(['role' => 'operador']);
    $obra = Obra::create(['nome' => 'Obra NF', 'status' => 'em_andamento']);

    return [$operador, $obra];
}

test('operador exporta pdf de notas fiscais do periodo', function () {
    [$operador, $obra] = operadorComObraAtivaParaNotaFiscal();

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

test('operador altera data de nota fiscal', function () {
    [$operador, $obra] = operadorComObraAtivaParaNotaFiscal();

    $nota = NotaFiscal::create([
        'obra_id' => $obra->id,
        'numero_nota' => '99999',
        'descricao' => 'Ferragens',
        'valor' => 800.00,
        'quem_recebeu' => 'Maria',
        'data_recebimento' => now()->subMonths(2)->toDateString(),
        'observacao' => null,
    ]);

    $novaData = now()->subMonth()->toDateString();

    $this->actingAs($operador)
        ->withSession(['active_obra_id' => $obra->id])
        ->put(route('nota-fiscals.update', $nota), [
            'data_recebimento' => $novaData,
        ])
        ->assertRedirect(route('nota-fiscals.index'));

    expect($nota->fresh()->data_recebimento->toDateString())->toBe($novaData);
});

test('operador nao altera nota fiscal de outra obra', function () {
    [$operador, $obra] = operadorComObraAtivaParaNotaFiscal();
    $outraObra = Obra::create(['nome' => 'Outra Obra', 'status' => 'em_andamento']);

    $nota = NotaFiscal::create([
        'obra_id' => $outraObra->id,
        'numero_nota' => '11111',
        'descricao' => 'Tinta',
        'valor' => 200.00,
        'quem_recebeu' => 'Pedro',
        'data_recebimento' => now()->toDateString(),
    ]);

    $this->actingAs($operador)
        ->withSession(['active_obra_id' => $obra->id])
        ->put(route('nota-fiscals.update', $nota), [
            'data_recebimento' => now()->subWeek()->toDateString(),
        ])
        ->assertForbidden();
});
