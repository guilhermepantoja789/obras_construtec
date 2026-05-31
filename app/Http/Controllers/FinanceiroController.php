<?php

namespace App\Http\Controllers;

use App\Models\DespesaObra;
use App\Models\Empreiteira;
use App\Models\Obra;
use App\Models\Pagamento;
use App\Models\Proposta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class FinanceiroController extends Controller
{
    private const CATEGORIAS = [
        'material' => 'Material',
        'mao_de_obra' => 'Mão de obra',
        'equipamento' => 'Equipamento',
        'servico' => 'Serviço',
        'outros' => 'Outros',
    ];

    public function index(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index');
        }

        $filters = $this->resolveFilters($request);

        $obra = Obra::with('propostas')->findOrFail($obraId);
        $proposta = $obra->propostas->where('status', 'aceita')->first()
                    ?? $obra->propostas->first();

        $pagamentosBase = $proposta
            ? $proposta->pagamentos()->orderBy('data_pagamento', 'desc')->get()
            : collect();

        $despesasBase = DespesaObra::where('obra_id', $obraId)
            ->with('empreiteira:id,nome')
            ->orderBy('data', 'desc')
            ->get();

        [$pagamentos, $despesas] = $this->applyFilters($pagamentosBase, $despesasBase, $filters);

        $totalRecebidoGeral = $proposta
            ? $proposta->pagamentos()->sum('valor_pago')
            : 0;

        $lancamentos = $this->buildLancamentos($pagamentos, $despesas);
        $kpis = $this->buildKpis($pagamentos, $despesas, $lancamentos, $filters);

        $empreiteiras = Empreiteira::where('obra_id', $obraId)
            ->orderBy('nome')
            ->get(['id', 'nome', 'valor_acordado']);

        return view('financeiro.index', [
            'obra' => $obra,
            'proposta' => $proposta,
            'lancamentos' => $lancamentos,
            'kpis' => $kpis,
            'filters' => $filters,
            'totalRecebidoGeral' => $totalRecebidoGeral,
            'categorias' => self::CATEGORIAS,
            'empreiteiras' => $empreiteiras,
        ]);
    }

    public function comprovantePagamento(Pagamento $pagamento)
    {
        $obraId = session('active_obra_id');
        if (!$obraId || $pagamento->proposta->obra_id != $obraId) {
            abort(403);
        }

        return $this->serveComprovante($pagamento->comprovante_path);
    }

    public function comprovanteDespesa(DespesaObra $despesaObra)
    {
        $obraId = session('active_obra_id');
        if (!$obraId || $despesaObra->obra_id != $obraId) {
            abort(403);
        }

        return $this->serveComprovante($despesaObra->comprovante_path);
    }

    public function storePagamento(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return back()->with('error', 'Selecione uma obra primeiro.');
        }

        $validated = $request->validate([
            'proposta_id' => 'required|exists:propostas,id',
            'valor_pago' => 'required|numeric|min:0',
            'data_pagamento' => 'required|date',
            'forma_pagamento' => 'nullable|string|max:50',
            'observacao' => 'nullable|string',
            'comprovante' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ]);

        $proposta = Proposta::findOrFail($validated['proposta_id']);
        if ($proposta->obra_id != $obraId) {
            abort(403);
        }

        $data = collect($validated)->except('comprovante')->all();

        if ($request->hasFile('comprovante')) {
            $data['comprovante_path'] = $request->file('comprovante')
                ->store('comprovantes/pagamentos', 'public');
        }

        Pagamento::create($data);

        return back()->with('success', 'Recebimento registrado!');
    }

    public function destroyPagamento(Pagamento $pagamento)
    {
        $obraId = session('active_obra_id');
        if (!$obraId || $pagamento->proposta->obra_id != $obraId) {
            abort(403);
        }

        if ($pagamento->comprovante_path) {
            Storage::disk('public')->delete($pagamento->comprovante_path);
        }

        $pagamento->delete();

        return back()->with('success', 'Recebimento removido.');
    }

    public function storeDespesa(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return back()->with('error', 'Selecione uma obra primeiro.');
        }

        $validated = $request->validate([
            'valor' => 'required|numeric|min:0',
            'data' => 'required|date',
            'descricao' => 'required|string|max:255',
            'fornecedor' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|in:material,mao_de_obra,equipamento,servico,outros',
            'status' => 'required|in:pago,pendente',
            'forma_pagamento' => 'nullable|string|max:50',
            'empreiteira_id' => 'nullable|exists:empreiteiras,id',
            'observacao' => 'nullable|string',
            'comprovante' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ]);

        if (!empty($validated['empreiteira_id'])) {
            $empreiteira = Empreiteira::findOrFail($validated['empreiteira_id']);
            if ($empreiteira->obra_id != $obraId) {
                abort(403);
            }
        }

        $data = collect($validated)->except('comprovante')->all();
        $data['obra_id'] = $obraId;

        if ($request->hasFile('comprovante')) {
            $data['comprovante_path'] = $request->file('comprovante')
                ->store('comprovantes/despesas', 'public');
        }

        DespesaObra::create($data);

        return back()->with('success', 'Despesa registrada!');
    }

    public function destroyDespesa(DespesaObra $despesaObra)
    {
        $obraId = session('active_obra_id');
        if (!$obraId || $despesaObra->obra_id != $obraId) {
            abort(403);
        }

        if ($despesaObra->comprovante_path) {
            Storage::disk('public')->delete($despesaObra->comprovante_path);
        }

        $despesaObra->delete();

        return back()->with('success', 'Despesa removida.');
    }

    private function resolveFilters(Request $request): array
    {
        $validated = $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'periodo' => 'nullable|in:mes_atual,mes_anterior,30_dias,ano_atual',
            'tipo' => 'nullable|in:todos,recebida,paga,pendente',
            'categoria' => 'nullable|in:material,mao_de_obra,equipamento,servico,outros',
            'busca' => 'nullable|string|max:100',
        ]);

        $dataInicio = $validated['data_inicio'] ?? null;
        $dataFim = $validated['data_fim'] ?? null;
        $periodo = $validated['periodo'] ?? null;

        if ($periodo && !$dataInicio && !$dataFim) {
            [$dataInicio, $dataFim] = match ($periodo) {
                'mes_atual' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                'mes_anterior' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                '30_dias' => [now()->subDays(29)->toDateString(), now()->toDateString()],
                'ano_atual' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            };
        }

        return [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'periodo' => $periodo,
            'tipo' => $validated['tipo'] ?? 'todos',
            'categoria' => $validated['categoria'] ?? null,
            'busca' => trim($validated['busca'] ?? ''),
        ];
    }

    private function applyFilters(Collection $pagamentos, Collection $despesas, array $filters): array
    {
        $pagamentos = $pagamentos->filter(function ($p) use ($filters) {
            if ($filters['data_inicio'] && $p->data_pagamento->lt(Carbon::parse($filters['data_inicio'])->startOfDay())) {
                return false;
            }
            if ($filters['data_fim'] && $p->data_pagamento->gt(Carbon::parse($filters['data_fim'])->endOfDay())) {
                return false;
            }
            if ($filters['busca'] && ! $this->matchesBusca($p->observacao, $filters['busca'])) {
                return false;
            }

            return true;
        });

        $despesas = $despesas->filter(function ($d) use ($filters) {
            if ($filters['data_inicio'] && $d->data->lt(Carbon::parse($filters['data_inicio'])->startOfDay())) {
                return false;
            }
            if ($filters['data_fim'] && $d->data->gt(Carbon::parse($filters['data_fim'])->endOfDay())) {
                return false;
            }
            if ($filters['categoria'] && $d->categoria !== $filters['categoria']) {
                return false;
            }
            if ($filters['busca'] && ! $this->matchesBusca(
                implode(' ', array_filter([$d->descricao, $d->fornecedor, $d->observacao])),
                $filters['busca']
            )) {
                return false;
            }

            return true;
        });

        return match ($filters['tipo']) {
            'recebida' => [$pagamentos->values(), collect()],
            'paga' => [collect(), $despesas->where('status', 'pago')->values()],
            'pendente' => [collect(), $despesas->where('status', 'pendente')->values()],
            default => [$pagamentos->values(), $despesas->values()],
        };
    }

    private function matchesBusca(?string $text, string $busca): bool
    {
        if (!$text) {
            return false;
        }

        return str_contains(mb_strtolower($text), mb_strtolower($busca));
    }

    private function buildKpis(Collection $pagamentos, Collection $despesas, Collection $lancamentos, array $filters): array
    {
        $totalRecebido = $pagamentos->sum('valor_pago');
        $despesasPagas = $despesas->where('status', 'pago');
        $despesasPendentes = $despesas->where('status', 'pendente');
        $totalPago = $despesasPagas->sum('valor');
        $totalPendente = $despesasPendentes->sum('valor');
        $saldoOperacional = $totalRecebido - $totalPago;
        $totalMovimentado = $totalRecebido + $totalPago;

        $topCategoria = $despesasPagas
            ->filter(fn ($d) => $d->categoria)
            ->groupBy('categoria')
            ->map(fn ($items) => $items->sum('valor'))
            ->sortDesc()
            ->keys()
            ->first();

        $comComprovante = $lancamentos->filter(fn ($l) => !empty($l['comprovante_path']))->count();

        return [
            'total_recebido' => $totalRecebido,
            'total_pago' => $totalPago,
            'total_pendente' => $totalPendente,
            'saldo_operacional' => $saldoOperacional,
            'total_movimentado' => $totalMovimentado,
            'margem_percentual' => $totalRecebido > 0 ? round(($saldoOperacional / $totalRecebido) * 100, 1) : 0,
            'qtd_lancamentos' => $lancamentos->count(),
            'qtd_recebidas' => $pagamentos->count(),
            'qtd_despesas_pagas' => $despesasPagas->count(),
            'qtd_pendentes' => $despesasPendentes->count(),
            'media_recebimento' => $pagamentos->count() > 0 ? $totalRecebido / $pagamentos->count() : 0,
            'media_despesa' => $despesasPagas->count() > 0 ? $totalPago / $despesasPagas->count() : 0,
            'maior_recebimento' => $pagamentos->max('valor_pago') ?? 0,
            'maior_despesa' => $despesasPagas->max('valor') ?? 0,
            'com_comprovante' => $comComprovante,
            'sem_comprovante' => $lancamentos->count() - $comComprovante,
            'top_categoria' => $topCategoria,
            'top_categoria_label' => $topCategoria ? (self::CATEGORIAS[$topCategoria] ?? $topCategoria) : null,
            'top_categoria_valor' => $topCategoria
                ? $despesasPagas->where('categoria', $topCategoria)->sum('valor')
                : 0,
            'filtro_ativo' => $this->hasActiveFilters($filters),
        ];
    }

    private function hasActiveFilters(array $filters): bool
    {
        return ($filters['data_inicio'] || $filters['data_fim'] || $filters['periodo']
            || $filters['tipo'] !== 'todos' || $filters['categoria'] || $filters['busca']);
    }

    private function buildLancamentos($pagamentos, $despesas)
    {
        $items = collect();

        foreach ($pagamentos as $pagamento) {
            $items->push([
                'tipo' => 'recebida',
                'id' => $pagamento->id,
                'data' => $pagamento->data_pagamento,
                'valor' => $pagamento->valor_pago,
                'descricao' => $pagamento->observacao ?: 'Recebimento',
                'status' => 'recebido',
                'forma_pagamento' => $pagamento->forma_pagamento,
                'comprovante_path' => $pagamento->comprovante_path,
                'comprovante_url' => $pagamento->comprovante_path
                    ? route('pagamentos.comprovante', $pagamento)
                    : null,
                'model' => $pagamento,
            ]);
        }

        foreach ($despesas as $despesa) {
            $items->push([
                'tipo' => 'despesa',
                'id' => $despesa->id,
                'data' => $despesa->data,
                'valor' => $despesa->valor,
                'descricao' => $despesa->descricao,
                'status' => $despesa->status,
                'fornecedor' => $despesa->fornecedor,
                'categoria' => $despesa->categoria,
                'forma_pagamento' => $despesa->forma_pagamento,
                'comprovante_path' => $despesa->comprovante_path,
                'comprovante_url' => $despesa->comprovante_path
                    ? route('despesas.comprovante', $despesa)
                    : null,
                'empreiteira_id' => $despesa->empreiteira_id,
                'empreiteira_nome' => $despesa->empreiteira?->nome,
                'model' => $despesa,
            ]);
        }

        return $items
            ->sort(function ($a, $b) {
                $dateCompare = $b['data']->toDateString() <=> $a['data']->toDateString();
                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                $createdA = $a['model']->created_at?->timestamp ?? 0;
                $createdB = $b['model']->created_at?->timestamp ?? 0;
                if ($createdB !== $createdA) {
                    return $createdB <=> $createdA;
                }

                return $b['id'] <=> $a['id'];
            })
            ->values();
    }

    private function serveComprovante(?string $path)
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}
