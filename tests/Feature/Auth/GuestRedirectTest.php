<?php

use App\Models\User;

test('guest accessing dashboard is redirected to login in production', function () {
    $this->app->detectEnvironment(fn () => 'production');

    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login', absolute: false));
});

test('legacy dashboard url redirects into the app', function () {
    $this->get('/dashboard')->assertRedirect('/app/dashboard');
});

test('guest accessing root sees the landing page', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Construtec');
    $response->assertSee('Área do cliente');
});
