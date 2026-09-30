<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\FindOrCreateUserFromGoogleAction;
use App\Actions\Auth\StartUserSessionAction;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse as ExternalRedirectResponse;

class GoogleAuthenticationController extends Controller
{
    public function redirect(): ExternalRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(
        Request $request,
        FindOrCreateUserFromGoogleAction $findOrCreateUserFromGoogle,
        StartUserSessionAction $startUserSession,
    ): RedirectResponse {
        if ($request->filled('error')) {
            return $this->redirectToLoginWithError('errors.google.login_cancelled');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException|ClientException) {
            return $this->redirectToLoginWithError('errors.google.login_failed');
        }

        $user = $findOrCreateUserFromGoogle->handle($googleUser);

        if ($user === null) {
            return $this->redirectToLoginWithError('errors.google.email_not_verified');
        }

        $requiresTwoFactorChallenge = $startUserSession->handle($user, $request);

        return $requiresTwoFactorChallenge
            ? to_route('two-factor.login')
            : redirect()->intended(route('dashboard'));
    }

    private function redirectToLoginWithError(string $translationKey): RedirectResponse
    {
        return to_route('login')->withErrors(['email' => __($translationKey)]);
    }
}
