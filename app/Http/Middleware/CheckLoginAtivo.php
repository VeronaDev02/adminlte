<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLoginAtivo
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->use_login_ativo) {
            // Faz logout
            Auth::logout();

            // Invalida a sessão e regenera o token CSRF
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redireciona para login com mensagem de erro
            return redirect()->route('login')->withErrors([
                'use_username' => 'Seu login foi desativado pelo administrador.',
            ]);
        }

        return $next($request);
    }
}
