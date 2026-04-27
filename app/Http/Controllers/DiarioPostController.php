<?php

namespace App\Http\Controllers;

use App\Models\DiarioPost;
use App\Models\DiarioReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;

class DiarioPostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'texto' => 'nullable|string',
            'foto' => 'required|image|max:10240', // Max 10MB
        ]);

        $obraId = session('active_obra_id');
        if (!$obraId) {
            return back()->with('error', 'Nenhuma obra selecionada.');
        }

        // LOCK: block posts if the daily report has already been issued
        $reportEmitido = DiarioReport::where('obra_id', $obraId)
            ->whereDate('data_relatorio', Carbon::today())
            ->exists();

        if ($reportEmitido) {
            return back()->with('error', 'O diário deste dia já foi encerrado e não pode mais receber publicações.');
        }

        $file = $request->file('foto');
        $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
        $path = 'posts/' . $filename;

        // Resize and optimize image
        $image = Image::read($file);
        
        // Scale down to max 1200px width/height while maintaining aspect ratio
        $image->scaleDown(width: 1200, height: 1200);

        // Save as WebP with 80% quality
        Storage::disk('public')->put($path, (string) $image->toWebp(80));

        DiarioPost::create([
            'obra_id' => $obraId,
            'user_id' => auth()->id(),
            'texto' => $validated['texto'],
            'foto_path' => $path,
            'data_postagem' => Carbon::now(),
        ]);

        return back()->with('success', 'Atualização publicada no diário!');
    }

    public function destroy(DiarioPost $diarioPost)
    {
        // Only the author or a chef can delete
        if (auth()->id() !== $diarioPost->user_id && auth()->user()->role !== 'chefe') {
            abort(403);
        }

        // LOCK: block deletion if the daily report has already been issued
        $reportEmitido = DiarioReport::where('obra_id', $diarioPost->obra_id)
            ->whereDate('data_relatorio', $diarioPost->data_postagem)
            ->exists();

        if ($reportEmitido) {
            return back()->with('error', 'O diário deste dia já foi encerrado. Não é possível remover publicações de dias com relatório emitido.');
        }

        if ($diarioPost->foto_path) {
            Storage::disk('public')->delete($diarioPost->foto_path);
        }

        $diarioPost->delete();

        return back()->with('success', 'Postagem removida.');
    }
}
