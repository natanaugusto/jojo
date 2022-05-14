<?php

use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

test(description: 'registration screen can be rendered', closure: function () {
    $response = $this->get(uri: '/register');

    $response->assertStatus(status: SymfonyResponse::HTTP_OK);
});

test(description: 'new users can register', closure: function () {
    $response = $this->post(uri: '/register', data: [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(uri: RouteServiceProvider::HOME);
});

test(description: 'new users can register by API', closure: function () {
    $response = $this->json(method: 'post', uri: '/register', data: [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
});
