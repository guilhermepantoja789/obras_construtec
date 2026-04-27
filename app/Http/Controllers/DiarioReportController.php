<?php

namespace App\Http\Controllers;

use App\Models\DiarioReport;
use App\Models\DiarioPost;
use App\Models\Obra;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class DiarioReportController extends Controller
{
    public function create()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) return redirect()->route('obras.index');

        $obra = Obra::findOrFail($obraId);
        
        // Pre-fill services from today's posts
        $todayPosts = DiarioPost::where('obra_id', $obraId)
            ->whereDate('data_postagem', Carbon::today())
            ->pluck('texto')
            ->filter()
            ->implode("\n- ");

        if ($todayPosts) {
            $todayPosts = "- " . $todayPosts;
        }

        return view('diario.reports.create', compact('obra', 'todayPosts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'clima_horario' => 'nullable|array',
            'mao_de_obra' => 'nullable|array',
            'maquinario' => 'nullable|array',
            'servicos_iniciados' => 'nullable|string',
            'servicos_execucao' => 'nullable|string',
            'servicos_concluidos' => 'nullable|string',
            'materiais_recebidos' => 'nullable|string',
            'ocorrencias' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'motivo_paralisacao' => 'nullable|string',
            'dia_improdutivo' => 'nullable|boolean',
        ]);

        $obraId = session('active_obra_id');

        DiarioReport::updateOrCreate(
            ['obra_id' => $obraId, 'data_relatorio' => Carbon::today()],
            array_merge($validated, [
                'user_id' => auth()->id(),
                'dia_improdutivo' => $request->has('dia_improdutivo')
            ])
        );

        return redirect()->route('feed.index')->with('success', 'Relatório diário finalizado com sucesso!');
    }

    public function show(DiarioReport $diarioReport)
    {
        $obra = $diarioReport->obra;
        
        // Calculations
        $hoje = $diarioReport->data_relatorio;
        $inicio = $obra->data_inicio ? Carbon::parse($obra->data_inicio) : $hoje;
        
        $diasCorridos = $inicio->diffInDays($hoje) + 1;
        
        $diasImprodutivos = DiarioReport::where('obra_id', $obra->id)
            ->where('data_relatorio', '<=', $hoje)
            ->where('dia_improdutivo', true)
            ->count();
            
        $diasRestantes = max(0, $obra->prazo_dias - $diasCorridos);

        // Get photos from today
        $postsComFoto = DiarioPost::where('obra_id', $obra->id)
            ->whereDate('data_postagem', $hoje)
            ->whereNotNull('foto_path')
            ->get();

        return view('diario.reports.show', compact('diarioReport', 'obra', 'diasCorridos', 'diasImprodutivos', 'diasRestantes', 'postsComFoto'));
    }

    public function edit(DiarioReport $diarioReport)
    {
        // Only 'chefe' role can edit
        if (Auth::user()->role !== 'chefe') {
            abort(403, 'Apenas o responsável pela obra pode editar o relatório.');
        }

        $obra = $diarioReport->obra;
        return view('diario.reports.edit', compact('diarioReport', 'obra'));
    }

    public function update(Request $request, DiarioReport $diarioReport)
    {
        // Only 'chefe' role can edit
        if (Auth::user()->role !== 'chefe') {
            abort(403);
        }

        $validated = $request->validate([
            'clima_horario'      => 'nullable|array',
            'mao_de_obra'        => 'nullable|array',
            'maquinario'         => 'nullable|array',
            'servicos_iniciados' => 'nullable|string',
            'servicos_execucao'  => 'nullable|string',
            'servicos_concluidos'=> 'nullable|string',
            'materiais_recebidos'=> 'nullable|string',
            'ocorrencias'        => 'nullable|string',
            'observacoes'        => 'nullable|string',
            'dia_improdutivo'    => 'nullable|boolean',
        ]);

        $diarioReport->update(array_merge($validated, [
            'dia_improdutivo' => $request->has('dia_improdutivo'),
            'editado_em'      => Carbon::now(),
            'editado_por'     => Auth::id(),
        ]));

        return redirect()
            ->route('diario-reports.show', $diarioReport)
            ->with('success', 'Relatório atualizado com sucesso. Nota de edição registrada.');
    }

    public function downloadPdf(DiarioReport $diarioReport)
    {
        $obra = $diarioReport->obra;

        $hoje = $diarioReport->data_relatorio;
        $inicio = $obra->data_inicio ? Carbon::parse($obra->data_inicio) : $hoje;
        $diasCorridos = $inicio->diffInDays($hoje) + 1;
        $diasImprodutivos = DiarioReport::where('obra_id', $obra->id)
            ->where('data_relatorio', '<=', $hoje)
            ->where('dia_improdutivo', true)
            ->count();
        $diasRestantes = max(0, $obra->prazo_dias - $diasCorridos);

        $postsComFoto = DiarioPost::where('obra_id', $obra->id)
            ->whereDate('data_postagem', $hoje)
            ->whereNotNull('foto_path')
            ->get()
            ->map(function ($post) {
                $path = storage_path('app/public/' . $post->foto_path);
                if (file_exists($path)) {
                    $mime = mime_content_type($path);
                    $data = base64_encode(file_get_contents($path));
                    $post->foto_base64 = "data:{$mime};base64,{$data}";
                } else {
                    $post->foto_base64 = null;
                }
                return $post;
            });

        $pdf = Pdf::loadView('diario.reports.pdf', compact(
            'diarioReport', 'obra', 'diasCorridos', 'diasImprodutivos', 'diasRestantes', 'postsComFoto'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        $filename = 'diario-obra-' . $obra->id . '-' . $diarioReport->data_relatorio->format('Y-m-d') . '.pdf';

        $pdfContent = $pdf->output();

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}

