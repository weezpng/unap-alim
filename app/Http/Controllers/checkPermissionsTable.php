<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\user_type_permissions;

/**
 * @ignore
 */
class checkPermissionsTable extends Controller
{
    public function ACCESS_TABLE($permission){
      return user_type_permissions::where('permission', $permission)->first();
    }

    public function RETURN_RESULT($userType, $table){
      if ($userType=="HELPDESK") return true;
      elseif ($userType=="ADMIN" && $table->usergroupAdmin=="Y") return true;
      elseif ($userType=="POC" && $table->usergroupSuper=="Y") return true;
      elseif ($userType=="USER" && $table->usergroupUser=="Y") return true;
      return false;
    }

    public function VIEW_GENERAL_STATS(){
      $table = $this::ACCESS_TABLE("VER ESTATÍSTICAS GERAIS");
      return $this::RETURN_RESULT(auth()->user()->user_type, $table);
    }

    public function VIEW_USERS(){
      $table = $this::ACCESS_TABLE("VER TODOS OS UTILIZADORES");
      return $this::RETURN_RESULT(auth()->user()->user_type, $table);
    }

    public function VIEW_EDIT_EMENTA(){
      $table = $this::ACCESS_TABLE("ALTERAR \ PUBLICAR EMENTA");
      return $this::RETURN_RESULT(auth()->user()->user_type, $table);
    }
}
