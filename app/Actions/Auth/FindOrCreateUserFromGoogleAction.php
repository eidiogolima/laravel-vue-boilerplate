<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Contracts\User as GoogleUser;

class FindOrCreateUserFromGoogleAction
{
    public function handle(GoogleUser $googleUser): ?User
    {
        if (! $this->hasVerifiedEmail($googleUser)) {
            return null;
        }

        $user = User::query()->where('google_id', $googleUser->getId())->first()
            ?? User::query()->where('email', $googleUser->getEmail())->first()
            ?? new User([
                'name' => $googleUser->getName() ?? $googleUser->getEmail(),
                'email' => $googleUser->getEmail(),
                'password' => Str::password(32),
            ]);

        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar() ?? $user->avatar,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        return $user;
    }

    private function hasVerifiedEmail(GoogleUser $googleUser): bool
    {
        return $googleUser instanceof AbstractUser
            && filled($googleUser->getEmail())
            && ($googleUser->getRaw()['email_verified'] ?? false) === true;
    }
}
