<?php

namespace App\Http\Controllers;

use App\Models\NotaFiscal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotaFiscalController extends Controller
{
    public function index()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index')->with('error', 'Selecione uma obra primeiro.');
        }

        $notas = NotaFiscal::where('obra_id', $obraId)
            ->orderBy('data_recebimento', 'desc')
            ->get();

        return view('nota-fiscals.index', compact('notas'));
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
            'arquivo' => 'nullable|file|mimes:pdf|max:10240', // 10MB PDF
            'observacao' => 'nullable|string',
        ]);

        $data = $validated;
        $data['obra_id'] = $obraId;

        if ($request->hasFile('arquivo')) {
            $path = $request->file('arquivo')->store('notas_fiscais', 'public');
            $data['arquivo_path'] = $path;
        }

        NotaFiscal::create($data);

        return redirect()->route('nota-fiscals.index')->with('success', 'Nota fiscal registrada com sucesso!');
    }

    public function destroy(NotaFiscal $notaFiscal)
    {
        if ($notaFiscal->arquivo_path) {
            Storage::disk('public')->delete($notaFiscal->arquivo_path);
        }
        
        $notaFiscal->delete();

        return redirect()->route('nota-fiscals.index')->with('success', 'Nota fiscal removida com sucesso!');
    }
}
