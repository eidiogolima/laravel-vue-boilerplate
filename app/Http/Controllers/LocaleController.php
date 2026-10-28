<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Locale\RememberLocaleAction;
use App\Http\Requests\Locale\UpdateLocaleRequest;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class LocaleController extends Controller
{
    public function update(UpdateLocaleRequest $request, RememberLocaleAction $rememberLocale): Response
    {
        $rememberLocale->handle($request->locale());

        return Inertia::location(url()->previous(route('home')));
    }
}
