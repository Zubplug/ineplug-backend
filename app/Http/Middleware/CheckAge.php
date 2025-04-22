<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAge
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->age < 18) {
            return response('Access denied: You must be at least 18 years old.', 403);
        }

        return $next($request);
    }
}
