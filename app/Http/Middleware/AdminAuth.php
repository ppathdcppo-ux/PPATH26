<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // 🔐 BLOCK UNAUTHENTICATED USERS
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
