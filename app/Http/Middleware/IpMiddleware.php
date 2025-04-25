<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class IpMiddleware extends Middleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function getIp($request)
    {
        foreach (
            [
                "HTTP_CLIENT_IP",
                "HTTP_X_FORWARDED_FOR",
                "HTTP_X_FORWARDED",
                "HTTP_X_CLUSTER_CLIENT_IP",
                "HTTP_FORWARDED_FOR",
                "HTTP_FORWARDED",
                "REMOTE_ADDR",
            ]
            as $key
        ) {
            if (array_key_exists($key, $request->server->all()) === true) {
                foreach (explode(",", $request->server->get($key)) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }
}