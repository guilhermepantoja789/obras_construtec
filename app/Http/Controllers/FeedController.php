<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\DiarioPost;
use App\Models\DiarioReport;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        $obraId = session('active_obra_id');
        
        if (!$obraId) {
            return redirect()->route('obras.index')->with('info', 'Selecione uma obra para ver o diário.');
        }

        $obra = Obra::findOrFail($obraId);
        $posts = DiarioPost::where('obra_id', $obraId)
            ->whereDate('data_postagem', \Carbon\Carbon::today())
            ->with(['user', 'etapa'])
            ->latest('data_postagem')
            ->get();

        $etapas = \App\Models\EtapaObra::where('obra_id', $obraId)
            ->where('status', '!=', 'concluida')
            ->orderBy('ordem')
            ->get();

        $report = DiarioReport::where('obra_id', $obraId)
            ->whereDate('data_relatorio', \Carbon\Carbon::today())
            ->first();

        return view('diario.index', compact('obra', 'posts', 'report', 'etapas'));
    }
}
