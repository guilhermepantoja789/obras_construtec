<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));

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

        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            \Log::error('Database error: ' . $e->getMessage(), ['exception' => $e]);

            // GET/HEAD + redirect()->back() (fallback "/") vira loop quando a sessão/DB está fora.
            if ($request->isMethodSafe()) {
                return app()->hasDebugModeEnabled()
                    ? null
                    : response()->view('errors.500', [], 500);
            }

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

        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            // Deixar o Laravel redirecionar convidados ao login, validação, etc.
            if ($e instanceof AuthenticationException || $e instanceof ValidationException) {
                return null;
            }

            if (app()->isProduction()) {
                $context = [
                    'message' => $e->getMessage(),
                    'exception' => $e,
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'user_id' => $request->user()?->id,
                ];

                \Log::error('Erro não tratado em produção', $context);

                // Arquivo simples para diagnóstico no servidor: cat storage/logs/last-error.log
                @file_put_contents(
                    storage_path('logs/last-error.log'),
                    sprintf(
                        "[%s] %s %s | user=%s | %s | %s:%d\n",
                        now()->toDateTimeString(),
                        $request->method(),
                        $request->fullUrl(),
                        $request->user()?->id ?? 'guest',
                        $e->getMessage(),
                        $e->getFile(),
                        $e->getLine()
                    ),
                    LOCK_EX
                );

                return response()->view('errors.500', [], 500);
            }

            return null;
        });
    })->create();
