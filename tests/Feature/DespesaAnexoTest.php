<?php

use App\Models\DespesaObra;
use App\Models\DespesaObraAnexo;
use App\Models\Obra;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('chefe anexa multiplos comprovantes em despesa', function () {
    Storage::fake('public');

    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Teste', 'status' => 'em_andamento']);
    $chefe->obras()->attach($obra->id);

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('despesas.store'), [
            'valor' => 500.00,
            'data' => now()->toDateString(),
            'descricao' => 'Compra materiais',
            'status' => 'pago',
            'comprovantes' => [
                UploadedFile::fake()->image('nota1.jpg'),
                UploadedFile::fake()->create('recibo.pdf', 100, 'application/pdf'),
            ],
        ])
        ->assertRedirect();

    $despesa = DespesaObra::first();
    expect($despesa)->not->toBeNull();
    expect($despesa->anexos)->toHaveCount(2);
    expect($despesa->comprovante_path)->not->toBeNull();

    Storage::disk('public')->assertExists($despesa->anexos[0]->path);
    Storage::disk('public')->assertExists($despesa->anexos[1]->path);
});

test('chefe registra despesa com categoria retirada', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Teste', 'status' => 'em_andamento']);
    $chefe->obras()->attach($obra->id);

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('despesas.store'), [
            'valor' => 200.00,
            'data' => now()->toDateString(),
            'descricao' => 'Retirada caixa',
            'status' => 'pago',
            'categoria' => 'retirada',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('despesa_obras', [
        'obra_id' => $obra->id,
        'categoria' => 'retirada',
        'descricao' => 'Retirada caixa',
    ]);
});

test('chefe acessa anexo individual da despesa', function () {
    Storage::fake('public');

    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Teste', 'status' => 'em_andamento']);
    $chefe->obras()->attach($obra->id);

    $path = UploadedFile::fake()->image('comprovante.jpg')->store('comprovantes/despesas', 'public');

    $despesa = DespesaObra::create([
        'obra_id' => $obra->id,
        'valor' => 100,
        'data' => now(),
        'descricao' => 'Teste',
        'status' => 'pago',
        'comprovante_path' => $path,
    ]);

    $anexo = DespesaObraAnexo::create([
        'despesa_obra_id' => $despesa->id,
        'path' => $path,
        'nome_original' => 'comprovante.jpg',
        'mime' => 'image/jpeg',
    ]);

    $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->get(route('despesas.anexo', [$despesa, $anexo]))
        ->assertOk();
});
