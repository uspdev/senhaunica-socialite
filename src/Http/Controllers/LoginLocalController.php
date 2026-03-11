<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginLocalController extends Controller
{

    public function create()
    {
        return view('senhaunica::local.login');
    }

    public function store()
    {
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Desculpe, as credenciais não são válidas.'
            ]);
        }

        request()->session()->regenerate();

        return redirect('/');
    }

}
