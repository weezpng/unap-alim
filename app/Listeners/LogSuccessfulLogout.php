<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $trocarUnidadePendente = $event->user->trocarUnidade;
        $trocaPendente = ($trocarUnidadePendente != null ? true : false);
        if ($trocaPendente==true) {
          if ($event->user->user_type=="SUPER" || $event->user->user_type=="ADMIN") {
            if ($event->user->accountReplacementPOC==null) {
                $user = \App\Models\User::where('id', $event->user->id)->first();
                $try = $user->trocarUnidade;
                $try = \App\Models\unap_unidades::where('slug', $try)->value('name');
                $user->trocarUnidade = null;
                $user->save();
                $notifications = new \App\Http\Controllers\notificationsHandler;
                $notifications->new_notification(/*TITLE*/'Transferência de unidade', /*TEXT*/'O seu pedido de troca de unidade para a '.$try.' foi cancelada, porquê não chegou a ser indicado um utilizador para herdar as suas responsabilidades.',
                  /*TYPE*/'WARNING', /*GERAL*/null, /*TO USER*/ Auth::user()->id, /*CREATED BY*/'SYSTEM: ACCOUNT MOVE CANCELED @SYSTEM', null);
            }
          }
        }
        return redirect()->route('index');
    }
}
