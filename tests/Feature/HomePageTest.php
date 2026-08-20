<?php

use App\Models\Obra;
use App\Models\User;

test('landing page is public and links to login', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Construtec');
    $response->assertSee('Área do cliente');
    $response->assertSee(route('login'), false);
});

test('authenticated chefe sees dashboard cta on landing', function () {
    $user = User::factory()->create(['role' => 'chefe']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Abrir sistema');
});

test('chefe can access dashboard route under app prefix', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create([
        'nome' => 'Obra Dashboard',
        'status' => 'em_andamento',
    ]);

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->get('/app/dashboard')
        ->assertOk()
        ->assertSee($obra->nome);
});

test('chefe can open obras create form', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);

    $this->actingAs($chefe)
        ->get(route('obras.create'))
        ->assertOk();
});
