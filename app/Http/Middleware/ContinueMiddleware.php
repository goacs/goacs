<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class ContinueMiddleware
{
    public function handle(Request $request, \Closure $next, ...$guards)
    {
       if($request->headers->contains('Expect', '100-continue')) {
           return response(null, 100);
       }
        return $next($request);
    }
}
