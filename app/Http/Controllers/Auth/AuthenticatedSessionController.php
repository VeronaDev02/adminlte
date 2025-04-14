<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\User\Auth\Login as UserLogin;
use App\Events\User\Auth\Logout as UserLogout;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("auth.login");
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        event(new UserLogin($request->use_username, $request->getIp()));

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Depuração para o terminal/console
        error_log("=== LOGOUT INICIADO ===");
        error_log("Usuário: " . (Auth::user() ? Auth::user()->use_username : 'não encontrado'));
        
        try {
            error_log("Tentando disparar evento de logout");
            event(new UserLogout(Auth::user()->use_username, $request->ip()));
            error_log("Evento de logout disparado com sucesso");
        } catch (\Exception $e) {
            error_log("ERRO AO DISPARAR EVENTO: " . $e->getMessage());
        }
        
        error_log("Executando logout...");
        Auth::guard("web")->logout();
        
        error_log("Invalidando sessão");
        $request->session()->invalidate();
        
        error_log("Regenerando token");
        $request->session()->regenerateToken();
        
        error_log("=== LOGOUT FINALIZADO ===");
        
        return redirect("/");
    }
}
