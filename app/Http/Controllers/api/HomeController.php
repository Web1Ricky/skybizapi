<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

class HomeController extends Controller
{
    public function AccessTokenDenied(Request $request)
    {
        return ResponseFormatter::error(null,"Invalid Access Token", 503);
    }
    public function ScopeDenied(Request $request)
    {
        return ResponseFormatter::error(null,"Invalid Scope Integration", 503);
    }
}
