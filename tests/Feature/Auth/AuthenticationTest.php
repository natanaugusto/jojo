<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

test(description: 'login screen can be rendered', closure: function () {
    $response = $this->get(uri: '/login');
    $response->assertStatus(status: SymfonyResponse::HTTP_OK);
});

test(description: 'users can login/logout', closure: function () {
    $user = User::factory()->create();

    $response = $this->post(uri: '/login', data: [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $response->assertStatus(status: SymfonyResponse::HTTP_FOUND);
    $response->assertRedirect(uri: RouteServiceProvider::HOME);
    $this->assertAuthenticated();

    $response = $this->post(uri: '/logout');
    $response->assertStatus(status: SymfonyResponse::HTTP_FOUND);
    $response->assertRedirect(uri: '/');
    $this->assertGuest();
});

test(description: 'users can login/logout using RestFul', closure: function () {
    $user = User::factory()->create();
    $response = $this->json(method: 'post', uri: '/login', data: [
        'email' => $user->email,
        'password' => 'password'
    ]);
    $response->assertStatus(status: SymfonyResponse::HTTP_NO_CONTENT);
    $response->assertNoContent();
    $this->assertAuthenticated();

    $response = $this->json(method: 'post', uri: '/logout');
    $response->assertStatus(status: SymfonyResponse::HTTP_NO_CONTENT);
    $response->assertNoContent();
    $this->assertGuest();
});

test(description: 'users can not authenticate with invalid password', closure: function () {
    $user = User::factory()->create();

    $this->post(uri: '/login', data: [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
