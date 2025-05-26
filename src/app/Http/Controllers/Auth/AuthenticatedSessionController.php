<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyController;

class AuthenticatedSessionController extends FortifyController
{
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('mypage.profile.edit'));
        }

        return back()->withErrors([
            'email' => '認証に失敗しました。',
        ]);
    }
}
