<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

/**
* Verifica se o utilizador com sessão iniciada se encontra com a receber marcações a dinheiro.
* Se sim, verifica se esta na data de fim.
*/
class checkIsTagOblig
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
          $__today = date("Y-m-d");
        if (Auth::user()->isTagOblig!=null){
          $__token = Auth::user()->isTagOblig;
          $__from = \App\Models\users_tagged_conf::where('id', $__token)->value('data_inicio');
          $__until = \App\Models\users_tagged_conf::where('id', $__token)->value('data_fim');
          if ($__until < $__today) {
            try{
              $id = Auth::user()->id;
              $len = strlen((string)$id);
              while ((strlen((string)$id)) < 8) {
                $id = 0 . (string)$id;
              }
              $user = \App\Models\User::where('id', $id)->first();
              $user->isTagOblig = null;
              $user->save();
              $__tag = \App\Models\users_tagged_conf::where('id', $__token)->first();
              $__tag->delete();
              $newNotification = new \App\Http\Controllers\notificationsHandler;
              $newNotification->new_notification('Marcações', 'Você já não está marcado para receber as refeições a dinheiro.', 'WARNING', null, Auth::user()->id, 'SYSTEM: ACCOUNT UNTAGGED @SYSTEM', $__until);
            } catch(\Exception $e) {

            }
          }
        }
      }

      return $next($request);
    }
}
