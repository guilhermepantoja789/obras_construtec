<?php

use App\Models\EtapaObra;
use App\Models\Obra;
use App\Models\User;

function chefeComObraAtiva(): array
{
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Cronograma', 'status' => 'em_andamento']);

    return [$chefe, $obra];
}

test('chefe can create etapa with nome only and descricao defaults to nome', function () {
    [$chefe, $obra] = chefeComObraAtiva();

    $response = $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('etapa-obras.store'), [
            'nome' => 'Fundação',
            'valor' => 0,
            'ordem' => '1',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $etapa = EtapaObra::first();
    expect($etapa)->not->toBeNull();
    expect($etapa->nome)->toBe('Fundação');
    expect($etapa->descricao)->toBe('Fundação');
    expect($etapa->obra_id)->toBe($obra->id);
});

test('chefe cannot create etapa without active obra', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);

    $response = $this->actingAs($chefe)
        ->post(route('etapa-obras.store'), [
            'nome' => 'Fundação',
            'valor' => 0,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(EtapaObra::count())->toBe(0);
});

test('etapa store requires nome', function () {
    [$chefe, $obra] = chefeComObraAtiva();

    $response = $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('etapa-obras.store'), [
            'nome' => '',
            'valor' => 0,
        ]);

    $response->assertSessionHasErrors('nome');
    expect(EtapaObra::count())->toBe(0);
});
