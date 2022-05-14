<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

test(description: 'email verification screen can be rendered', closure: function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get(uri: '/verify-email');

    $response->assertStatus(status: SymfonyResponse::HTTP_OK);
});

test(description: 'email can be verified', closure: function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        name: 'verification.verify',
        expiration: now()->addMinutes(value: 60),
        parameters: ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(event: Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(uri: config(key: 'app.frontend_url').RouteServiceProvider::HOME.'?verified=1');
});

test(description: 'email is not verified with invalid hash', closure: function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        name: 'verification.verify',
        expiration: now()->addMinutes(value: 60),
        parameters: ['id' => $user->id, 'hash' => sha1(string: 'wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
