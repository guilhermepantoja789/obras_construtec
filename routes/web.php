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

    Route::resource('obras', \App\Http\Controllers\ObraController::class)->only(['index']);

    // Public/All roles (View only or basic access)
    Route::get('/feed', [\App\Http\Controllers\FeedController::class, 'index'])->name('feed.index');
    
    // Moved up to avoid conflict with {diarioReport} parameter
    Route::middleware('role:chefe,operador')->group(function () {
        Route::get('diario-reports/create', [\App\Http\Controllers\DiarioReportController::class, 'create'])->name('diario-reports.create');
        Route::post('diario-reports', [\App\Http\Controllers\DiarioReportController::class, 'store'])->name('diario-reports.store');
    });

    Route::get('diario-reports/calendar', [\App\Http\Controllers\DiarioReportController::class, 'calendar'])->name('diario-reports.calendar');
    Route::get('diario-reports', [\App\Http\Controllers\DiarioReportController::class, 'index'])->name('diario-reports.index');
    Route::get('diario-reports/{diarioReport}', [\App\Http\Controllers\DiarioReportController::class, 'show'])->name('diario-reports.show');
    Route::get('diario-reports/{diarioReport}/pdf', [\App\Http\Controllers\DiarioReportController::class, 'downloadPdf'])->name('diario-reports.pdf');
    Route::get('etapa-obras', [\App\Http\Controllers\EtapaObraController::class, 'index'])->name('etapa-obras.index');

    // Chefe Only
    Route::middleware('role:chefe')->group(function () {
        Route::resource('obras', \App\Http\Controllers\ObraController::class)->except(['index', 'show']);
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('propostas', \App\Http\Controllers\PropostaController::class);
        Route::post('propostas/import', [\App\Http\Controllers\PropostaController::class, 'import'])->name('propostas.import');
        Route::get('financeiro', [\App\Http\Controllers\EtapaObraController::class, 'financeiro'])->name('financeiro.index');
        Route::post('pagamentos', [\App\Http\Controllers\EtapaObraController::class, 'storePagamento'])->name('pagamentos.store');
        Route::delete('pagamentos/{pagamento}', [\App\Http\Controllers\EtapaObraController::class, 'destroyPagamento'])->name('pagamentos.destroy');
        Route::get('obras/{obra}/contrato', [\App\Http\Controllers\ContratoController::class, 'edit'])->name('contrato.edit');
        Route::put('obras/{obra}/contrato', [\App\Http\Controllers\ContratoController::class, 'update'])->name('contrato.update');
        Route::get('obras/{obra}/contrato/pdf', [\App\Http\Controllers\ContratoController::class, 'pdf'])->name('contrato.pdf');
        Route::resource('etapa-obras', \App\Http\Controllers\EtapaObraController::class)->except(['index']);
        Route::get('diario-reports/{diarioReport}/edit', [\App\Http\Controllers\DiarioReportController::class, 'edit'])->name('diario-reports.edit');
        Route::put('diario-reports/{diarioReport}', [\App\Http\Controllers\DiarioReportController::class, 'update'])->name('diario-reports.update');
        Route::post('diario-reports/{diarioReport}/photos', [\App\Http\Controllers\DiarioReportController::class, 'addPhoto'])->name('diario-reports.add-photo');
    });

    Route::get('obras/{obra}', [\App\Http\Controllers\ObraController::class, 'show'])
        ->name('obras.show')
        ->whereNumber('obra');

    // Chefe & Operador
    Route::middleware('role:chefe,operador')->group(function () {
        Route::resource('diario-posts', \App\Http\Controllers\DiarioPostController::class)->only(['store', 'destroy']);
        Route::resource('nota-fiscals', \App\Http\Controllers\NotaFiscalController::class);
    });

    // CSRF token endpoint for service worker Background Sync
    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('csrf.token');

    Route::post('/context/switch', [\App\Http\Controllers\ContextController::class, 'switch'])->name('context.switch');
});

require __DIR__.'/auth.php';

Route::view('/offline', 'offline')->name('offline');
