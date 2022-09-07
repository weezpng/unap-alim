<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;

/**
* Verifica se o utilizador com sessão iniciada se encontra com a conta bloqueada.
* Se sim, filtra quais URL's pode visitar.
*/
class checkAccountLock
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
      if (Auth::check()) {
        if (Auth::user()->lock=='Y'){
          try {
            $route =\Route::getCurrentRoute()->uri;
          } catch (\Exception $e) {
            abort(500);
          }
          $is_ementa = $route=="ementa";
          $is_index = $route=="/";
          if ($is_ementa || $is_index) {
            return $next($request);
          } else {
            return redirect()->route('locked');
          }
        }
      }
      return $next($request);
    }
}
