<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
     public function handle($request, Closure $next)
     {
        # $response = $next($request);
        # $response->header('Access-Control-Allow-Origin', '*' );
        # $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type' );
        # $response->header('Access-Control-Allow-Methods', "PUT, POST, DELETE, GET, OPTIONS");
        # $response->header('Access-Control-Allow-Headers', "Accept, Authorization, Content-Type");
        # return $response;
        return $next($request);
     }
}
