<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('obras', \App\Http\Controllers\ObraController::class);
    Route::resource('propostas', \App\Http\Controllers\PropostaController::class);
    Route::post('propostas/import', [\App\Http\Controllers\PropostaController::class, 'import'])->name('propostas.import');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::get('/feed', [\App\Http\Controllers\FeedController::class, 'index'])->name('feed.index');
    Route::resource('diario-posts', \App\Http\Controllers\DiarioPostController::class);
    Route::resource('diario-reports', \App\Http\Controllers\DiarioReportController::class);
    Route::get('diario-reports/{diarioReport}/pdf', [\App\Http\Controllers\DiarioReportController::class, 'downloadPdf'])->name('diario-reports.pdf');
    Route::post('/context/switch', [\App\Http\Controllers\ContextController::class, 'switch'])->name('context.switch');

    // CSRF token endpoint for service worker Background Sync
    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('csrf.token');

    Route::resource('etapa-obras', \App\Http\Controllers\EtapaObraController::class);
    Route::resource('nota-fiscals', \App\Http\Controllers\NotaFiscalController::class);
    Route::get('financeiro', [\App\Http\Controllers\EtapaObraController::class, 'financeiro'])->name('financeiro.index');
    Route::post('pagamentos', [\App\Http\Controllers\EtapaObraController::class, 'storePagamento'])->name('pagamentos.store');
    Route::delete('pagamentos/{pagamento}', [\App\Http\Controllers\EtapaObraController::class, 'destroyPagamento'])->name('pagamentos.destroy');
    Route::get('obras/{obra}/contrato', [\App\Http\Controllers\ContratoController::class, 'edit'])->name('contrato.edit');
    Route::put('obras/{obra}/contrato', [\App\Http\Controllers\ContratoController::class, 'update'])->name('contrato.update');
    Route::get('obras/{obra}/contrato/pdf', [\App\Http\Controllers\ContratoController::class, 'pdf'])->name('contrato.pdf');
});

require __DIR__.'/auth.php';

Route::view('/offline', 'offline')->name('offline');
