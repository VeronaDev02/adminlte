<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
//Pegar o usuário logado
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Closure;

class VerificaAtivo extends Middleware
{
    use AuthenticatesUsers;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()) {
            if (Auth::getDefaultDriver() == "web" && !Auth::user()->use_active) {
                $this->guard()->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                if ($response = $this->loggedOut($request)) {
                    return $response;
                }

                throw ValidationException::withMessages([
                    "use_username" => ["Este usuário foi desativado"],
                ]);
            }
        }
        return $next($request);
    }
}