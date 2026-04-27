<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\DiarioPost;
use App\Models\EtapaObra;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $obraId = session('active_obra_id');

        if (!$obraId) {
            // Se não houver obra ativa, redireciona para a listagem de obras
            return redirect()->route('obras.index')->with('info', 'Por favor, selecione uma obra para visualizar o painel.');
        }

        $obra = Obra::with(['users', 'etapas', 'propostas'])->findOrFail($obraId);

        // Estatísticas da Obra
        $stats = [
            'total_posts' => DiarioPost::where('obra_id', $obraId)->count(),
            'today_posts' => DiarioPost::where('obra_id', $obraId)->whereDate('data_postagem', Carbon::today())->count(),
            'equipe_count' => $obra->users()->count(),
            'progresso_geral' => $obra->etapas()->avg('percentual_concluido') ?? 0,
            'etapas_concluidas' => $obra->etapas()->where('status', 'concluida')->count(),
            'total_etapas' => $obra->etapas()->count(),
        ];

        // Financeiro simplificado
        $proposta = $obra->propostas->where('status', 'aceita')->first() ?? $obra->propostas->first();
        $financeiro = [
            'valor_total' => $proposta ? $proposta->valor_total : 0,
            'valor_pago' => $proposta ? $proposta->pagamentos()->sum('valor_pago') : 0,
        ];
        $financeiro['saldo_devedor'] = $financeiro['valor_total'] - $financeiro['valor_pago'];
        $financeiro['percentual_pago'] = $financeiro['valor_total'] > 0 
            ? ($financeiro['valor_pago'] / $financeiro['valor_total']) * 100 
            : 0;

        // Próximas etapas (não concluídas)
        $proximas_etapas = $obra->etapas()
            ->where('status', '!=', 'concluida')
            ->orderBy('ordem')
            ->take(3)
            ->get();

        // Posts recentes
        $recent_posts = DiarioPost::where('obra_id', $obraId)
            ->with('user')
            ->latest('data_postagem')
            ->take(5)
            ->get();

        return view('dashboard', compact('obra', 'stats', 'financeiro', 'proximas_etapas', 'recent_posts'));
    }
}
