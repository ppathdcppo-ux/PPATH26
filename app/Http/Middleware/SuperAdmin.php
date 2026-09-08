<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (session('role') !== 'super_admin') {
            return redirect('/login')
                ->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
