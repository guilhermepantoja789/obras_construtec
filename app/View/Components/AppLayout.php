<?php

namespace App\View\Components;

use App\Models\DiarioReport;
use App\Models\Proposta;
use App\Support\OrdemHelper;
use Carbon\Carbon;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public bool $diaEncerrado;
    public $etapas;
    public ?int $clientePropostaId;

    public function __construct()
    {
        $obraId = session('active_obra_id');

        $this->diaEncerrado = $obraId
            ? DiarioReport::where('obra_id', $obraId)
                ->whereDate('data_relatorio', Carbon::today())
                ->exists()
            : false;

        $this->etapas = $obraId
            ? OrdemHelper::sortCollection(
                \App\Models\EtapaObra::where('obra_id', $obraId)
                    ->where('status', '!=', 'concluida')
                    ->get()
            )
            : collect();

        $this->clientePropostaId = null;
        if ($obraId && auth()->check() && auth()->user()->isClient()) {
            $this->clientePropostaId = Proposta::where('obra_id', $obraId)
                ->where('status', 'aceita')
                ->latest('data_proposta')
                ->value('id');
        }
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}
