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
            $obraColumns = ['id', 'nome', 'status'];

            $availableObras = $user->isChefe()
                ? Obra::query()->select($obraColumns)->orderBy('nome')->get()
                : $user->obras()->select(['obras.id', 'obras.nome', 'obras.status'])->get();

            $obraId = session('active_obra_id');

            if (! $obraId) {
                $firstObra = $availableObras->first();

                if ($firstObra) {
                    session(['active_obra_id' => $firstObra->id]);
                    $obraId = $firstObra->id;
                }
            }

            $activeObra = $obraId
                ? $availableObras->firstWhere('id', (int) $obraId)
                : null;

            if ($obraId && ! $activeObra) {
                session()->forget('active_obra_id');
            } elseif ($activeObra) {
                View::share('activeObra', $activeObra);
            }

            View::share('availableObras', $availableObras);
        }

        return $next($request);
    }
}
