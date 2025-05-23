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
            return Auth::check() && Auth::user()->role()->value("rol_id") == 1;

        } 
        return Auth::check() && Auth::user()->role()->value("rol_id") != 1;
    }
}
