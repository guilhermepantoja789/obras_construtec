<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PwaManifestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::permanentRedirect('/login', '/app/login');
Route::permanentRedirect('/dashboard', '/app/dashboard');

Route::prefix('app')->group(function () {
    Route::get('/manifest.json', PwaManifestController::class)->name('pwa.manifest');
    Route::view('/offline', 'offline')->name('offline');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('obras', [\App\Http\Controllers\ObraController::class, 'index'])->name('obras.index');

        Route::middleware('role:chefe')->group(function () {
            Route::get('obras/create', [\App\Http\Controllers\ObraController::class, 'create'])->name('obras.create');
            Route::post('obras', [\App\Http\Controllers\ObraController::class, 'store'])->name('obras.store');
            Route::get('obras/{obra}/edit', [\App\Http\Controllers\ObraController::class, 'edit'])->name('obras.edit');
            Route::match(['put', 'patch'], 'obras/{obra}', [\App\Http\Controllers\ObraController::class, 'update'])->name('obras.update');
            Route::delete('obras/{obra}', [\App\Http\Controllers\ObraController::class, 'destroy'])->name('obras.destroy');
        });

        Route::get('obras/{obra}', [\App\Http\Controllers\ObraController::class, 'show'])->name('obras.show');

        Route::get('/feed', [\App\Http\Controllers\FeedController::class, 'index'])->name('feed.index');

        Route::middleware('role:chefe,operador')->group(function () {
            Route::get('diario-reports/create', [\App\Http\Controllers\DiarioReportController::class, 'create'])->name('diario-reports.create');
            Route::post('diario-reports', [\App\Http\Controllers\DiarioReportController::class, 'store'])->name('diario-reports.store');
        });

        Route::get('diario-reports/calendar', [\App\Http\Controllers\DiarioReportController::class, 'calendar'])->name('diario-reports.calendar');
        Route::get('diario-reports', [\App\Http\Controllers\DiarioReportController::class, 'index'])->name('diario-reports.index');
        Route::get('diario-reports/{diarioReport}', [\App\Http\Controllers\DiarioReportController::class, 'show'])->name('diario-reports.show');
        Route::get('diario-reports/{diarioReport}/pdf', [\App\Http\Controllers\DiarioReportController::class, 'downloadPdf'])->name('diario-reports.pdf');
        Route::get('propostas/{proposta}/cliente', [\App\Http\Controllers\PropostaController::class, 'showCliente'])
            ->middleware('role:chefe,cliente')
            ->name('propostas.cliente.show');
        Route::get('etapa-obras', [\App\Http\Controllers\EtapaObraController::class, 'index'])->name('etapa-obras.index');

        Route::middleware('role:chefe')->group(function () {
            Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
            Route::resource('propostas', \App\Http\Controllers\PropostaController::class);
            Route::post('propostas/import', [\App\Http\Controllers\PropostaController::class, 'import'])->name('propostas.import');
            Route::get('empreiteiras', [\App\Http\Controllers\EmpreiteiraController::class, 'index'])->name('empreiteiras.index');
            Route::get('empreiteiras/{empreiteira}', [\App\Http\Controllers\EmpreiteiraController::class, 'show'])->name('empreiteiras.show');
            Route::post('empreiteiras', [\App\Http\Controllers\EmpreiteiraController::class, 'store'])->name('empreiteiras.store');
            Route::match(['put', 'patch'], 'empreiteiras/{empreiteira}', [\App\Http\Controllers\EmpreiteiraController::class, 'update'])->name('empreiteiras.update');
            Route::delete('empreiteiras/{empreiteira}', [\App\Http\Controllers\EmpreiteiraController::class, 'destroy'])->name('empreiteiras.destroy');
            Route::get('financeiro', [\App\Http\Controllers\FinanceiroController::class, 'index'])->name('financeiro.index');
            Route::post('pagamentos', [\App\Http\Controllers\FinanceiroController::class, 'storePagamento'])->name('pagamentos.store');
            Route::get('pagamentos/{pagamento}/comprovante', [\App\Http\Controllers\FinanceiroController::class, 'comprovantePagamento'])->name('pagamentos.comprovante');
            Route::delete('pagamentos/{pagamento}', [\App\Http\Controllers\FinanceiroController::class, 'destroyPagamento'])->name('pagamentos.destroy');
            Route::post('despesas', [\App\Http\Controllers\FinanceiroController::class, 'storeDespesa'])->name('despesas.store');
            Route::get('despesas/{despesaObra}/comprovante', [\App\Http\Controllers\FinanceiroController::class, 'comprovanteDespesa'])->name('despesas.comprovante');
            Route::get('despesas/{despesaObra}/anexos/{anexo}', [\App\Http\Controllers\FinanceiroController::class, 'anexoDespesa'])->name('despesas.anexo');
            Route::delete('despesas/{despesaObra}', [\App\Http\Controllers\FinanceiroController::class, 'destroyDespesa'])->name('despesas.destroy');
            Route::get('obras/{obra}/contrato', [\App\Http\Controllers\ContratoController::class, 'edit'])->name('contrato.edit');
            Route::put('obras/{obra}/contrato', [\App\Http\Controllers\ContratoController::class, 'update'])->name('contrato.update');
            Route::get('obras/{obra}/contrato/pdf', [\App\Http\Controllers\ContratoController::class, 'pdf'])->name('contrato.pdf');
            Route::resource('etapa-obras', \App\Http\Controllers\EtapaObraController::class)->except(['index']);
            Route::post('etapa-obras/reorder', [\App\Http\Controllers\EtapaObraController::class, 'reorder'])->name('etapa-obras.reorder');
            Route::post('etapa-obras/regenerar', [\App\Http\Controllers\EtapaObraController::class, 'regenerarFromProposta'])->name('etapa-obras.regenerar');
            Route::get('diario-reports/{diarioReport}/edit', [\App\Http\Controllers\DiarioReportController::class, 'edit'])->name('diario-reports.edit');
            Route::put('diario-reports/{diarioReport}', [\App\Http\Controllers\DiarioReportController::class, 'update'])->name('diario-reports.update');
            Route::post('diario-reports/{diarioReport}/photos', [\App\Http\Controllers\DiarioReportController::class, 'addPhoto'])->name('diario-reports.add-photo');
        });

        Route::middleware('role:chefe,operador')->group(function () {
            Route::resource('diario-posts', \App\Http\Controllers\DiarioPostController::class)->only(['store', 'destroy']);
            Route::resource('nota-fiscals', \App\Http\Controllers\NotaFiscalController::class);
        });

        Route::get('/csrf-token', function () {
            return response()->json(['token' => csrf_token()]);
        })->name('csrf.token');

        Route::post('/context/switch', [\App\Http\Controllers\ContextController::class, 'switch'])->name('context.switch');
    });

    require __DIR__.'/auth.php';
});
