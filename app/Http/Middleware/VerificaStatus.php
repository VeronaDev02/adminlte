<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerificaStatus extends Middleware
{
    public function handle($request, Closure $next)
    {
        if (
            Auth::user() &&
            Auth::getDefaultDriver() == "web" &&
            Auth::user()->use_status_password != true &&
            $request->getRequestUri() != "/redefinirSenha" &&
            $request->getRequestUri() != "/logout"
        ) {
            return redirect("/redefinirSenha");
        }
        return $next($request);
    }
}
