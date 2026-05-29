<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\PropostaItem;
use App\Models\EtapaObra;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('propostas.create', compact('obra'));
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

        ]);

        $obraId = session('active_obra_id');
        if (!$obraId) {
            return redirect()->route('obras.index')->with('error', 'Selecione uma obra primeiro.');
        }

        DB::transaction(function () use ($validated, $obraId) {
            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['quantidade'] * $item['valor_unitario'];
            }

            $proposta = Proposta::create([
                'obra_id' => $obraId,
                'titulo' => $validated['titulo'],
                'escopo' => $validated['escopo'],
                'data_proposta' => $validated['data_proposta'],
                'valor_total' => $total,
                'status' => $validated['status'],
            ]);

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantidade'] * $itemData['valor_unitario'];
                $item = $proposta->items()->create(array_merge($itemData, [
                    'subtotal' => $subtotal,
                    'is_etapa' => isset($itemData['is_etapa']) && $itemData['is_etapa'] == '1',
                ]));

                // If it's an accepted proposal and item is an etapa, create EtapaObra
                if ($proposta->status === 'aceita' && $item->is_etapa) {
                    EtapaObra::create([
                        'obra_id' => $obraId,
                        'nome' => $item->descricao,
                        'valor' => $item->subtotal,
                        'descricao' => 'Gerado automaticamente via Proposta',
                        'ordem' => $item->ordem,
                        'status' => 'pendente',
                        'percentual_concluido' => 0,
                    ]);
                }
            }
        });

        return redirect()->route('obras.show', $obraId)->with('success', 'Proposta criada com sucesso!');
    }
    
    public function edit(Proposta $proposta)
    {
        $proposta->load('items');
        $obra = $proposta->obra;
        return view('propostas.edit', compact('proposta', 'obra'));
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

        ]);

        DB::transaction(function () use ($validated, $proposta) {
            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['quantidade'] * $item['valor_unitario'];
            }

            $wasAlreadyAccepted = $proposta->status === 'aceita';

            $proposta->update([
                'titulo' => $validated['titulo'],
                'escopo' => $validated['escopo'],
                'data_proposta' => $validated['data_proposta'],
                'valor_total' => $total,
                'status' => $validated['status'],
            ]);

            // Simple approach: remove old items and create new ones
            $proposta->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantidade'] * $itemData['valor_unitario'];
                $item = $proposta->items()->create(array_merge($itemData, [
                    'subtotal' => $subtotal,
                    'is_etapa' => isset($itemData['is_etapa']) && $itemData['is_etapa'] == '1',
                ]));

                // If it just became accepted (or stayed accepted and we want to sync - though syncing is complex)
                // For now, only generate if it WAS NOT accepted and NOW IS.
                if (!$wasAlreadyAccepted && $proposta->status === 'aceita' && $item->is_etapa) {
                    EtapaObra::create([
                        'obra_id' => $proposta->obra_id,
                        'nome' => $item->descricao,
                        'valor' => $item->subtotal,
                        'descricao' => 'Gerado automaticamente via Proposta',
                        'ordem' => $item->ordem,
                        'status' => 'pendente',
                        'percentual_concluido' => 0,
                    ]);
                }
            }
        });

        return redirect()->route('propostas.show', $proposta)->with('success', 'Proposta atualizada com sucesso!');
    }

    public function show(Proposta $proposta)
    {
        $proposta->load('items');
        return view('propostas.show', compact('proposta'));
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

