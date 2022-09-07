<?php

namespace App\Http\Middleware;

use Auth;
use App\Models\User;

use Closure;
use Illuminate\Http\Request;

/**
* Verifica se existem pedidos de associação pendentes.
*/
class checkAssociationRequests
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
      if (Auth::Check() && Auth::user()->user_type=="USER" && Auth::user()->isAccountChildren=="WAITING") {
        $pendingAction = \App\Models\pending_actions::where('from_id', Auth::user()->id)->where('action_type', 'ASSOCIATION')->where('is_valid', 'Y')->first();
        if ($pendingAction) {
          return $next($request);
        }
        if (\Route::getCurrentRoute()->uri != "association/confirm" && \Route::getCurrentRoute()->uri != "association/decline" ){
          $id_req = Auth::user()->accountChildrenOf;
          $len = strlen((string)$id_req);
          if ($len < 8)
          {
              $id_req = 0 . (string)$id_req;
          }
          $requestingUser = User::where('id', $id_req)->first();
          return response()->view('messages.association_ask',[
            'nim' => $requestingUser->id,
            'posto' => $requestingUser->posto,
            'nome' => strtoupper($requestingUser->name)
          ]);
        } 
        return $next($request);
      } else {
        return $next($request);
      }
    }
}
