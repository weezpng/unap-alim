<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\marcacaotable;
use App\Models\notification_table;
use App\Models\users_children;
use App\Models\express_account_verification_tokens;
use App\Models\helpdesk_settings;
use Facades\App\Models\user_type_permissions;
use Facades\App\Models\helpdesk_permissions;
use App\Mail\passwordResetNotification;
use App\Mail\ementaChanged;
use Illuminate\Support\Facades\Mail;

/**
 * Controlador do sistema Helpdesk
 */

class helpdeskController extends Controller
{
    /**
     * Acede à página de grupos de permissões.
     *
     * @return view
     */
    public function gestao_permissoes_index()
    {
        if (auth()->user()->user_type != "HELPDESK") abort(401);
        $permissions = user_type_permissions::get()->all();
        $permissionsAccAtrib = helpdesk_permissions::get()->all();

        return view('helpdesk.permissions', ['permissions' => $permissions, 'permissionsAccAtrib' => $permissionsAccAtrib]);
    }

    /**
     * Altera uma permissão para incluir ou não incluir um tipo de utilizador.
     *
     * @deprecated
     * @param Request $request
     * @return json
     */
    public function gestao_permissoes_change(Request $request)
    {
        if (auth()->user()->user_type != "HELPDESK") return response()
            ->json('HELPDESK ONLY.', 200);
        try
        {
            $user = user_type_permissions::where('id', $request->id)
                ->first();
            $notifications = new notificationsHandler;
            if ($request->user_perm == "ADMIN")
            {
                if ($request->enable == "true")
                {
                    $user->usergroupAdmin = "Y";
                }
                else
                {
                    $user->usergroupAdmin = "N";
                }
            }
            if ($request->user_perm == "POC")
            {
                if ($request->enable == "true")
                {
                    $user->usergroupSuper = "Y";
                }
                else
                {
                    $user->usergroupSuper = "N";
                }
            }
            if ($request->user_perm == "USER")
            {
                if ($request->enable == "true")
                {
                    $user->usergroupUser = "Y";
                }
                else
                {
                    $user->usergroupUser = "N";
                }
            }
            $user->updated_by = Auth::user()->id;
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
     * Altera uma permissão para incluir ou não incluir um grupo de permissões
     *
     * @param Request $request
     * @return json
     */
    public function change_permissao(Request $request)
    {
        if (auth()->user()->user_type != "HELPDESK") return response()
            ->json('HELPDESK ONLY.', 200);
        try
        {
            $permission = helpdesk_permissions::where('id', $request->id)
                ->first();
            if ($request->enable == "true")
            {
                if ($permission->permission_apply_to == "")
                {
                    $permission->permission_apply_to = $request->user_perm;
                }
                else
                {
                    $permission->permission_apply_to = $permission->permission_apply_to . ',' . $request->user_perm;
                }
            }
            else
            {
                $moreThanOneGroupStr = $request->user_perm . ',';
                $moreThanOneGroupStr2nd = ',' . $request->user_perm;
                if (str_contains($permission->permission_apply_to, $moreThanOneGroupStr))
                {
                    $permission->permission_apply_to = str_replace($moreThanOneGroupStr, '', $permission->permission_apply_to);
                }
                elseif (str_contains($permission->permission_apply_to, $moreThanOneGroupStr2nd))
                {
                    $permission->permission_apply_to = str_replace($moreThanOneGroupStr2nd, '', $permission->permission_apply_to);
                }
                else
                {
                    $permission->permission_apply_to = str_replace($request->user_perm, '', $permission->permission_apply_to);
                }
            }
            $permission->updated_by = Auth::user()->id;
            $permission->save();
            return response()
                ->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json($e->getMessage() , 200);
        }
    }

    /**
     * Ver página de seleção para consultas de conta.
     *
     * @return view
     */
    public function gestao_consultas_index()
    {
        if (auth()
            ->user()->user_type != "HELPDESK") abort(401);
        return view('helpdesk.consultas');
    }

     /**
     * Pesquisa utilizadores por NIM para efectuar uma consulta
     *
     * @param Request $request
     * @return json
     */
    public function gestao_consultas_search(Request $request)
    {
        if (auth()->user()->user_type != "HELPDESK") abort(401);
        if ($request->ajax())
        {
            $users = User::where('id', 'LIKE', '%' . $request->search . "%")
                ->get();
            if ($users)
            {
                foreach ($users as $key => $user)
                {
                    $id = $user->id;
                    $name = $user->name;
                    $posto = $user->posto;
                    $unidade = $user->unidade;
                    $user_type = $user->user_type;
                    $user_per = $user->user_permission;
                }
                return response()
                    ->json(['id' => $id, 'nome' => $name, 'posto' => $posto, 'unidade' => $unidade, 'user_type' => $user_type, 'user_perm' => $user_per]);
            }
            return response()->json(['nome' => null]);
        }
    }

    /**
     * Efetua uma consulta para uma conta
     *
     * @param Request $request
     * @return view
     */
    public function gestao_consultas_result($id)
    {
        if (auth()->user()->user_type != "HELPDESK") abort(401);

        $origin_id = $id;

        // Obter marcações por DATA em que foi marcada
        $marcacoes = marcacaotable::where('NIM', $id)->orderBy('created_at', 'DESC')
            ->get();
        // Obter notificações para este USER
        $notificacoesTo = notification_table::where('notification_toUser', $id)->orderBy('created_at', 'DESC')
            ->get();
        // Obter notificações criadas por ação deste USER
        $notificacoesBy = notification_table::where('created_by', 'LIKE', '%' . $id . '%')->orderBy('created_at', 'DESC')
            ->get();
        // Formatar historico
        $countMarcacoes = $marcacoes->count();
        $countNotTo = $notificacoesTo->count();
        $countNotBy = $notificacoesBy->count();
        $total = ($countMarcacoes + $countNotTo + $countNotBy);
        $joiner = array_merge($marcacoes->all() , $notificacoesBy->all());
        $joiner = array_merge($joiner, $notificacoesTo->all());
        $historic = array();
        for ($i = 0;$i < $total;$i++)
        {
            if ($joiner[$i]->getTable() == "notification_table")
            {
                if (str_contains($joiner[$i]['created_by'], $id))
                {
                    $historic[$i]['data'] = date($joiner[$i]['created_at']);
                    $historic[$i]['type'] = "CREATE_NOTIFICATION";
                    $historic[$i]['title'] = "Criação de notificação";
                    if ($joiner[$i]['notification_toUser'] != "")
                    {
                        $historic[$i]['message'] = " •  " . $joiner[$i]['notification_text'] . "\n\n Foi enviada para o utilizador: " . $joiner[$i]['notification_toUser'];
                    }
                    else
                    {
                        $historic[$i]['message'] = " •  " . $joiner[$i]['notification_text'] . "\n\n Foi enviada para o(s) grupo(s): " . $joiner[$i]['notification_geral'];
                    }
                    if ($joiner[$i]['notification_type'] == "NORMAL")
                    {
                        $historic[$i]['PS'] = "NORMAL";
                    }
                    else
                    {
                        $historic[$i]['PS'] = "IMPORTANTE";
                    }
                }
                else
                {
                    $historic[$i]['data'] = date($joiner[$i]['created_at']);
                    $historic[$i]['type'] = "NOTIFIED";
                    $historic[$i]['title'] = "Utilizador notificado";
                    $historic[$i]['message'] = "Notificação por\n" . $joiner[$i]['created_by'] . "\n\nNotificação:\n •  " . $joiner[$i]['notification_text'];
                    if ($joiner[$i]['notification_type'] == "NORMAL")
                    {
                        $historic[$i]['PS'] = "NORMAL";
                    }
                    else
                    {
                        $historic[$i]['PS'] = "IMPORTANTE";
                    }
                }
            }
            elseif ($joiner[$i]->getTable() == "marcacaotable")
            {
                $historic[$i]['data'] = date($joiner[$i]['created_at']);
                $historic[$i]['type'] = "REF";
                $historic[$i]['title'] = "Marcação de " . $joiner[$i]['meal'];
                $historic[$i]['message'] = "O utilizador marcou a " . $joiner[$i]['meal'] . " para o dia " . $joiner[$i]['data_marcacao'];
                $historic[$i]['PS'] = $joiner[$i]['local_ref'];
            }
        }
        if(!empty($historic)){
            $historic = collect($historic)->sortBy('data')
              ->reverse()
              ->toArray();
        }

        // Obter informaçao de utilizador
        $len = strlen((string)$id);
        if ($len < 8)
        {
            $id = 0 . (string)$id;
        }
        $user = User::where('id', $id)->first();

        if ($user->user_type == "POC")
        {
            $childrenUsers = users_children::where('parentNIM', $user->id,)
                ->orderBy('childUnidade')
                ->get();
        }
        else
        {
            $childrenUsersError = "Este utilizador não tem perfis associados à sua conta.";
        }

        $tokenVer = express_account_verification_tokens::where('NIM', $user->id)
            ->first();
        if ($tokenVer != null)
        {
            $verified['id'] = $tokenVer->token;
            $verified['created'] = $tokenVer->created_by;
        }
        else
        {
            $verified = null;
        }

        $today = date('Y-m-d');
        $resolver = strtotime("-1 months", strtotime($today));

        return view('helpdesk.consultaresult', [
          'user' => $user,
          'token' => $verified,
          'historico' => $historic,
          'ementas_publicadas' => \App\Models\ementatable::where('created_by', $id)->orWhere('edited_by', $id)->orWhere('created_by', $origin_id)->orWhere('edited_by', $origin_id)->get()->all(),
          'permissions' => \App\Models\helpdesk_permissions::where('updated_by', $id)->orWhere('updated_by', $origin_id)->get()->all(),
          'settings' => \App\Models\helpdesk_settings::where('updated_by', $id)->orWhere('updated_by', $origin_id)->get()->all(),
          'pedidos_quant' => \App\Models\pedidosueoref::where('registeredByNIM', $id)->orWhere('registeredByNIM', $origin_id)->get()->all(),
          'plat_warnings' => \App\Models\PlatformWarnings::where('created_by', $id)->orWhere('created_by', $origin_id)->get()->all(),
          'posts' => \App\Models\TeamPosts::where('posted_by', $id)->orWhere('posted_by', $origin_id)->get()->all(),
          'usrs' => \App\Models\User::where('updated_by', $id)->orWhere('updated_by', $origin_id)->get()->all(),
          'tag_oblig' => \App\Models\users_tagged_conf::where('registered_by', $id)->orWhere('registered_by', $origin_id)->get()->all(),
          'dilis' => \App\Models\Ferias::where('registered_by', $id)->orWhere('registered_by', $origin_id)->get()->all(),
          'marcadas' => \App\Models\marcacaotable::orderBy('data_marcacao')->where('NIM', $id)->orWhere('NIM', $origin_id)->where('data_marcacao', '>=', $today)->get()->all(),
          'quiosque_entries' => \App\Models\entradasQuiosque::orderBy('REGISTADO_DATE', 'DESC')->where('NIM', $id)->orWhere('NIM', $origin_id)->where('REGISTADO_DATE', '<=', $today)->where('REGISTADO_DATE', '>=', $resolver)->get()->all(),
        ]);
    }

    /**
     * Página de definições de plataforma
     *
     * @return view
     */
    public function settings_index()
    {
        if (auth()->user()->user_type != "HELPDESK") abort(401);
        $settings = helpdesk_settings::get()->all();
        return view('helpdesk.settings', ['settings' => $settings]);
    }

    /**
     * Altera uma definição com o tipo boolean
     *
     * @param Request $request
     * @return json
     */
    public function gestao_permissoes_change_bools(Request $request)
    {
        if (auth()->user()->user_type != "HELPDESK") return response()
            ->json('HELPDESK ONLY.', 200);
        try
        {
            $settings = helpdesk_settings::where('id', $request->id)
                ->first();

            if ($request->enable == "true")
            {
                $settings->settingToggleBoolean = "Y";
            }
            else
            {
                $settings->settingToggleBoolean = "N";
            }
            $settings->updated_by = Auth::user()->id;
            $settings->save();
            return response()
                ->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json($e->getMessage() , 200);
        }
    }

    /**
     * Altera uma definição com o tipo integer
     *
     * @param Request $request
     * @return json
     */
    public function gestao_permissoes_change_int(Request $request)
    {
        if (auth()->user()->user_type != "HELPDESK") return response()
            ->json('HELPDESK ONLY.', 200);
        try
        {
            $settings = helpdesk_settings::where('id', $request->id)
                ->first();
            $settings->settingToggleInt = $request->value;
            $settings->updated_by = Auth::user()->id;
            $settings->save();
            return response()
                ->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json($e->getMessage() , 200);
        }
    }

    /**
     * Apaga um utilizador
     *
     * @param string $id NIM de utilizador
     * @return redirect
     */
    public function helpdeskApagarUser($id)
    {
        if (!(new ActiveDirectoryController)->DELETE_MEMBERS()) abort(403);
        
        $NIM = $id;
        while ((strlen((string)$NIM)) < 8) {
          $NIM = 0 . (string)$NIM;
        }
        $user = User::where('id', $NIM)->first();
        $user->delete();
        return redirect()
            ->route('gestão.usersAdmin');
    }

    /**
     * Efectua um reset de conta de utilizador.
     *
     * @param string $id NIM de utilizador
     * @return view
     */
    public function resetUser($id)
    {
        if (auth()->user()->user_type != "HELPDESK") return response()
            ->json('HELPDESK ONLY.', 200);
        $NIM = $id;
        while ((strlen((string)$NIM)) < 8) {
          $NIM = 0 . (string)$NIM;
        }
        $user = User::where('id', $NIM)->first();

        if ($user->user_type == "POC")
        {
            $childrenUsers = users_children::where('parentNIM', $user->id)
                ->get();
            foreach ($childrenUsers as $childrenUser)
            {
                $childrenUser->parentNIM = null;
                $childrenUser->save();
            }

            $associatedUsers = User::where('accountChildrenOf', $id)->get();
            foreach ($associatedUsers as $associatedUser)
            {
                $notifications = new notificationsHandler;
                $notifications->new_notification( /*TITLE*/
                'Conta desassociada', /*TEXT*/
                'A sua conta foi desassociada de ' . $user->id,
                /*TYPE*/
                'WARNING', /*GERAL*/
                null, /*TO USER*/
                $associatedUser->id, /*CREATED BY*/
                'SYSTEM: ACCOUNT DISSOCIATION @' . Auth::user()->id, null);
                $associatedUser->isAccountChildren = 'N';
                $associatedUser->accountChildrenGroup = null;
                $associatedUser->accountChildrenSubGroup = null;
                $associatedUser->save();
            }

            $associatedGroups = users_children_subgroups::where('parentNIM', $id)->get();
            foreach ($associatedGroups as $group)
            {
                $group->delete();
            }
            $associatedSubGroups = users_children_subgroups::where('parentNIM', $id)->get();
            foreach ($associatedSubGroups as $subgroup)
            {
                $subgroup->delete();
            }
        }
        $marcacoes = marcacaotable::where('NIM', $user->id)
            ->get();
        foreach ($marcacoes as $marcacao)
        {
            $marcacao->delete();
        }
        $user->telf = null;
        $user->user_type = 'USER';
        $user->localRefPref = null;
        $user->isAccountChildren = 'N';
        $user->accountChildrenGroup = null;
        $user->accountChildrenSubGroup = null;
        $user->save();
        $notifications = new notificationsHandler;
        $notifications->new_notification( /*TITLE*/
        'Conta reinicializada', /*TEXT*/
        'A sua conta foi reinicializada.',
        /*TYPE*/
        'WARNING', /*GERAL*/
        null, /*TO USER*/
        $user->id, /*CREATED BY*/
        'SYSTEM: ACCOUNT DISSOCIATION @' . Auth::user()->id, null);
        if (isset($childrenUsers))
        {
            return view('helpdesk.childrenUsers-postreset', ['childrenUsers' => $childrenUsers]);
        }
        $returnMessage = "A conta de " . $user->id . " " . $user->posto . " " . $user->name . " foi reinicializada com sucesso.";
        return view('messages.success', ['message' => $returnMessage, 'url' => route('index') , ]);
    }

    /**
     * Página de avisos de plataforma.
     *
     * @return view
     */
    public function appWarningsIndex(){
       try {
        $warnings = \App\Models\PlatformWarnings::get()->all();
        $routes = array();
        $it = 0;
        foreach (\Route::getRoutes()->get() as $value) {
            if ($value->methods()[0]=="GET" && $value->getName()!=null && $value->parameters==null && !str_contains($value->action['controller'], 'Facade')) {
                $routes[$it] = $value->getName();
                $it++;
            }
        }
        return view('helpdesk.warnings', [
            'warnings' => $warnings,
            'routes' => $routes,
        ]);
       } catch (\Throwable $th) {
            abort(500);
       }
    }

     /**
     * Cria um novo aviso de plataforma.
     *
     * @param Request $request
     * @return view
     */
    public function appWarningsNew(Request $request){
        try {
            $GENERAL_WARNING_CREATION = ((new ActiveDirectoryController)->GENERAL_WARNING_CREATION());
            $HELPDESK = Auth::user()->user_type=="HELPDESK";
            if ($HELPDESK==false && $GENERAL_WARNING_CREATION==false) abort(403);
            $warn = new \App\Models\PlatformWarnings();
            $warn->title = $request->title;
            $warn->message = $request->message;
            if($request->link){
                $warn->link = $request->link;
            }
            $warn->to_show = $request->show;
            $warn->created_by = Auth::user()->id;
            $warn->save();

            if ($HELPDESK) {
                return redirect()->route('helpdesk.warnings.index');
            } else {
                return redirect()->route('gestão.warnings.index');
            }
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    /**
     * Apaga um aviso de plataforma.
     *
     * @param string $id
     * @return view
     */
    public function appWarningsDel($id){
        try {
            $GENERAL_WARNING_CREATION = ((new ActiveDirectoryController)->GENERAL_WARNING_CREATION());
            $HELPDESK = Auth::user()->user_type!="HELPDESK";
            if (!$HELPDESK || !$GENERAL_WARNING_CREATION) abort(403);
            $warning = \App\Models\PlatformWarnings::where('id', $id)->delete();
            if ($HELPDESK) {
                return redirect()->route('helpdesk.warnings.index');
            } else {
                return redirect()->route('gestão.warnings.index');
            }
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    /**
     * Retira permissões de um utilizador
     *
     * @param int USER ID
     * @return redirect
     */
    public function takePerms($id){
        try {
            if (! Auth::user()->user_type!="HELPDESK") abort(403);
            while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
            $user = User::where('id', $id)->first();
            $user->user_type = 'USER';
            $user->user_permission = 'GENERAL';
            $user->save();
            return back()->withInput();
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    /**
     * Limpa todos pedidos pendentes de um e para o utilizador
     *
     * @param int USER ID
     * @return redirect
     */
    public function takePendings($id){
        try {
            if (! Auth::user()->user_type!="HELPDESK") abort(403);
            while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
            $pedidos = \App\Models\pending_actions::where('from_id', $id)->orWhere('to_id', $id)->get()->all();
            foreach ($pedidos as $key => $_pd) {
              $not = $_pd['notification_id'];
              $nots = \App\Models\notification_table::where('id', $not)->first()->delete();
              $_pd->delete();
            }
            return back()->withInput();
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    /**
     * Limpar todas as preferencias de uma conta
     *
     * @param int USER ID
     * @return redirect
     */
    public function takePreferencias($id){
        try {
            if (! Auth::user()->user_type!="HELPDESK") abort(403);
            while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
            $user = User::where('id', $id)->first();
            $user->dark_mode = 'Y';
            $user->compact_mode = 'N';
            $user->lite_mode = 'N';
            $user->auto_collapse = 'Y';
            $user->sticky_top = 'N';
            $user->use_icons = 'Y';
            $user->resize_box = 'N';
            $user->flat_mode = 'N';
            $user->auto_expand = 'Y';
            $user->last_nots = '0';
            $user->dismissed_nots = '0';
            $user->last_login = null;
            $user->localRefPref = null;
            $user->user_type = 'USER';
            $user->user_permission = 'GENERAL';
            $user->save();
            $pedidos = \App\Models\pending_actions::where('from_id', $id)->orWhere('to_id', $id)->get()->all();
            foreach ($pedidos as $key => $_pd) {
              $not = $_pd['notification_id'];
              $nots = \App\Models\notification_table::where('id', $not)->first()->delete();
              $_pd->delete();
            }
            return back()->withInput();
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function clearAllPrefPerm($id){
      try {
          if (! Auth::user()->user_type!="HELPDESK") abort(403);
          while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }

          $user = User::where('id', $id)->first();
          $user->dark_mode = 'Y';
          $user->compact_mode = 'N';
          $user->lite_mode = 'N';
          $user->auto_collapse = 'Y';
          $user->sticky_top = 'N';
          $user->use_icons = 'Y';
          $user->resize_box = 'N';
          $user->flat_mode = 'N';
          $user->auto_expand = 'Y';
          $user->last_nots = '0';
          $user->dismissed_nots = '0';
          $user->last_login = null;
          $user->localRefPref = null;
          return back()->withInput();
      } catch (\Exception $e) {
        abort(500);
      }
    }

    public function disableAccount($id){
      try {
        if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }

        $user = User::where('id', $id)->first();
        $user->account_verified = 'N';
        $user->save();
        return back()->withInput();
      } catch (\Exception $e) {
          abort(500);
      }
    }

    public function blockAccount($id){
      try {
        if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $user = User::where('id', $id)->first();
        $user->lock = 'Y';
        $user->save();
        return back()->withInput();
      } catch (\Exception $e) {
          abort(500);
      }
    }

    public function deleteAccount($id){
      try {
        if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $user = User::where('id', $id)->first();
        $marcacoes = \App\Models\marcacaotable::where('NIM', $id)->get()->all();
        foreach ($marcacoes as $tag) { $tag->delete(); }
        $user->delete();
        return back()->withInput();
      } catch (\Exception $e) {
          abort(500);
      }
    }

    public function logOffAll($id){
      try {
        if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        Auth::logoutOtherDevices($id);
        return back()->withInput();
      } catch (\Exception $e) {
          abort(500);
      }
    }

    public function removeTags($id, $ref){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\marcacaotable::where('data', '>=', $today)
                  ->where('NIM', $id)->where('meal', $ref)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }

    public function removeAllTags($id){
      try {
        if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\marcacaotable::where('data', '>=', $today)
                  ->where('NIM', $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
        abort(500);
      }
    }

    public function removeAllQuantByUsr($id){
      try {
        if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');

        $tags = \App\Models\pedidosueoref::where('data_pedido', '>=', $today)
                  ->where('registeredByNIM', $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
        abort(500);
      }
    }

    public function userRemoveTagOblig($id){
      try {
        if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $user = User::where('id', $id)->first();
        $tag = \App\Models\users_tagged_conf::where('registered_to', $id)->first();
        $user->isTagOblig = null;
        $user->save();
        $tag->delete();
        return back()->withInput();
      } catch (\Exception $e) {
          abort(500);
      }
    }

    public function removeEntries($id, $ref){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\entradasQuiosque::where('NIM', $id)->where('REF', $ref)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }

    public function removeAllEntries($id){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\entradasQuiosque::where('NIM', $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }

    public function removeAssocTo($id){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\pending_actions::where('action_type', 'ASSOCIATION')->where('to_id', $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }

    public function removeAssocFrom($id){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\pending_actions::where('action_type', 'ASSOCIATION')->where('from_id', $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }

    public function removeNotsTo($id){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\notification_table::where('notification_toUser', $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }

    public function removeNotsFrom($id){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\notification_table::where('created_by', 'LIKE', '%' . $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }

    public function removePosts($id){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\TeamPosts::where('posted_by', $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }

    public function removeWarnings($id){
      try {
      	if (! Auth::user()->user_type!="HELPDESK") abort(403);
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $today = date('Y-m-d');
        $tags = \App\Models\PlatformWarnings::where('created_by', $id)->get()->all();
        foreach ($tags as $tag) {
          $tag->delete();
        }
        return back()->withInput();
      } catch (\Exception $e) {
      	abort(500);
      }
    }






  /**
   * @ignore
   */
    public function test2()
    {
        $today = date("Y-m-d");
        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
        $mes_index = date('m', strtotime($today));
        $data['posto'] = "2ºCABO";
        $data['nome'] = "PEDRO ROCHA";
        $data['pw'] = "123412I4HU1239IE4BN21E3NI12";
        $data['data_alteracao'] = date('d', strtotime($today)).' '.$mes[($mes_index - 1)];
        $data['ref'] = "almoço";
        $data['sopa_old'] = "sopa_old teste";
        $data['prato_old'] = "prato_old teste";
        $data['sobremesa_old'] = "sobremesa_old teste";
        $data['sopa'] = "sopa";
        $data['prato'] = "prato";
        $data['sobremesa'] = "sobremesa";
        $data['by'] = "0000000" . ' SOLDADO ' . 'TESTE';
        $NIM = Auth::user()->id;
        $len = strlen((string)$NIM);
        if ($len < 8)
        {
            $NIM = 0 . (string)$NIM;
        }

        return new ementaChanged($data);
    }

    /**
     * @ignore
     */
    public function addAllToQR(){
        $users = User::get()->all();
        foreach ($users as $key => $user) {
            if ($user['unidade']=="UnAp/CmdPess" || $user['unidade']=="UnAp/CmdPess/QSO" || $user['unidade']=="GCSVNGaia") continue;
            $pedido = new \App\Models\QRsGerados();
            $pedido->NIM = $user['id'];
            $pedido->save();
        }
        return "OK";
    }

    /**
     * @ignore
     */
    public function RemovePasswordFieldReseter(){
        $users = User::get()->all();
        foreach ($users as $key => $user) {
            if( $user->mustResetPassword != 'N'){
                $user->mustResetPassword = 'N';
                $user->save();
            }
        }
        return "OK";
    }

    /**
     * @ignore
     */
    public function RemoveTagObligPERM(){
        $users = User::get()->all();
        foreach ($users as $key => $user) {
            if( $user->isTagOblig == 'PERM'){
                $user->isTagOblig = null;
                $user->save();
            }
        }
        return "OK";
    }

    /**
     * @ignore
     */
    public function resetAllPasswords(){
        $users = User::where('id', '06140614')->first();

        $users->password = \Hash::make('06140614');
        $users->save();

        return "OK";
    }

/**
     * @ignore
     */
    public function test()
    {

        $marcacoes =  \App\Models\marcacaotable::get()->all();

        foreach($marcacoes as $key => $marc){
            $_user_id = $marc['NIM'];

            while ((strlen((string)$_user_id)) < 8) {
                $_user_id = 0 . (string)$_user_id;
            }

            try {
                $marc->unidade = \App\Models\User::where('id', $_user_id)->value('unidade');
                $marc->save();
            } catch (\Throwable $th) {
                $marc->unidade = null;
                $marc->save();
            }
        }
        return "OK";

    }

    /**
     * @ignore
     */
    public function PopulateUnidade(){
        $equiosque = \App\Models\entradasquiosque::get()->all();

        foreach ($equiosque as $key => $et) {
            $u_id = $et['NIM'];
            while ((strlen((string)$u_id)) < 8) {
                $u_id = 0 . (string)$u_id;
            }

            $user_unidade = \App\Models\User::where('id', $u_id)->first();
            if (isset($user_unidade) && $user_unidade != null) {
                $user_unidade = $user_unidade['unidade'];
                $et->UNIDADE = $user_unidade;
                $et->save();
            }

        }
    }

    /**
     * @ignore
     */
    public function getAllMarcações(){
        $marcacoes = \App\Models\marcacaotable::where('created_by', 'POC@28311793')->count();
        return $marcacoes;
    }

    /**
     * @ignore
     */
    public function populateCivilPostoQuio(){
        $marcacoes = \App\Models\entradasquiosque::all();
        $usersss = array();
        $it = 0;
        foreach ($marcacoes as $key => $tag) {
            $NIM = $tag['NIM'];

            $user = User::where('id', $NIM)->first();

            if($user==null){
                while ((strlen((string)$NIM)) < 8) {
                    $NIM = 0 . (string)$NIM;
                }

                $user = User::where('id', $NIM)->first();
            }

            if($user==null){
                $usersss[$it] = $NIM;
                $it++;
                continue;
            }

            if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP." ||
                $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" ||$user['posto'] == "TIG.1" || $user['posto'] == "TIE"){
                    $marcacoes[$key]->CIVIL = 'Y';
                    $marcacoes[$key]->save();
            } else {
                $marcacoes[$key]->CIVIL = 'N';
                $marcacoes[$key]->save();
            }

        }
        return $usersss;
    }

    /**
     * @ignore
     */
    public function CountEntradas(){
        $entradas = \App\Models\entradasquiosque::where('REF', '2REF')->where('REGISTADO_DATE', '2022-02-04')->where('unidade', 'UnAp/CmdPess')->get();
        $count = 0;

        foreach ($entradas as $key => $value) {
            $NIM = $value['NIM'];
            while ((strlen((string)$NIM)) < 8) {
                $NIM = 0 . (string)$NIM;
            }

            $user = \App\Models\User::where('id', $NIM)->first()->get();
            if ($user != null) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @ignore
     */
    public function DelNotsField(){
        $users = User::all();
        foreach ($users as $key => $user) {
            $user->last_nots = 0;
            $user->save();
        }
        return "OK";
    }

    /**
     * @ignore
     */
    public function DownloadAllPics(){
      $users = User::all();
      $users_not_down = array();

      $path = public_path('assets\profiles');


      foreach ($users as $key => $user) {
        $NIM = $user['id'];
        while ((strlen((string)$NIM)) < 8) {
            $NIM = 0 . (string)$NIM;
        }
        $url = 'http://cpes-wise2/Unidades/Fotos/'.$NIM.'.jpg';
        $file_name = basename($url);

          try {
            if (file_put_contents($path.'/'. $file_name, file_get_contents($url)))
            {
                echo $NIM.": descarregado<br />";
            }
          } catch (\Exception $e) {
              echo $NIM.": 404<br />";
          }

      }
    }

    /**
     * @ignore
     */
    public function GenerateReport(){
      return Excel::download(new \App\Exports\PedidosQuantExport('2022/03/31'), 'test.xlsx');
    }

    /**
     * @ignore
     */
    public function GenerateReport2(){
      return Excel::download(new \App\Exports\MonthlyExport('03'), 'test.xlsx');
    }

    /**
     * @ignore
     */
    public function GenerateReport3(){
      $_date = "2022-04-07|2022-04-07";
      $date = explode("|", $_date);

      return Excel::download(new \App\Exports\GeneralExport($date), 'test.xlsx');
    }

    /**
     * @ignore
     */
    public function populateCivilPostoMarca(){
        $marcacoes = marcacaotable::all();
        $usersss = array();
        $it = 0;
        foreach ($marcacoes as $key => $tag) {
            $NIM = $tag['NIM'];

            $user = User::where('id', $NIM)->first();

            if($user==null){
                while ((strlen((string)$NIM)) < 8) {
                    $NIM = 0 . (string)$NIM;
                }

                $user = User::where('id', $NIM)->first();
            }

            if($user==null){
                $usersss[$it] = $NIM;
                $it++;
                continue;
            }

            if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP." ||
                $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" ||$user['posto'] == "TIG.1" || $user['posto'] == "TIE"){
                    $marcacoes[$key]->civil = 'Y';
                    $marcacoes[$key]->save();
            } else {
                $marcacoes[$key]->civil = 'N';
                $marcacoes[$key]->save();
            }

        }
        return $usersss;
    }
}
