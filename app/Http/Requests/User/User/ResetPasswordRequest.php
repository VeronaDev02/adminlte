<?php

namespace App\Http\Requests\User\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Validation\Rules\PasswordPortuguese;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        if (Auth::getDefaultDriver() == "web") {
            return true;
        }
        return false;
    }

    public function rules()
    {
        return [
            "new_password" => ["required", "string", "min:8"],
            "confirm_password" => ["required", "same:new_password"],
        ];
    }

    public function getIp()
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
            if (array_key_exists($key, $this->server->all()) === true) {
                foreach (explode(",", $this->server->get($key)) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }
}
