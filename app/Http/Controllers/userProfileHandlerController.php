<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\editUserSettings;
use App\Models\express_account_verification_tokens;
use App\Models\User;
use App\Models\unap_unidades;

/**
* Funcionalidades associadas a perfis de utilizadores.
*/
class userProfileHandlerController extends Controller
{

    /**
    * Contar marcações de Auth::user()
    *
    * @return view
    */
    public function countMarcações()
    {
        if (Auth::check())
        {
            return \App\Models\marcacaotable::where('NIM', Auth::user()->id)
                ->where('data_marcacao', '>=', date('Y-m-d'))
                ->orderBy('data_marcacao')
                ->count();
        }
        return 0;
    }

    /**
    * Ver o perfil do utilizador com sessão iniciada.
    *
    * @return view
    */
    public function profile_index()
    {
        $trocarUnidadePendente = User::where('id', Auth::user()->id)
            ->first()
            ->value('trocarUnidade');

        $trocaPendente = ($trocarUnidadePendente != null ? true : false);        
        if ($trocaPendente == true)
        {
            if (Auth::user()->user_type == "POC" || Auth::user()->user_type == "ADMIN")
            {
                if (Auth::user()->accountReplacementPOC == null)
                {
                    $user = User::where('id', Auth::user()->id)
                        ->first();
                    $try = \App\Models\unap_unidades::where('slug', $trocarUnidadePendente)->value('name');
                    $user->trocarUnidade = null;
                    $user->save();

                    $notifications = new \App\Http\Controllers\notificationsHandler;
                    $notifications->new_notification( /*TITLE*/
                    'Transferência de unidade', /*TEXT*/
                    'O seu pedido de troca de unidade para a ' . $try . ' foi cancelada, porquê não chegou a ser indicado um utilizador para herdar as suas responsabilidades.',
                    /*TYPE*/
                    'WARNING', /*GERAL*/
                    null, /*TO USER*/
                    Auth::user()->id, /*CREATED BY*/
                    'SYSTEM: ACCOUNT MOVE CANCELED @SYSTEM', null);
                    $trocaPendente = false;
                }
            }
        }

        $unidades = unap_unidades::get()->all();
        return view('profile.index', ['pendenteTrocaUnidade' => $trocaPendente, 'unidades' => $unidades,

        ]);
    }


    /**
    * @ignore
    * Verifica o tipo de user (USER/CHILDREN) e devolve utilizadores
    *
    * @return view
    */
    public function usersChild()
    {
        $this::checkUserType();
        $users = User::orderBy('posto')->get();
        return view('gestao.utilizadores', ['users' => $users]);
    }

    /**
    * Guardar alterações feitas na conta do Auth::user()
    *
    * @param Request $request
    * @return view
    */
    public function profile_settings_save(Request $request)
    {

        $user = User::where('id', Auth::user()->id)
            ->first();

        if ($request->inputUEO != null)
        {
            $changed = ($user->unidade != $request->inputUEO);
            if ($changed == true)
            {

                if ($user->unidade=="UnAp/CmdPess" && $request->inputUEO=="UnAp/CmdPess/QSO"){
                    $transfer_between_sides = true;
                } else if($user->unidade=="UnAp/CmdPess/QSO" && $request->inputUEO=="UnAp/CmdPess") {
                    $transfer_between_sides = true;
                } else {
                    $transfer_between_sides = false;
                }


               if ($transfer_between_sides == true) {
                    $oldUnidade = $user->unidade;
                    $user->unidade = $request->inputUEO;
               } else {
                $oldUnidade = $user->unidade;
                $user->trocarUnidade = $request->inputUEO;
               }
            }
            else
            {
                $changed = false;
                $oldUnidade = null;
            }

        }
        else
        {
            $changed = false;
            $oldUnidade = null;
        }

        $user->name = $request->inputName;
        $user->posto = $request->inputPosto;
        $user->email = $request->inputEmail;
        $user->telf = $request->inputTelf;
        $user->seccao = $request->inputSecçao;
        $user->descriptor = $request->inputDescriptor;
        $user->localRefPref = $request->inputLocalRefPref;
        $user->save();

        if ($user->user_type == "POC" || $user->user_type == "ADMIN")
        {
            if ($changed==true && $transfer_between_sides==false)
            {
                return view('gestao.transf_assoc.transf_users', ['from' => $user->id, ]);
            }

        }

        if (isset($transfer_between_sides) && $transfer_between_sides==false) { $changed = false; }

        return view('profile.saved', ['changedUnidade' => $changed, 'newUnidade' => \App\Models\unap_unidades::where('slug', $request->inputUEO)
            ->value('name') , 'oldUnidade' => \App\Models\unap_unidades::where('slug', $oldUnidade)->value('name') , 'url' => route('profile.index') ]);
    }

    /**
    * Ver pedidos de transferencia de unidade e novas contas.
    *
    * @return view
    */
    public function newUsersAdmin()
    {
        if ((new ActiveDirectoryController)
            ->CONFIRM_UNIT_CHANGE())
        {
            $newToUnidade = User::where('trocarUnidade', '!=', null)
                ->get();
            $newToUnidadeFormatted = array();
            $i = 0;
            foreach ($newToUnidade as $user)
            {
                if (Auth::user()->user_type == "HELPDESK" || Auth::user()->user_type == "ADMIN")
                {
                    $isAuthOK = true;
                }
                else
                {
                    if (Auth::user()->user_type == "POC" && $user->user_type == "USER")
                    {
                        $isAuthOK = true;
                    }
                    else
                    {
                        $isAuthOK = false;
                    }
                }
                if ($user->user_type == "POC" || $user->user_type == "ADMIN")
                {
                    if ($user->accountReplacementPOC == null)
                    {
                        $try = $user->trocarUnidade;
                        $try = \App\Models\unap_unidades::where('slug', $try)->value('name');
                        $user->trocarUnidade = null;
                        $user->save();
                        $notifications = new \App\Http\Controllers\notificationsHandler;
                        $notifications->new_notification( /*TITLE*/
                        'Transferência de unidade', /*TEXT*/
                        'O seu pedido de troca de unidade para a ' . $try . ' foi cancelada, porquê não chegou a ser indicado um utilizador para herdar as suas responsabilidades.',
                        /*TYPE*/
                        'WARNING', /*GERAL*/
                        null, /*TO USER*/
                        $user->id, /*CREATED BY*/
                        'SYSTEM: ACCOUNT MOVE CANCELED @SYSTEM', null);
                        continue;
                    }
                }
                $newToUnidadeFormatted[$i]['nim'] = $user->id;
                $newToUnidadeFormatted[$i]['name'] = $user->name;
                $newToUnidadeFormatted[$i]['email'] = $user->email;
                $newToUnidadeFormatted[$i]['old_unidade'] = $user->unidade;
                $newToUnidadeFormatted[$i]['new_unidade'] = $user->trocarUnidade;
                $newToUnidadeFormatted[$i]['type'] = $user->user_type;
                $newToUnidadeFormatted[$i]['isAuthOK'] = $isAuthOK;
                $i++;
            }
            $newToUnidadeChildrens = \App\Models\users_children::where('trocarUnidade', Auth::user()->unidade)
                ->get();
            foreach ($newToUnidadeChildrens as $user)
            {
                $newToUnidadeFormatted[$i]['nim'] = $user->childID;
                $newToUnidadeFormatted[$i]['name'] = $user->childNome;
                $newToUnidadeFormatted[$i]['email'] = $user->childEmail;
                $newToUnidadeFormatted[$i]['old_unidade'] = $user->childUnidade;
                $newToUnidadeFormatted[$i]['new_unidade'] = $user->trocarUnidade;
                $newToUnidadeFormatted[$i]['type'] = "Utilizador associado";
                $newToUnidadeFormatted[$i]['isAuthOK'] = true;
                $i++;
            }
        }
        else
        {
            $newToUnidadeFormatted = 401;
        }
        if ((new ActiveDirectoryController)->ACCEPT_NEW_MEMBERS())
        {
            $users = User::where('account_verified', 'N')
                ->get();
            $newUsers = array();
            $i = 0;

            foreach ($users as $user)
            {
                $NIM = $user->id;
                while ((strlen((string)$NIM)) < 8) { $NIM = 0 . (string)$NIM; }
                $unidade = \App\Models\unap_unidades::where('slug', $user->unidade)->first();
                $newUsers[$i]['nim'] = $NIM;
                $newUsers[$i]['name'] = $user->name;
                $newUsers[$i]['email'] = $user->email;
                $newUsers[$i]['unidade'] = $unidade['name'];
                $newUsers[$i]['type'] = $user->user_type;
                $i++;
            }

            $usersChildren = \App\Models\users_children::where('accountVerified', 'N')->get();
            foreach ($usersChildren as $user)
            {
                $NIM = $user->childID;
                while ((strlen((string)$NIM)) < 8) { $NIM = 0 . (string)$NIM; }
                $unidade = \App\Models\unap_unidades::where('slug', $user->childUnidade)->first();
                $newUsers[$i]['nim'] = $NIM;
                $newUsers[$i]['name'] = $user->childNome;
                $newUsers[$i]['email'] = $user->childEmail;
                $newUsers[$i]['unidade'] = $unidade['name'];
                $newUsers[$i]['type'] = "Utilizador associado";
                $i++;
            }
        }
        else
        {
            $newUsers = 401;
        }
        return view('gestao.new_users', ['users' => $newUsers, 'transferedUsers' => $newToUnidadeFormatted]);
    }

    /**
    * Activa uma conta
    *
    * @param Request $request
    * @return redirect
    */
    public function newUsersConfirm(Request $request)
    {

      $id = $request->nim;

      while ((strlen((string)$id)) < 8) {
        $id = 0 . (string)$id;
      }

        $user = User::where('id', $id)->first();

        if (!$user)
        {
            $user = \App\Models\users_children::where('childID', $request->nim)
                ->first();
            $user->accountVerified = 'Y';
        }
        else
        {
            $user->account_verified = 'Y';
            $user->updated_by = Auth::user()->id;
            $user->verified_at = now();
            $user->verified_by = Auth::user()->id;
            $notifications = new notificationsHandler;
            $notifications->new_notification( /*TITLE*/
            'Conta autorizada', /*TEXT*/
            'A sua conta foi autorizada por ' . Auth::user()->id,
            /*TYPE*/
            'NORMAL', /*GERAL*/
            null, /*TO USER*/
            $request->nim, /*CREATED BY*/
            'SYSTEM: ACCOUNT VERIFICATION @' . Auth::user()->id, null);
        }
        $user->save();
        return redirect()->route('user.profile', $id);
    }

    /**
    * Rejeita a activação de uma conta: elimina a conta.
    *
    * @param Request $request
    * @return redirect
    */
    public function newUsersReject(Request $request)
    {

        $id = $request->nim;
        while ((strlen((string)$id)) < 8) {
          $id = 0 . (string)$id;
        }

        $user = User::where('id', $id)->first();
        if (!$user) $user = \App\Models\users_children::where('childID', $id)->first();

        $marcaçoes = \App\Models\marcacaotable::where('NIM', $id)->get();
        foreach ($marcaçoes as $key => $marcacao)
        {
            $marcacao->delete();
        }

        $user->delete();
        return view('messages.success', [
          'message' => "O utilizador foi removido com sucesso!",
          'url' => route('gestão.usersAdmin')
        ]);
    }

    /**
    * Apaga uma conta e transfere todos os utilizadores associados para outra conta.
    *
    * @param Request $request
    * @return redirect
    */
    public function transferUsersAndDeleteOriginal(Request $request)
    {
        $user = User::where('id', $request->old_user)
            ->first();
        $allChildren = \App\Models\users_children::where('parentNIM', $request->old_user)
            ->get();
        $parent = 1;

        if (!$allChildren) {
          $allChildren = \App\Models\users_children::where('parent2nNIM', $request->old_user)
              ->get();
          $parent = 2;
        }


        if ($parent==1) {
          $allUsers = \App\Models\User::where('accountChildrenOf', $request->old_user)
              ->get();
        } else {
          $allUsers = \App\Models\User::where('account2ndChildrenOf', $request->old_user)
              ->get();
        }


        $marcaçoes = \App\Models\marcacaotable::where('NIM', $request->old_user)
            ->get();
        foreach ($marcaçoes as $key => $marcacao)
        {
            $marcacao->delete();
        }
        foreach ($allChildren as $key => $childrenUser)
        {
          if ($parent==1) {
            $childrenUser->parentNIM = $request->nim;
          } else {
            $childrenUser->parent2nNIM = $request->nim;
          }
            $childrenUser->save();
        }
        foreach ($allUsers as $key => $associatedUser)
        {
          if ($parent==1) {
            $associatedUser->accountChildrenOf = $request->nim;
          } else {
            $associatedUser->account2ndChildrenOf = $request->nim;
          }
            $associatedUser->save();
        }
        $user->delete();
        return redirect()->route('gestão.newUsersAdmin');
    }

    /**
    * Cria um token de activação de conta EXPRESS.
    *
    * @param Request $request
    * @return redirect
    */
    public function newUsersExpressToken(Request $request)
    {
        if (!((new ActiveDirectoryController)->EXPRESS_TOKEN_GENERATION())) abort(401);
        $token = new express_account_verification_tokens;
        $token->token = \Str::random(15);
        $token->NIM = $request->inputNIM;
        $token->created_by = Auth::user()->id;
        $token->save();
        return redirect()->route('gestão.newUsersAdmin');
    }

    /**
    * Cria um pedido de associação
    *
    * @param Request $request
    * @return redirect
    */
    public function association_request(Request $request)
    {
        $len = strlen((string)$request->nim);
        if ($len < 8)
        {
            $id = 0 . (string)$request->nim;
        }
        else
        {
            $id = $request->nim;
        }
        $me = User::where('id', Auth::user()->id)
            ->first();
        $toAssociate = User::where('id', $id)->first();
        $me->isAccountChildren = 'WAITING';
        $me->accountChildrenOf = $toAssociate->id;
        $me->updated_by = Auth::user()->id;
        $me->save();
        $newNotification = new notificationsHandler;
        $notification_id = $newNotification->new_notification( /*TITLE*/
        'Pedido de associação', /*TEXT*/
        'O ' . $me->posto . ' ' . $me->id . ' ' . $me->name . ' pediu para se associar ao seu grupo de gestão.',
        /*TYPE*/
        'NORMAL', /*GERAL*/
        null, /*TO USER*/
        $toAssociate->id, /*CREATED BY*/
        'SYSTEM: ASSOCIATION REQUEST @' . Auth::user()->id, null);
        $newPendingAction = new \App\Models\pending_actions;
        $newPendingAction->action_type = 'ASSOCIATION';
        $newPendingAction->from_id = Auth::user()->id;
        $newPendingAction->to_id = $toAssociate->id;
        $newPendingAction->notification_id = $notification_id;
        $newPendingAction->save();
        $returnMessage = "O pedido de associação de conta foi concluido com sucesso. Por favor aguarde resposta.";
        return view('messages.success', ['message' => $returnMessage, 'url' => route('index') , ]);
    }

    /**
    * Confirmar um pedido de associação
    *
    * @return redirect
    */
    public function association_confirm()
    {
        $thisUser = User::where('id', Auth::user()->id)->first();
        $thisUser->isAccountChildren = "Y";
        $thisUser->save();
        $returnMessage = "O seu perfil foi associado com sucesso.";
        return view('messages.success', ['message' => $returnMessage, 'url' => route('index') , ]);
    }

    /**
    * Negar um pedido de associação
    *
    * @return redirect
    */
    public function association_decline()
    {
        $thisUser = User::where('id', Auth::user()->id)->first();
        $thisUser->isAccountChildren = "N";
        $thisUser->accountChildrenOf = null;
        $thisUser->accountChildrenGroup = null;
        $thisUser->accountChildrenSubGroup = null;
        $thisUser->save();
        return redirect()->route('index');
    }

    /**
    * Cancela um pedido de associação
    *
    * @return redirect
    */
    public function association_cancel()
    {
        $thisUser = User::where('id', Auth::user()->id)
            ->first();
        $pendingAction = \App\Models\pending_actions::where('from_id', Auth::user()->id)
            ->where('to_id', $thisUser->accountChildrenOf)
            ->where('action_type', 'ASSOCIATION')
            ->first();
        $notification = \App\Models\notification_table::where('id', $pendingAction->notification_id)
            ->where('notification_toUser', $thisUser->accountChildrenOf)
            ->first();
        $thisUser->isAccountChildren = "N";
        $thisUser->accountChildrenOf = null;
        $thisUser->accountChildrenGroup = null;
        $thisUser->accountChildrenSubGroup = null;
        $pendingAction->delete();
        $notification->delete();
        $thisUser->save();
        return redirect()
            ->route('index');
    }

    /**
    * Aceita um pedido de associação
    *
    * @param Request $request
    * @return redirect
    */
    public function association_by_USER_confirm(Request $request)
    {
      #dd($request->all());
        $pendingAction = \App\Models\pending_actions::where('id', $request->actionAcceptActionID)
            ->first();
        $len = strlen((string)$pendingAction->from_id);
        if ($len < 8)
        {
            $user_id = 0 . (string)$pendingAction->from_id;
        }
        else
        {
            $user_id = $pendingAction->from_id;
        }
        $thisUser = User::where('id', $user_id)->first();
        $notification = \App\Models\notification_table::where('id', $pendingAction->notification_id)
            ->where('notification_toUser', Auth::user()
            ->id)
            ->first();
        $thisUser->isAccountChildren = "Y";
        $thisUser->save();
        $pendingAction->delete();
        $notification->delete();
        $newNotification = new notificationsHandler;
        $notification_id = $newNotification->new_notification(
          /*TITLE*/
          'Associação aceite',
          /*TEXT*/
          'O ' . Auth::user()->posto . ' ' . Auth::user()->id . ' ' . Auth::user()->name . ' aceitou o seu pedido de associação. Você agora faz parte do grupo de gestão.',
          /*TYPE*/
          'NORMAL',
          /*GERAL*/
          null,
          /*TO USER*/
          $pendingAction->from_id,
          /*CREATED BY*/
          'SYSTEM: ASSOCIATION COMPLETE @' . Auth::user()->id,
          /* LAPSES AT */
          null
        );
        $returnMessage = "O " . $thisUser->posto . " " . $thisUser->id . " " . $thisUser->name . " foi associado com sucesso.";
        return view('messages.success', ['message' => $returnMessage, 'url' => route('index') , ]);
    }

    /**
    * Rejeitar um pedido de associação
    *
    * @param Request $request
    * @return redirect
    */
    public function association_by_USER_decline(Request $request)
    {
        $pendingAction = \App\Models\pending_actions::where('id', $request->actionAcceptActionID)
            ->first();
        $len = strlen((string)$pendingAction->from_id);
        if ($len < 8)
        {
            $user_id = 0 . (string)$pendingAction->from_id;
        }
        else
        {
            $user_id = $pendingAction->from_id;
        }
        $thisUser = User::where('id', $user_id)->first();
        $notification = \App\Models\notification_table::where('id', $pendingAction->notification_id)
            ->where('notification_toUser', Auth::user()
            ->id)
            ->first();
        $thisUser->isAccountChildren = "N";
        $thisUser->accountChildrenOf = null;
        $thisUser->accountChildrenGroup = null;
        $thisUser->accountChildrenSubGroup = null;
        $thisUser->save();
        $newNotification = new notificationsHandler;
        $notification_id = $newNotification->new_notification(
            /*TITLE*/
          'Associação negada',
          /*TEXT*/
          'O ' . Auth::user()->posto . ' ' . Auth::user()->id . ' ' . Auth::user()->name . ' negou o seu pedido de associação.',
          /*TYPE*/
          'NORMAL',
          /*GERAL*/
          null,
          /*TO USER*/
          $pendingAction->from_id,
          /*CREATED BY*/
          'SYSTEM: ASSOCIATION DECLINED @' . Auth::user()->id,
          null
        );
        $returnMessage = "O " . $thisUser->posto . " " . $thisUser->id . " " . $thisUser->name . " foi associado com sucesso.";
        $pendingAction->delete();
        $notification->delete();
        return redirect()
            ->route('index');
    }

   /**
    * Altera a definição para ON/OFF
    *
    * @param Request $request
    * @return json
    */
    public function toggle_dark_mode(Request $request){
      try{
        $user = User::where('id', Auth::user()->id)->first();
        if ($request->enable=="false") $change = 'N';
        else $change = 'Y';
        $user->dark_mode = $change;
        $user->save();
        return response()
            ->json('success', 200);
        }
      catch(\Exception $e)
      {
          return response()->json($e->getMessage() , 200);
      }
    }

    /**
    * Altera a definição para ON/OFF
    *
    * @param Request $request
    * @return json
    */
    public function toggle_compact_mode(Request $request){
      try{
        $user = User::where('id', Auth::user()->id)->first();
        if ($request->enable=="false") $change = 'N';
        else $change = 'Y';
        $user->compact_mode = $change;
        $user->save();
        return response()
            ->json('success', 200);
        }
      catch(\Exception $e)
      {
          return response()->json($e->getMessage() , 200);
      }
    }

    /**
    * Altera a definição para ON/OFF
    *
    * @param Request $request
    * @return json
    */
    public function toggle_flat_mode(Request $request){
      try{
        $user = User::where('id', Auth::user()->id)->first();
        if ($request->enable=="false") $change = 'N';
        else $change = 'Y';
        $user->flat_mode = $change;
        $user->save();
        return response()
            ->json('success', 200);
        }
      catch(\Exception $e)
      {
          return response()->json($e->getMessage() , 200);
      }
    }

    /**
    * Altera a definição para ON/OFF
    *
    * @param Request $request
    * @return json
    */
    public function toggle_icons(Request $request){
      try{
        $user = User::where('id', Auth::user()->id)->first();
        if ($request->enable=="false") $change = 'N';
        else $change = 'Y';
        $user->use_icons = $change;
        $user->save();
        return response()
            ->json('success', 200);
        }
      catch(\Exception $e)
      {
          return response()->json($e->getMessage() , 200);
      }
    }

    /**
    * Altera a definição para ON/OFF
    *
    * @param Request $request
    * @return json
    */
    public function toggle_lite_mode(Request $request){
      try{
        $user = User::where('id', Auth::user()->id)->first();
        if ($request->enable=="false") $change = 'N';
        else $change = 'Y';
        $user->lite_mode = $change;
        $user->save();
        return response()
            ->json('success', 200);
        }
      catch(\Exception $e)
      {
          return response()->json($e->getMessage() , 200);
      }
    }

    /**
    * Altera a definição para ON/OFF
    *
    * @param Request $request
    * @return json
    */
    public function toggle_auto_collapse_mode(Request $request){
      try{
        $user = User::where('id', Auth::user()->id)->first();
        if ($request->enable=="false") $change = 'N';
        else $change = 'Y';
        $user->auto_collapse = $change;
        $user->save();
        return response()
            ->json('success', 200);
        }
      catch(\Exception $e)
      {
          return response()->json($e->getMessage() , 200);
      }
    }

    /**
    * Altera a definição para ON/OFF
    *
    * @param Request $request
    * @return json
    */
    public function toggle_sticky_nav_mode(Request $request){
        try{
          $user = User::where('id', Auth::user()->id)->first();
          if ($request->enable=="false") $change = 'N';
          else $change = 'Y';
          $user->sticky_top = $change;
          $user->save();
          return response()
              ->json('success', 200);
          }
        catch(\Exception $e)
        {
            return response()->json($e->getMessage() , 200);
        }
      }

    /**
    * Altera a definição para ON/OFF
    *
    * @param Request $request
    * @return json
    */
      public function toggle_resizer_mode(Request $request){
          try{
            $user = User::where('id', Auth::user()->id)->first();
            if ($request->enable=="false") $change = 'N';
            else $change = 'Y';
            $user->resize_box = $change;
            $user->save();
            return response()
                ->json('success', 200);
            }
          catch(\Exception $e)
          {
              return response()->json($e->getMessage() , 200);
          }
        }

    /**
    * Faz upload de uma imagem a usar como foto de perfil
    *
    * @param Request $request
    * @return redirect
    */
    public function profile_picture_upload(Request $request)
    {
        $file = $request->file('profilePicUpload');        
        $name = Auth::user()->id . ".jpg";
        
        $name2 = Auth::user()->id;
        while ((strlen((string)$name2)) < 8) {
          $name2 = 0 . (string)$name2;
        }

        $name2 = $name2 . ".jpg";
        $newCopyFile = public_path("\\assets\\profiles\\");
        $file->move($newCopyFile, $name);
        copy(($newCopyFile . $name), ($newCopyFile.$name2));        
        return redirect()->route('profile.index');
    }
}
