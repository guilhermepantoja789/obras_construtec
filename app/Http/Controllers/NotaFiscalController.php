<?php

namespace App\Http\Controllers;

use App\Models\NotaFiscal;
use App\Models\Obra;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotaFiscalController extends Controller
{
    public function index(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index')->with('error', 'Selecione uma obra primeiro.');
        }

        $obra = Obra::findOrFail($obraId);

        [$periodoAtivo, $dataInicio, $dataFim] = $this->resolvePeriod($request);

        session(['nota_fiscals_filters' => [
            'periodo' => $periodoAtivo,
            'data_inicio' => $periodoAtivo === 'custom' ? $dataInicio->toDateString() : null,
            'data_fim' => $periodoAtivo === 'custom' ? $dataFim->toDateString() : null,
        ]]);

        $notas = NotaFiscal::where('obra_id', $obraId)
            ->whereDate('data_recebimento', '>=', $dataInicio)
            ->whereDate('data_recebimento', '<=', $dataFim)
            ->orderBy('data_recebimento', 'desc')
            ->get();

        $totalValor = $notas->sum('valor');
        $totalCount = $notas->count();
        $totalNotasObra = NotaFiscal::where('obra_id', $obraId)->count();

        return view('nota-fiscals.index', compact(
            'obra',
            'notas',
            'totalValor',
            'totalCount',
            'totalNotasObra',
            'dataInicio',
            'dataFim',
            'periodoAtivo'
        ));
    }

    public function store(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return back()->with('error', 'Selecione uma obra primeiro.');
        }

        $validated = $request->validate([
            'numero_nota' => 'required|string|max:255',
            'descricao' => 'required|string|max:255',
            'data_recebimento' => 'required|date',
            'valor' => 'required|numeric|min:0',
            'quem_recebeu' => 'required|string|max:255',
            'arquivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
            'observacao' => 'nullable|string',
        ]);

        $data = $validated;
        $data['obra_id'] = $obraId;

        if ($request->hasFile('arquivo')) {
            $path = $request->file('arquivo')->store('notas_fiscais', 'public');
            $data['arquivo_path'] = $path;
        }

        NotaFiscal::create($data);

        return redirect()->route('nota-fiscals.index', $this->filterParams())
            ->with('success', 'Nota fiscal registrada com sucesso!');
    }

    public function destroy(NotaFiscal $notaFiscal)
    {
        if ($notaFiscal->arquivo_path) {
            Storage::disk('public')->delete($notaFiscal->arquivo_path);
        }

        $notaFiscal->delete();

        return redirect()->route('nota-fiscals.index', $this->filterParams())
            ->with('success', 'Nota fiscal removida com sucesso!');
    }

    private function resolvePeriod(Request $request): array
    {
        $periodo = $request->input('periodo', 'este_mes');
        $validPeriodos = ['este_mes', 'mes_anterior', 'ultimos_30', 'custom'];

        if (!in_array($periodo, $validPeriodos)) {
            $periodo = 'este_mes';
        }

        $now = Carbon::now();

        switch ($periodo) {
            case 'mes_anterior':
                $dataInicio = $now->copy()->subMonth()->startOfMonth();
                $dataFim = $now->copy()->subMonth()->endOfMonth();
                break;

            case 'ultimos_30':
                $dataInicio = $now->copy()->subDays(29)->startOfDay();
                $dataFim = $now->copy()->endOfDay();
                break;

            case 'custom':
                if ($request->filled('data_inicio') && $request->filled('data_fim')) {
                    $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
                    $dataFim = Carbon::parse($request->data_fim)->endOfDay();

                    if ($dataInicio->gt($dataFim)) {
                        [$dataInicio, $dataFim] = [$dataFim->copy()->startOfDay(), $dataInicio->copy()->endOfDay()];
                    }
                } else {
                    $periodo = 'este_mes';
                    $dataInicio = $now->copy()->startOfMonth();
                    $dataFim = $now->copy()->endOfMonth();
                }
                break;

            default:
                $dataInicio = $now->copy()->startOfMonth();
                $dataFim = $now->copy()->endOfMonth();
                break;
        }

        return [$periodo, $dataInicio, $dataFim];
    }

    private function filterParams(): array
    {
        $filters = session('nota_fiscals_filters', ['periodo' => 'este_mes']);

        return array_filter([
            'periodo' => $filters['periodo'] ?? 'este_mes',
            'data_inicio' => $filters['data_inicio'] ?? null,
            'data_fim' => $filters['data_fim'] ?? null,
        ]);
    }
}
