<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ObraController extends Controller
{
    public function index()
    {
        $query = Obra::withCount('users')->latest();
        
        if (!auth()->user()->isChefe()) {
            $query->whereHas('users', function($q) {
                $q->where('users.id', auth()->id());
            });
        }

        $obras = $query->get();
        $chefesCount = \App\Models\User::where('role', 'chefe')->count();
        return view('obras.index', compact('obras', 'chefesCount'));
    }

    public function create()
    {
        return view('obras.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateObra($request);

        Obra::create($this->prepareObraData($validated));

        return redirect()->route('obras.index')->with('success', 'Obra criada com sucesso!');
    }

    public function show(Obra $obra)
    {
        if (!auth()->user()->isChefe() && !auth()->user()->obras->contains($obra->id)) {
            abort(403);
        }

        $obra->load(['users', 'diarioPosts', 'etapas', 'propostas', 'diarioReports' => function($query) {
            $query->orderBy('data_relatorio', 'desc');
        }]);
        $chefes = \App\Models\User::where('role', 'chefe')->get();
        
        return view('obras.show', compact('obra', 'chefes'));
    }

    public function edit(Obra $obra)
    {
        return view('obras.edit', compact('obra'));
    }

    public function update(Request $request, Obra $obra)
    {
        $validated = $this->validateObra($request);

        $obra->update($this->prepareObraData($validated));

        return redirect()->route('obras.index')->with('success', 'Obra atualizada com sucesso!');
    }

    private function validateObra(Request $request): array
    {
        $request->merge($this->normalizeObraRequest($request));

        return $request->validate($this->obraRules(), $this->obraMessages());
    }

    private function obraRules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'localizacao' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:9',
            'logradouro' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'data_inicio' => 'nullable|date',
            'data_fim_prevista' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|string',
            'contratante' => 'nullable|string',
            'cnpj_contratante' => 'nullable|string|max:18',
            'empresa_contratada' => 'nullable|string',
            'cnpj_empresa_contratada' => 'nullable|string|max:18',
            'engenheiro_responsavel' => 'nullable|string',
            'prazo_dias' => 'nullable|integer|min:0',
        ];
    }

    private function obraMessages(): array
    {
        return [
            'data_fim_prevista.after_or_equal' => 'A previsão de término deve ser igual ou posterior à data de início.',
        ];
    }

    private function normalizeObraRequest(Request $request): array
    {
        $data = [];

        foreach (['data_inicio', 'data_fim_prevista'] as $field) {
            $value = $request->input($field);
            $data[$field] = ($value === null || $value === '') ? null : $value;
        }

        $prazo = $request->input('prazo_dias');
        $data['prazo_dias'] = ($prazo === null || $prazo === '') ? null : $prazo;

        return $data;
    }

    private function prepareObraData(array $validated): array
    {
        foreach (['data_inicio', 'data_fim_prevista'] as $field) {
            if (empty($validated[$field] ?? null)) {
                $validated[$field] = null;
            }
        }

        $prazo = $validated['prazo_dias'] ?? null;

        if ($prazo === null || $prazo === '') {
            if (! empty($validated['data_inicio']) && ! empty($validated['data_fim_prevista'])) {
                $prazo = (int) Carbon::parse($validated['data_inicio'])
                    ->startOfDay()
                    ->diffInDays(Carbon::parse($validated['data_fim_prevista'])->startOfDay());
            } else {
                $prazo = 0;
            }
        }

        $validated['prazo_dias'] = max(0, (int) $prazo);

        return $validated;
    }

    public function destroy(Obra $obra)
    {
        $obra->delete();
        return redirect()->route('obras.index')->with('success', 'Obra excluída com sucesso!');
    }
}
