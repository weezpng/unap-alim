<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Auth;

/**
* Verifica se  o utilizador com sessão iniciada é um POC.
* Se sim, filtra certos pedidos.
*/
class POCCheck
{
      /**
     * Handle navegação por parte do utilizador.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Closure
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->user_type!="POC") {
          abort(403);
        } else {
          return $next($request);
        }
    }
}
