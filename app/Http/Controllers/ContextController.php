<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obra;

class ContextController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'obra_id' => 'required|exists:obras,id',
        ]);

        $user = auth()->user();
        $obra = Obra::findOrFail($request->obra_id);

        // Verifica permissão
        if ($user->isChefe() || $user->obras->contains($obra->id)) {
            session(['active_obra_id' => $obra->id]);
            return back()->with('success', "Contexto alterado para: {$obra->nome}");
        }

        return back()->with('error', 'Você não tem permissão para acessar esta obra.');
    }
}
