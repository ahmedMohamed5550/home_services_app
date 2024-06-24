<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->userType === 'user') {
            return $next($request);
        }
    
        else{
            return response()->json([
                'status' => false,
                'message' => 'unauthoraized',
            ],401);
        }
    }
}
