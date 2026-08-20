<?php

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

uses(Tests\TestCase::class);

test('database errors on get do not redirect to the same url', function () {
    $this->app->detectEnvironment(fn () => 'production');

    $exception = new QueryException(
        'mysql',
        'select 1',
        [],
        new \PDOException('SQLSTATE[HY000] [2002] Connection refused'),
    );

    $request = Request::create('https://www.construtec.app.br/', 'GET');
    $response = app(Illuminate\Contracts\Debug\ExceptionHandler::class)->render($request, $exception);

    expect($response->getStatusCode())->toBe(500)
        ->and($response->isRedirect())->toBeFalse()
        ->and($response->getContent())->toContain('Falha temporária');
});
