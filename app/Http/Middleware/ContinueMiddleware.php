<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContinueMiddleware
{
    public function handle(Request $request, \Closure $next, ...$guards)
    {
       if($request->headers->contains('Expect', '100-continue')) {
           /** @var Response $response */
           $response = $next($request);
           $response->setStatusCode(100);
       }
        return $next($request);
    }
}
