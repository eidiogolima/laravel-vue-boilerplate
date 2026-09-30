<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StartUserSessionAction
{
    public function handle(User $user, Request $request): bool
    {
        if ($user->hasEnabledTwoFactorAuthentication()) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => true,
            ]);

            return true;
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return false;
    }
}
