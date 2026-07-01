<?php

use App\Models\EtapaObra;
use App\Models\Obra;
use App\Models\Proposta;
use App\Models\PropostaItem;
use App\Models\User;
use App\Services\EtapaObraSyncService;

test('regenerate removes proposta etapas and recreates from items', function () {
    $chefe = User::factory()->create(['role' => 'chefe']);
    $obra = Obra::create(['nome' => 'Obra Regen', 'status' => 'em_andamento']);

    $proposta = Proposta::create([
        'obra_id' => $obra->id,
        'titulo' => 'P1',
        'data_proposta' => now(),
        'subtotal_itens' => 1000,
        'valor_total' => 1000,
        'status' => 'aceita',
    ]);

    $item = PropostaItem::create([
        'proposta_id' => $proposta->id,
        'descricao' => 'FUNDAÇÃO',
        'quantidade' => 1,
        'valor_unitario' => 1000,
        'subtotal' => 1000,
        'is_etapa' => true,
        'ordem' => '1',
    ]);

    EtapaObra::create([
        'obra_id' => $obra->id,
        'proposta_item_id' => $item->id,
        'nome' => 'FUNDAÇÃO',
        'descricao' => 'Gerado automaticamente via Proposta',
        'valor' => 1000,
        'ordem' => '1',
        'percentual_concluido' => 50,
        'status' => 'em_progresso',
    ]);

    $proposta->load('items');
    $result = EtapaObraSyncService::regenerateFromProposta($proposta);

    expect($result['removidas'])->toBe(1);
    expect($result['criadas'])->toBe(1);
    expect(EtapaObra::count())->toBe(1);

    $nova = EtapaObra::first();
    expect($nova->percentual_concluido)->toBe(0);
    expect($nova->status)->toBe('pendente');
});

test('sync does not overwrite manual etapa with same ordem', function () {
    $obra = Obra::create(['nome' => 'Obra Manual', 'status' => 'em_andamento']);

    EtapaObra::create([
        'obra_id' => $obra->id,
        'nome' => 'Etapa Manual',
        'descricao' => 'Criada manualmente',
        'valor' => 0,
        'ordem' => '1',
        'status' => 'pendente',
        'percentual_concluido' => 0,
    ]);

    $proposta = Proposta::create([
        'obra_id' => $obra->id,
        'titulo' => 'P2',
        'data_proposta' => now(),
        'subtotal_itens' => 500,
        'valor_total' => 500,
        'status' => 'aceita',
    ]);

    PropostaItem::create([
        'proposta_id' => $proposta->id,
        'descricao' => 'ALVENARIA',
        'quantidade' => 1,
        'valor_unitario' => 500,
        'subtotal' => 500,
        'is_etapa' => true,
        'ordem' => '1',
    ]);

    $proposta->load('items');
    EtapaObraSyncService::syncFromProposta($proposta);

    expect(EtapaObra::count())->toBe(2);
    expect(EtapaObra::where('descricao', 'Criada manualmente')->exists())->toBeTrue();
    expect(EtapaObra::where('nome', 'ALVENARIA')->exists())->toBeTrue();
});
