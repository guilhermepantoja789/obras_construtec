<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use Illuminate\Http\Request;

class ObraController extends Controller
{
    public function index()
    {
        $obras = Obra::withCount('users')->latest()->get();
        $chefesCount = \App\Models\User::where('role', 'chefe')->count();
        return view('obras.index', compact('obras', 'chefesCount'));
    }

    public function create()
    {
        return view('obras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'localizacao' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:9',
            'logradouro' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'data_inicio' => 'nullable|date',
            'data_fim_prevista' => 'nullable|date',
            'status' => 'required|string',
            'contratante' => 'nullable|string',
            'cnpj_contratante' => 'nullable|string|max:18',
            'empresa_contratada' => 'nullable|string',
            'cnpj_empresa_contratada' => 'nullable|string|max:18',
            'engenheiro_responsavel' => 'nullable|string',
            'prazo_dias' => 'nullable|integer',
        ]);

        Obra::create($validated);

        return redirect()->route('obras.index')->with('success', 'Obra criada com sucesso!');
    }

    public function show(Obra $obra)
    {
        $obra->load(['users', 'diarioPosts', 'etapas', 'propostas']);
        $chefes = \App\Models\User::where('role', 'chefe')->get();
        
        return view('obras.show', compact('obra', 'chefes'));
    }

    public function edit(Obra $obra)
    {
        return view('obras.edit', compact('obra'));
    }

    public function update(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'localizacao' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:9',
            'logradouro' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'data_inicio' => 'nullable|date',
            'data_fim_prevista' => 'nullable|date',
            'status' => 'required|string',
            'contratante' => 'nullable|string',
            'cnpj_contratante' => 'nullable|string|max:18',
            'empresa_contratada' => 'nullable|string',
            'cnpj_empresa_contratada' => 'nullable|string|max:18',
            'engenheiro_responsavel' => 'nullable|string',
            'prazo_dias' => 'nullable|integer',
        ]);

        $obra->update($validated);

        return redirect()->route('obras.index')->with('success', 'Obra atualizada com sucesso!');
    }

    public function destroy(Obra $obra)
    {
        $obra->delete();
        return redirect()->route('obras.index')->with('success', 'Obra excluída com sucesso!');
    }
}
