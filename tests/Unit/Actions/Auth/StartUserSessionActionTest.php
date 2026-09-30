<?php

declare(strict_types=1);

use App\Actions\Auth\StartUserSessionAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

function requestWithSession(): Request
{
    $request = Request::create('/auth/google/callback');
    $request->setLaravelSession(app('session.store'));

    return $request;
}

it('logs in users without two factor authentication', function () {
    $user = User::factory()->create();

    $requiresTwoFactorChallenge = app(StartUserSessionAction::class)->handle($user, requestWithSession());

    expect($requiresTwoFactorChallenge)->toBeFalse()
        ->and(Auth::id())->toBe($user->id);
});

it('prepares the two factor challenge instead of logging in', function () {
    $user = User::factory()->withTwoFactor()->create();
    $request = requestWithSession();

    $requiresTwoFactorChallenge = app(StartUserSessionAction::class)->handle($user, $request);

    expect($requiresTwoFactorChallenge)->toBeTrue()
        ->and(Auth::check())->toBeFalse()
        ->and($request->session()->get('login.id'))->toBe($user->id)
        ->and($request->session()->get('login.remember'))->toBeTrue();
});
