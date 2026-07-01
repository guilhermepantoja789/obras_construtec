<?php

namespace App\Http\Controllers;

use App\Models\Empreiteira;
use App\Models\Obra;
use Illuminate\Http\Request;

class EmpreiteiraController extends Controller
{
    public function index()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index');
        }

        $obra = Obra::findOrFail($obraId);
        $empreiteiras = Empreiteira::where('obra_id', $obraId)
            ->withSum(['despesas as valor_pago_sum' => fn ($q) => $q->where('status', 'pago')], 'valor')
            ->withSum(['despesas as valor_pendente_sum' => fn ($q) => $q->where('status', 'pendente')], 'valor')
            ->orderBy('nome')
            ->get();

        return view('empreiteiras.index', [
            'obra' => $obra,
            'empreiteiras' => $empreiteiras,
        ]);
    }

    public function show(Empreiteira $empreiteira)
    {
        $obraId = session('active_obra_id');
        if (!$obraId || $empreiteira->obra_id != $obraId) {
            abort(403);
        }

        $empreiteira->load(['despesas' => fn ($q) => $q->with('anexos')->orderByDesc('data')->orderByDesc('id')]);

        return view('empreiteiras.show', [
            'obra' => $empreiteira->obra,
            'empreiteira' => $empreiteira,
        ]);
    }

    public function store(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return back()->with('error', 'Selecione uma obra primeiro.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'valor_acordado' => 'required|numeric|min:0.01',
            'servico' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:50',
            'observacao' => 'nullable|string',
        ]);

        Empreiteira::create([
            ...$validated,
            'obra_id' => $obraId,
        ]);

        return back()->with('success', 'Empreiteira cadastrada!');
    }

    public function update(Request $request, Empreiteira $empreiteira)
    {
        $obraId = session('active_obra_id');
        if (!$obraId || $empreiteira->obra_id != $obraId) {
            abort(403);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'valor_acordado' => 'required|numeric|min:0.01',
            'servico' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:50',
            'observacao' => 'nullable|string',
        ]);

        $empreiteira->update($validated);

        return back()->with('success', 'Empreiteira atualizada!');
    }

    public function destroy(Empreiteira $empreiteira)
    {
        $obraId = session('active_obra_id');
        if (!$obraId || $empreiteira->obra_id != $obraId) {
            abort(403);
        }

        $empreiteira->delete();

        return redirect()->route('empreiteiras.index')->with('success', 'Empreiteira removida.');
    }
}
