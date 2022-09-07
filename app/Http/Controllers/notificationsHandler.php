<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use \App\Models\notification_table;

/**
 * Lógica de criação e desactivação de notificações
 */
class notificationsHandler extends Controller
{

  /**
   * Cria uma nova notificação.
   * 
   * @param string $title Titulo da notificação
   * @param string $text Texto da notificação
   * @param string $type Tipo de notificação
   * @param string $geral NULLABLE - Tipo de utilizadores a receber a notificação
   * @param string $toUser NULLABLE - Utilizador a receber a notificação
   * @param string $createdBy Criador da notificação
   * @param string $lapses_at Expiração da notificação
   * 
   * @return string id da nova notificação
   */
    public function new_notification($title, $text, $type, $geral, $toUser, $createdBy, $lapses_at){
      $notification = new notification_table;
      $notification->notification_title = $title;
      $notification->notification_text = $text;
      $notification->notification_type = $type;
      $notification->notification_geral = $geral;
      $notification->notification_toUser = $toUser;
      $notification->created_by = $createdBy;
      $notification->lapses_at = $lapses_at;
      $notification->save();
      return $notification->id;
    }

  /**
   * Remove uma notificação
   * 
   * @param string $id ID da notificação
   * 
   * @return void
   */
    public function disable_notification($id){
      $notification = notification_table::where('id', $id)->first();
      $notification->delete();
    }
}
