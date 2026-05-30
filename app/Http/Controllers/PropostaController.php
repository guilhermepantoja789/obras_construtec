<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\PropostaItem;
use App\Models\EtapaObra;
use App\Models\Obra;
use App\Services\EtapaObraSyncService;
use App\Services\PropostaCalculoService;
use App\Support\OrdemHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;



class PropostaController extends Controller
{
    public function index()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) return redirect()->route('obras.index');

        $propostas = Proposta::where('obra_id', $obraId)->latest()->get();
        return view('propostas.index', compact('propostas'));
    }

    public function create()
    {
        $obraId = session('active_obra_id');
        if (!$obraId) return redirect()->route('obras.index');
        
        $obra = Obra::findOrFail($obraId);
        $encargosIniciais = PropostaCalculoService::templateEncargos($obra->encargos_padrao);

        return view('propostas.create', compact('obra', 'encargosIniciais'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'escopo' => 'nullable|string',
            'data_proposta' => 'required|date',
            'status' => 'required|in:rascunho,enviada,aceita,recusada',
            'items' => 'required|array|min:1',
            'items.*.descricao' => 'required|string|max:255',
            'items.*.unidade' => 'nullable|string|max:20',
            'items.*.quantidade' => 'required|numeric',
            'items.*.valor_unitario' => 'required|numeric',

            'items.*.is_etapa' => 'nullable',
            'items.*.ordem' => 'nullable|string',
            'encargos' => 'nullable|array',
            'encargos.*.percent' => 'nullable|numeric|min:0|max:100',
            'encargos.*.ativo' => 'nullable',
        ]);

        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index')->with('error', 'Selecione uma obra primeiro.');
        }

        $encargos = PropostaCalculoService::normalizarEncargosRequest($validated['encargos'] ?? []);
        $totais = PropostaCalculoService::calcularTotais($validated['items'], $encargos);

        DB::transaction(function () use ($validated, $obraId, $encargos, $totais) {
            $proposta = Proposta::create([
                'obra_id' => $obraId,
                'titulo' => $validated['titulo'],
                'escopo' => $validated['escopo'],
                'data_proposta' => $validated['data_proposta'],
                'subtotal_itens' => $totais['subtotal_itens'],
                'encargos' => $encargos,
                'valor_total' => $totais['valor_total'],
                'status' => $validated['status'],
            ]);

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantidade'] * $itemData['valor_unitario'];
                $proposta->items()->create(array_merge($itemData, [
                    'subtotal' => $subtotal,
                    'is_etapa' => isset($itemData['is_etapa']) && $itemData['is_etapa'] == '1',
                ]));
            }

            if ($proposta->status === 'aceita') {
                $proposta->load('items');
                EtapaObraSyncService::syncFromProposta($proposta);
            }

            Obra::where('id', $obraId)->update(['encargos_padrao' => $encargos]);
        });

        return redirect()->route('obras.show', $obraId)->with('success', 'Proposta criada com sucesso!');
    }
    
    public function edit(Proposta $proposta)
    {
        $proposta->setRelation(
            'items',
            OrdemHelper::sortCollection($proposta->items()->get())
        );
        $obra = $proposta->obra;
        $encargosIniciais = PropostaCalculoService::templateEncargos(
            $proposta->encargos ?? $obra->encargos_padrao
        );

        return view('propostas.edit', compact('proposta', 'obra', 'encargosIniciais'));
    }

    public function update(Request $request, Proposta $proposta)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'escopo' => 'nullable|string',
            'data_proposta' => 'required|date',
            'status' => 'required|in:rascunho,enviada,aceita,recusada',
            'items' => 'required|array|min:1',
            'items.*.descricao' => 'required|string|max:255',
            'items.*.unidade' => 'nullable|string|max:20',
            'items.*.quantidade' => 'required|numeric',
            'items.*.valor_unitario' => 'required|numeric',

            'items.*.is_etapa' => 'nullable',
            'items.*.ordem' => 'nullable|string',
            'encargos' => 'nullable|array',
            'encargos.*.percent' => 'nullable|numeric|min:0|max:100',
            'encargos.*.ativo' => 'nullable',
        ]);

        $encargos = PropostaCalculoService::normalizarEncargosRequest($validated['encargos'] ?? []);
        $totais = PropostaCalculoService::calcularTotais($validated['items'], $encargos);

        DB::transaction(function () use ($validated, $proposta, $encargos, $totais) {
            $wasAlreadyAccepted = $proposta->status === 'aceita';

            $proposta->update([
                'titulo' => $validated['titulo'],
                'escopo' => $validated['escopo'],
                'data_proposta' => $validated['data_proposta'],
                'subtotal_itens' => $totais['subtotal_itens'],
                'encargos' => $encargos,
                'valor_total' => $totais['valor_total'],
                'status' => $validated['status'],
            ]);

            $proposta->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantidade'] * $itemData['valor_unitario'];
                $proposta->items()->create(array_merge($itemData, [
                    'subtotal' => $subtotal,
                    'is_etapa' => isset($itemData['is_etapa']) && $itemData['is_etapa'] == '1',
                ]));
            }

            $proposta->load('items');

            if ($proposta->status === 'aceita') {
                EtapaObraSyncService::syncFromProposta($proposta);
            } elseif ($wasAlreadyAccepted && $proposta->status !== 'aceita') {
                EtapaObra::where('obra_id', $proposta->obra_id)
                    ->where(function ($query) {
                        $query->whereNotNull('proposta_item_id')
                            ->orWhere('descricao', 'Gerado automaticamente via Proposta');
                    })
                    ->delete();
            }

            Obra::where('id', $proposta->obra_id)->update(['encargos_padrao' => $encargos]);
        });

        return redirect()->route('propostas.show', $proposta)->with('success', 'Proposta atualizada com sucesso!');
    }

    public function show(Proposta $proposta)
    {
        $proposta->load('items');
        $items = OrdemHelper::sortCollection($proposta->items);
        $grupos = OrdemHelper::groupPropostaItems($items);
        $encargosResumo = PropostaCalculoService::calcularTotais(
            $items->map(fn ($item) => [
                'quantidade' => $item->quantidade,
                'valor_unitario' => $item->valor_unitario,
            ])->all(),
            PropostaCalculoService::templateEncargos($proposta->encargos)
        );

        return view('propostas.show', compact('proposta', 'items', 'grupos', 'encargosResumo'));
    }

    public function showCliente(Proposta $proposta)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        if (!$user->isChefe() && !$user->obras->contains($proposta->obra_id)) {
            abort(403, 'Você não tem permissão para acessar esta proposta.');
        }

        // Cliente só visualiza propostas aceitas (sem rascunhos internos).
        if ($user->isClient() && $proposta->status !== 'aceita') {
            abort(403, 'Proposta ainda não disponível para visualização do cliente.');
        }

        $proposta->load('items');
        $items = OrdemHelper::sortCollection($proposta->items);
        $grupos = OrdemHelper::groupPropostaItems($items);

        return view('propostas.show-client', compact('proposta', 'items', 'grupos'));
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);


            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'csv' || $extension === 'txt') {
                $reader = new Csv();
                // Tentar detectar delimitador
                $content = file_get_contents($file->getRealPath());
                $semicolons = substr_count($content, ';');
                $commas = substr_count($content, ',');
                $delimiter = ($semicolons > $commas) ? ';' : ',';
                
                $reader->setDelimiter($delimiter);
                $reader->setEnclosure('"');
                $reader->setInputEncoding('UTF-8');
                $spreadsheet = $reader->load($file->getRealPath());
            } else {
                $spreadsheet = IOFactory::load($file->getRealPath());
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, true);

            $items = [];
            $headerFound = false;
            $stageCounter = 0;
            
            // Colunas padrão baseadas no PDF e nos exemplos do usuário
            $cols = [
                'item' => 'A',
                'descricao' => 'B',
                'col_3' => 'C',
                'col_4' => 'D',
                'valor_unitario' => 'E',
                'total' => 'F',
            ];

            foreach ($rows as $index => $row) {
                // Pular linhas vazias
                $rowText = implode('', array_filter($row));
                if (empty($rowText)) continue;

                // Detectar cabeçalho
                if (!$headerFound) {
                    $search = mb_strtoupper($rowText);
                    if (str_contains($search, 'DESCRIÇÃO') || str_contains($search, 'ESPECIFICAÇÃO') || str_contains($search, 'SERVIÇOS')) {
                        $headerFound = true;
                        continue;
                    }
                    if ($index > 10) $headerFound = true; // Forçar início após 10 linhas
                    if (!$headerFound) continue;
                }

                $descricao = $row[$cols['descricao']] ?? '';
                if (empty($descricao) || mb_strtoupper($descricao) === 'DESCRIÇÃO') continue;

                $itemNumber = $row[$cols['item']] ?? '';
                $val3 = $row[$cols['col_3']] ?? '';
                $val4 = $row[$cols['col_4']] ?? '';
                $valorUnitario = $row[$cols['valor_unitario']] ?? 0;
                
                // Heurística de Quantidade/Unidade
                $quantidade = 1;
                $unidade = 'un';
                
                $v3_num = $this->parseNumeric($val3);
                $v4_num = $this->parseNumeric($val4);

                if (!empty($val3) && is_numeric(str_replace(',', '.', (string)$val3))) {
                    $quantidade = $v3_num;
                    $unidade = $val4 ?: 'un';
                } elseif (!empty($val4) && is_numeric(str_replace(',', '.', (string)$val4))) {
                    $quantidade = $v4_num;
                    $unidade = $val3 ?: 'un';
                }

                $valorUnitario = $this->parseNumeric($valorUnitario);

                // Heurística de Etapa:
                $isEtapa = false;
                if (!empty($descricao)) {
                    $isAllCaps = (mb_strtoupper($descricao) === $descricao && preg_match('/[A-Z]/', $descricao));
                    $isRoundNumber = preg_match('/^\d+\.0$/', (string)$itemNumber);
                    $noPrice = ($valorUnitario == 0);

                    // Se é ALL CAPS ou termina em .0, e não tem preço unitário, é quase certeza que é etapa
                    if (($isAllCaps || $isRoundNumber) && $noPrice) {
                        $isEtapa = true;
                    }
                }


                $items[] = [
                    'descricao' => (string)$descricao,
                    'unidade' => (string)$unidade,
                    'quantidade' => $quantidade ?: 1,
                    'valor_unitario' => $valorUnitario ?: 0,
                    'is_etapa' => $isEtapa,
                    'ordem' => $itemNumber ?: (count($items) + 1),
                ];
            }




            return response()->json(['items' => $items]);

        } catch (\Exception $e) {
            \Log::error('Erro ao importar proposta: ' . $e->getMessage(), ['exception' => $e]);

            $payload = [
                'error' => 'Erro ao processar arquivo: ' . $e->getMessage(),
            ];

            if (config('app.debug')) {
                $payload['trace'] = $e->getTraceAsString();
            }

            return response()->json($payload, 500);
        }
    }

    private function parseNumeric($value)
    {
        if (is_numeric($value)) return (float)$value;
        if (empty($value)) return 0;
        
        // Remover R$, pontos de milhar e trocar vírgula por ponto
        $value = str_replace(['R$', ' ', '.'], '', $value);
        $value = str_replace(',', '.', $value);
        
        return is_numeric($value) ? (float)$value : 0;
    }
}

