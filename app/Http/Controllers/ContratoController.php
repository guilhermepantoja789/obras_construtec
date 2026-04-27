<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Obra;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ContratoController extends Controller
{
    public function edit(Obra $obra)
    {
        $contrato = $obra->contrato ?? new Contrato(['obra_id' => $obra->id, 'conteudo' => '']);
        return view('contratos.edit', compact('obra', 'contrato'));
    }

    public function update(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'conteudo' => 'required|string',
        ]);

        $obra->contrato()->updateOrCreate(
            ['obra_id' => $obra->id],
            ['conteudo' => $validated['conteudo']]
        );

        return back()->with('success', 'Contrato atualizado!');
    }

    public function pdf(Obra $obra)
    {
        $contrato = $obra->contrato;
        if (!$contrato) return back()->with('error', 'Contrato não encontrado.');

        $pdf = Pdf::loadView('contratos.pdf', compact('obra', 'contrato'));
        
        return $pdf->stream("CONTRATO_{$obra->id}.pdf");
    }
}
