<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Obra;
use Illuminate\Support\Facades\View;

class SetObraContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $obraId = session('active_obra_id');

            // Se não houver obra na sessão, tenta pegar a primeira disponível
            if (!$obraId) {
                $firstObra = $user->isChefe() 
                    ? Obra::first() 
                    : $user->obras()->first();
                
                if ($firstObra) {
                    session(['active_obra_id' => $firstObra->id]);
                    $obraId = $firstObra->id;
                }
            }

            if ($obraId) {
                $activeObra = Obra::find($obraId);
                // Verifica se o usuário ainda tem acesso a essa obra
                if ($activeObra && ($user->isChefe() || $user->obras->contains($activeObra->id))) {
                    View::share('activeObra', $activeObra);
                } else {
                    session()->forget('active_obra_id');
                }
            }

            // Disponibiliza as obras disponíveis para o seletor
            $availableObras = $user->isChefe() ? Obra::all() : $user->obras;
            View::share('availableObras', $availableObras);
        }

        return $next($request);
    }
}
