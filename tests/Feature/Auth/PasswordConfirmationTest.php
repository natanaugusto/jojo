<?php

use App\Models\User;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

test(description: 'confirm password screen can be rendered', closure: function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(uri: '/confirm-password');

    $response->assertStatus(status: SymfonyResponse::HTTP_OK);
});

test(description: 'password can be confirmed', closure: function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(uri: '/confirm-password', data: [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test(description: 'password is not confirmed with invalid password', closure: function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(uri: '/confirm-password', data: [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});
