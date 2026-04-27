<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\PropostaItem;
use App\Models\EtapaObra;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropostaController extends Controller
{
    public function index()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) return redirect()->route('obras.index');

        $propostas = Proposta::where('obra_id', $obraId)->latest()->get();
        return view('propostas.index', compact('propostas'));
    }

    public function create()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) return redirect()->route('obras.index');
        
        $obra = Obra::findOrFail($obraId);
        return view('propostas.create', compact('obra'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'escopo' => 'nullable|string',
            'data_proposta' => 'required|date',
            'status' => 'required|in:rascunho,enviada,aceita,recusada',
            'items' => 'required|array|min:1',
            'items.*.descricao' => 'required|string|max:255',
            'items.*.unidade' => 'nullable|string|max:20',
            'items.*.quantidade' => 'required|numeric|min:0',
            'items.*.valor_unitario' => 'required|numeric|min:0',
            'items.*.is_etapa' => 'nullable',
            'items.*.ordem' => 'nullable|integer',
        ]);

        $obraId = session('active_obra_id');

        DB::transaction(function () use ($validated, $obraId) {
            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['quantidade'] * $item['valor_unitario'];
            }

            $proposta = Proposta::create([
                'obra_id' => $obraId,
                'titulo' => $validated['titulo'],
                'escopo' => $validated['escopo'],
                'data_proposta' => $validated['data_proposta'],
                'valor_total' => $total,
                'status' => $validated['status'],
            ]);

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantidade'] * $itemData['valor_unitario'];
                $item = $proposta->items()->create(array_merge($itemData, [
                    'subtotal' => $subtotal,
                    'is_etapa' => isset($itemData['is_etapa']) && $itemData['is_etapa'] == '1',
                ]));

                // If it's an accepted proposal and item is an etapa, create EtapaObra
                if ($proposta->status === 'aceita' && $item->is_etapa) {
                    EtapaObra::create([
                        'obra_id' => $obraId,
                        'nome' => $item->descricao,
                        'valor' => $item->subtotal,
                        'descricao' => 'Gerado automaticamente via Proposta',
                        'ordem' => $item->ordem,
                        'status' => 'pendente',
                        'percentual_concluido' => 0,
                    ]);
                }
            }
        });

        return redirect()->route('obras.show', $obraId)->with('success', 'Proposta criada com sucesso!');
    }
    
    public function edit(Proposta $proposta)
    {
        $proposta->load('items');
        $obra = $proposta->obra;
        return view('propostas.edit', compact('proposta', 'obra'));
    }

    public function update(Request $request, Proposta $proposta)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'escopo' => 'nullable|string',
            'data_proposta' => 'required|date',
            'status' => 'required|in:rascunho,enviada,aceita,recusada',
            'items' => 'required|array|min:1',
            'items.*.descricao' => 'required|string|max:255',
            'items.*.unidade' => 'nullable|string|max:20',
            'items.*.quantidade' => 'required|numeric|min:0',
            'items.*.valor_unitario' => 'required|numeric|min:0',
            'items.*.is_etapa' => 'nullable',
            'items.*.ordem' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($validated, $proposta) {
            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['quantidade'] * $item['valor_unitario'];
            }

            $wasAlreadyAccepted = $proposta->status === 'aceita';

            $proposta->update([
                'titulo' => $validated['titulo'],
                'escopo' => $validated['escopo'],
                'data_proposta' => $validated['data_proposta'],
                'valor_total' => $total,
                'status' => $validated['status'],
            ]);

            // Simple approach: remove old items and create new ones
            $proposta->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantidade'] * $itemData['valor_unitario'];
                $item = $proposta->items()->create(array_merge($itemData, [
                    'subtotal' => $subtotal,
                    'is_etapa' => isset($itemData['is_etapa']) && $itemData['is_etapa'] == '1',
                ]));

                // If it just became accepted (or stayed accepted and we want to sync - though syncing is complex)
                // For now, only generate if it WAS NOT accepted and NOW IS.
                if (!$wasAlreadyAccepted && $proposta->status === 'aceita' && $item->is_etapa) {
                    EtapaObra::create([
                        'obra_id' => $proposta->obra_id,
                        'nome' => $item->descricao,
                        'valor' => $item->subtotal,
                        'descricao' => 'Gerado automaticamente via Proposta',
                        'ordem' => $item->ordem,
                        'status' => 'pendente',
                        'percentual_concluido' => 0,
                    ]);
                }
            }
        });

        return redirect()->route('propostas.show', $proposta)->with('success', 'Proposta atualizada com sucesso!');
    }

    public function show(Proposta $proposta)
    {
        $proposta->load('items');
        return view('propostas.show', compact('proposta'));
    }
}
