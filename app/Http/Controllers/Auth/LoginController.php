<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Events\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("guest")->except("logout");
    }

    public function showLoginForm()
    {
        return view("auth.login");
    }

    public function login(LoginRequest $request)
    {
        $this->validateLogin($request);

        if (
            method_exists($this, "hasTooManyLoginAttempts") &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put("auth.password_confirmed_at", time());
            }

            Auth::user()->update([
                'use_last_seen' => now(),
                'use_ip_origin' => $request->getIp(),
                'use_login_ativo' => true
            ]);

            event(new User\Auth\Login($request->use_username, $request->getIp()));

            return $this->sendLoginResponse($request);
        }
        event(
            new User\Auth\FailedLoginAttempt(
                $request->use_username,
                $request->getIp()
            )
        );

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function username()
    {
        return "use_username";
    }

    public function logout(Request $request)
    {

        $user = Auth::user();
        
        if ($user) {
            try {
                $ip = $request->ip();
                event(new User\Auth\Logout($user->use_username, $ip));

                $user->update([
                    'use_last_seen' => now(),
                    'use_ip_origin' => $ip,
                    'use_login_ativo' => false
                ]);                

            } catch (\Exception $e) {
                \Log::error("Erro ao atualizar o usuário: " . $e->getMessage());
                dd($e->getMessage());
            }
        } 
        $this->guard()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
}
