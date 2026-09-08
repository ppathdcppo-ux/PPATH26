<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('userId')) {

            $id = session('userId');

            Cache::put('admin-online-' . $id, true);
        }

        return $next($request);
    }
}