<?php

use App\Models\DiarioPost;
use App\Models\DiarioReport;
use App\Models\Obra;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function operadorComObraAtiva(): array
{
    $user = User::factory()->create(['role' => 'operador']);
    $obra = Obra::create(['nome' => 'Obra Teste']);
    $obra->users()->attach($user);

    return [$user, $obra];
}

test('operador can store diario post with photo', function () {
    Storage::fake('public');

    [$user, $obra] = operadorComObraAtiva();

    $response = $this->actingAs($user)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('diario-posts.store'), [
            'texto' => 'Progresso da fundação',
            'foto' => UploadedFile::fake()->image('obra.jpg', 800, 600),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(DiarioPost::count())->toBe(1);
    expect(DiarioPost::first())
        ->obra_id->toBe($obra->id)
        ->user_id->toBe($user->id)
        ->texto->toBe('Progresso da fundação');
});

test('diario post without photo is rejected', function () {
    [$user, $obra] = operadorComObraAtiva();

    $response = $this->actingAs($user)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('diario-posts.store'), [
            'texto' => 'Somente texto',
        ]);

    $response->assertSessionHasErrors('foto');
    expect(DiarioPost::count())->toBe(0);
});

test('operador cannot post when daily report is already issued', function () {
    Storage::fake('public');

    [$user, $obra] = operadorComObraAtiva();

    DiarioReport::create([
        'obra_id' => $obra->id,
        'user_id' => $user->id,
        'data_relatorio' => now()->toDateString(),
    ]);

    $response = $this->actingAs($user)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('diario-posts.store'), [
            'texto' => 'Tentativa após encerramento',
            'foto' => UploadedFile::fake()->image('obra.jpg'),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(DiarioPost::count())->toBe(0);
});

test('chefe can post even when daily report is already issued', function () {
    Storage::fake('public');

    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Chefe']);
    $obra->users()->attach($chefe);

    DiarioReport::create([
        'obra_id' => $obra->id,
        'user_id' => $chefe->id,
        'data_relatorio' => now()->toDateString(),
    ]);

    $response = $this->actingAs($chefe)
        ->withSession(['active_obra_id' => $obra->id])
        ->post(route('diario-posts.store'), [
            'texto' => 'Chefe pode postar',
            'foto' => UploadedFile::fake()->image('obra.jpg'),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect(DiarioPost::count())->toBe(1);
});
