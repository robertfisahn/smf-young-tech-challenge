<?php

declare(strict_types=1);



namespace App\Features\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class LoginController
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('invoices.index'));
        }

        return back()
            ->withErrors(['email' => 'Nieprawidłowy email lub hasło.'])
            ->onlyInput('email');
    }
}

