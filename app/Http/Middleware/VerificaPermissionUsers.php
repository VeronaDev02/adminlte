<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
// Pegar o usuário logado
use Illuminate\Support\Facades\Auth;
use Closure;

class VerificaPermissionUsers extends Middleware
{

    public function handle($request, Closure $next)
    {
        if(Auth::user()) {
            if((Auth::check() && Auth::user()->role()->value("rol_id") != 1) || 
                Auth::user()->use_active != true) {
                return redirect("home");
            }
        }   
        return $next($request);
    }
}