<?php

namespace App\Http\Controllers;

use App\Models\EtapaObra;
use App\Models\Obra;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class EtapaObraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index')->with('info', 'Selecione uma obra para ver o cronograma.');
        }

        $obra = Obra::findOrFail($obraId);
        $etapas = EtapaObra::where('obra_id', $obraId)
            ->orderBy('ordem')
            ->get();

        return view('etapa-obras.index', compact('obra', 'etapas'));
    }

    /**
     * Financial view.
     */
    public function financeiro()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) return redirect()->route('obras.index');

        $obra = Obra::with('propostas')->findOrFail($obraId);
        $proposta = $obra->propostas->where('status', 'aceita')->first() 
                    ?? $obra->propostas->first();

        $pagamentos = $proposta ? $proposta->pagamentos()->orderBy('data_pagamento', 'desc')->get() : collect();

        return view('financeiro.index', compact('obra', 'proposta', 'pagamentos'));
    }

    /**
     * Store a newly created payment.
     */
    public function storePagamento(Request $request)
    {
        $validated = $request->validate([
            'proposta_id' => 'required|exists:propostas,id',
            'valor_pago' => 'required|numeric|min:0',
            'data_pagamento' => 'required|date',
            'observacao' => 'nullable|string',
        ]);

        Pagamento::create($validated);

        return back()->with('success', 'Pagamento registrado!');
    }

    /**
     * Remove a payment.
     */
    public function destroyPagamento(Pagamento $pagamento)
    {
        $pagamento->delete();
        return back()->with('success', 'Pagamento removido.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return back()->with('error', 'Selecione uma obra primeiro.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'descricao' => 'nullable|string',
            'data_inicio_prevista' => 'nullable|date',
            'data_fim_prevista' => 'nullable|date',
            'ordem' => 'nullable|string',
        ]);

        $validated['descricao'] = $validated['descricao'] ?? $validated['nome'];

        EtapaObra::create(array_merge($validated, ['obra_id' => $obraId]));

        return back()->with('success', 'Etapa adicionada ao cronograma!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EtapaObra $etapaObra)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'descricao' => 'nullable|string',
            'data_inicio_prevista' => 'nullable|date',
            'data_fim_prevista' => 'nullable|date',
            'data_inicio_real' => 'nullable|date',
            'data_fim_real' => 'nullable|date',
            'percentual_concluido' => 'nullable|integer|min:0|max:100',
            'status' => 'nullable|in:pendente,em_progresso,concluida,atrasada',
            'ordem' => 'nullable|string',
        ]);

        $validated['descricao'] = $validated['descricao'] ?? $validated['nome'];

        $etapaObra->update($validated);

        return back()->with('success', 'Etapa atualizada!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EtapaObra $etapaObra)
    {
        $etapaObra->delete();
        return back()->with('success', 'Etapa removida.');
    }
}
