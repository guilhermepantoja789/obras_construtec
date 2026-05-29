<?php

namespace App\View\Components;

use App\Models\DiarioReport;
use Carbon\Carbon;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public bool $diaEncerrado;
    public $etapas;

    public function __construct()
    {
        $obraId = session('active_obra_id');

        $this->diaEncerrado = $obraId
            ? DiarioReport::where('obra_id', $obraId)
                ->whereDate('data_relatorio', Carbon::today())
                ->exists()
            : false;

        $this->etapas = $obraId
            ? \App\Models\EtapaObra::where('obra_id', $obraId)
                ->where('status', '!=', 'concluida')
                ->orderBy('ordem')
                ->get()
            : collect();
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}
