<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

test(description: 'reset password link screen can be rendered', closure: function () {
    $response = $this->get(uri: '/forgot-password');

    $response->assertStatus(status: SymfonyResponse::HTTP_OK);
});

test(description: 'reset password link can be requested', closure: function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post(uri: '/forgot-password', data: ['email' => $user->email]);
    $response->assertStatus(status: SymfonyResponse::HTTP_FOUND);
    $response->assertRedirect(uri: '/');

    Notification::assertSentTo($user, notification: ResetPassword::class);
});

test(description: 'reset password link can be requested RestFul', closure: function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->json(method: 'post', uri: '/forgot-password', data: ['email' => $user->email]);
    $response->assertStatus(status: SymfonyResponse::HTTP_NO_CONTENT);
    $response->assertNoContent();

    Notification::assertSentTo($user, notification: ResetPassword::class);
});

test(description: 'reset password screen can be rendered', closure: function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post(uri: '/forgot-password', data: ['email' => $user->email]);
    $response->assertStatus(status: SymfonyResponse::HTTP_FOUND);
    $response->assertRedirect(uri: '/');

    Notification::assertSentTo($user, notification: ResetPassword::class, callback: function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(status: SymfonyResponse::HTTP_OK);

        return true;
    });
});

test(description: 'reset password screen can be rendered RestFul', closure: function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->json(method: 'post', uri: '/forgot-password', data: ['email' => $user->email]);
    $response->assertStatus(status: SymfonyResponse::HTTP_NO_CONTENT);
    $response->assertNoContent();

    Notification::assertSentTo($user, notification: ResetPassword::class, callback: function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(status: SymfonyResponse::HTTP_OK);

        return true;
    });
});

test(description: 'password can be reset with valid token', closure: function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(uri: '/forgot-password', data: ['email' => $user->email]);

    Notification::assertSentTo($user, notification: ResetPassword::class, callback: function ($notification) use ($user) {
        $response = $this->post(uri: '/reset-password', data: [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors();

        return true;
    });
});

test(description: 'password can be reset with valid token RestFul', closure: function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->json(method: 'post', uri: '/forgot-password', data: ['email' => $user->email]);
    $response->assertStatus(status: SymfonyResponse::HTTP_NO_CONTENT);
    $response->assertNoContent();

    Notification::assertSentTo($user, notification: ResetPassword::class, callback: function ($notification) use ($user) {
        $response = $this->json(method: 'post', uri: '/reset-password', data: [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(SymfonyResponse::HTTP_NO_CONTENT);
        $response->assertNoContent();

        $response->assertSessionHasNoErrors();

        return true;
    });
});
