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
    public function index(Request $request)
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index')->with('error', 'Selecione uma obra primeiro.');
        }

        $obra = Obra::findOrFail($obraId);
        
        $query = DiarioReport::where('obra_id', $obraId);

        // Filtro por Busca
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('servicos_execucao', 'like', "%{$busca}%")
                  ->orWhere('ocorrencias', 'like', "%{$busca}%")
                  ->orWhere('observacoes', 'like', "%{$busca}%");
            });
        }

        // Filtro por Status
        if ($request->filled('status')) {
            $query->where('status_dia', $request->status);
        }

        // Filtro por Data Inicio
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_relatorio', '>=', $request->data_inicio);
        }

        // Filtro por Data Fim
        if ($request->filled('data_fim')) {
            $query->whereDate('data_relatorio', '<=', $request->data_fim);
        }

        $reports = $query->orderBy('data_relatorio', 'desc')->paginate(15)->withQueryString();

        return view('diario.reports.index', compact('obra', 'reports'));
    }

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
            'status_dia' => 'required|string|in:trabalhado,meio_expediente,nao_trabalhado',
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
        
        $postsComFoto = DiarioPost::where('obra_id', $obra->id)
            ->whereDate('data_postagem', $diarioReport->data_relatorio)
            ->whereNotNull('foto_path')
            ->get();

        return view('diario.reports.edit', compact('diarioReport', 'obra', 'postsComFoto'));
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
            'status_dia'         => 'required|string|in:trabalhado,meio_expediente,nao_trabalhado',
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
                    try {
                        // Redimensionar para 800px de largura mantendo proporção para ser mais rápido
                        $img = \Intervention\Image\Laravel\Facades\Image::read($path);
                        $img->scale(width: 800);
                        $data = (string) $img->encodeByExtension('jpg', quality: 70);
                        $post->foto_base64 = "data:image/jpeg;base64," . base64_encode($data);
                    } catch (\Exception $e) {
                        $post->foto_base64 = null;
                    }
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
    public function addPhoto(Request $request, DiarioReport $diarioReport)
    {
        if (Auth::user()->role !== 'chefe') {
            abort(403);
        }

        $request->validate([
            'foto' => 'required|image|max:10240', // Max 10MB
        ]);

        $file = $request->file('foto');
        $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
        $path = 'posts/' . $filename;

        // Resize and optimize image
        $image = \Intervention\Image\Laravel\Facades\Image::read($file);
        
        // Scale down to max 1200px width/height while maintaining aspect ratio
        $image->scaleDown(width: 1200, height: 1200);

        try {
            $encoded = $image->toWebp(75);
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $encoded);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar imagem no storage: ' . $e->getMessage());
            return back()->with('error', 'Erro ao salvar a imagem. Por favor, tente novamente.');
        }

        DiarioPost::create([
            'obra_id' => $diarioReport->obra_id,
            'user_id' => auth()->id(),
            'texto' => 'Foto adicionada retroativamente.',
            'foto_path' => $path,
            'data_postagem' => $diarioReport->data_relatorio->copy()->setHour(12), // Retroactive date
        ]);

        return back()->with('success', 'Foto adicionada ao diário retroativamente com sucesso.');
    }
}

