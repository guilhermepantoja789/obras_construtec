<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetObraContext::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthorizationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return response()->view('errors.403', [], 403);
        });

        $exceptions->render(function (HttpExceptionInterface $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            $status = $e->getStatusCode();
            if (in_array($status, [403, 404, 405], true)) {
                return response()->view("errors.$status", [], $status);
            }

            return null;
        });

        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if (app()->isProduction()) {
                return response()->view('errors.500', [], 500);
            }

            return null;
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            \Log::error('Database error: ' . $e->getMessage(), ['exception' => $e]);

            $message = $e->getMessage();

            if (str_contains($message, "doesn't have a default value") || str_contains($message, 'cannot be null')) {
                $userMessage = 'Dados incompletos. Verifique os campos obrigatórios e tente novamente.';
            } elseif (str_contains($message, 'Duplicate entry')) {
                $userMessage = 'Registro duplicado. Este item já existe.';
            } else {
                $userMessage = 'Erro ao salvar os dados. Tente novamente ou contate o suporte.';
            }

            return redirect()->back()->withInput()->with('error', $userMessage);
        });
    })->create();
