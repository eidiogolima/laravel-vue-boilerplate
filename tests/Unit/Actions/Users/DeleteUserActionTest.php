<?php

declare(strict_types=1);

use App\Actions\Users\DeleteUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('deletes the user', function () {
    $user = User::factory()->create();

    app(DeleteUserAction::class)->handle($user);

    expect($user->fresh())->toBeNull();
});
