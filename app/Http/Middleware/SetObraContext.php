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

            $availableObras = $user->isChefe()
                ? Obra::query()->select('id', 'nome', 'status')->orderBy('nome')->get()
                : $user->obras()->select('obras.id', 'obras.nome', 'obras.status')->orderBy('obras.nome')->get();

            // Se não houver obra na sessão, tenta pegar a primeira disponível
            if (!$obraId && $availableObras->isNotEmpty()) {
                $obraId = $availableObras->first()->id;
                session(['active_obra_id' => $obraId]);
            }

            if ($obraId) {
                $activeObra = Obra::find($obraId);
                if ($activeObra && ($user->isChefe() || $availableObras->contains('id', $activeObra->id))) {
                    View::share('activeObra', $activeObra);
                } else {
                    session()->forget('active_obra_id');
                }
            }

            View::share('availableObras', $availableObras);
        }

        return $next($request);
    }
}
