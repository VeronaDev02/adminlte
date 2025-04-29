<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SelfsMonitorController extends Controller
{
    public function show(Request $request)
    {
        return view('user.selfs.monitor');
    }
}