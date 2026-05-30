<?php

test('guest accessing dashboard is redirected to login in production', function () {
    $this->app->detectEnvironment(fn () => 'production');

    $response = $this->get('/dashboard');

    $response->assertRedirect(route('login', absolute: false));
});

test('guest accessing root is redirected to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login', absolute: false));
});
