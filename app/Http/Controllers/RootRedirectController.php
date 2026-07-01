<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class RootRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('login');
    }
}
