<?php

namespace App\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;

class MenuFilter implements FilterInterface
{
    public function transform($item)
    {
        if (!$this->isVisible($item)) {
            return false;
        }

        if (isset($item["header"])) {
            $item = $item["header"];
        }

        return $item;
    }

    protected function isVisible($item)
    {
        if (isset($item["admin"]) && $item["admin"]) {
            return Auth::check() &&
                Auth::user()
                    ->role()
                    ->value("rol_id") == 1 &&
                Auth::user()->use_active == true;

        } elseif (isset($item["user"]) && $item["user"]) {
            return Auth::check() &&
                Auth::user()
                    ->role()
                    ->value("rol_id") == 2 &&
                    Auth::user()->use_active == true;
        } elseif (!isset($item["guard"])) {
            return true;
        }
        return false;
    }
}
