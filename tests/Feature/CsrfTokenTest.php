<?php

use App\Models\User;

test('csrf token endpoint requires authentication', function () {
    $response = $this->get(route('csrf.token'));

    $response->assertRedirect(route('login'));
});

test('authenticated user receives csrf token as json', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('csrf.token'));

    $response->assertOk();
    $response->assertJsonStructure(['token']);
    expect($response->json('token'))->not->toBeEmpty();
});
