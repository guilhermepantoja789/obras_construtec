<?php

namespace App\Http\Controllers;

use App\Models\EtapaObra;
use App\Models\Obra;
use App\Models\Proposta;
use App\Services\EtapaObraSyncService;
use App\Support\OrdemHelper;
use Illuminate\Http\Request;

class EtapaObraController extends Controller
{
    public function index()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index')->with('info', 'Selecione uma obra para ver o cronograma.');
        }

        $obra = Obra::findOrFail($obraId);
        $etapas = OrdemHelper::sortCollection(
            EtapaObra::where('obra_id', $obraId)->get()
        );

        $grupos = OrdemHelper::groupEtapas($etapas);
        $progressoGeral = EtapaObraSyncService::calcularProgressoPonderado($etapas);

        $propostaAceita = Proposta::where('obra_id', $obraId)
            ->where('status', 'aceita')
            ->latest()
            ->first();

        return view('etapa-obras.index', compact('obra', 'etapas', 'grupos', 'progressoGeral', 'propostaAceita'));
    }

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
        $validated = (new EtapaObra())->applyAutoStatus($validated);

        EtapaObra::create(array_merge($validated, ['obra_id' => $obraId]));

        return back()->with('success', 'Etapa adicionada ao cronograma!');
    }

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
        $validated = $etapaObra->applyAutoStatus($validated);

        $etapaObra->update($validated);

        return back()->with('success', 'Etapa atualizada!');
    }

    public function destroy(EtapaObra $etapaObra)
    {
        $etapaObra->delete();
        return back()->with('success', 'Etapa removida.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array|min:1',
            'order.*' => 'integer|exists:etapa_obras,id',
        ]);

        $obraId = session('active_obra_id');

        foreach ($validated['order'] as $index => $id) {
            $etapa = EtapaObra::findOrFail($id);

            if ($obraId && (int) $etapa->obra_id !== (int) $obraId) {
                abort(403);
            }

            $etapa->update(['ordem' => (string) ($index + 1)]);
        }

        return back()->with('success', 'Ordem das etapas atualizada!');
    }

    public function regenerarFromProposta(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return back()->with('error', 'Selecione uma obra primeiro.');
        }

        $proposta = Proposta::where('obra_id', $obraId)
            ->where('status', 'aceita')
            ->latest()
            ->first();

        if (!$proposta) {
            return back()->with('error', 'Nenhuma proposta aceita encontrada para esta obra.');
        }

        $proposta->load('items');
        $resultado = EtapaObraSyncService::regenerateFromProposta($proposta);

        return back()->with(
            'success',
            "Cronograma regenerado: {$resultado['removidas']} removida(s), {$resultado['criadas']} criada(s)."
        );
    }
}
