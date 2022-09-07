<?php
namespace App\Http\Controllers;

use PDF;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\marcacaotable;
use App\Models\users_children;
use App\Models\users_children_subgroups;
use App\Http\Controllers\QuiosqueController;
use Facades\App\Models\user_type_permissions;
use Facades\App\Models\helpdesk_permissions;
use App\Models\express_account_verification_tokens;
use App\Mail\passwordResetNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Oferece várias das funcionalidades de gestão.
 */
class gestaoHandlerController extends Controller
{


    /**
     * Formata o NIM para ter os 8 carácteres.
     * @param string $NIM
     *
     * @return string NIM formatado
     */
    public function checkNIMLen($NIM)
    {
        while ((strlen((string)$NIM)) < 8) {
          $NIM = 0 . (string)$NIM;
        }

        return $NIM;
    }

    /**
     * Calcula uma percentagem.
     * @param int $num_amount
     * @param int $num_total
     *
     * @return int Percentagem de (($num_amount * 100) / $num_total)
     */
    function cal_percentage($num_amount, $num_total) {
        return number_format((($num_amount * 100) / $num_total), 0);
      }

    public function gestao_index(){
      return view('gestao.index');
    }

  /**
   * Procura ADMINS/POCS a que um USER se pode associar
   * @param Request $request
   *
   * @return json
   */
    public function searchUserAssociate(Request $request)
    {
        if ($request->ajax())
        {
            $users = User::where('id', 'LIKE', '%' . $request->search . "%")
                ->where('unidade', Auth::user()->unidade)
                ->where('trocarUnidade', null)
                ->where('user_type', 'ADMIN')
                ->orWhere('user_type', 'POC')
                ->get();
            if ($users)
            {
                $usersArray = [];
                foreach ($users as $key => $user)
                {
                    if ($user->user_type != "USER" || $user->user_type != "HELPDESK")
                    {
                        $usersArray['id'] = $user->id;
                        $usersArray['name'] = $user->name;
                        $usersArray['posto'] = $user->posto;
                        $usersArray['type'] = $user->user_type;
                    }
                }
                return response()
                    ->json(['data' => $usersArray]);
            }
            return response()->json(['nome' => null]);
        }
    }

  /**
   * Associa um utilizador a um ADMIN\POC.
   * @param string $user
   * @param string $toParent
   *
   * @return redirect
   */
    public function associateUser($user, $toParent)
    {
        $parentUser = User::where('id', $toParent)->first();
        $newNotification = new notificationsHandler;
        $userToAssc = users_children::where('childID', $user)->first();
        if ($userToAssc)
        {
            $userToAssc->childUnidade = $parentUser->unidade;
            $userToAssc->trocarUnidade = null;
            $userToAssc->parentNIM = $toParent;
            $userToAssc->childGroup = null;
            $userToAssc->childSubGroup = null;
            $name = $userToAssc->childNome;
            $posto = $userToAssc->childPosto;
        }
        else
        {
            $userToAssc = User::where('id', $user)->first();
            $userToAssc->unidade = $parentUser->unidade;
            $userToAssc->trocarUnidade = null;
            $userToAssc->isAccountChildren = 'Y';
            $userToAssc->accountChildrenOf = $toParent;
            $userToAssc->accountChildrenGroup = null;
            $userToAssc->accountChildrenSubGroup = null;
            $userToAssc->updated_by = Auth::user()->id;
            $name = $userToAssc->name;
            $posto = $userToAssc->posto;
            $notifications->new_notification( /*TITLE*/
            'Conta transferida', /*TEXT*/
            'A sua conta foi transferida, e agora faz parte do grupo de gestão do ' . $parentUser->posto . ' ' . $parentUser->name . '.',
            /*TYPE*/
            'NORMAL', /*GERAL*/
            null, /*TO USER*/
            $user, /*CREATED BY*/
            'SYSTEM: ACCOUNT MOVED @' . Auth::user()->id, null);
        }
        $userToAssc->save();
        $newNotification->new_notification('Conta transferida', 'O ' . $posto . ' ' . $user . ' ' . $name . ' foi transferido para o seu grupo de gestão por um administrador.', 'NORMAL', null, $toParent, 'SYSTEM: ACCOUNT MOVED @' . Auth::user()->id, null);
        return redirect()
            ->back();
    }

    /**
     * Bloqueia um utilizador, e remove TODAS as marcações deste utilizador.
     * @param Request $request
     *
     * @return redirect
     */
    public function lockUser(Request $request)
    {
        if (!(new ActiveDirectoryController)->BLOCK_MEMBERS()) abort(403);
        $id = $request->nim;
        while ((strlen((string)$id)) < 8) {
          $id = 0 . (string)$id;
        }

        $marcaçoes = \App\Models\marcacaotable::where('NIM', $id)->get()
            ->all();

        foreach ($marcaçoes as $key => $meal)
        {
            $meal->delete();
        }

        $user = User::where('id', $id)->first();
        $user->lock = 'Y';
        $user->save();
        return redirect()
            ->back();
    }

    /**
     * Desbloqueia um utilizador.
     * @param Request $request
     *
     * @return redirect
     */
    public function unlockUser(Request $request)
    {
        if (!(new ActiveDirectoryController)->BLOCK_MEMBERS()) abort(403);

        $id = $request->nim;
        while ((strlen((string)$id)) < 8) {
          $id = 0 . (string)$id;
        }
        $user = User::where('id', $id)->first();
        $user->lock = 'N';
        $user->save();
        $notifications = new notificationsHandler;
        $notifications->new_notification(
          /*TITLE*/
          'Conta desbloqueada.',
          /*TEXT*/
          'A sua conta foi desbloqueada. A partir de agora, tem novamente acesso às funcionalidades.',
          /*TYPE*/
          'WARNING',
          /*GERAL*/
          '',
          /*TO USER*/
          $id,
          /*CREATED BY*/
          'UNLOCKED @' . Auth::user()->id,
          /*LAPSES AT*/
          null
        );

        return redirect()
            ->back();
    }


    /**
     * Transfere um utilizador de uma unidade para a outra
     * Unidade NOVA guardada em DB, campo 'trocarUnidade'
     * @param Request $request
     *
     * @return view profile.saved
     */
    public function transferUsers(Request $request)
    {

        $id = $request->from_user;

        while ((strlen((string)$id)) < 8) {
          $id = 0 . (string)$id;
        }

        $from_user = User::where('id', $id)->first();
        $from_user->accountReplacementPOC = $request->to_user;
        $from_user->save();
        return view('profile.saved', ['changedUnidade' => true, 'newUnidade' => \App\Models\unap_unidades::where('slug', $from_user->trocarUnidade)
            ->value('name') , 'oldUnidade' => \App\Models\unap_unidades::where('slug', $from_user->unidade)
            ->value('name') , 'url' => route('profile.index') ]);
    }

    /**
     * Procura utilizadores a partir do NIM
     * Utilizando SQL LIKE;
     * @param Request $request
     *
     * @return json
     */
    public function NIM_search(Request $request)
    {
            if ($request->ajax())
            {
                $users = User::where('id', 'LIKE', '%' . $request->search . "%")
                    ->get();
                if ($users)
                {
                    $users_return = array();
                    foreach ($users as $key => $user)
                    {
                        $id = $user->id;
                        while ((strlen((string)$id)) < 8) {
                           $id = 0 . (string)$id;
                        }

                        $filename = "assets/profiles/".$id.".PNG";
                        $filename2 = "/assets/profiles/".$id.".JPG";

                        if(file_exists(public_path($filename))) {
                          $pic = asset($filename);
                        } else if (file_exists(public_path($filename2))) {
                          $pic = asset($filename2);
                        } else {
                            $pic = 'http://cpes-wise2/Unidades/Fotos/'.$id.'.jpg';
                        }

                        $users_return[$user->id]['pic'] = $pic;
                        $users_return[$user->id]['id'] = $id;
                        $users_return[$user->id]['name'] = $user->name;
                        $users_return[$user->id]['posto'] = ($user->posto!=null) ? $user->posto : "Não preenchido";
                        $users_return[$user->id]['unidade'] = \App\Models\unap_unidades::where('slug', $user->unidade)->value('name');
                        $users_return[$user->id]['user_type'] = $user->user_type;
                    }
                    return (empty($users_return)) ? 0 : response()->json($users_return);
                }
                return response()->json(['nome' => null]);
            }

    }

    /**
     * Procura hóspedes a partir do número do quarto.
     * @param Request $request
     *
     * @return json
     */
    public function QUARTO_search(Request $request)
    {
            if ($request->ajax())
            {
                $users = \App\Models\hospede::where('quarto', 'LIKE', '%' . $request->search . "%")
                    ->where('local', auth()->user()->unidade)->get();
                if ($users)
                {
                    $users_return = array();
                    foreach ($users as $key => $user)
                    {
                        $id = $user->id;
                        while ((strlen((string)$id)) < 8) {
                           $id = 0 . (string)$id;
                        }
                        $users_return[$user->id]['id'] = $id;
                        $users_return[$user->id]['name'] = $user->name;
                        $users_return[$user->id]['type'] = $user->type;
                        $users_return[$user->id]['type2'] = ($user->type_temp=="PERM") ? "Permanente" : "Temporário";
                        $users_return[$user->id]['contacto'] = $user->contacto;
                    }
                    return (empty($users_return)) ? 0 : response()->json($users_return);
                }
                return response()->json(['nome' => null]);
            }
    }

    /**
     * Procura hóspedes a partir do nome.
     * @param Request $request
     *
     * @return json
     */
    public function HOSPEDE_search(Request $request)
    {
            if ($request->ajax())
            {
                $users = \App\Models\hospede::where('name', 'LIKE', '%' . $request->search . "%")
                    ->where('local', auth()->user()->unidade)->get();
                if ($users)
                {
                    $users_return = array();
                    foreach ($users as $key => $user)
                    {
                        $id = $user->id;
                        while ((strlen((string)$id)) < 8) {
                           $id = 0 . (string)$id;
                        }
                        $users_return[$user->id]['id'] = $id;
                        $users_return[$user->id]['name'] = $user->name;
                        $users_return[$user->id]['type'] = $user->type;
                        $users_return[$user->id]['type2'] = ($user->type_temp=="PERM") ? "Permanente" : "Temporário";
                        $users_return[$user->id]['contacto'] = $user->contacto;
                    }
                    return (empty($users_return)) ? 0 : response()->json($users_return);
                }
                return response()->json(['nome' => null]);
            }
    }


    /**
     * Procura um utilizador a partir do nome.
     * @param Request $request
     *
     * @return json
     */
    public function NAME_search(Request $request)
    {
            if ($request->ajax())
            {
                $users = User::where('name', 'LIKE', '%' . $request->search . "%")
                    ->get();
                if ($users)
                {
                    $users_return = array();
                    foreach ($users as $key => $user)
                    {
                        $id = $user->id;
                        while ((strlen((string)$id)) < 8) {
                           $id = 0 . (string)$id;
                        }

                        $filename = "assets/profiles/".$id.".PNG";
                        $filename2 = "/assets/profiles/".$id.".JPG";

                        if(file_exists(public_path($filename))) {
                          $pic = asset($filename);
                        } else if (file_exists(public_path($filename2))) {
                          $pic = asset($filename2);
                        } else {
                            $pic = 'http://cpes-wise2/Unidades/Fotos/'.$id.'.jpg';
                        }

                        $users_return[$user->id]['pic'] = $pic;
                        $users_return[$user->id]['id'] = $id;
                        $users_return[$user->id]['name'] = $user->name;
                        $users_return[$user->id]['posto'] = ($user->posto!=null) ? $user->posto : "Não preenchido";
                        $users_return[$user->id]['unidade'] = \App\Models\unap_unidades::where('slug', $user->unidade)->value('name');
                        $users_return[$user->id]['user_type'] = $user->user_type;
                    }

                    return (empty($users_return)) ? 0 : response()->json($users_return);
                }
                return response()->json(['nome' => null]);
            }

    }

    /**
    * Ver utilizadores USERS\CHILDREN associados a $id.
    * Ordenados por grupos / subgrupos
    * @param string $id
    *
    * @return View
    */
    public function viewChildreUser($id)
    {
        while ((strlen((string)$id)) < 8) {
          $id = 0 . (string)$id;
        }
        $childrenUsers = users_children::where('childID', $id)->first();
        if ($childrenUsers == null)
        {
            $childrenUsers = User::where('id', $id)->first();
            if ($childrenUsers->accountChildrenOf != Auth::user()
                ->id) abort(403);
            if ($childrenUsers == null)
            {
                abort(500);
            }
            else
            {
                $type = "UTILIZADOR";
            }
        }
        else
        {
            $type = "CHILDREN";
        }
        $user = [];
        if ($type == "UTILIZADOR")
        {
            $user['type'] = "UTILIZADOR";
            $user['id'] = $childrenUsers->id;
            $user['name'] = $childrenUsers->name;
            $user['email'] = $childrenUsers->email;
            $user['posto'] = $childrenUsers->posto;
            $user['unidade'] = $childrenUsers->unidade;
            $user['trocarUnidade'] = $childrenUsers->trocarUnidade;
            $user['localRefPref'] = $childrenUsers->localRefPref;
            $user['groupID'] = $childrenUsers->accountChildrenGroup;
            $user['subGroupID'] = $childrenUsers->accountChildrenSubGroup;
            $user['seccao'] = $childrenUsers->seccao;
            $user['descriptor'] = $childrenUsers->descriptor;
            $user['already_tagged'] = ($childrenUsers->isTagOblig==null) ? false : true;
        }
        elseif ($type == "CHILDREN")
        {
            $user['type'] = "CHILDREN";
            $user['id'] = $childrenUsers->childID;
            $user['name'] = $childrenUsers->childNome;
            $user['email'] = $childrenUsers->childEmail;
            $user['posto'] = $childrenUsers->childPosto;
            $user['unidade'] = $childrenUsers->childUnidade;
            $user['trocarUnidade'] = $childrenUsers->trocarUnidade;
            $user['localRefPref'] = $childrenUsers->localRefPref;
            $user['groupID'] = $childrenUsers->groupID;
            $user['subGroupID'] = $childrenUsers->childSubGroup;
            $user['seccao'] = $childrenUsers->seccao;
            $user['descriptor'] = $childrenUsers->descriptor;
            $user['already_tagged'] = ($childrenUsers->isTagOblig==null) ? false : true;
        }
        $allGroupRefs = \App\Models\users_children_subgroups::where('parentNIM', Auth::user()->id)
            ->where('groupUnidade', Auth::user()
            ->unidade)
            ->get();
        if (!empty($allGroupRefs[0]))
        {
            $index = 0;
            foreach ($allGroupRefs as $key => $grupo)
            {
                $grupos[$index]['nome'] = $grupo->groupName;
                $grupos[$index]['ref'] = $grupo->groupID;
                $index++;
            }
        }
        else
        {
            $grupos = null;
        }
        if ($user['groupID'] != null)
        {
            $groupName = users_children_subgroups::where('groupID', $user['groupID'])->first()->groupName;
        }
        else
        {
            $groupName = "GERAL";
        }
        $user['groupName'] = $groupName;
        if ($user['subGroupID'] != null)
        {
            $subGroupName = \App\Models\users_children_sub2groups::where('subgroupID', $user['subGroupID'])->first()->groupName;
        }
        else
        {
            $subGroupName = "GERAL";
        }
        $user['subGroupName'] = $subGroupName;
        $marcaçoes = \App\Models\marcacaotable::where('NIM', $user['id'])->where('data_marcacao', '>', date('Y-m-d'))->orderBy('data_marcacao')
            ->get();

        $ementaTable = \App\Models\ementatable::orderBy('data')->where('data', '>=', date('Y-m-d'))->get();

        $checkedMarcacoes = null;
        # || auth()->user()->posto == "ENC.OP." || auth()->user()->posto == "TIA" || auth()->user()->posto == "TIG.1" || auth()->user()->posto == "TIE"
        if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP."||
        $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" ||$user['posto'] == "TIG.1" || $user['posto'] == "TIE" )
        {
            $checkedMarcacoes = \App\Models\user_children_checked_meals::where('user', $user['id'])->orderBy('data')->where('data', '>', date('Y-m-d'))
                ->get();
        }

        $datasMarcadas = array();
        $marcacaoEmenta = array();
        $marcacoesVerificadas = array();
        foreach ($marcaçoes as $marcaçao)
        {
            $datasMarcadas[$marcaçao
                ->id] = $marcaçao->data_marcacao;
            $refeiçaoMarcada[$marcaçao
                ->id] = $marcaçao->meal;
        }
        foreach ($datasMarcadas as $i => $dateToAdd)
        {
            (array)$marcacaoEmenta[$i] = $ementaTable->where('data', '=', $dateToAdd)->first();
            (array)$marcacaoEmenta[$i]['meal'] = $refeiçaoMarcada[$i];
            if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP."||
            $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" ||$user['posto'] == "TIG.1" || $user['posto'] == "TIE" )
            {
                (array)$marcacoesVerificadas[$i] = $checkedMarcacoes->where('data', '=', $dateToAdd)->first();
                (array)$marcacoesVerificadas[$i]['meal'] = $refeiçaoMarcada[$i];
            }
        }
        $marcaçoes =\App\Models\marcacaotable::where('NIM', $user['id'])->where('data_marcacao', '>', date('Y-m-d'))->orderBy('data_marcacao')
            ->get();
        $ementaTable = \App\Models\ementatable::orderBy('data')->get();
        $ementaPopulatedMarcaçoes[] = array();
        $datasMarcadas = [];
        foreach ($marcaçoes as $i => $marcaçao)
        {
            $datasMarcadas[$i]['data'] = $marcaçao->data_marcacao;
            $datasMarcadas[$i]['meal'] = $marcaçao->meal;
        }
        $ementaFormatadaDia = app('App\Http\Controllers\URLHandler')->formatEmenta($ementaTable);
        if ($datasMarcadas)
        {
            foreach ($ementaFormatadaDia as $key => $refPorDia)
            {
                if (app('App\Http\Controllers\URLHandler')->verificarEmMarcacoes($datasMarcadas, $refPorDia['data'], $refPorDia['meal']))
                {
                    $ementaFormatadaDia[$key]['marcado'] = "1";
                }
                else
                {
                    $ementaFormatadaDia[$key]['marcado'] = "0";
                }
            }
        }
        $unidades = \App\Models\unap_unidades::get()->all();
        $locaisRef = \App\Models\locaisref::get()->all();
        $max = ((new checkSettingsTable)->REMOVEMAX());
        $addmax = ((new checkSettingsTable)->ADDMAX());

        return view('gestao.children_user', ['user' => $user, 'group' => $groupName, 'subgroup' => $subGroupName, 'possibleGroups' => $grupos, 'marcadas' => $marcaçoes, 'ementa' => $marcacaoEmenta, 'marcadasVerificadas' => $marcacoesVerificadas, 'allRefs' => $ementaFormatadaDia, 'maxDays' => $max, 'maxDaysMarcar' => $addmax, 'locais' => $locaisRef, 'unidades' => $unidades, ]);
    }

    /**
    * Formatar informação de utilizadores assocadiados por Grupos / SubGroupos
    * @param string $id
    *
    * @return array $gruposFormatted
    */
    public function formatChildrenUsers($table)
    {
        $iteration = 0;
        $todosGrupos = \App\Models\users_children_subgroups::where('parentNIM', auth()->user()
            ->id)
            ->orWhere('parent2nNIM', auth()
            ->user()
            ->id)
            ->where('groupUnidade', auth()
            ->user()
            ->unidade)
            ->get();
        $todosUsers = \App\Models\User::where('isAccountChildren', 'Y')->where('accountChildrenOf', auth()
            ->user()
            ->id)
            ->orWhere('account2ndChildrenOf', auth()
            ->user()
            ->id)
            ->get();
        $todosChildren = \App\Models\users_children::where('parentNIM', auth()->user()
            ->id)
            ->orWhere('parent2nNIM', auth()
            ->user()
            ->id)
            ->get();
        $gruposFormatted = [];
        foreach ($todosGrupos as $key => $grupo)
        {
            $gruposFormatted[$grupo->groupID]['groupID'] = $grupo->groupID;
            $gruposFormatted[$grupo->groupID]['groupName'] = $grupo->groupName;
            $gruposFormatted[$grupo->groupID]['groupUnidade'] = $grupo->groupUnidade;
            $gruposFormatted[$grupo->groupID]['groupLocalPrefRef'] = $grupo->groupLocalPrefRef;
            $todosSubgrupos = \App\Models\users_children_sub2groups::where('parentGroupID', $grupo->groupID)
                ->get();
            if ($todosSubgrupos)
            {
                foreach ($todosSubgrupos as $key => $subgrupo)
                {
                    $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['subgroupID'] = $subgrupo->subgroupID;
                    $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['subgroupName'] = $subgrupo->subgroupName;
                    $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['subgroupLocalPref'] = $subgrupo->subgroupLocalPref;
                    foreach ($todosUsers as $key => $users)
                    {
                        if ($users->accountChildrenGroup == $grupo->groupID && $users->accountChildrenSubGroup == $subgrupo->subgroupID)
                        {
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->id]['type'] = "UTILIZADOR";
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->id]['id'] = $users->id;
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->id]['name'] = $users->name;
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->id]['posto'] = $users->posto;
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->id]['unidade'] = $users->unidade;
                        }
                    }
                    foreach ($todosChildren as $key => $users)
                    {
                        if ($users->childGroup == $grupo->groupID && $users->childSubGroup == $subgrupo->subgroupID)
                        {
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->childID]['type'] = "CHILDREN";
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->childID]['id'] = $users->childID;
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->childID]['name'] = $users->childNome;
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->childID]['posto'] = $users->childPosto;
                            $gruposFormatted[$grupo->groupID]['SUBGRUPOS'][$subgrupo->subgroupID]['USERS'][$users->childID]['unidade'] = $users->childUnidade;
                        }
                    }
                }
            }
            foreach ($todosUsers as $key => $users)
            {
                if ($users->accountChildrenGroup == $grupo->groupID && $users->accountChildrenSubGroup == null)
                {
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['type'] = "UTILIZADOR";
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['id'] = $users->id;
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['name'] = $users->name;
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['posto'] = $users->posto;
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['unidade'] = $users->unidade;
                }
            }
            foreach ($todosChildren as $users)
            {
                if ($users->childGroup == $grupo->groupID && $users->childSubGroup == null)
                {
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['type'] = "CHILDREN";
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['id'] = $users->childID;
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['name'] = $users->childNome;
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['posto'] = $users->childPosto;
                    $gruposFormatted[$grupo->groupID]['USERS'][$users->id]['unidade'] = $users->childUnidade;
                }
            }
        }
        if (empty($gruposFormatted))
        {
            return null;
        }
        return $gruposFormatted;
    }

    /**
    * Ver todos os utilizadores com a mesma unidade consoante nivel de permissao de Auth::user()
    *
    * @return View gestao.utilizadores
    */
    public function usersAdmin()
    {
        if (!(new ActiveDirectoryController)
            ->VIEW_ALL_MEMBERS()) abort(403);
        if ((new ActiveDirectoryController)
            ->MEALS_TO_EXTERNAL())
        {
            $users = User::orderBy('unidade')->orderBy('posto')
                ->where('account_verified', 'Y')
                ->get();
        }
        else
        {
            $users = User::orderBy('unidade')->orderBy('posto')
                ->where('account_verified', 'Y')
                ->where('unidade', Auth::user()
                ->unidade)
                ->get();
        }

        foreach ($users as $key => $user) {
          $sec[] = $user['seccao'];
        }
        $sec = array_unique($sec);
        $__all_secs = "";

        end($sec);
        $__last_key = key($sec);

        foreach ($sec as $key => $value) {
          $value = str_replace("\\", "/", $value);
          $__all_secs = $__all_secs . "'" .  $value . "'";
          if ($key!=$__last_key) {
            $__all_secs = $__all_secs . ", ";
          }
        }

        $unidades = \App\Models\unap_unidades::get()->all();
        return view('gestao.utilizadores', ['users' => $users, 'unidades' => $unidades, ]);
    }

    /**
    * Ver utilizadores associados de Auth::user()
    *
    * @return View gestao.assoc_users
    */
    public function associatedUsersAdmin()
    {
        if (Auth::user()->user_type == "HELPDESK" || Auth::user()->user_type == "USER") abort(403);
        $childrenUsers = $this::formatChildrenUsers(\App\Models\User::where('accountChildrenOf', Auth::user()->id)->orWhere('account2ndChildrenOf', Auth::user()->id)
            ->where('accountChildrenGroup', '<>', null)
            ->where('unidade', Auth::user()->unidade)
            ->where('isAccountChildren', 'Y')
            ->orderBy('accountChildrenGroup')
            ->get());
        $allChildren = \App\Models\users_children::where('parentNIM', auth()->user()->id)->orWhere('parent2nNIM', auth()->user()->id)
            ->where('accountVerified', 'Y')
            ->where('childGroup', null)
            ->where('childUnidade', Auth::user()->unidade)
            ->get();
        $allUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)->orWhere('account2ndChildrenOf', Auth::user()->id)
            ->where('accountChildrenGroup', null)
            ->where('unidade', Auth::user()->unidade)
            ->where('isAccountChildren', 'Y')
            ->orderBy('accountChildrenGroup')
            ->orderBy('accountChildrenSubGroup')
            ->get();
        $todosUtilizadores = [];
        foreach ($allChildren as $key => $users)
        {
            $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
            $todosUtilizadores[$users->childID]['id'] = intval($users->childID);
            $todosUtilizadores[$users->childID]['name'] = $users->childNome;
            $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
            $todosUtilizadores[$users->childID]['unidade'] = $users->childUnidade;
        }
        foreach ($allUsers as $key => $users)
        {
          if ($users->isAccountChildren=="Y") {
            $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
            $todosUtilizadores[$users->id]['id'] = intval($users->id);
            $todosUtilizadores[$users->id]['name'] = $users->name;
            $todosUtilizadores[$users->id]['posto'] = $users->posto;
            $todosUtilizadores[$users->id]['unidade'] = $users->unidade;
          }
        }
        if (Auth::user()->accountPartnerPOC)
        {
            $id = Auth::user()->accountPartnerPOC;
            while ((strlen((string)$id)) < 8) {
              $id = 0 . (string)$id;
            }
            $myPartner = \App\Models\User::where('id', $id)->first();
        }
        else
        {
            $myPartner = null;
        }
        return view('gestao.assoc_users', ['childrenUsers' => $childrenUsers, 'allUsers' => $todosUtilizadores, 'partner' => $myPartner, ]);
    }

    /**
    * Ver perfil de utilizador associado como parelha a Auth::user()
    *
    * @return View profile.parelha
    */
    public function viewParelhaProfile(){

      $id = $this::checkNIMLen(Auth::user()->accountPartnerPOC);
      $parelha = \App\Models\User::where('id', $id)->first();

      $marcaçoes = \App\Models\marcacaotable::where('NIM', Auth::user()->accountPartnerPOC)
          ->orderBy('data_marcacao')
          ->orderBy('meal')
          ->get();
      $ementaTable = \App\Models\ementatable::orderBy('data')->get();
      $datasMarcadas = array();
      $marcacaoEmenta = array();
      foreach ($marcaçoes as $marcaçao)
      {
          $datasMarcadas[$marcaçao
              ->id] = $marcaçao->data_marcacao;
          $refeiçaoMarcada[$marcaçao
              ->id] = $marcaçao->meal;
      }
      foreach ($datasMarcadas as $i => $dateToAdd)
      {
          $marcacaoEmenta[$i] = $ementaTable->where('data', '=', $dateToAdd)->first();
          (array)$marcacaoEmenta[$i]['meal'] = $refeiçaoMarcada[$i];
      }
      $locaisRef = \App\Models\locaisref::get()->all();
      return view('profile.parelha', ['partner' => $parelha, 'marcaçoes' => $marcaçoes, 'ementa' => $marcacaoEmenta, 'locais' => $locaisRef]);
    }

    /**
    * Ver um perfil de utilizador.
    *
    * @param string $id
    * @return View profile.admin_view_profile
    */
    public function viewUserProfile($id)
    {

        $id = $this::checkNIMLen($id);
        $user = \App\Models\User::where('id', $id)->first();
        if (!$user) abort(500);
        $trocarUnidadePendente = User::where('id', $id)->get()->pluck('trocarUnidade');
        $trocaPendente = ($trocarUnidadePendente[0] != null ? true : false);
        $is_tagged = ($user->isTagOblig==null) ? false : true;

        $childrenUsers = null;
        $unidades = \App\Models\unap_unidades::get()->all();
        $childrenUsersError = null;
        $todosUtilizadores = null;
        if ($user->user_type == "POC" || $user->user_type == "ADMIN")
        {
            $childrenUsers = users_children::where('parentNIM', $user->id,)
                ->orderBy('childUnidade')
                ->get();
            $allChildren = \App\Models\users_children::where('parentNIM', $user->id)
                ->where('accountVerified', 'Y')
                ->orderBy('childGroup')
                ->orderBy('childSubGroup')
                ->get();
            $allUsers = \App\Models\User::where('accountChildrenOf', $user->id)
                ->where('account_verified', 'Y')
                ->where('isAccountChildren', 'Y')
                ->orderBy('accountChildrenGroup')
                ->orderBy('accountChildrenSubGroup')
                ->get();

            foreach ($allChildren as $key => $users)
            {
                $grupoName = \App\Models\users_children_subgroups::where('groupID', $users->childGroup)
                    ->value("groupName");
                $subGrupoName = \App\Models\users_children_sub2groups::where('subgroupID', $users->childSubGroup)
                    ->value("subgroupName");
                $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
                $todosUtilizadores[$users->childID]['id'] = $users->childID;
                $todosUtilizadores[$users->childID]['name'] = $users->childNome;
                $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
                $todosUtilizadores[$users->childID]['unidade'] = $users->childUnidade;
                $todosUtilizadores[$users->childID]['grupo_id'] = $users->childGroup;
                $todosUtilizadores[$users->childID]['grupo_name'] = $grupoName;
                $todosUtilizadores[$users->childID]['subgrupo_name'] = $subGrupoName;
                $todosUtilizadores[$users->childID]['subgrupo_id'] = $users->childSubGroup;
            }
            foreach ($allUsers as $key => $users)
            {
                $grupoName = \App\Models\users_children_subgroups::where('groupID', $users->accountChildrenGroup)
                    ->value("groupName");
                $subGrupoName = \App\Models\users_children_sub2groups::where('subgroupID', $users->accountChildrenSubGroup)
                    ->value("subgroupName");
                $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
                $todosUtilizadores[$users->id]['id'] = $users->id;
                $todosUtilizadores[$users->id]['name'] = $users->name;
                $todosUtilizadores[$users->id]['posto'] = $users->posto;
                $todosUtilizadores[$users->id]['unidade'] = $users->unidade;
                $todosUtilizadores[$users->id]['grupo_id'] = $users->accountChildrenGroup;
                $todosUtilizadores[$users->id]['grupo_name'] = $grupoName;
                $todosUtilizadores[$users->id]['subgrupo_name'] = $subGrupoName;
                $todosUtilizadores[$users->id]['subgrupo_id'] = $users->accountChildrenSubGroup;
            }
        }
        else
        {
            $childrenUsersError = "Este utilizador não tem perfis associados à sua conta.";
        }

        $verified = $user->verified_by;
        $tokenVer = express_account_verification_tokens::where('NIM', $user->id)
            ->first();
        if ($tokenVer != null)
        {
            $verified = "Token ID: " . $tokenVer->token . ' | CRIADO POR: ' . $tokenVer->NIM;
        }

        if ((new ActiveDirectoryController)->EDIT_MEMBERS() ||
            (new ActiveDirectoryController)->DELETE_MEMBERS() ||
            (new ActiveDirectoryController)->BLOCK_MEMBERS()) {
          $isUserOutClassed = false;
        } elseif(auth()->user()->user_type == "HELPDESK") {
            $isUserOutClassed = false;
        } elseif(auth()->user()->user_permission == "TUDO") {
          $isUserOutClassed = false;
        } else {
          $isUserOutClassed = true;
        }

        $marcaçoes = \App\Models\marcacaotable::where('NIM', $user->id)
            ->where('data_marcacao', '>=', date("Y-m-d"))
            ->orderBy('data_marcacao')
            ->orderBy('meal')
            ->get()->all();



        $ementaTable = \App\Models\ementatable::orderBy('data')->get();
        $datasMarcadas = array();
        $marcacaoEmenta = array();
        foreach ($marcaçoes as $marcaçao)
        {            
            $refeiçaoMarcada[$marcaçao->id] = $marcaçao->meal;
        }

        $checkedMarcacoes = null;
        $marcacoesVerificadas = null;

        if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP." ||
        $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" ||$user['posto'] == "TIG.1" || $user['posto'] == "TIE" || $user['isTagOblig']!=null)
        {
            $checkedMarcacoes = \App\Models\user_children_checked_meals::where('user', $user['id'])->orderBy('data')->get();
        }

        $today = date('Y-m-d');
        $datefirst = date("Y-m-d",strtotime($today."+1 days"));  
        $dateNex = date('Y-m-d', strtotime($datefirst.'+2 months'));
        $refs = array('1REF', '2REF', '3REF');
        $i = 0;

        $NIM = $user->id;
        while ((strlen((string)$NIM)) < 8) {
            $NIM = 0 . (string)$NIM;
        }

        foreach($this::dateRange($datefirst, $dateNex) as $it => $date){

          $ementa = \App\Models\ementatable::where('data', $date)->first();
          $ementaFormatadaDia[$i]["data"] = $date;

          if($ementa){
              $ementaFormatadaDia[$i]['sopa_almoço'] = $ementa->sopa_almoço;
              $ementaFormatadaDia[$i]['prato_almoço'] = $ementa->prato_almoço;
              $ementaFormatadaDia[$i]['sobremesa_almoço'] = $ementa->sobremesa_almoço;
              $ementaFormatadaDia[$i]['sopa_jantar'] = $ementa->sopa_jantar;
              $ementaFormatadaDia[$i]['prato_jantar'] = $ementa->prato_jantar;
              $ementaFormatadaDia[$i]['sobremesa_jantar'] = $ementa->sobremesa_jantar;
          } else {
              $ementaFormatadaDia[$i]['sopa_almoço'] = 'Não publicado';
              $ementaFormatadaDia[$i]['prato_almoço'] = 'Não publicado';
              $ementaFormatadaDia[$i]['sobremesa_almoço'] = 'Não publicado';
              $ementaFormatadaDia[$i]['sopa_jantar'] = 'Não publicado';
              $ementaFormatadaDia[$i]['prato_jantar'] = 'Não publicado';
              $ementaFormatadaDia[$i]['sobremesa_jantar'] = 'Não publicado';
          }

          foreach($refs as $ref){
            $marcada =  \App\Models\marcacaotable::where('NIM', $NIM)
                        ->where('data_marcacao', $date)
                        ->where('meal', $ref)->first();

            $ementaFormatadaDia[$i][$ref]['id'] = ($marcada) ? $marcada->id : null;
            $ementaFormatadaDia[$i][$ref]['marcada'] = ($marcada) ? "1" : "0";
            $ementaFormatadaDia[$i][$ref]['local'] = ($marcada) ? $marcada->local_ref : "0";            
          }

          $i++;
        }

        #dd($ementaFormatadaDia);
        
        $it_ferias = 0;
        $ferias = \App\Models\Ferias::where('to_user', $user['id'])->where('data_fim', '>', $today)->get()->all();
        $ferias_array = array();
        foreach ($ferias as $key => $_ferias_entry) {
          $ferias_array[$it_ferias]['id'] = $_ferias_entry['id'];
          $ferias_array[$it_ferias]['data_inicio'] = $_ferias_entry['data_inicio'];
          $ferias_array[$it_ferias]['data_fim'] = $_ferias_entry['data_fim'];
          $ferias_array[$it_ferias]['registered_by'] = $_ferias_entry['registered_by'];
          $it_ferias++;
        }

        $maxDateAdd = ((new checkSettingsTable)->ADDMAX());

        $minDayTag = date("Y-m-d", strtotime("+".$maxDateAdd." days"));

        $minDayTag = date("d-m-Y", strtotime($minDayTag));

        $max = ((new checkSettingsTable)->REMOVEMAX());

        #dd($marcacaoEmenta);

        $levelPermission = $user->user_permission;
        $locaisRef = \App\Models\locaisref::get()->all();
        return view('profile.admin_view_profile', [
            'id' => $user->id, 
            'name' => $user->name, 
            'email' => $user->email, 
            'telf' => $user->telf, 
            'user_type' => $user->user_type, 
            'posto' => $user->posto, 
            'unidade' => $user->unidade, 
            'localPref' => $user->localRefPref, 
            'lock' => $user->lock, 
            'permissionLevel' => $levelPermission, 
            'account_verified' => $user->account_verified, 
            'created_at' => $user->created_at, 
            'last_modification_at' => $user->updated_at, 
            'last_modification_by' => $user->updated_by, 
            'verified_at' => $user->verified_at, 
            'verified_by' => $verified, 
            'already_tagged' => $is_tagged, 
            'descriptor' => $user->descriptor, 
            'seccao' => $user->seccao,  
            'childrenUsers' => $todosUtilizadores, 
            'childrenUsersError' => $childrenUsersError, 
            'isUserOutClassed' => $isUserOutClassed, 
            'marcadasVerificadas' => $marcacoesVerificadas, 
            'marcaçoes' => $marcaçoes, 
            'ementa' => $ementaFormatadaDia, 
            'ferias' => $ferias_array, 
            'maxDays' => $max, 
            'pendenteTrocaUnidade' => $trocaPendente, 
            'unidades' => $unidades, 
            'locais' => $locaisRef, 
            'minDayTag' => $minDayTag,
          ]);
    }

    public function SecLogTagMeal(Request $post){
      try
        {
            $NIM = $this::checkNIMLen($post->uid);
            $data_marcacao = $post->data;
            $meal = $post->ref;
            $marcaçao = new \App\Models\marcacaotable;
            $marcaçao->NIM = $NIM;
            $marcaçao->data_marcacao = $data_marcacao;
            $marcaçao->meal = $meal;
            $marcaçao->local_ref = $post->localDeRef;
            $marcaçao->unidade = auth()->user()->unidade;
            $marcaçao->created_by = \Auth::user()->id;

            if ($post->posto=="ASS.TEC." || $post->posto=="ASS.OP." || $post->posto=="TEC.SUP." ||
            $post->posto == "ENC.OP." || $post->posto == "TIA" ||$post->posto == "TIG.1" || $post->posto == "TIE"){
                $marcaçao->civil = 'Y';
            } else {
                $marcaçao->civil = 'N';
            }

            $dieta = \App\Models\Dietas::where('NIM', $NIM)
                      ->where('data_inicio', '<', $post->data)->where('data_fim', '>', $post->data)
                      ->first();

            if ($dieta!=null) $marcaçao->dieta = "Y";
            else $marcaçao->dieta = "N";

            if(\App\Models\marcacaotable::where('NIM', $NIM)->where('data_marcacao', $data_marcacao)->where('meal', $meal)->where('local_ref', $post->localDeRef)->first()==null){
                $marcaçao->save();
            }

            return response()->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json('error', 200);
        }
    }


    /**
    * Remove marcação de refeições a numerário a um utilizador.
    *
    * @param Request $post
    * @return redirect
    */
    public function RemoveIsTagOb(Request $post){
      if (!(new ActiveDirectoryController)->EDIT_MEMBERS()) abort(403);

      $NIM = $post->nim;
      while ((strlen((string)$NIM)) < 8) {
        $NIM = 0 . (string)$NIM;
      }
      
      $User = \App\Models\User::where('id', $NIM)->first();
      $User->isTagOblig = null;
      $User->save();
      return redirect()->back();
    }

    /**
    *  Guardar edição a perfil feita por terceiros
    *
    * @param Request $request
    * @return view profile.saved
    */
    public function profile_settings_save(Request $request)
    {
        if (!(new ActiveDirectoryController)->EDIT_MEMBERS()) abort(403);
        $id = $request->inputId;

        while ((strlen((string)$id)) < 8) {
          $id = 0 . (string)$id;
        }

        $user = \App\Models\User::where('id', $id)->first();
        $user->name = $request->inputName;
        $user->posto = $request->inputPosto;
        $user->email = $request->inputEmail;
        $user->telf = $request->inputTelf;
        $user->descriptor = $request->inputFunc;
        $user->seccao = $request->inputSecc;

        $changed = false;
        $oldUnidade = $user->unidade;

        if ($user->trocarUnidade == null)
        {
            if ($user->unidade != $request->inputUEO)
            {
                $changed = true;
                $user->trocarUnidade = $request->inputUEO;
            }
        }

        if ($request->inputLocalRefPref != null)
        {
            $user->localRefPref = $request->inputLocalRefPref;
        }

        if ($request->inputUserType != null)
        {
            $user->user_type = $request->inputUserType;
        }

        if ($request->inputUserPerm)
        {
            $user->user_permission = $request->inputUserPerm;
        }
        else
        {
            $user->user_permission = "GENERAL";
        }

        $user->updated_by = Auth::user()->id;
        $user->save();
        $newNotification = new notificationsHandler;
        $newNotification->new_notification('Alteração de perfil', 'O utilizador ' . Auth::user()->id . ' alterou o seu perfil.', 'NORMAL', null, $request->inputId, 'SYSTEM: PROFILE EDIT @' . Auth::user()->id, null);
        return view('profile.saved', ['url' => route('user.profile', $request->inputId) , 'changedUnidade' => $changed, 'newUnidade' => \App\Models\unap_unidades::where('slug', $request->inputUEO)
            ->value('name') , 'oldUnidade' => \App\Models\unap_unidades::where('slug', $oldUnidade)->value('name') , ]);
    }

    /**
    * Formatar informação de marcações.
    * Redirect página de estatisticas com informação.
    *
    *
    * @return view gestao.stats2
    */
    public function statsAdmin(){
        if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS()) abort(403);
        $today_date = date('Y-m-d');
        $days_of_deletion = (new checkSettingsTable)->REMOVEMAX();
        $days_after_date = ((new checkSettingsTable)->ADDMAX() + ($days_of_deletion + 1));
        $refs = array();
        $locaisRef = \App\Models\locaisref::get()->all();
        $possible_refs = ['1REF', '2REF', '3REF'];
        for ($i=$days_of_deletion; $i < $days_after_date; $i++) {
            $date_current = date("Y-m-d", strtotime($today_date . "+ ".$i." days"));
            $refs[$i]['DATA'] = $date_current;
            foreach ($possible_refs as $key => $ref) {
                $refs[$i]['TOTAL'][$ref]['NORMAL'] = marcacaotable::where('meal', $ref)
                ->where('data_marcacao', $date_current)
                ->where('dieta', 'N')->count();
                $refs[$i]['TOTAL'][$ref]['DIETAS'] = marcacaotable::where('meal', $ref)
                    ->where('data_marcacao', $date_current)
                    ->where('dieta', 'Y')->count();

                $pedidos = 0;
                foreach (\App\Models\pedidosueoref::where('meal', $ref)->where('data_pedido', $date_current)->get() as $key => $req) {
                    $pedidos += $req['quantidade'];
                }
                $refs[$i]['TOTAL'][$ref]['PEDIDOS'] = $pedidos;
                $refs[$i]['TOTAL'][$ref]['NORMAL'] = ($refs[$i]['TOTAL'][$ref]['NORMAL'] == null) ? 0 : $refs[$i]['TOTAL'][$ref]['NORMAL'];
                $refs[$i]['TOTAL'][$ref]['DIETAS'] = ($refs[$i]['TOTAL'][$ref]['DIETAS'] == null) ? 0 : $refs[$i]['TOTAL'][$ref]['DIETAS'];
                $refs[$i]['TOTAL'][$ref]['PEDIDOS'] = ($refs[$i]['TOTAL'][$ref]['PEDIDOS'] == null) ? 0 : $refs[$i]['TOTAL'][$ref]['PEDIDOS'];
                $refs[$i]['TOTAL'][$ref]['TOTAL'] = ($refs[$i]['TOTAL'][$ref]['PEDIDOS'] + $refs[$i]['TOTAL'][$ref]['DIETAS'] + $refs[$i]['TOTAL'][$ref]['NORMAL']);


                foreach ($locaisRef as $key => $local) {
                    $refs[$i][$local['refName']][$ref]['NORMAL'] = marcacaotable::where('meal', $ref)
                        ->where('data_marcacao', $date_current)
                        ->where('local_ref', $local['refName'])
                        ->where('dieta', 'N')->count();
                    $refs[$i][$local['refName']][$ref]['DIETAS'] = marcacaotable::where('meal', $ref)
                        ->where('data_marcacao', $date_current)
                        ->where('local_ref', $local['refName'])
                        ->where('dieta', 'Y')->count();
                    $pedidos = 0;
                    foreach (\App\Models\pedidosueoref::where('meal', $ref)->where('local_ref', $local['refName'])->where('data_pedido', $date_current)->get() as $key => $req) {
                        $pedidos += $req['quantidade'];
                    }
                    $refs[$i][$local['refName']][$ref]['PEDIDOS'] = $pedidos;
                    $refs[$i][$local['refName']][$ref]['NORMAL'] = ($refs[$i][$local['refName']][$ref]['NORMAL'] == null) ? 0 : $refs[$i][$local['refName']][$ref]['NORMAL'];
                    $refs[$i][$local['refName']][$ref]['DIETAS'] = ($refs[$i][$local['refName']][$ref]['DIETAS'] == null) ? 0 : $refs[$i][$local['refName']][$ref]['DIETAS'];
                    $refs[$i][$local['refName']][$ref]['PEDIDOS'] = ($refs[$i][$local['refName']][$ref]['PEDIDOS'] == null) ? 0 : $refs[$i][$local['refName']][$ref]['PEDIDOS'];

                    $refs[$i][$local['refName']][$ref]['TOTAL'] = ($refs[$i][$local['refName']][$ref]['NORMAL'] + $refs[$i][$local['refName']][$ref]['DIETAS'] + $refs[$i][$local['refName']][$ref]['PEDIDOS']);
                }
            }
        }

        $totals_perc = array();

        $totals_perc['QSP'] = 0;
        $totals_perc['QSO'] = 0;
        $totals_perc['MMANTAS'] = 0;
        $totals_perc['MMBATALHA'] = 0;
        $totals_perc['TOTAL'] = 0;

        foreach ($refs as $key => $ref) {
            $totals_perc['QSP'] += ($ref["QSP"]['1REF']['TOTAL'] + $ref["QSP"]['2REF']['TOTAL'] + $ref["QSP"]['3REF']['TOTAL']);
            $totals_perc['QSO'] += ($ref['QSO']['1REF']['TOTAL'] + $ref['QSO']['2REF']['TOTAL'] + $ref['QSO']['3REF']['TOTAL']);
            $totals_perc['MMANTAS'] += ($ref['MMANTAS']['1REF']['TOTAL'] + $ref['MMANTAS']['2REF']['TOTAL'] + $ref['MMANTAS']['3REF']['TOTAL']);
            $totals_perc['MMBATALHA'] += ($ref['MMBATALHA']['1REF']['TOTAL'] + $ref['MMBATALHA']['2REF']['TOTAL'] + $ref['MMBATALHA']['3REF']['TOTAL']);
            $totals_perc['TOTAL'] +=$totals_perc['QSP'] +$totals_perc['QSO'] +$totals_perc['MMANTAS'] +$totals_perc['MMBATALHA'];
        }

        $my_unit_name = \App\Models\unap_unidades::where('slug', Auth::user()->unidade)->value('name');
        return view('gestao.stats2', [
            'REFS' => $refs,
            'TOTAL_PERCS' => $totals_perc,
            'POSSIBLE_REFS' => $possible_refs,
            'LOCAIS' => $locaisRef,
            'myLocal' => Auth::user()->unidade,
            'myLocalName' => $my_unit_name,
            'MAX_DATE' => date("Y-m-d", strtotime($today_date . "+ ".((new checkSettingsTable)->ADDMAX())." days")),
            'MIN_DATE' => date("Y-m-d", strtotime($today_date . "+ ".((new checkSettingsTable)->REMOVEMAX())." days")),
        ]);
    }

    /**
    * Devolve um array com todas as datas entre duas datas
    *
    * @param string $first Primeira data
    * @param string $first Ultima data
    * @param string $step 1 dia de intervalo entre datas.
    * @param string $format Formato a devolver 'Y-m-d';
    * @return array Lista de datas
    */
    function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = [];
        $current = strtotime( $first );
        $last = strtotime( $last );

        while( $current <= $last ) {

            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }

        return $dates;
    }

    /**
    * Devolve duas datas de uma semana em especifico e um ano.
    *
    * @param int $week Número da semana
    * @param string $year Ano
    * @return array Primeira e ultima data.
    */
    function getStartAndEndDate($week, $year)
    {
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('Y-m-d');
        return $ret;
    }

    /**
    * Devolve pàgina de estatísticas 'cons_stats_day' s/ informação.
    *
    * @return view gestao.cons_stats_day
    */
    public function statsUnitsRemoved(){
        return view('gestao.cons_stats_day', [
            'data' => false
        ]);
    }

    /**
    * Devolve pàgina de estatísticas 'cons_stats_day' c/ informação.
    *
    * @return view gestao.cons_stats_day
    */
    public function statsAdminUnits(Request $post){

        if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS()) abort(403);

        $marcacoes =  \App\Models\marcacaotable::get()->all();

        $date = $post['date'];

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

        $all_trashed = array();
        $all_pedidos = array();
        $refs = array('1REF', '2REF', '3REF');

        $unidades = \App\Models\unap_unidades::get()->all();

        $total_marc = marcacaotable::where('data_marcacao', $date)->count();
        $total_cons = \App\Models\entradasquiosque::where('REGISTADO_DATE', $date)->count();

        $all_trashed['date'] = $date;
        $all_trashed['total_marc'] = $total_marc;
        $all_trashed['total_cons'] = $total_cons;
        $all_trashed['total_perc'] = ($total_marc>0) ? $this->cal_percentage($total_cons, $total_marc) : "NA";

        foreach ($unidades as $unit_key => $unit) {
            $all_trashed[$unit['slug']]['unit_name'] = $unit['name'];
            $all_trashed[$unit['slug']]['unit_slug'] = $unit['slug'];
            $all_trashed[$unit['slug']]['local'] = $unit['local'];

            $total_marc = marcacaotable::where('data_marcacao', $date)->where('unidade', $unit['slug'])->count();
            $total_cons = \App\Models\entradasquiosque::where('REGISTADO_DATE', $date)->where('unidade', $unit['slug'])->count();

            $all_trashed[$unit['slug']]['cons_rate'] = ($total_marc>0) ? $this->cal_percentage($total_cons, $total_marc) : "NA";
            $all_trashed[$unit['slug']]['cons_total'] = $total_cons;
            $all_trashed[$unit['slug']]['marc_total'] = $total_marc;



            $locais_ref = \App\Models\locaisref::get(['refName', 'localName'])->all();
            $cont_consumid = 0;
            $cont_pedido = 0;
            foreach ($locais_ref as $lc_key => $local) {
            $count_local_ped = 0;
            $count_local_cons = 0;
            foreach ($refs as $ref) {

                $all_pedidos[$local['refName']]['local'] = $local['refName'];
                $all_pedidos[$local['refName']]['local_name'] = $local['localName'];
                $count_total = 0;

                $pedidos = \App\Models\pedidosueoref::where('local_ref', $local['refName'])->where('data_pedido', $date)->where('meal', $ref)->get(['quantidade'])->all();
                foreach ($pedidos as $pddo) {
                    $count_total += $pddo['quantidade'];
                    $count_local_ped += $count_total;
                }

                $entradas_quant = \App\Models\entradasQuiosque::where('LOCAL', $local['refName'])
                ->where('REGISTADO_DATE', $date)
                ->where('REF', $ref)->get(['QTY'])->all();
                $count_cons = 0;
                foreach ($entradas_quant as $key => $ent) {
                    if($ent['QTY']!=null){
                        $count_cons += $ent['QTY'];
                        $count_local_cons += $ent['QTY'];
                    }
                }
                $all_pedidos[$local['refName']]['cons'][$ref] = $count_cons;
                $cont_consumid += $count_cons;
                $all_pedidos[$local['refName']]['total'][$ref] = $count_total;
                $all_pedidos[$local['refName']]['perc'][$ref] = ($count_total>0) ? $this->cal_percentage($count_cons, $count_total) : "NA";
                $cont_pedido += $count_total;
                $all_pedidos[$local['refName']]['qty_ped'] = $count_local_ped;
                $all_pedidos[$local['refName']]['qty_cons'] = $count_local_cons;

                }
            }

            $all_pedidos['total_cons'] = $cont_consumid;
            $all_pedidos['total_pedido'] = $cont_pedido;
            $all_pedidos['total_perc'] = ($cont_pedido>0) ? $this->cal_percentage($cont_consumid, $cont_pedido) : "NA";

            foreach($refs as $ref){
                #TOTAL
                $marc_mil = marcacaotable::where('data_marcacao', $date)->where('meal', $ref)->where('unidade', $unit['slug'])->where('civil', 'N')->get()->count();
                $marc_civ = marcacaotable::where('data_marcacao', $date)->where('meal', $ref)->where('unidade', $unit['slug'])->where('civil', 'Y')->get()->count();
                $all_trashed[$unit['slug']]['total'][$ref]['mil'] = $marc_mil;
                $all_trashed[$unit['slug']]['total'][$ref]['civ'] = $marc_civ;
                #CONSUMIDAS
                $cons_mil  = \App\Models\entradasquiosque::where('REF', $ref)->where('REGISTADO_DATE', $date)->where('unidade', $unit['slug'])->where('civil', 'N')->get()->count();
                $cons_civ  = \App\Models\entradasquiosque::where('REF', $ref)->where('REGISTADO_DATE', $date)->where('unidade', $unit['slug'])->where('civil', 'Y')->get()->count();
                $all_trashed[$unit['slug']]['cons'][$ref]['mil'] = $cons_mil;
                $all_trashed[$unit['slug']]['cons'][$ref]['civ'] = $cons_civ;
                #PERC
                $all_trashed[$unit['slug']]['perc'][$ref]['mil'] = ($marc_mil>0) ? $this->cal_percentage($cons_mil, $marc_mil) : "NA";
                $all_trashed[$unit['slug']]['perc'][$ref]['civ'] = ($marc_civ>0) ? $this->cal_percentage($cons_civ, $marc_civ) : "NA";
            }

        }

        $total = array();

        $total['cons'] = ($all_pedidos['total_cons'] + $all_trashed['total_cons']);
        $total['ped'] = ($all_pedidos['total_pedido'] + $all_trashed['total_marc']);
        $total['perc'] = ($total['ped']>0) ? $this->cal_percentage($total['cons'], $total['ped']) : "NA";
        // dd($all_pedidos);
        return view('gestao.cons_stats_day', [
            'stats' => $all_trashed,
            'pedidos' => $all_pedidos,
            'total_stats' => $total,
        ]);
    }

    /**
    * Devolve pàgina de estatísticas 'cons_stats' s/ informação.
    *
    * @return view gestao.cons_stats_day
    */
    public function statsRemoved(){
        return view('gestao.cons_stats', [
            'data' => false
        ]);
    }

    /**
    * Devolve pàgina de estatísticas 'cons_stats' c/ informação.
    *
    * @return view gestao.cons_stats_day
    */
    public function statsAdminRemoved(Request $post){
        if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS()) abort(403);
        $currentYear = date("Y", strtotime('now'));
        $currentWeekInt = date("W", strtotime('now'));
        $currentWeek = $this::getStartAndEndDate($currentWeekInt, $currentYear);
        $nextWeek = $this::getStartAndEndDate(($currentWeekInt + 1) , $currentYear);

        if ($post->timeframe == "WEEK")
        {
            $date = $currentWeek['week_start'];
            $dateNex = $currentWeek['week_end'];
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($post->timeframe == "NEXTWEEK")
        {
            $date = $nextWeek['week_start'];
            $dateNex = $nextWeek['week_end'];
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($post->timeframe == "MONTH")
        {
            $date = date('Y-m-01', strtotime($date));
            $dateNex = date('Y-m-t', strtotime($date));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($post->timeframe == "NEXTMONTH")
        {
            $date = date('Y-m-01', strtotime($date));
            $dateNex = date('Y-m-t', strtotime($date));
            $date = strtotime("+1 months", strtotime($date));
            $dateNex = strtotime("+1 months", strtotime($dateNex));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($post->timeframe == "PERSON")
        {
            $date = date('Y-m-d', strtotime($post->startdate));
            $dateNex = date('Y-m-d', strtotime($post->enddate));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }

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

        $today = date('Y-m-d');

        $datePrev = $date;
        $dateNex = $dateNex;


        $unidades = \App\Models\unap_unidades::get()->all();
        $all_trashed = array();
        $all_pedidos = array();
        $iter = 0;

        $date_range = $this->dateRange($datePrev, $dateNex);
        $refs = array('1REF', '2REF', '3REF');

        $total_marc = marcacaotable::where('data_marcacao', '<=', $dateNex)->where('data_marcacao', '>=', $datePrev)->count();
        $total_cons = \App\Models\entradasquiosque::where('REGISTADO_DATE', '<=', $dateNex)->where('REGISTADO_DATE', '>=', $datePrev)->count();

        $all_trashed['tf_start'] = $datePrev;
        $all_trashed['tf_end'] = $dateNex;
        $all_pedidos['tf_start'] = $datePrev;
        $all_pedidos['tf_end'] = $dateNex;

        $all_trashed['g_cons_rate'] = ($total_marc>0) ? $this->cal_percentage($total_cons, $total_marc) : "NA";
        $all_trashed['total_marc'] = $total_marc;
        $all_trashed['total_cons'] = $total_cons;

        foreach ($unidades as $key => $unit) {
            $all_trashed[$iter]['unit_name'] = $unit['name'];
            $all_trashed[$iter]['unit_slug'] = $unit['slug'];
            $all_trashed[$iter]['local'] = $unit['local'];

           $total_marc = marcacaotable::where('data_marcacao', '<=', $dateNex)
                ->where('data_marcacao', '>=', $datePrev)
                ->where('unidade', $unit['slug'])->count();

            $total_cons = \App\Models\entradasquiosque::where('REGISTADO_DATE', '<=', $dateNex)
                ->where('REGISTADO_DATE', '>=', $datePrev)
                ->where('unidade', $unit['slug'])->count();

            $all_trashed[$iter]['cons_rate'] = ($total_marc>0) ? $this->cal_percentage($total_cons, $total_marc) : "NA";
            $all_trashed[$iter]['cons_total'] = $total_cons;
            $all_trashed[$iter]['marc_total'] = $total_marc;
            $locais_ref = \App\Models\locaisref::get(['refName', 'localName'])->all();
            $cont_consumid = 0;
            $cont_pedido = 0;
            foreach ($locais_ref as $lc_key => $local) {
                $count_local_ped = 0;
                $count_local_cons = 0;
                foreach($date_range as $index_date => $date){
                    foreach ($refs as $ref) {

                        $all_pedidos[$local['refName']]['local'] = $local['refName'];
                        $all_pedidos[$local['refName']]['local_name'] = $local['localName'];
                        $count_total = 0;

                        $pedidos = \App\Models\pedidosueoref::where('local_ref', $local['refName'])->where('data_pedido', $date)->where('meal', $ref)->get(['quantidade'])->all();
                        foreach ($pedidos as $pddo) {
                            $count_total += $pddo['quantidade'];
                            $count_local_ped += $count_total;

                        }
                        $all_pedidos[$local['refName']]['dates'][$date]['total'][$ref] = $count_total;
                        $cont_pedido += $count_total;


                        $entradas_quant = \App\Models\entradasQuiosque::where('LOCAL', $local['refName'])
                        ->where('REGISTADO_DATE', $date)
                        ->where('REF', $ref)->get(['QTY'])->all();
                        $count_cons = 0;
                        foreach ($entradas_quant as $key => $ent) {
                            if($ent['QTY']!=null){
                                $count_cons += $ent['QTY'];
                                $count_local_cons += $ent['QTY'];
                            }
                        }
                        $all_pedidos[$local['refName']]['dates'][$date]['cons'][$ref] = $count_cons;
                        $cont_consumid += $count_cons;
                        $all_pedidos[$local['refName']]['dates'][$date]['perc'][$ref] = ($count_total>0) ? $this->cal_percentage($count_cons, $count_total) : "NA";

                    }
                }
                $all_pedidos[$local['refName']]['qty_ped'] = $count_local_ped;
                $all_pedidos[$local['refName']]['qty_cons'] = $count_local_cons;
                $all_pedidos[$local['refName']]['perc'] = ($count_local_ped>0) ? $this->cal_percentage($count_local_cons, $count_local_ped) : "NA";

            }
            $all_pedidos['total_cons'] = $cont_consumid;
            $all_pedidos['total_pedido'] = $cont_pedido;
            $all_pedidos['total_perc'] = ($cont_pedido>0) ? $this->cal_percentage($cont_consumid, $cont_pedido) : "NA";

            // dd($all_pedidos);

            foreach ($date_range as $index_date => $date) {
                foreach($refs as $ref){
                    #TOTAL
                    $marc_mil = marcacaotable::where('data_marcacao', $date)->where('meal', $ref)->where('unidade', $unit['slug'])->where('civil', 'N')->get()->count();
                    $marc_civ = marcacaotable::where('data_marcacao', $date)->where('meal', $ref)->where('unidade', $unit['slug'])->where('civil', 'Y')->get()->count();
                    $all_trashed[$iter]['dates'][$date]['total'][$ref]['mil'] = $marc_mil;
                    $all_trashed[$iter]['dates'][$date]['total'][$ref]['civ'] = $marc_civ;
                    $all_trashed[$iter]['dates'][$date]['total'][$ref]['quant'] = $marc_civ;
                    #CONSUMIDAS
                    $cons_mil  = \App\Models\entradasquiosque::where('REF', $ref)->where('REGISTADO_DATE', $date)->where('unidade', $unit['slug'])->where('civil', 'N')->get()->count();
                    $cons_civ  = \App\Models\entradasquiosque::where('REF', $ref)->where('REGISTADO_DATE', $date)->where('unidade', $unit['slug'])->where('civil', 'Y')->get()->count();
                    $all_trashed[$iter]['dates'][$date]['cons'][$ref]['mil'] = $cons_mil;
                    $all_trashed[$iter]['dates'][$date]['cons'][$ref]['civ'] = $cons_civ;
                    #PERC
                    $all_trashed[$iter]['dates'][$date]['perc'][$ref]['mil'] = ($marc_mil>0) ? $this->cal_percentage($cons_mil, $marc_mil) : "NA";
                    $all_trashed[$iter]['dates'][$date]['perc'][$ref]['civ'] = ($marc_civ>0) ? $this->cal_percentage($cons_civ, $marc_civ) : "NA";
                }
            }

            $iter++;
        }

        $total = array();

        $total['cons'] = ($all_pedidos['total_cons'] + $all_trashed['total_cons']);
        $total['ped'] = ($all_pedidos['total_pedido'] + $all_trashed['total_marc']);
        $total['perc'] = ($total['ped']>0) ? $this->cal_percentage($total['cons'], $total['ped']) : "NA";

        return view('gestao.cons_stats', [
            'stats' => $all_trashed,
            'pedidos' => $all_pedidos,
            'total_stats' => $total,
        ]);

    }

    /**
    * Devolve pàgina de estatísticas 'cons_stats_unit' c/ informação.
    *
    * @return view gestao.cons_stats_day
    */
    public function statsAdminDay(){
        if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS()) abort(403);
        $today_date = date('Y-m-d');
        $days_of_deletion = (new checkSettingsTable)->REMOVEMAX();
        $days_after_date = ((new checkSettingsTable)->ADDMAX() + ($days_of_deletion + 1));
        $refs = array();
        $locaisRef = \App\Models\locaisref::get()->all();
        $possible_refs = ['1REF', '2REF', '3REF'];
        for ($i=$days_of_deletion; $i < $days_after_date; $i++) {
            $date_current = date("Y-m-d", strtotime($today_date . "+ ".$i." days"));
            $refs[$i]['DATA'] = $date_current;
            foreach ($possible_refs as $key => $ref) {
                $refs[$i]['TOTAL'][$ref]['NORMAL'] = marcacaotable::where('meal', $ref)
                ->where('data_marcacao', $date_current)
                ->where('dieta', 'N')->count();
                $refs[$i]['TOTAL'][$ref]['DIETAS'] = marcacaotable::where('meal', $ref)
                    ->where('data_marcacao', $date_current)
                    ->where('dieta', 'Y')->count();
                $pedidos = 0;
                foreach (\App\Models\pedidosueoref::where('meal', $ref)->where('data_pedido', $date_current)->get() as $key => $req) {
                    $pedidos += $req['quantidade'];
                }
                $refs[$i]['TOTAL'][$ref]['PEDIDOS'] = $pedidos;
                $refs[$i]['TOTAL'][$ref]['NORMAL'] = ($refs[$i]['TOTAL'][$ref]['NORMAL'] == null) ? 0 : $refs[$i]['TOTAL'][$ref]['NORMAL'];
                $refs[$i]['TOTAL'][$ref]['DIETAS'] = ($refs[$i]['TOTAL'][$ref]['DIETAS'] == null) ? 0 : $refs[$i]['TOTAL'][$ref]['DIETAS'];
                $refs[$i]['TOTAL'][$ref]['PEDIDOS'] = ($refs[$i]['TOTAL'][$ref]['PEDIDOS'] == null) ? 0 : $refs[$i]['TOTAL'][$ref]['PEDIDOS'];
                $refs[$i]['TOTAL'][$ref]['TOTAL'] = ($refs[$i]['TOTAL'][$ref]['PEDIDOS'] + $refs[$i]['TOTAL'][$ref]['DIETAS'] + $refs[$i]['TOTAL'][$ref]['NORMAL']);
                foreach ($locaisRef as $key => $local) {
                    $refs[$i][$local['refName']][$ref]['NORMAL'] = marcacaotable::where('meal', $ref)
                        ->where('data_marcacao', $date_current)
                        ->where('local_ref', $local['refName'])
                        ->where('dieta', 'N')->count();
                    $refs[$i][$local['refName']][$ref]['DIETAS'] = marcacaotable::where('meal', $ref)
                        ->where('data_marcacao', $date_current)
                        ->where('local_ref', $local['refName'])
                        ->where('dieta', 'Y')->count();
                    $pedidos = 0;
                    foreach (\App\Models\pedidosueoref::where('meal', $ref)->where('local_ref', $local['refName'])->where('data_pedido', $date_current)->get() as $key => $req) {
                        $pedidos += $req['quantidade'];
                    }
                    $refs[$i][$local['refName']][$ref]['PEDIDOS'] = $pedidos;
                    $refs[$i][$local['refName']][$ref]['NORMAL'] = ($refs[$i][$local['refName']][$ref]['NORMAL'] == null) ? 0 : $refs[$i][$local['refName']][$ref]['NORMAL'];
                    $refs[$i][$local['refName']][$ref]['DIETAS'] = ($refs[$i][$local['refName']][$ref]['DIETAS'] == null) ? 0 : $refs[$i][$local['refName']][$ref]['DIETAS'];
                    $refs[$i][$local['refName']][$ref]['PEDIDOS'] = ($refs[$i][$local['refName']][$ref]['PEDIDOS'] == null) ? 0 : $refs[$i][$local['refName']][$ref]['PEDIDOS'];

                    $refs[$i][$local['refName']][$ref]['TOTAL'] = ($refs[$i][$local['refName']][$ref]['NORMAL'] + $refs[$i][$local['refName']][$ref]['DIETAS'] + $refs[$i][$local['refName']][$ref]['PEDIDOS']);
                }
            }
        }
        return view('gestao.stats', [
            'REFS' => $refs,
            'POSSIBLE_REFS' => $possible_refs,
            'locals' => $locaisRef,
            'MAX_DATE' => date("Y-m-d", strtotime($today_date . "+ ".((new checkSettingsTable)->ADDMAX())." days")),
              'MIN_DATE' => date("Y-m-d", strtotime($today_date . "+ ".((new checkSettingsTable)->REMOVEMAX())." days")),
        ]);
    }

    /**
    * Devolve pàgina de estatísticas 'cons_stats_unit' s/ informação.
    *
    * @return view gestao.cons_stats_day
    */
    public function statsUnit(){
      return view('gestao.cons_stats_unit', [
        'data' => false,
        'unidades' => \App\Models\unap_unidades::get()->all(),
      ]);
    }

    /**
    * Devolve pàgina de estatísticas 'cons_stats_unit' c/ informação.
    *
    * @return view gestao.cons_stats_day
    */
    public function statsAdminUnit(Request $post){

        if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS()) abort(403);

        $date = $post['date'];
        $unit = \App\Models\unap_unidades::where('slug', $post['unitSelect'])->first();

        # MARCAÇÕES
        $marcacoes =  \App\Models\marcacaotable::where('unidade', $post['unitSelect'])
          ->where('data_marcacao', $date)->orderBy('meal')
          ->get()->all();
        $users_tag = array();
        $refs = array('1REF','2REF', '3REF');

        foreach ($marcacoes as $tag_key => $tag) {
          $ID = $this::checkNIMLen($tag['NIM']);
          $usr = \App\Models\User::where('id', $ID)->first();
          $users_tag[$ID]['NIM'] = $ID;
          $users_tag[$ID]['NAME'] = $usr['name'];
          $users_tag[$ID]['POSTO'] = $usr['posto'];
          $users_tag[$ID]['UNIDADE'] = $usr['unidade'];

          foreach ($refs as $key => $MEAL) {;
            if ($tag['meal']!=$MEAL) {

              $conf = \App\Models\entradasquiosque::where('NIM', $ID)
                ->where('REGISTADO_DATE', $tag['data_marcacao'])->where('REF', $MEAL)
                ->first();
              $users_tag[$ID][$MEAL]['CONSUMIDA'] = ($conf==null) ? 'N' : 'Y';
              if ($conf!=null) $users_tag[$ID][$MEAL]['LOCAL_CONSUMO'] = $conf['LOCAL'];
            } else {
              $users_tag[$ID][$MEAL]['MEAL'] = $MEAL;
              if ($MEAL=="1REF") $users_tag[$ID][$MEAL]['MEAL_DESCRIPTOR'] = "Pequeno-almoço";
              elseif ($MEAL=="2REF") $users_tag[$ID][$MEAL]['MEAL_DESCRIPTOR'] = "Almoço";
              elseif ($MEAL=="3REF") $users_tag[$ID][$MEAL]['MEAL_DESCRIPTOR'] = "Jantar";
              $users_tag[$ID][$MEAL]['LOCAL_MARCADO'] = $tag['local_ref'];
              $users_tag[$ID][$MEAL]['LOCAL_MARCADO_DESCRIPTOR'] = \App\Models\locaisref::where('refName', $tag['local_ref'])->first()->value('localName');

              $conf = \App\Models\entradasquiosque::where('NIM', $ID)
                ->where('REGISTADO_DATE', $tag['data_marcacao'])->where('REF', $MEAL)
                ->first();

              $users_tag[$ID][$MEAL]['CONSUMIDA'] = ($conf==null) ? 'N' : 'Y';
              if ($conf!=null){
                $users_tag[$ID][$MEAL]['LOCAL_CONSUMO'] = $conf['LOCAL'];
                $users_tag[$ID][$MEAL]['LOCAL_CONSUMO_IGUAL'] = ($conf['LOCAL']==$tag['local_ref']) ? 'Y' : 'N';
              }
            }
          }
        }
        return view('gestao.cons_stats_unit', [
            'unit' => $unit,
            'date' => $date,
            'marcadas' => $users_tag,
        ]);
    }


    /**
    * Procura um utilizador para uma listagem,
    * para formular um relatorio PDF desse utilizador
    * response JSON
    *
    * @param Request $request
    * @return json
    */
    public function searchUserForReport(Request $request)
    {
        try
        {
            $allChildren = \App\Models\users_children::where('childID', 'LIKE', '%' . $request->search . "%")
                ->where('accountVerified', 'Y')
                ->get();
            $allUsers = \App\Models\User::where('id', 'LIKE', '%' . $request->search . "%")
                ->where('account_verified', 'Y')
                ->get();
            $todosUtilizadores = [];
            foreach ($allChildren as $key => $users)
            {
                if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS())
                {
                    if ($users->childUnidade != Auth::user()
                        ->unidade)
                    {
                        continue;
                    }
                }
                $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
                $todosUtilizadores[$users->childID]['id'] = $users->childID;
                $todosUtilizadores[$users->childID]['name'] = $users->childNome;
                $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;

            }
            foreach ($allUsers as $key => $users)
            {
                if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS())
                {
                    if ($users->unidade != Auth::user()
                        ->unidade)
                    {
                        continue;
                    }
                }
                $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
                $todosUtilizadores[$users->id]['id'] = $users->id;
                $todosUtilizadores[$users->id]['name'] = $users->name;
                $todosUtilizadores[$users->id]['posto'] = $users->posto;
            }
            return response()
                ->json($todosUtilizadores);
        }
        catch(\Exception $e)
        {
            return response()->json('405', 200);
        }
    }

    /**
    * Confirma uma refeição para faturação
    *
    * @param Request $request
    * @return json
    */
    public function marcarRefeicao(Request $request)
    {
        $tagRef = \App\Models\user_children_checked_meals::where('user', $request->user)
            ->where('data', $request->data)
            ->where('ref', $request->meal)
            ->first();
        if ($tagRef)
        {
            $tagRef->check = "Y";
            $tagRef->save();
            return redirect()
                ->route('gestão.viewUserChildren', $request->user);
        }
        $tagRef = new \App\Models\user_children_checked_meals;
        $tagRef->data = $request->data;
        $tagRef->ref = $request->meal;
        $tagRef->check = "Y";
        $tagRef->user = $request->user;
        $tagRef->save();
        return redirect()
            ->route('gestão.viewUserChildren', $request->user);
    }

    /**
    * Desmarca uma refeição para faturação.
    *
    * @param Request $request
    * @return redirect
    */
    public function desmarcarRefeicao(Request $request)
    {
        $tagRef = \App\Models\user_children_checked_meals::where('user', $request->user)
            ->where('data', $request->data)
            ->where('ref', $request->meal)
            ->first();
        $tagRef->check = "N";
        $tagRef->save();
        return redirect()
            ->route('gestão.viewUserChildren', $request->user);
    }

    /**
    * Procura uma conta sem login.
    *
    * @param Request $request
    * @return json
    */
    public function searchUser(Request $request)
    {
        if ($request->ajax())
        {
            $childID = "";
            $childNome = "";
            $childPosto = "";
            $childUnidade = "";
            $users = \App\Models\users_children::where('childUnidade', $request->unidade)
                ->where('childID', 'LIKE', '%' . $request->search . "%")
                ->get();
            if ($users)
            {
                foreach ($users as $key => $user)
                {
                    $childID = $user->childID;
                    $childNome = $user->childNome;
                    $childPosto = $user->childPosto;
                    $childUnidade = $user->childUnidade;
                }
                return response()
                    ->json(['id' => $childID, 'nome' => $childNome, 'posto' => $childPosto, 'unidade' => $childUnidade]);
            }
        }
    }

    /**
    * Obtem users associados a um grupo, pertencent a Auth::user()
    *
    * @param Request $request
    * @return json
    */
    public function getGroups(Request $request)
    {
        if ($request->ajax())
        {
            $allGroupRefs = \App\Models\users_children_subgroups::where('parentNIM', Auth::user()->id)
                ->orWhere('parent2nNIM', Auth::user()
                ->id)
                ->where('groupUnidade', Auth::user()
                ->unidade)
                ->get();
                $grupos = array();
            if ($allGroupRefs)
            {
                $index = 0;
                foreach ($allGroupRefs as $key => $grupo)
                {
                    $grupos[$index]['nome'] = $grupo->groupName;
                    $grupos[$index]['ref'] = $grupo->groupID;

                    $index++;
                }
                return response()->json(['gruposRef' => $grupos, ]);
            }
        }
    }

    /**
    * Marcar refeição para um ChildrenUser (user sem conta fisica)
    *
    * @param Request $request
    * @return redirect
    */
    public function storeChidlrenMarcaçao(Request $request)
    {
        $marcaçao = new \App\Models\marcacaotable;
        $marcaçao->NIM = $request->user;
        $marcaçao->data_marcacao = $request->data;
        $marcaçao->meal = $request->ref;
        $marcaçao->save();
        return redirect()
            ->route('gestão.viewUserChildren', $request->user);
    }

    /**
    * Desmarca uma refeição para um ChildrenUser (user sem conta fisica)
    *
    * @param Request $request
    * @return redirect
    */
    public function destroyChidlrenMarcaçao(Request $request)
    {
        $marcaçao = \App\Models\marcacaotable::where('data_marcacao', $request->data)
            ->where('NIM', $request->user)
            ->where('meal', $request->ref);
        $marcaçao->delete();
        return redirect()
            ->route('gestão.viewUserChildren', $request->user);
    }

    /**
    * Altera o nome de um grupo de utilizadores
    *
    * @param Request $request
    * @return redirect
    */
    public function editGroupName(Request $request)
    {
        $group = \App\Models\users_children_subgroups::where('parentNIM', Auth::user()->id)
            ->where('groupID', $request->inputRefer)
            ->first();
        if (!$group) abort(500);
        $group->groupName = $request->inputName;
        $group->groupLocalPrefRef = $request->inputLocalRefPref;
        $group->save();
        return view('profile.saved');
    }

    /**
    * Confirma a transferência de unidade de um utilizador.
    *
    * @param Request $request
    * @return redirect
    */
    public function movedUsersConfirm(Request $request)
    {
        $id = $request->nim;
        while ((strlen((string)$id)) < 8) {
            $id = 0 . (string)$id;
          }
        $user = User::where('id', $id)->first();
        if ($user)
        {
            $user->unidade = $user->trocarUnidade;
            $user->trocarUnidade = null;
            $user->isAccountChildren = 'N';
            $user->accountChildrenOf = null;
            $user->accountChildrenGroup = null;
            $user->accountChildrenSubGroup = null;
            $user->updated_by = Auth::user()->id;
        }
        $user->save();
        $notifications = new notificationsHandler;
        $notifications->new_notification( /*TITLE*/
        'Conta transferida', /*TEXT*/
        'A sua conta foi transferida para ' . $user->unidade . ' por o utilizador ' . Auth::user()->id,
        /*TYPE*/
        'NORMAL',
        /*GERAL*/
        null,
         /*TO USER*/
        $request->nim,
        /*CREATED BY*/
        'SYSTEM: ACCOUNT MOVED @' . Auth::user()->id,
        /*EXPIRE*/
        null);

        $today = date('Y-m-d');
        $new_unidade = \App\Models\unap_unidades::where('slug', $user->unidade)->first();
        $marca_utilizador = \App\Models\marcacaotable::where('NIM', $id)
            ->where('meal', '2REF')->where('data_marcacao', '=<', $today)->get();

        foreach ($marca_utilizador as $key => $usr_tag) {
          $usr_tag->local_ref = $new_unidade['local'];
          $usr_tag->unidade = $new_unidade['slug'];
          $usr_tag->save();
        }

        return redirect()->route('user.profile', $request->nim);
    }

    /**
    * Cancela/rejeita a transferência de unidade de um utilizador.
    *
    * @param Request $request
    * @return redirect
    */
    public function movedUsersReject(Request $request)
    {
        $id = $request->nim;
        while ((strlen((string)$id)) < 8) {
            $id = 0 . (string)$id;
          }
        $user = User::where('id', $id)->first();
        if (!$user)
        {
            $user = \App\Models\users_children::where('childID', $id)->first();
        }
        else
        {
            $user->updated_by = Auth::user()->id;
        }

        $tryUnidade = $user->trocarUnidade;
        $user->trocarUnidade = null;
        $user->save();
        $notifications = new notificationsHandler;
        $notifications->new_notification( /*TITLE*/
        'Transferência negada', /*TEXT*/
        'Um admistrador rejeitou o seu pedido de troca de unidade para a ' . $tryUnidade . ".",
        /*TYPE*/
        'NORMAL', /*GERAL*/
        null, /*TO USER*/
        $id, /*CREATED BY*/
        'SYSTEM: ACCOUNT MOVE REJECT @' . Auth::user()->id, null);

        return redirect()->route('gestão.newUsersAdmin');
    }

    /**
    * Aplicar filtros a página de todos os utilizadores
    *
    * @param Request $request
    * @return view
    */
    public function filterUsers(Request $request)
    {
        if (!(new ActiveDirectoryController)->VIEW_ALL_MEMBERS()) abort(403);
        $users = User::orderBy('unidade')->orderBy('posto');

        $filters = array();
        $filters['account_type_filter'] = ($request->filter_account_type!=null) ? $request->filter_account_type : "0";
        $filters['account_permission_filter'] = ($request->filter_account_permission!=null) ? $request->filter_account_permission : "0";
        $filters['account_posto_filter'] = ($request->filter_account_posto!=null) ? $request->filter_account_posto : "0";
        $filters['account_assoc_filter'] = ($request->filter_account_assoc!=null) ? $request->filter_account_assoc : "0";
        $filters['account_lock_filter'] = ($request->filter_account_locked!=null) ? $request->filter_account_locked : "0";
        $filters['account_local_pref_filter'] = ($request->filter_account_pref_type!=null) ? $request->filter_account_pref_type : "0";
        $filters['account_unit_filter'] = ($request->filter_account_unit!=null) ? $request->filter_account_unit : "0";
        $filters['account_unit_change'] = ($request->filter_unit_change!=null) ? $request->filter_unit_change : "0";
        $filters['tagged_to_fat'] = ($request->filter_tagged_fat!=null) ? $request->filter_tagged_fat : "0";

        if ($filters['account_type_filter'] != "0") $users = $users->where('user_type', $filters['account_type_filter']);
        if ($filters['account_permission_filter'] != "0") $users = $users->where('user_permission', $filters['account_permission_filter']);
        if ($filters['account_posto_filter'] != "0") $users = $users->where('posto', $filters['account_posto_filter']);
        if ($filters['account_assoc_filter'] == "N") $users = $users->where('accountChildrenOf', '<>', null);
        if ($filters['account_lock_filter'] != "0") $users = $users->where('lock', $filters['account_lock_filter']);
        if ($filters['account_local_pref_filter'] != "0") $users = $users->where('localRefPref', $filters['account_local_pref_filter']);
        if ($filters['account_unit_filter'] == 'N') $users = $users->where('unidade', $filters['account_unit_filter']);
        if ($filters['account_unit_change'] == "Y") $users = $users->where('trocarUnidade', '<>', null);
        if ($filters['account_unit_change'] == "N" || $filters['account_unit_change'] == null) $users = $users->where('trocarUnidade',  null);
        if ($filters['tagged_to_fat'] == "Y") $users = $users->where('isTagOblig', '<>', null);
        if ($filters['tagged_to_fat'] == "N") $users = $users->where('isTagOblig', null);

        if (!(new ActiveDirectoryController)->MEALS_TO_EXTERNAL())
        {
            $users = $users->where('unidade', Auth::user()->unidade);
        }

        $users = $users->where('account_verified', 'Y')->get()->all();

        $unidades = \App\Models\unap_unidades::get()->all();
        $filters_applied = array_unique($filters);
        if (count($filters_applied) < 2)
        {
            $key = array_key_first($filters_applied);
            if ($filters_applied[$key] == "0")
            {
                $filters = null;
            }
        }
        return view('gestao.utilizadores', ['users' => $users, 'unidades' => $unidades, 'filters' => $filters, ]);
    }

    /**
    * Associa um parelha com ID do paramêtro a Auth::user()
    *
    * @param string $id NIM do parelha
    * @return view
    */
    public function assoc_secundary_gestor($id)
    {
        try
        {
            $me_id = Auth::user()->id;
            while ((strlen((string)$me_id)) < 8) {
                $me_id = 0 . (string)$me_id;
            }

            $partner_id = $id;
            while ((strlen((string)$partner_id)) < 8) {
                $partner_id = 0 . (string)$partner_id;
            }

            $me = User::where('id', $me_id)->first();
            $partner = User::where('id', $partner_id)->first();
            $me->accountPartnerPOC = $partner_id;
            $me->save();
            $partner->accountPartnerPOC = $me_id;
            $partner->save();
            $assocUsers = User::where('isAccountChildren', 'Y')->where('accountChildrenOf', $partner_id)
                ->orWhere('account2ndChildrenOf', $partner_id)
                ->get();
            foreach ($assocUsers as $key => $usr)
            {
                if ($usr->account2ndChildrenOf == null) $usr->account2ndChildrenOf = $partner_id;
                elseif ($usr->accountChildrenOf == null) $usr->accountChildrenOf = $partner_id;
                else abort(500);
                $usr->save();
            }
            $assocChildUsers = users_children::where('parentNIM', $partner_id)
                ->orWhere('parent2nNIM', Auth::user()
                ->id)
                ->get();
            foreach ($assocChildUsers as $key => $usr)
            {

              if ($usr->parent2nNIM == null) $usr->parent2nNIM = $partner_id;
              elseif ($usr->parentNIM == null) $usr->parentNIM = $partner_id;
              else abort(500);
              $usr->save();
            }

            $assocUserGroups = users_children_subgroups::where('parentNIM', $partner_id)
                ->orWhere('parent2nNIM', Auth::user()
                ->id)
                ->get();
            foreach ($assocUserGroups as $key => $group)
            {
                if ($group->parent2nNIM == null) $group->parent2nNIM = $partner_id;
                elseif ($group->parentNIM == null) $group->parentNIM = $partner_id;
                else $group->parent2nNIM = $partner_id;
                $group->save();
            }

            $assocUser2Groups = \App\Models\users_children_sub2groups::where('parentNIM', $partner_id)
                ->orWhere('parent2nNIM', Auth::user()
                ->id)
                ->get();
            foreach ($assocUser2Groups as $key => $group)
            {
                if ($group->parent2nNIM == null) $group->parent2nNIM = $partner_id;
                elseif ($group->parentNIM == null) $group->parentNIM = $partner_id;
                else $group->parent2nNIM = $partner_id;
                $group->save();
            }

            $notifications = new notificationsHandler;
            $notifications->new_notification(
              /*TITLE*/
              'Associado como parceiro.',
              /*TEXT*/
              'Você foi associado como parceiro ao utilizador '. $partner_id . ' ' . Auth::user()->posto . ' ' . Auth::user()->name . '.',
              /*TYPE*/
              'WARNING',
              /*GERAL*/
              '',
              /*TO USER*/
              $partner['id'],
              /*CREATED BY*/
              'ASSOCIATED @' . $partner_id,
              /*LAPSES AT*/
              null
            );

            $returnMessage = "O utilizador " . $partner['id'] . " " . $partner['posto'] . " " . $partner['name'] . " foi associado como sub-gestor com sucesso.";
            return view('messages.success', ['message' => $returnMessage, 'url' => route('gestão.associatedUsersAdmin') , ]);
        }
        catch(\Exception $e)
        {
            abort(500);
        }
    }

    /**
    * Dessassocia um parelha da conta de Auth::user()
    *
    * @return view
    */
    public function desassoc_secundary_gestor()
    {
        try
        {

            $me_id = Auth::user()->id;
            while ((strlen((string)$me_id)) < 8) {
                $me_id = 0 . (string)$me_id;
            }

            $partner_id = User::where('id', $me_id)->get('accountPartnerPOC')->first();
            $partner_id = $partner_id['accountPartnerPOC'];
            while ((strlen((string)$partner_id)) < 8) {
                $partner_id = 0 . (string)$partner_id;
            }

            $me = User::where('id', $me_id)->first();
            $partner = User::where('id', $partner_id)->first();

            if ($me->accountPartnerPOC == $partner_id)
            {
                $me->accountPartnerPOC = null;
            }
            $me->save();
            $partner->accountPartnerPOC = null;
            $partner->save();
            $assocUsers = User::where('isAccountChildren', 'Y')->where('accountChildrenOf', $me_id)
                ->orWhere('account2ndChildrenOf', $me_id)
                ->get();
            foreach ($assocUsers as $key => $usr)
            {
                if ($usr->accountndChildrenOf == $me_id) $usr->account2ndChildrenOf = null;
                else $usr->accountChildrenOf = null;
                $usr->save();
            }
            $assocChildUsers = users_children::where('parentNIM', $me_id)
                ->orWhere('parent2nNIM', $me_id)
                ->get();
            foreach ($assocChildUsers as $key => $usr)
            {
                if ($usr->parentNIM == $me_id) $usr->parent2nNIM = null;
                else $usr->parentNIM = null;
                $usr->save();
            }

            $assocUserGroups = users_children_subgroups::where('parentNIM', $me_id)
                ->orWhere('parent2nNIM', $me_id)
                ->get();
            foreach ($assocUserGroups as $key => $group)
            {
                if ($group->parent2nNIM != null) $group->parentNIM = $partner_id;
                else $group->parent2nNIM = $partner_id;
                $group->save();
            }

            $assocUser2Groups = \App\Models\users_children_sub2groups::where('parentNIM', $me_id)
                ->orWhere('parent2nNIM', $me_id)
                ->get();
            foreach ($assocUser2Groups as $key => $group)
            {
                if ($group->parent2nNIM != null) $group->parentNIM = $partner_id;
                else $group->parent2nNIM = $partner_id;
                $group->save();
            }

            $notifications = new notificationsHandler;
            $notifications->new_notification(
              /*TITLE*/
              'Desassociado como parceiro.',
              /*TEXT*/
              'Você foi desassociado como parceiro do utilizador '. $me_id . ' ' . Auth::user()->posto . ' ' . Auth::user()->name . '.',
              /*TYPE*/
              'WARNING',
              /*GERAL*/
              '',
              /*TO USER*/
              $partner['id'],
              /*CREATED BY*/
              'ASSOCIATED @' . $me_id,
              /*LAPSES AT*/
              null
            );

            $returnMessage = "O utilizador " . $partner['id'] . " " . $partner['posto'] . " " . $partner['name'] . " foi dessaciado como sub-gestor com sucesso.";
            return view('messages.success', ['message' => $returnMessage, 'url' => route('gestão.associatedUsersAdmin') , ]);
        }
        catch(\Exception $e)
        {
            abort(500);
        }
    }

    /**
    * Efectua um reset de password.
    *
    * @deprecated
    * @param string $id NIM do utilizador
    * @return view
    */
    public function resetPassword($id)
    {
      while ((strlen((string)$id)) < 8) {
        $id = 0 . (string)$id;
      }
        $user = User::where('id', $id)->first();
        $newPassword = \Str::random(8);
        $user->password = \Hash::make($newPassword);
        $user->mustResetPassword = 'Y';
        $user->save();
        $token = \Str::random(20);
        $to_email = $user->email;
        $data = array();
        $data['posto'] = strtoupper($user['posto']);
        $data['nome'] = strtoupper($user['name']);
        $data['pw'] = $newPassword;
        $data['token'] = $token;
        $NIM = Auth::user()->id;
        while ((strlen((string)$NIM)) < 8) {
          $NIM = 0 . (string)$NIM;
        }

            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL))
            {
                throw new Exception('Email inválido.');
            }
            foreach ([$to_email] as $recipient)
            {
                Mail::to($recipient)->send(new passwordResetNotification($data));
            }
            $returnMessage = "Foi enviado um email para redefinir a password para o endereço '" . $to_email . "'. Será necessário redefinir a password quando este utilizador iniciar sessão.";
            return view('messages.success', ['message' => $returnMessage, 'url' => url()->previous() , ]);

    }

    /**
    * Marca um utilizador a receber refeições a numerário.
    *
    * @param Request $request
    * @return view
    */
    public function marcarUserConfRef(Request $request){

      if (!(new ActiveDirectoryController)->USERS_NEED_FATUR()) abort(403);
      $id = $request->user_ID;
      while ((strlen((string)$id)) < 8) {
        $id = 0 . (string)$id;
      }
      $user = User::where('id', $id)->first();
      $is__user__children = false;
      if ($user==null){
         $user = \App\Models\users_children::where('childID', $id)->first();
         $is__user__children = true;
      }
      $dates = explode(" até ", $request->dateRangePicker);
      $date_inicio = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
      $date_fim = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');

      $is_tagged = ($user->isTagOblig==null) ? false : true;
      $new__id = \Str::random(20);
      $new__tag = new \App\Models\users_tagged_conf();
      $new__tag->id = $new__id;
      $new__tag->data_inicio = $date_inicio;
      $new__tag->data_fim = $date_fim;
      $new__tag->registered_to = $id;
      $new__tag->registered_by = Auth::user()->id;
      $new__tag->save();

      $old__start_date = null;
      $old__end_date = null;

      if ($is_tagged) {
        $old__tag = \App\Models\users_tagged_conf::where('id', $user->isTagOblig)->first();
        $old__start_date = $old__tag->data_inicio;
        $old__end_date = $old__tag->data_fim;
        $old__tag->delete();
      }

      $user->isTagOblig = $new__id;
      $user->save();

      if (!$is__user__children) {
        $newNotification = new notificationsHandler;
        $newNotification->new_notification('Marcações', 'A sua conta foi marcada como a receber refeições a dinheiro até à data de '.$dates[1].'.', 'WARNING', null, $request->user_ID, 'SYSTEM: ACCOUNT TAGGED @' . Auth::user()->id, $date_fim);
      }

      $returnMessage = "O utilizador " . $id . " " . $user->posto . " " . strtoupper($user->name) . " foi marcado como a refeições a dinheiro.";
      return view('messages.success', ['message' => $returnMessage, 'url' => url()->previous() , ]);

    }


    /**
    * Ver página de definições com APENAS definições que o utilizador pode alterar
    *
    * @return view
    */
    public function settings_index(){
      $EDIT_DEADLINES_TAG = ((new ActiveDirectoryController)->EDIT_DEADLINES_TAG());
      $EDIT_DEADLINES_UNTAG = ((new ActiveDirectoryController)->EDIT_DEADLINES_UNTAG());
      $EDIT_PESSOAL_SVC = ((new ActiveDirectoryController)->EDIT_PESSOAL_SVC());
      if (!$EDIT_DEADLINES_TAG && !$EDIT_DEADLINES_UNTAG && !$EDIT_PESSOAL_SVC) abort(401);
      $settings = \App\Models\helpdesk_settings::where('id', '<>', null);

      $settings_temp = [];

      if ($EDIT_DEADLINES_TAG) {
        $settings_temp[] = $settings->where('settingSlug', 'AddMax')->first();
      }

      $settings = \App\Models\helpdesk_settings::where('id', '<>', null);
      if ($EDIT_DEADLINES_UNTAG) {
        $settings_temp[] = $settings->where('settingSlug', 'RemoveMax')->first();
      }

      $settings = \App\Models\helpdesk_settings::where('id', '<>', null);
      if ($EDIT_PESSOAL_SVC) {
        $settings_temp[] = $settings->where('settingSlug', 'SvcSemanaQSP')->first();
      }

      $settings = \App\Models\helpdesk_settings::where('id', '<>', null);
      if ($EDIT_PESSOAL_SVC) {
        $settings_temp[] = $settings->where('settingSlug', 'SvcFDSemanaQSP')->first();
      }

      $settings = \App\Models\helpdesk_settings::where('id', '<>', null);
      if ($EDIT_PESSOAL_SVC) {
        $settings_temp[] = $settings->where('settingSlug', 'SvcSemanaQSO')->first();
      }

      $settings = \App\Models\helpdesk_settings::where('id', '<>', null);
      if ($EDIT_PESSOAL_SVC) {
        $settings_temp[] = $settings->where('settingSlug', 'SvcFDSemanaQSO')->first();
      }

      return view('gestao.settings', ['settings' => $settings_temp]);
    }

    /**
    * Editar definição em que valor é um BOOLEAN
    *
    * @param Request $request
    * @return json
    */
    public function gestao_permissoes_change_bools(Request $request)
    {

      $EDIT_DEADLINES_TAG = ((new ActiveDirectoryController)->EDIT_DEADLINES_TAG());
      $EDIT_DEADLINES_UNTAG = ((new ActiveDirectoryController)->EDIT_DEADLINES_UNTAG());
      $EDIT_PESSOAL_SVC = ((new ActiveDirectoryController)->EDIT_PESSOAL_SVC());

        if (!$EDIT_DEADLINES_TAG && !$EDIT_DEADLINES_UNTAG && !$EDIT_PESSOAL_SVC) return response()
            ->json('Permissões insuficientes.', 200);
        try
        {
            $settings = \App\Models\helpdesk_settings::where('id', $request->id)
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
    * Editar definição em que valor é um INT
    *
    * @param Request $request
    * @return json
    */
    public function gestao_permissoes_change_int(Request $request)
    {

      $EDIT_DEADLINES_TAG = ((new ActiveDirectoryController)->EDIT_DEADLINES_TAG());
      $EDIT_DEADLINES_UNTAG = ((new ActiveDirectoryController)->EDIT_DEADLINES_UNTAG());
      $EDIT_PESSOAL_SVC = ((new ActiveDirectoryController)->EDIT_PESSOAL_SVC());

      if (!$EDIT_DEADLINES_TAG && !$EDIT_DEADLINES_UNTAG  && !$EDIT_PESSOAL_SVC) return response()
          ->json('Permissões insuficientes.', 200);
        try
        {
            $settings = \App\Models\helpdesk_settings::where('id', $request->id)->first();
            $settings->settingToggleInt = $request->value;
            $settings->updated_by = Auth::user()->id;

            $notifications = new notificationsHandler;

            if ($settings->settingSlug == "AddMax") {
                $notifications->new_notification(
                    /*TITLE*/
                    'Alteração ao periodo de marcações.',
                    /*TEXT*/
                    'Agora é necessário ' . $request->value . ' dias de antecedência para marcar refeições.',
                    /*TYPE*/
                    'WARNING',
                    /*GERAL*/
                    'HELPDESK,ADMINS,SUPERS,USERS',
                    /*TO USER*/
                    '',
                    /*CREATED BY*/
                    'CHANGE @System',
                    /*LAPSES AT*/
                    null
                );
            } else if ($settings->settingSlug == "RemoveMax"){
                $notifications->new_notification(
                    /*TITLE*/
                    'Alteração ao periodo de marcações.',
                    /*TEXT*/
                    'Agora é necessário ' . $request->value . ' dias de antecedência para remover uma marcação.',
                    /*TYPE*/
                    'WARNING',
                    /*GERAL*/
                    'HELPDESK,ADMINS,SUPERS,USERS',
                    /*TO USER*/
                    '',
                    /*CREATED BY*/
                    'CHANGE @System',
                    /*LAPSES AT*/
                    null
                    );
            }
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
    * Carrega o ficheiro Excel de utilizadores, separa e formata a informação, devolve a página ordenada
    *
    * @param Request $request
    * @return view
    */

    public function loadUsers(Request $request){
      if (!(new ActiveDirectoryController)->EXPRESS_MEMBERS_CHECK()) abort(403);
      $__users_file = Excel::toArray([], $request->file('customFile'));
      $__user_arr_key = 5;
      $__users = $__users_file[1];
      $__users_size = (count($__users)-1);
      $__users = array_slice($__users, $__user_arr_key, $__users_size, true);
      $__user_ueo_key = 0;
      $__user_nim_key = 4;
      $__user_posto_key = 5;
      $__user_name_key = 7;
      $__user_situation_key = 8;
      $__user_dataNasc_key = 11;
      $__users_formatted = array();
      $__NIM_in_file = array();
      $iteration=0;
      foreach ($__users as $key => $__usr) {
          if (($__usr[$__user_ueo_key]=="#GERAL!A1000" ||
            $__usr[$__user_ueo_key]=="CmdPess" ||
            $__usr[$__user_ueo_key]=="CRVNGaia" ||
            $__usr[$__user_ueo_key]=="DARH" ||
            $__usr[$__user_ueo_key]=="DSP" ||
            $__usr[$__user_ueo_key]=="DSPVNGaia" ||
            $__usr[$__user_ueo_key]=="GCSVNGaia" ||
            $__usr[$__user_ueo_key]=="DirSaúde" ||
            $__usr[$__user_ueo_key]=="DIEN" ||
            $__usr[$__user_ueo_key]=="UnAp/CmdPess")
            && ($__usr[$__user_situation_key]!="RESERVA")) {
            $id = strval($__usr[$__user_nim_key]);
            while ((strlen((string)$id)) < 8) {
              $id = 0 . (string)$id;
            }
            $__users_formatted[$iteration]['id'] = $id;
            $__NIM_in_file[$iteration] = $id;
            $apelido = explode (" " , $__usr[$__user_name_key]);
            $apelido_arr_size = (count($apelido) - 2);

            $initial = $apelido[0];
            $apelido = $apelido[$apelido_arr_size] . " " . $apelido[$apelido_arr_size + 1];
            $name = $initial . " " . $apelido;


            $unidade = \App\Models\unap_unidades::where('slug', $__usr[$__user_ueo_key])->first();
            $__posto = $__usr[$__user_posto_key];
            $__users_formatted[$iteration]['name'] = $name;
            $__users_formatted[$iteration]['unidade'] = ($__usr[$__user_ueo_key] == "CmdPess") ? "GabAGE" : $__usr[$__user_ueo_key];
            $__users_formatted[$iteration]['unidade_name'] = ($unidade) ? $unidade['name'] : null;
            $__users_formatted[$iteration]['data_nasc'] =$__usr[$__user_ueo_key];
            $__date_nasc = date('Y', strtotime($__users_formatted[$iteration]['data_nasc']));
            if ($__posto=="1Cb") {
              $__users_formatted[$iteration]['posto'] = "1ºCABO";
            } elseif ($__posto=="1Sarg"){
              $__users_formatted[$iteration]['posto'] = "1ºSARGENTO";
            } elseif ($__posto=="TIG"){
                $__users_formatted[$iteration]['posto'] = "TIG.1";
            } elseif ($__posto=="2Cb"){
              $__users_formatted[$iteration]['posto'] = "2ºCABO";
            } elseif ($__posto=="2Sarg"){
              $__users_formatted[$iteration]['posto'] = "2ºSARGENTO";
            } elseif ($__posto=="Alf"){
              $__users_formatted[$iteration]['posto'] = "Alferes";
            } elseif ($__posto=="AspOf"){
              $__users_formatted[$iteration]['posto'] = "Aspirante";
            } elseif ($__posto=="AssOp"){
              $__users_formatted[$iteration]['posto'] = "ASS.OP.";
            } elseif ($__posto=="AssTec"){
              $__users_formatted[$iteration]['posto'] = "ASS.TEC.";
            } elseif ($__posto=="Cap"){
              $__users_formatted[$iteration]['posto'] = "CAPITAO";
            } elseif ($__posto=="CbAdj"){
              $__users_formatted[$iteration]['posto'] = "CABO-ADJUNTO";
            } elseif ($__posto=="Cor"){
              $__users_formatted[$iteration]['posto'] = "CORONEL";
            } elseif ($__posto=="Furr"){
              $__users_formatted[$iteration]['posto'] = "FURRIEL";
            } elseif ($__posto=="Maj"){
              $__users_formatted[$iteration]['posto'] = "MAJOR";
            } elseif ($__posto=="SAj"){
              $__users_formatted[$iteration]['posto'] = "SARGENTO-AJUDANTE";
            } elseif ($__posto=="SCh"){
              $__users_formatted[$iteration]['posto'] = "SARGENTO-CHEFE";
            } elseif ($__posto=="SMor"){
              $__users_formatted[$iteration]['posto'] = "SARGENTO-MOR";
            } elseif ($__posto=="Sold"){
              $__users_formatted[$iteration]['posto'] = "SOLDADO";
            } elseif ($__posto=="TCor"){
              $__users_formatted[$iteration]['posto'] = "TENENTE-CORONEL";
            } elseif ($__posto=="TecSup"){
              $__users_formatted[$iteration]['posto'] = "TEC.SUP.";
            } elseif ($__posto=="Ten"){
              $__users_formatted[$iteration]['posto'] = "TENENTE";
            } elseif ($__posto=="TGen"){
              $__users_formatted[$iteration]['posto'] = "TENENTE-GENERAL";
            } elseif ($__posto=="2Furr"){
              $__users_formatted[$iteration]['posto'] = "2ºFURRIEL";
            } elseif ($__posto=="BGen"){
              $__users_formatted[$iteration]['posto'] = "BRIGADEIRO-GENERAL";
            } elseif ($__posto=="MGen"){
              $__users_formatted[$iteration]['posto'] = "MAJOR-GENERAL";
            } else {
              $__users_formatted[$iteration]['posto'] = null;
            }
            $iteration++;
          }
      }

      $__users_exist = array();
      $__users_new = array();

      foreach ($__users_formatted as $key => $__usr) {
        $user_from_db = \App\Models\User::where('id', $__usr['id'])->first();
        if ($user_from_db) {
          $__users_exist[$key] = $__usr;
          $__users_exist[$key]['db_data'] = $user_from_db;
        } else {
          $__users_new[$key] = $__usr;
        }
      }

      $__users_old = array();
      $user_from_db_check = \App\Models\User::get()->all();

      foreach ($user_from_db_check as $key => $_usr_from_db) {
        if (!in_array($_usr_from_db['id'], $__NIM_in_file)) {
          $__users_old[$key] = $_usr_from_db;
        }
      }

      foreach ($__users_old as $key => $__old_usr) {
        $unidade = \App\Models\unap_unidades::where('slug', $__old_usr['unidade'])->first();
        $__users_old[$key]['unidade_name'] = ($unidade) ? $unidade['name'] : null;
      }

      $unidades = \App\Models\unap_unidades::get()->all();

      return view('gestao.utilizadores_ver', [
        '__users_exist' => $__users_exist,
        '__users_new' => $__users_new,
        '__users_old' => $__users_old,
        'unidades' => $unidades,
      ]);

    }

    /**
    * Atualiza a informaçao de um utilizador a partir do procedimento de atualização via ficheiro Excel.
    *
    * @param Request $request
    * @return json
    */
    public function update_user_fromfile(Request $request){
      try
      {
        if (!(new ActiveDirectoryController)->EXPRESS_MEMBERS_CHECK()) abort(403);
        $id = $request->user_id;
        while ((strlen((string)$id)) < 8) {
          $id = 0 . (string)$id;
        }
        $user = User::where('id', $id)->first();
        $user->name = $request->name;
        $user->posto = $request->posto;
        $user->unidade = $request->unidade;
        $user->seccao = $request->seccao;
        $user->descriptor = $request->funcao;
        $user->isTagOblig = ($request->refDinheiro=="0") ? null : "PERM";
        $user->save();
        $notifications = new notificationsHandler;
        $notifications->new_notification(
          /*TITLE*/
          'Perfil actualizado.',
          /*TEXT*/
          'O seu perfil foi actualizado através de uma verificação automática. Confirme se está tudo de acordo.',
          /*TYPE*/
          'WARNING',
          /*GERAL*/
          '',
          /*TO USER*/
          $id,
          /*CREATED BY*/
          'UPDATED @System',
          /*LAPSES AT*/
          null
        );

        return response()
            ->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json('error', 200);
        }
    }

    /**
    * Cria um utilizador a partir do procedimento de atualização via ficheiro Excel.
    *
    * @param Request $request
    * @return json
    */
    public function create_user_fromfile(Request $request){
       try
       {
        if (!(new ActiveDirectoryController)->EXPRESS_MEMBERS_CHECK()) abort(403);

        $__user = new User();
        $__user->id = $request->user_id;
        $__user->name = $request->name;
        $__user->descriptor = $request->funcao;
        $__user->password = \Hash::make($request->user_id);

        $__user->mustResetPassword = 'Y';
        $__user->posto = $request->posto;
        $__user->unidade = $request->unidade;
        $__user->seccao = $request->section;
        $__user->user_type = $request->user_type;;
        $__user->user_permission = $request->user_perm;

        $__user->account_verified = 'Y';
        $__user->verified_at = now();
        $__user->verified_by = "SYSTEM";

        $pedido = new \App\Models\QRsGerados();
        $pedido->NIM = $request->user_id;
        $pedido->save();

        $token = new \App\Models\express_account_verification_tokens;
        $token->token = \Str::random(15);
        $token->NIM = $request->user_id;
        $token->created_by = 'AUTO@System';
        $token->save();
        $__user->save();
        return response()
            ->json('success', 200);
       }
       catch(\Exception $e)
       {
           return response()->json($e->getMessage(), 200);
       }
    }

    /**
    * Elimina um utilizador a partir do procedimento de atualização via ficheiro Excel.
    *
    * @param Request $request
    * @return json
    */
    public function remove_user_fromfile(Request $request){
      try
      {
        if (!(new ActiveDirectoryController)->EXPRESS_MEMBERS_CHECK()) abort(403);
        $user = User::where('id', $this::checkNIMLen($request->user_id))->first();
        $user->delete();

        return response()->json('success', 200);
      }
      catch(\Exception $e)
      {
          return response()->json($e->getMessage(), 200);
      }
    }

    /**
    * Ver hóspedes, se Auth::user() for do grupo de messes.
    * Ver apenas hospedes da MMP a que pertence MMANTAS || MMBATALHA
    *
    * @return view
    */
    public function hospedes_index(){
      if (!(Auth::user()->user_permission=="MESSES")) abort(403);
      $hospedes_temp = \App\Models\hospede::where('local', Auth::user()->unidade)
        ->where('type_temp', 'TEMP')
        ->orderBy('type', 'desc')->get()->all();
      $hospedes_perm = \App\Models\hospede::where('local', Auth::user()->unidade)
        ->where('type_temp', 'PERM')
        ->orderBy('type', 'desc')->get()->all();
      $room_array = array();
      for ($i=601; $i <= 909 ; $i++) {
          $room_array[$i] = "Quarto ".$i;
      }
      return view('gestao.hospedes',  [
        'hospedes_temp' => $hospedes_temp,
        'hospedes_perm' => $hospedes_perm,
        'rooms' => $room_array,
      ]);
    }


    /**
    * Ver centro de marcações de refeições para hóspedes
    *
    * @return view
    */
    public function hospedes_marca(){
        if (!(Auth::user()->user_permission=="MESSES")) abort(403);

        $today = date('Y-m-d');
        $ementa = \App\Models\ementaTable::where('data', '>', $today)->get()->all();
        $users = \App\Models\hospede::where('local', Auth::user()->unidade)->get()->all();

        $ementa_formatted = array();
        $it = 0;
        $it_sec = 0;

        foreach ($ementa as $key => $_ementa_entry) {
            $weekday_number = date('N',  strtotime($_ementa_entry['data']));
            if ($weekday_number==6 || $weekday_number==7) continue;
            $ementa_formatted[$it]['id'] = $_ementa_entry['id'] ;
            $ementa_formatted[$it]['data'] = $_ementa_entry['data'] ;
            $ementa_formatted[$it]['sopa_almoço'] = $_ementa_entry['sopa_almoço'] ;
            $ementa_formatted[$it]['prato_almoço'] = $_ementa_entry['prato_almoço'] ;
            $ementa_formatted[$it]['sobremesa_almoço'] = $_ementa_entry['sobremesa_almoço'] ;
            $count_marcados = 0;
            $refs = array('1REF', '2REF', '3REF');
            foreach ($refs as $meal) {
                foreach ($users as $key => $usr) {
                    $u_id = $usr['fictio_nim'];
                    $_is_marcado = \App\Models\marcacaotable::where('data_marcacao', $_ementa_entry['data'])->where('NIM', $u_id)->where('messes_pc', 'Y')->where('meal', $meal)->first();
                    if (!$_is_marcado) {
                        $ementa_formatted[$it]['users_available'][$meal][$it_sec]['id'] = $u_id;
                        $ementa_formatted[$it]['users_available'][$meal][$it_sec]['name'] = $usr['name'];
                        $ementa_formatted[$it]['users_available'][$meal][$it_sec]['type'] = $usr['type'];
                        $ementa_formatted[$it]['users_available'][$meal][$it_sec]['contacto'] = $usr['contacto'];
                        $ementa_formatted[$it]['users_available'][$meal][$it_sec]['quarto'] = $usr['quarto'];
                        $it_sec++;
                    } else {
                        $ementa_formatted[$it]['users_tagged'][$meal][$it_sec]['id'] = $u_id;
                        $ementa_formatted[$it]['users_tagged'][$meal][$it_sec]['name'] = $usr['name'];
                        $ementa_formatted[$it]['users_tagged'][$meal][$it_sec]['type'] = $usr['type'];
                        $ementa_formatted[$it]['users_tagged'][$meal][$it_sec]['contacto'] = $usr['contacto'];
                        $ementa_formatted[$it]['users_tagged'][$meal][$it_sec]['quarto'] = $usr['quarto'];
                        $ementa_formatted[$it]['users_tagged'][$meal][$it_sec]['localRefActual'] = $_is_marcado['local_ref'];

                        $it_sec++;
                        $count_marcados++;
                    }
                }
            }

            $ementa_formatted[$it]['users_total_count'] = count($users);
            $ementa_formatted[$it]['users_available_count'] = (isset($ementa_formatted[$it]['users_available'])) ? count($ementa_formatted[$it]['users_available']) : 0 ;
            $ementa_formatted[$it]['users_marcados_count'] = $count_marcados;

            $count_rfs = (isset($ementa_formatted[$it]['users_tagged']['1REF']) ? count($ementa_formatted[$it]['users_tagged']['1REF']) : 0)
            + (isset($ementa_formatted[$it]['users_tagged']['2REF']) ? count($ementa_formatted[$it]['users_tagged']['2REF']) : 0)
            + (isset($ementa_formatted[$it]['users_tagged']['3REF']) ? count($ementa_formatted[$it]['users_tagged']['3REF']) : 0);
            $ementa_formatted[$it]['refs_marcadas_count'] = $count_rfs;
            $it++;
            $it_sec = 0;
        }
        return view('gestao.hospedes.hosp-center', [
            'entries' => $ementa_formatted,
            'refs' => $refs,
            'ADD_MAX' => ((new checkSettingsTable)->ADDMAX()),
            'REMOVE_MAX' => ((new checkSettingsTable)->REMOVEMAX()),
        ]);
    }


    /**
    * Criar pedido de código QR para as messes, dos quartos selecionados.
    *
    * @param Request $post
    * @return json
    */
    public function hospedes_request_qr(Request $post){
        try {
            if (!$post->ajax()) abort(405);
            $requests = $post['data'];
            foreach ($requests as $key => $rq) {
              for ($i=0; $i < 3; $i++) {
                $req = new \App\Models\QRsGerados;
                $req->UNIDADE = auth()->user()->unidade;
                $req->NIM = "Q".$rq['value'];
                $req->save();
              }
            }
            return response()->json('success', 200);
        } catch (\Throwable $th) {
            return response()->json("error", 200);
        }
    }

    /**
    * Efectua uma marcação a hóspedes selecionados a partir do Centro de Marcações das MMP
    *
    * @param Request $post
    * @return json
    */
    public function TagCenter_Marcar(Request $post){
        try {
            if (!$post->ajax()) abort(405);
            $_data = $post["data"][0]["value"];
            $_users = $post["data"];
            unset($_users[0]);

            foreach ($_users as $key => $_entry) {
                $_user_ID = $_entry['value'];
                $_ref = $_entry['name'];

                switch ($_entry['name']) {
                    case "IDs1[]":
                        $_ref = "1REF";
                        break;
                    case "IDs2[]":
                        $_ref = "2REF";
                        break;
                    case "IDs3[]":
                        $_ref = "3REF";
                        break;
                }

                $slug = Auth::user()->unidade;
                $slug = \App\Models\unap_unidades::where('slug', $slug)->first();
                $local = $slug['local'];

                $hosp = \App\Models\Hospede::where('fictio_nim', $_user_ID)->first();

                $marcaçoes = new \App\Models\marcacaotable;
                $marcaçoes->messes_pc = "Y";
                $marcaçoes->NIM = $_user_ID;
                $marcaçoes->data_marcacao = $_data;
                $marcaçoes->local_ref = $local;
                $marcaçoes->meal = $_ref;
                $marcaçoes->created_by = "HOSP@" . Auth::user()->id;
                $marcaçoes->civil = ($hosp['type']=='CIVIL') ? 'Y' : 'N';
                $marcaçoes->save();

            }
            return response()->json('success', 200);
        } catch (\Exception $th) {
             return response()->json("error", 200);
        }
    }

    /**
    * Remove uma marcação a hóspedes selecionados a partir do Centro de Marcações das MMP
    *
    * @param Request $post
    * @return json
    */
    public function TagCenter_Desmarcar(Request $request){
        try {

            if (!$request->ajax()) abort(500);
            $marcacao = \App\Models\marcacaotable::where('data_marcacao', $request->data)
            ->where('NIM', $request->user)
            ->where('meal', $request->ref)->first();
            if (!$marcacao) return response()->json('Não foi possivel encontrar esta marcação na base de dados.', 200);
            $marcacao->delete();
            return response()->json('success', 200);
        } catch (\Exception $th) {
            return response()->json($th->getMessage(), 200);
        }
    }

    /**
    * Ver perfil de um hóspede.
    *
    * @param string $id ID do hóspede
    * @return view
    */
    public function hospede_profile($id){
      if (!(Auth::user()->user_permission=="MESSES")) abort(403);
      $hospede = \App\Models\hospede::where('local', Auth::user()->unidade)
        ->where('id', $id)->first();
      $marcaçoes = \App\Models\marcacaotable::where('NIM', $hospede['fictio_nim'])
        ->where('data_marcacao', '>', date('Y-m-d'))
        ->orderBy('data_marcacao')
        ->get();


      $ementaTable = \App\Models\ementatable::orderBy('data')
      ->where('data', '>=', date('Y-m-d'))->get()->all();
      #dd($ementaTable);

      $datasMarcadas = array();
      $marcacaoEmenta = array();
      $marcacoesVerificadas = array();
      foreach ($marcaçoes as $marcaçao)
      {
          $datasMarcadas[$marcaçao->id] = $marcaçao->data_marcacao;
          $refeiçaoMarcada[$marcaçao->id] = $marcaçao->meal;
      }
      foreach ($datasMarcadas as $i => $dateToAdd)
      {
          (array)$marcacaoEmenta[$i] = \App\Models\ementatable::where('data', $dateToAdd)->first();
          (array)$marcacaoEmenta[$i]['meal'] = $refeiçaoMarcada[$i];
      }
      $marcaçoes =\App\Models\marcacaotable::where('NIM', $hospede['fictio_nim'])->where('data_marcacao', '>', date('Y-m-d'))->orderBy('data_marcacao')
          ->get();
      $ementaPopulatedMarcaçoes[] = array();
      $datasMarcadas = [];
      foreach ($marcaçoes as $i => $marcaçao)
      {
          $datasMarcadas[$i]['data'] = $marcaçao->data_marcacao;
          $datasMarcadas[$i]['meal'] = $marcaçao->meal;
      }
      $ementaFormatadaDia = app('App\Http\Controllers\URLHandler')->formatEmenta($ementaTable);
      if ($datasMarcadas)
      {
          foreach ($ementaFormatadaDia as $key => $refPorDia)
          {
              if (app('App\Http\Controllers\URLHandler')->verificarEmMarcacoes($datasMarcadas, $refPorDia['data'], $refPorDia['meal']))
              {
                  $ementaFormatadaDia[$key]['marcado'] = "1";
              }
              else
              {
                  $ementaFormatadaDia[$key]['marcado'] = "0";
              }
          }
      }

      return view('gestao.hospede_profile',  [
        'hospede' => $hospede,
        'marcadas' => $marcaçoes,
        'ementa' => $marcacaoEmenta,
        'allRefs' => $ementaFormatadaDia,
      ]);
    }

    /**
    * Cria um hóspede.
    *
    * @param Request $request
    * @return json
    */
    public function hospede_new(Request $request){
      try {
        if (!(Auth::user()->user_permission=="MESSES")) abort(403);
        if(\App\Models\hospede::where('quarto', $request->inputRoom)->count() >= 3) return response()->json("Só é permitido até 3 hóspedes por quarto!");

        $__random_id = random_int(100000000, 999999999);
        $hospede = new \App\Models\hospede;
        $hospede->name = $request->inputName;
        $hospede->type = $request->inputType;
        $hospede->type_temp = $request->inputTypePermTemp;
        $hospede->contacto = $request->inputCont;
        $hospede->quarto = $request->inputRoom;
        $hospede->fictio_nim = $__random_id;
        $hospede->local = Auth::user()->unidade;
        $hospede->save();
        return response()->json('success');
      } catch (\Exception $th) {
        return response()->json($th->message());
      }
    }

    /**
    * Guardar alterações a perfil de hóspede
    *
    * @param Request $request
    * @return view
    */
    public function hospede_save(Request $request){
      if (!(Auth::user()->user_permission=="MESSES")) abort(403);
      $hospede = \App\Models\hospede::where('local', Auth::user()->unidade)->where('id', $request->id)->first();
      $hospede->name = $request->inputName;
      $hospede->type = $request->inputType;
      $hospede->contacto = $request->inputCont;
      $hospede->save();
      $returnMessage = "O hóspede ".$hospede->name." foi actualizado com sucesso";
      return view('messages.success', ['message' => $returnMessage, 'url' => url()->previous() , ]);
    }

    /**
    * Remover um hóspede
    *
    * @param Request $request
    * @return json
    */
    public function hospede_remove(Request $request){
        try {
            if (!(Auth::user()->user_permission=="MESSES")) abort(403);
            $hospede = \App\Models\hospede::where('id', $request->nim)->first();
            $hospede->delete();
            return response()->json('success');
          } catch (\Throwable $th) {
            return response()->json('error');
          }
    }

    /**
    * Página inicial de férias, diligências e ausencias de utilizadores
    *
    * @return view
    */
    public function marcar_ferias_index(){
      if (!(new ActiveDirectoryController)->SCHEDULE_USER_VACATIONS()) abort(403);

      if (Auth::user()->unidade=="UnAp/CmdPess") {
        $allChildrenUsers = users_children::where('childUnidade', 'CRVNGaia')
            ->orWhere('childUnidade', 'DSP')
            ->orWhere('childUnidade', 'UnAp/CmdPess')
            ->orWhere('childUnidade', 'UnSaúde II')
            ->orWhere('childUnidade', 'GabClSelVNGaia')
            ->where('accountVerified', 'Y')
            ->get()->all();
      } else {
          $allChildrenUsers = users_children::where('childUnidade', Auth::user()->unidade)
          ->where('accountVerified', 'Y')
          ->get()->all();
      }

      if (Auth::user()->unidade=="UnAp/CmdPess") {
        $allUsers = User::where('unidade', 'CRVNGaia')
            ->orWhere('unidade', 'DSP')
            ->orWhere('unidade', 'UnAp/CmdPess')
            ->orWhere('unidade', 'UnSaúde II')
            ->orWhere('unidade', 'GabClSelVNGaia')
            ->where('account_verified', 'Y')
            ->get()->all();
      } else {
          $allUsers = User::where('unidade', Auth::user()->unidade)
          ->where('account_verified', 'Y')
          ->get()->all();
      }

      $__todosUtilizadores = array();
      $it = 0;

      $today = date('Y-m-d');


      foreach ($allChildrenUsers as $key => $_user) {
        $it_ferias = 0;
        $__todosUtilizadores[$it]['NIM'] = $_user['childID'];
        $__todosUtilizadores[$it]['POSTO'] = $_user['childPosto'];
        $__todosUtilizadores[$it]['NOME'] = $_user['childNome'];
        $__todosUtilizadores[$it]['UNIDADE'] = $_user['childUnidade'];
        $__todosUtilizadores[$it]['EMAIL'] = $_user['childEmail'];
        $__todosUtilizadores[$it]['DESCRIPTOR'] = $_user['descriptor'];
        $__todosUtilizadores[$it]['SECCAO'] = $_user['seccao'];
        $__todosUtilizadores[$it]['LOCAL_PREF'] = $_user['localRefPref'];

        $ferias = \App\Models\Ferias::where('to_user', $_user['childID'])->where('data_fim', '>', $today)->get()->all();

        foreach ($ferias as $key => $_ferias_entry) {
          $__todosUtilizadores[$it]['FERIAS'][$it_ferias]['id'] = $_ferias_entry['id'];
          $__todosUtilizadores[$it]['FERIAS'][$it_ferias]['data_inicio'] = $_ferias_entry['data_inicio'];
          $__todosUtilizadores[$it]['FERIAS'][$it_ferias]['data_fim'] = $_ferias_entry['data_fim'];
          $__todosUtilizadores[$it]['FERIAS'][$it_ferias]['registered_by'] = $_ferias_entry['registered_by'];
          $it_ferias++;
        }
        $it++;
      }

      foreach ($allUsers as $key => $_user) {
        $it_ferias = 0;
        $__todosUtilizadores[$it]['NIM'] = $_user['id'];
        $__todosUtilizadores[$it]['POSTO'] = $_user['posto'];
        $__todosUtilizadores[$it]['NOME'] = $_user['name'];
        $__todosUtilizadores[$it]['UNIDADE'] = $_user['unidade'];
        $__todosUtilizadores[$it]['EMAIL'] = $_user['email'];
        $__todosUtilizadores[$it]['DESCRIPTOR'] = $_user['descriptor'];
        $__todosUtilizadores[$it]['SECCAO'] = $_user['seccao'];
        $__todosUtilizadores[$it]['LOCAL_PREF'] = $_user['localRefPref'];

        $ferias = \App\Models\Ferias::where('to_user', $_user['id'])->where('data_fim', '>', $today)->get()->all();

        foreach ($ferias as $key => $_ferias_entry) {
          $__todosUtilizadores[$it]['FERIAS'][$it_ferias]['id'] = $_ferias_entry['id'];
          $__todosUtilizadores[$it]['FERIAS'][$it_ferias]['data_inicio'] = $_ferias_entry['data_inicio'];
          $__todosUtilizadores[$it]['FERIAS'][$it_ferias]['data_fim'] = $_ferias_entry['data_fim'];
          $__todosUtilizadores[$it]['FERIAS'][$it_ferias]['registered_by'] = $_ferias_entry['registered_by'];
          $it_ferias++;
        }
        $it++;
      }

      $maxDateAdd = ((new checkSettingsTable)->ADDMAX());

      $minDay = date("Y-m-d", strtotime("+".$maxDateAdd." days"));

      $minDay = date("d-m-Y", strtotime($minDay));

      return view('gestao.ferias_viewer', [
        'users' => $__todosUtilizadores,
        'minDay' => $minDay,
      ]);
    }

    /**
    * Remove uma entrada de ausencia de um utilizador.
    *
    * @param Request $post
    * @return view
    */
    public function marcar_ferias_remove(Request $post){
      try {
        $ferias = \App\Models\Ferias::where('id', $post->id)->first();
        $ferias->delete();
        return response()->json('success', 200);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }
    }

    /**
    * Insere uma ausencia da unidade ao utilizador, e remove marcações feitas para esse periodo.
    *
    * @param Request $post
    * @return json
    */
    public function marcar_ferias_create(Request $post){
      try {

        $ferias = new \App\Models\Ferias;
        $dates = explode(" até ", $post->dateRangePicker);
        $date0 = date('y-m-d', strtotime($dates[0]));
        $date1 = date('y-m-d', strtotime($dates[1]));
        $date0 = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
        $date1 = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');
        $ferias->to_user = $post->user_id;
        $ferias->data_inicio = $date0;
        $ferias->data_fim = $date1;
        $ferias->registered_by = Auth::user()->id;
        $ferias->save();

        $current_marcacoes = \App\Models\marcacaotable::where('NIM', $post->user_id)
          ->where('data_marcacao', '>=', $date0)
          ->where('data_marcacao', '<', $date1)
          ->get();

        foreach ($current_marcacoes as $key => $marcacao) {
          $marcacao->delete();
        }

        return response()->json('success', 200);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }
    }

    /**
    * Ver todas as entradas de quiosque.
    *
    * @return view
    */
    public function viewQuiosqueInfo(){
        if (!(new ActiveDirectoryController)->VIEW_DATA_QUIOSQUE()) abort(403);
        $today = date("Y-m-d");
        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
        $formatted = array();
        for ($i=0; $i < 15; $i++) {
            if($i==0) $new_Date = $today;
            else $new_Date = date('Y-m-d', strtotime($today . ' - ' . $i . ' days'));
            $entradas = \App\Models\entradasQuiosque::where('REGISTADO_DATE',  $new_Date)
            ->orderBy('REF')
            ->orderBy('REGISTADO_TIME')
            ->orderBy('REF', 'ASC')
            ->orderBy('MARCADA', 'ASC')
            ->get()->all();
            if (!empty($entradas)) {
                $mes_index = date('m', strtotime($new_Date));
                $formatted[$i]['date'] = date('d', strtotime($new_Date)) . " " . $mes[($mes_index - 1)];
                foreach ($entradas as $key => $entry) {

                    $_user_id = $entry['NIM'];
                    while ((strlen((string)$_user_id)) < 8) {
                        $_user_id = 0 . (string)$_user_id;
                    }

                    $user = User::where('id',  $_user_id)->first();
                    if($user==null) {
                        $formatted[$i][$key]['id'] = "";
                        $formatted[$i][$key]['NIM'] = "";
                        $formatted[$i][$key]['POSTO'] = \App\Models\entradasQuiosque::where('id',  $entry->id)->value('QTY') . " entradas";
                         if($_user_id = "DILIGENCIA"){
                            $name = "Diligência";
                         } else if($_user_id = "DDN"){
                            $name = "Dia de Defesa Nacional";
                         } elseif ($_user_id = "PCS") {
                            $name = "Provas de Classificação e Seleção";
                         } else {
                             $name = "Desconhecido";
                         }
                        $formatted[$i][$key]['NOME'] = $name;
                    } else {
                        $formatted[$i][$key]['id'] = $entry['id'];
                        $formatted[$i][$key]['NIM'] = $entry['NIM'];
                        $formatted[$i][$key]['POSTO'] = $user['posto'];
                        $formatted[$i][$key]['NOME'] = $user['name'];
                    }

                    if ($entry['REF']=="2REF") {
                        $formatted[$i][$key]['REF'] = "Almoço";
                    } else if ($entry['REF']=="3REF") {
                        $formatted[$i][$key]['REF'] = "Jantar";
                    } else {
                        $formatted[$i][$key]['REF'] = "Pequeno-almoço";
                    }

                    if ($entry['LOCAL']=="QSP") $formatted[$i][$key]['LOCAL']="Quartel da Serra do Pilar";
                    elseif ($entry['LOCAL']=="QSO") $formatted[$i][$key]['LOCAL']="Quartel de Santo Ovídeo";
                    elseif ($entry['LOCAL']=="MMANTAS") $formatted[$i][$key]['LOCAL']="Messe das Antas";
                    elseif ($entry['LOCAL']=="MMBATALHA") $formatted[$i][$key]['LOCAL']="Messe da Batalha";
                    else $formatted[$i][$key]['LOCAL']="";

                    $formatted[$i][$key]['MARCADA'] = ($entry['MARCADA']=="false") ? "0" : "1";

                    $mes_index = date('m', strtotime($entry['REGISTADO_DATE']));
                    $formatted[$i][$key]['REGISTADO_DATE'] = date('d', strtotime($entry['REGISTADO_DATE'])) . " " . $mes[($mes_index - 1)];
                    $formatted[$i][$key]['REGISTADO_TIME'] = $entry['REGISTADO_TIME'];

                }
            }
        }
        return view('gestao.quiosque_admin', [
            'info' => $formatted,
        ]);
    }

    /**
    * Cria um ficheiro PDF com todos os QR's de pedidos feitos.
    *
    * @return PDF
    */
    public function generate_mass_qrs(){


        $id = Auth::user()->id;
        while ((strlen((string)$id)) < 8) {
            $id = 0 . (string)$id;
        }

        $generated['id'] = $id;
        $generated['nome'] = Auth::user()->name;
        $generated['posto'] = Auth::user()->posto;
        $generated['email'] = Auth::user()->email;
        $generated['at_date'] = date('d/m/Y');
        $generated['at_hour'] = date('H:i:s');
        $generated['token'] = \Str::random(10);
        $users_pedidos = \App\Models\QRsGerados::get()->all();
        $qrs_print = array();
        $i = 0;

        foreach ($users_pedidos as $key => $pedidos) {

          $NIM = $pedidos['NIM'];
          while ((strlen((string)$NIM)) < 8) { $NIM = 0 . (string)$NIM; }

          $user = \App\Models\User::where('id', $NIM)->first();
          if ($user!=null) {

              $unidade = \App\Models\unap_unidades::where('slug', $user['unidade'])->first();
              $unidade = $unidade['name'];

              if (strlen($unidade)<=35) {
                $qrs_print[$i]['UNIDADE'] = $unidade;
              } else {
                $qrs_print[$i]['UNIDADE'] = $user['unidade'];
              }

            $qrs_print[$i]['NIM'] = $NIM;

            try {
              $name_exp = explode(" ", $user['name']);
              $name_exp = substr($user['name'], 0, 1)  . '. ' . end($name_exp);
            } catch (\Exception $e) {
              $name_exp =  $user['name'];
            }
            $name = $name_exp;

            $qrs_print[$i]['NOME'] = $user['posto'] . ' ' . $name;
            $filename_png = public_path('assets\profiles\QRS\qrcode_'.$NIM.'.png');

            if(!file_exists($filename_png)){
                $image = \QrCode::size(200)->format('svg')->margin(1)->generate($NIM, public_path('assets\profiles\QRS\qrcode_'.$NIM.'.svg'));
            }
            $file_name = "qrcode_" . $NIM . ".png";
            $qrs_print[$i]['QR'] = public_path('assets\profiles\QRS\\'.$file_name);
            $i++;

          } elseif(str_starts_with($pedidos['NIM'], 'Q')) {

            $nim = $pedidos['NIM'];

            $unidade = \App\Models\unap_unidades::where('slug', $pedidos['UNIDADE'])->first();
            $unidade = $unidade['name'];

            if (strlen($unidade)<=50) {
              $qrs_print[$i]['UNIDADE'] = $unidade;
            } else {
              $qrs_print[$i]['UNIDADE'] = $pedidos['UNIDADE'];
            }


            $qrs_print[$i]['NOME'] = "QUARTO";
            $qrs_print[$i]['NIM'] = str_replace('Q', '', $pedidos['NIM']);

            $filename_png = public_path('assets\profiles\QRS\qrcode_'.$nim.'.png');
            if(!file_exists($filename_png)){
                $image = \QrCode::size(200)->format('svg')->margin(1)->generate($nim, public_path('assets\profiles\QRS\qrcode_'.$nim.'.svg'));
            }
            $file_name = "qrcode_" . $nim . ".png";
            $qrs_print[$i]['QR'] = public_path('assets\profiles\QRS\\'.$file_name);
            $i++;
          }
            $users_pedidos[$key]->delete();
        }
        exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));

        $html = view('PdfGenerate.qrs_in_mass', ['generated' => $generated, 'users_qr' => $qrs_print]);
        #return $html;
        return PDF::loadHTML($html)->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->download('Mass_QRS.pdf');

    }

    /**
    * Cria um ficheiro PDF com o QR de um utilizador.
    *
    * @param int NIM do utilizador
    * @return PDF
    */
    public function generate_QR_USER($id){
        $generated['at_date'] = date('d/m/Y');
        $generated['at_hour'] = date('H:i:s');
        $generated['token'] = \Str::random(10);
        $qrs_print = array();
        $i = 0;
        $NIM = $id;

        while ((strlen((string)$NIM)) < 8) {
            $NIM = 0 . (string)$NIM;
        }

        $user = User::where('id',  $NIM)->first();
        $qrs_print[$i]['NIM'] = $NIM;
        $qrs_print[$i]['POSTO'] = $user['posto'];
        $qrs_print[$i]['NOME'] = $user['name'];
        $qrs_print[$i]['UNIDADE'] = $user['unidade'];

        $filename_png = public_path('assets\profiles\QRS\qrcode_'.$NIM.'.png');

        if(!file_exists($filename_png)){
            $image = \QrCode::size(200)->format('svg')->margin(1)->generate($NIM, public_path('assets\profiles\QRS\qrcode_'.$NIM.'.svg'));
        }

        exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));

        $file_name = "qrcode_" . $NIM . ".png";
        $qrs_print[$i]['QR'] = public_path('assets\profiles\QRS\\'.$file_name);

        $html = view('PdfGenerate.qrs_in_mass', ['generated' => $generated, 'users_qr' => $qrs_print]);
        // return $html;
        return PDF::loadHTML($html)->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->download();

    }

    /**
    * Cria um ficheiro PDF com o QR de PCS.
    *
    * @return PDF
    */
    public function generate_QR_PCS(){
        $generated['at_date'] = date('d/m/Y');
        $generated['at_hour'] = date('H:i:s');
        $generated['token'] = \Str::random(10);
        $qrs_print = array();
        $i = 0;
        $qrs_print[$i]['UNIDADE'] = "";
        $qrs_print[$i]['NIM'] = "PCS";
        $qrs_print[$i]['NOME'] = "CANDIDATO";
        $DATA = "PCS";
        $filename_png = public_path('assets\profiles\QRS\qrcode_'.$DATA.'.png');

        if(!file_exists($filename_png)){
            $image = \QrCode::size(200)->format('svg')->margin(1)->generate($DATA, public_path('assets\profiles\QRS\qrcode_'.$DATA.'.svg'));
        }

        exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));

        $file_name = "qrcode_" . $DATA . ".png";
        $qrs_print[$i]['QR'] = public_path('assets\profiles\QRS\\'.$file_name);

        $html = view('PdfGenerate.qrs_in_mass', ['generated' => $generated, 'users_qr' => $qrs_print]);
        // return $html;
        return PDF::loadHTML($html)->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->download();

    }

    /**
    * Cria um ficheiro PDF com o QR de DDN.
    *
    * @return PDF
    */
    public function generate_QR_DDN(){
        $generated['at_date'] = date('d/m/Y');
        $generated['at_hour'] = date('H:i:s');
        $generated['token'] = \Str::random(10);
        $qrs_print = array();
        $i = 0;
        $qrs_print[$i]['UNIDADE'] = "";
        $qrs_print[$i]['NIM'] = "NACIONAL";
        $qrs_print[$i]['NOME'] = "DIA DE DEFESA";
        $DATA = "DDN";
        $filename_png = public_path('assets\profiles\QRS\qrcode_'.$DATA.'.png');

        if(!file_exists($filename_png)){
            $image = \QrCode::size(200)->format('svg')->margin(1)->generate($DATA, public_path('assets\profiles\QRS\qrcode_'.$DATA.'.svg'));
        }

        exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));

        $file_name = "qrcode_" . $DATA . ".png";
        $qrs_print[$i]['QR'] = public_path('assets\profiles\QRS\\'.$file_name);

        $html = view('PdfGenerate.qrs_in_mass', ['generated' => $generated, 'users_qr' => $qrs_print]);
        return PDF::loadHTML($html)->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->download();
    }

    /**
    * Cria um ficheiro PDF com o QR de DILIGÊNCIA.
    *
    * @return PDF
    */
    public function generate_QR_DLG(){
        $generated['at_date'] = date('d/m/Y');
        $generated['at_hour'] = date('H:i:s');
        $generated['token'] = \Str::random(10);
        $qrs_print = array();
        $i = 0;
        $qrs_print[$i]['UNIDADE'] = "";
        $qrs_print[$i]['NIM'] = "PEDIDOS";
        $qrs_print[$i]['NOME'] = "OUTROS";
        $DATA = "DILIGENCIA";
        $filename_png = public_path('assets\profiles\QRS\qrcode_'.$DATA.'.png');

        if(!file_exists($filename_png)){
            $image = \QrCode::size(200)->format('svg')->margin(1)->generate($DATA, public_path('assets\profiles\QRS\qrcode_'.$DATA.'.svg'));
        }

        exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));

        $file_name = "qrcode_" . $DATA . ".png";
        $qrs_print[$i]['QR'] = public_path('assets\profiles\QRS\\'.$file_name);

        $html = view('PdfGenerate.qrs_in_mass', ['generated' => $generated, 'users_qr' => $qrs_print]);
        return PDF::loadHTML($html)->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->download();
    }

    /**
    * Ver página inicial de dietas de utilizadores.
    *
    * @return view
    */
    public function dietas_index(){

        if (!(new ActiveDirectoryController)->TAG_USER_DIETAS()) abort(403);

        if (Auth::user()->unidade=="UnAp/CmdPess") {
          $allChildrenUsers = users_children::where('childUnidade', 'CRVNGaia')
              ->orWhere('childUnidade', 'DSP')
              ->orWhere('childUnidade', 'UnAp/CmdPess')
              ->orWhere('childUnidade', 'UnSaúde II')
              ->orWhere('childUnidade', 'GabClSelVNGaia')
              ->where('accountVerified', 'Y')
              ->get()->all();
        } else {
            $allChildrenUsers = users_children::where('childUnidade', Auth::user()->unidade)
            ->where('accountVerified', 'Y')
            ->get()->all();
        }

        if (Auth::user()->unidade=="UnAp/CmdPess") {
          $allUsers = User::where('unidade', 'CRVNGaia')
              ->orWhere('unidade', 'DSP')
              ->orWhere('unidade', 'UnAp/CmdPess')
              ->orWhere('unidade', 'UnSaúde II')
              ->orWhere('unidade', 'GabClSelVNGaia')
              ->where('account_verified', 'Y')
              ->get()->all();
        } else {
            $allUsers = User::where('unidade', Auth::user()->unidade)
            ->where('account_verified', 'Y')
            ->get()->all();
        }

        $__todosUtilizadores = array();
        $it = 0;

        $today = date('Y-m-d');


        foreach ($allChildrenUsers as $key => $_user) {
          $it_dieta = 0;

          $__todosUtilizadores[$it]['NIM'] = $_user['childID'];
          $__todosUtilizadores[$it]['POSTO'] = $_user['childPosto'];
          $__todosUtilizadores[$it]['NOME'] = $_user['childNome'];
          $__todosUtilizadores[$it]['UNIDADE'] = $_user['childUnidade'];
          $__todosUtilizadores[$it]['EMAIL'] = $_user['childEmail'];
          $__todosUtilizadores[$it]['DESCRIPTOR'] = $_user['descriptor'];
          $__todosUtilizadores[$it]['SECCAO'] = $_user['seccao'];
          $__todosUtilizadores[$it]['LOCAL_PREF'] = $_user['localRefPref'];

          $Dieta = \App\Models\Dietas::where('NIM', $_user['childID'])->where('data_fim', '>', $today)->get()->all();

          foreach ($Dieta as $key => $_dieta_entry) {
            $__todosUtilizadores[$it]['DIETA'][$it_dieta]['id'] = $_dieta_entry['id'];
            $__todosUtilizadores[$it]['DIETA'][$it_dieta]['data_inicio'] = $_dieta_entry['data_inicio'];
            $__todosUtilizadores[$it]['DIETA'][$it_dieta]['data_fim'] = $_dieta_entry['data_fim'];
            $__todosUtilizadores[$it]['DIETA'][$it_dieta]['registered_by'] = $_dieta_entry['registered_by'];
            $it_dieta++;
          }
          $it++;
        }

        foreach ($allUsers as $key => $_user) {
          $it_dieta = 0;

          $__todosUtilizadores[$it]['NIM'] = $_user['id'];
          $__todosUtilizadores[$it]['POSTO'] = $_user['posto'];
          $__todosUtilizadores[$it]['NOME'] = $_user['name'];
          $__todosUtilizadores[$it]['UNIDADE'] = $_user['unidade'];
          $__todosUtilizadores[$it]['EMAIL'] = $_user['email'];
          $__todosUtilizadores[$it]['DESCRIPTOR'] = $_user['descriptor'];
          $__todosUtilizadores[$it]['SECCAO'] = $_user['seccao'];
          $__todosUtilizadores[$it]['LOCAL_PREF'] = $_user['localRefPref'];

          $ferias = \App\Models\Dietas::where('NIM', $_user['id'])->where('data_fim', '>', $today)->get()->all();

          foreach ($ferias as $key => $_ferias_entry) {
            $__todosUtilizadores[$it]['DIETA'][$it_dieta]['id'] = $_ferias_entry['id'];
            $__todosUtilizadores[$it]['DIETA'][$it_dieta]['data_inicio'] = $_ferias_entry['data_inicio'];
            $__todosUtilizadores[$it]['DIETA'][$it_dieta]['data_fim'] = $_ferias_entry['data_fim'];
            $__todosUtilizadores[$it]['DIETA'][$it_dieta]['registered_by'] = $_ferias_entry['registered_by'];
            $it_dieta++;
          }
          $it++;
        }

        $maxDateAdd = ((new checkSettingsTable)->ADDMAX());

        $minDay = date("Y-m-d", strtotime("+".$maxDateAdd." days"));

        $minDay = date("d-m-Y", strtotime($minDay));

        return view('gestao.dietas', [
            'users' => $__todosUtilizadores,
            'minDay' => $minDay,
        ]);
    }

    /**
    * Remove uma entrada de dieta a um utilizador.
    *
    * @param Request $post
    * @return json
    */
    public function dieta_remove(Request $post){
        try {
          $dieta = \App\Models\Dietas::where('id', $post->id)->first();
          $dieta->delete();
          return response()->json('success', 200);
        } catch (\Exception $e) {
          return response()->json($e->getMessage(), 200);
        }
      }


      /**
      * Adiciona uma entrada de dieta a um utilizador, mas remove entrada anterior.
      *
      * @param Request $post
      * @return json
      */
      public function dieta_create(Request $post){
        try {

          $dieta = new \App\Models\Dietas;
          $dates = explode(" até ", $post->dateRangePicker);
          $date0 = date('y-m-d', strtotime($dates[0]));
          $date1 = date('y-m-d', strtotime($dates[1]));
          $date0 = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
          $date1 = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');

          $NIM = $post->user_id;
          while ((strlen((string)$NIM)) < 8) {
            $NIM = 0 . (string)$NIM;
          }


          $dieta_exist = \App\Models\Dietas::where('NIM', $NIM)->first();
          if($dieta_exist){
            $dieta_exist->delete();
          }

          $dieta->NIM = $NIM;
          $dieta->data_inicio = $date0;
          $dieta->data_fim = $date1;


          $dieta->save();
          return response()->json('success', 200);
        } catch (\Exception $e) {
          return response()->json($e->getMessage(), 200);
        }
      }


    /**
    * Ver página inicial de locais de refeição.
    *
    * @return view
    */
    public function locaisRef_Index(){
      return view('gestao.locais_ref', [
        'locais' => \App\Models\locaisref::get()->all(),
      ]);
    }

    /**
    * Guarda alterações feitas a um local de refeição.
    *
    * @param Request $post
    * @return json
    */
    public function locaisRef_SaveEdit(Request $post){
        try {
          $local = \App\Models\locaisref::where('id', $post->id)->first();
          $local->localName = $post->data[0]['value'];
          $local->status = $post->data[1]['value'];
          $local->save();
          return response()->json('success', 200);
        } catch (\Exception $e) {
          return response()->json($e->getMessage(), 200);
        }
    }

    /**
    * Elimina um local de refeição.
    *
    * @param Request $post
    * @return json
    */
    public function locaisRef_Del(Request $post){
      try {
        $local = \App\Models\locaisref::where('id', $post->id)->first();
        $local->delete();
        return response()->json('success', 200);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }
    }

    /**
    * Cria um novo local de refeição.
    *
    * @param Request $post
    * @return json
    */
    public function locaisRef_Save(Request $post){
      try {
        $local = new \App\Models\locaisref;
        $local->refName = $post->data[0]['value'];
        $local->localName = $post->data[1]['value'];
        $local->status = $post->data[2]['value'];
        $local->capacity = 0;
        $local->save();
        return response()->json('success', 200);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }
    }

    /**
    * Ver página inicial das Unidades.
    *
    * @return view
    */
    public function Unidades_Index(Request $post){
      return view('gestao.unidades', [
        'unidades' => \App\Models\unap_unidades::get()->all(),
        'locais' => \App\Models\locaisref::get()->all(),
      ]);
    }


    /**
    * Guarda alterações feitas a uma unidade.
    *
    * @param Request $post
    * @return json
    */
    public function Unidades_SaveEdit(Request $post){
        try {
          $local = \App\Models\unap_unidades::where('id', $post->id)->first();
          $local->name = $post->data[0]['value'];
          $local->slug = $post->data[1]['value'];
          $local->local = $post->data[2]['value'];
          $local->save();
          return response()->json('success', 200);
        } catch (\Exception $e) {
          return response()->json($e->getMessage(), 200);
        }
    }

    /**
    * Elimina uma unidade.
    *
    * @param Request $post
    * @return json
    */
    public function Unidades_Del(Request $post){
      try {
        $local = \App\Models\unap_unidades::where('id', $post->id)->first();
        $local->delete();
        return response()->json('success', 200);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }
    }


    /**
    * Cria uma nova unidade.
    *
    * @param Request $post
    * @return json
    */
    public function Unidades_Save(Request $post){
      try {
        $local = new \App\Models\unap_unidades;
        $local->slug = $post->data[0]['value'];
        $local->name = $post->data[1]['value'];
        $local->local = $post->data[2]['value'];
        $local->save();
        return response()->json('success', 200);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }
    }

    /**
    * Guarda alterações ao hórario das refeições
    *
    * @param Request $post
    * @return json
    */
    public function Horario_Save(Request $post){
      try {
        $_1ref_min_hour = \Carbon\Carbon::createFromFormat('H:i', '06:00')->format('H:i');
        $_1ref_start_insert = \Carbon\Carbon::createFromFormat('H:i', substr($post['data'][0]['value'], 0, 5))->format('H:i');
        $_1ref_max_hour = \Carbon\Carbon::createFromFormat('H:i', '10:00')->format('H:i');
        $_1ref_end_insert = \Carbon\Carbon::createFromFormat('H:i', substr($post['data'][1]['value'], 0, 5))->format('H:i');

        $_2ref_min_hour = \Carbon\Carbon::createFromFormat('H:i', '11:00')->format('H:i');
        $_2ref_start_insert = \Carbon\Carbon::createFromFormat('H:i', substr($post['data'][2]['value'], 0, 5))->format('H:i');
        $_2ref_max_hour = \Carbon\Carbon::createFromFormat('H:i', '15:00')->format('H:i');
        $_2ref_end_insert = \Carbon\Carbon::createFromFormat('H:i', substr($post['data'][3]['value'], 0, 5))->format('H:i');

        $_3ref_min_hour = \Carbon\Carbon::createFromFormat('H:i', '18:00')->format('H:i');
        $_3ref_start_insert = \Carbon\Carbon::createFromFormat('H:i', substr($post['data'][4]['value'], 0, 5))->format('H:i');
        $_3ref_max_hour = \Carbon\Carbon::createFromFormat('H:i', '21:00')->format('H:i');
        $_3ref_end_insert = \Carbon\Carbon::createFromFormat('H:i', substr($post['data'][5]['value'], 0, 5))->format('H:i');

        $mText = "success";

        if (($_1ref_start_insert >= $_1ref_min_hour) && ($_1ref_end_insert <= $_1ref_max_hour)) {
          $meal = \App\Models\HorariosRef::where('meal', '1REF')->first();
          $meal->time_start = $_1ref_start_insert;
          $meal->time_end = $_1ref_end_insert;
          $meal->save();
        } else {
          $mText = "Os horários de 1º refeição não estão de acordo com os periodos minímos.";
        }

        if (($_2ref_start_insert >= $_2ref_min_hour) && ($_2ref_end_insert <= $_2ref_max_hour)) {
          $meal = \App\Models\HorariosRef::where('meal', '2REF')->first();
          $meal->time_start = $_2ref_start_insert;
          $meal->time_end = $_2ref_end_insert;
          $meal->save();
        } else {
          $mText = "Os horários de 2º refeição não estão de acordo com os periodos minímos.";
        }

        if (($_3ref_start_insert >= $_3ref_min_hour) && ($_3ref_end_insert <= $_3ref_max_hour)) {
          $meal = \App\Models\HorariosRef::where('meal', '3REF')->first();
          $meal->time_start = $_3ref_start_insert;
          $meal->time_end = $_3ref_end_insert;
          $meal->save();
        } else {
          $mText = "Os horários de 3º refeição não estão de acordo com os periodos minímos. O horário de 3ºrefeiçao pode ser definido das <b>18:00h</b> às <b>21:00h</b>.";
        }

        return response()->json($mText , 200);
      } catch (\Exception $e) {
         return response()->json("Ocorreu um erro de servidor.", 200);
      }
    }


    /**
    * Ver página inicial de posts do grupo de permissões.
    *
    * @return view
    */
    public function Equipa_Posts(){
        if (Auth::user()->user_permission=='GENERAL') abort(403);

        $posts = \App\Models\TeamPosts::where('posted_group', Auth::user()->user_permission)->orderBy('created_at', 'DESC')->get()->all();

        $publicado = array();
        $it = 0;

        foreach ($posts as $key => $posts) {

          $NIM = $posts['posted_by'];
          while ((strlen((string)$NIM)) < 8) {
              $NIM = 0 . (string)$NIM;
          }
          $usr = \App\Models\User::where('id', $NIM)->get(['name', 'posto'])->first();

          $publicado[$it]['id'] = $posts['id'];
          $publicado[$it]['by_ID'] = $NIM;
          $publicado[$it]['by_NAME'] = $usr['name'];
          $publicado[$it]['by_POST'] = $usr['posto'];
          $publicado[$it]['title'] = $posts['title'];
          $publicado[$it]['message'] = $posts['message'];
          $publicado[$it]['created_at_date'] =\Carbon\Carbon::parse($posts['created_at'])->format('d/m/Y');
          $publicado[$it]['created_at_time'] =\Carbon\Carbon::parse($posts['created_at'])->format('h:i');
          $it++;
        }

        return view('gestao.posts', [
          'posts' => $publicado,
        ]);
    }


    /**
    * Elimina um post do grupo de permissões.
    *
    * @param Request $post
    * @return json
    */
    public function Post_Delete(Request $post){
      try {
        if (Auth::user()->user_permission=='GENERAL') abort(403);

        $posted = \App\Models\TeamPosts::where('id', $post['post_id'])->first();
        if ($posted['posted_by']!=Auth::user()->id) abort(403);
        if ($posted['posted_group']!=Auth::user()->user_permission) abort(403);

        $posted->delete();
        return response()->json('success', 200);
      }
      catch(\Exception $e)
      {
          return response()->json($e->getMessage() , 200);
      }
    }

    /**
    * Cria um novo post do grupo de permissões.
    *
    * @param Request $post
    * @return json
    */
    public function Post_Save(Request $post){
      try{
        if (Auth::user()->user_permission=='GENERAL') return response()->json("Permissões insuficientes." , 200);

        $posted = new \App\Models\TeamPosts();

        $posted->posted_by = Auth::user()->id;
        $posted->posted_group = Auth::user()->user_permission;
        $posted->title = $post['data'][1]['value'];
        $posted->message = $post['data'][2]['value'];
        $posted->save();
        return response()->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json($e->getMessage() , 200);
        }
    }

    /**
    * Ver página inicial da equipa do utilizador.
    *
    * @return view
    */
    public function Equipa_Index(){
      if (Auth::user()->user_permission=='GENERAL') abort(403);
      $team = \App\Models\User::where('user_permission', Auth::user()->user_permission)
        ->where('unidade', Auth::user()->unidade)
        ->get(['id', 'posto', 'name', 'email', 'descriptor', 'telf', 'user_type', 'last_login'])->all();

      return view('gestao.team', [
        'team_members' => $team
      ]);
    }

    /**
    * Envia um ping para um utilizador no grupo de permissões.
    *
    * @param Request $post
    * @return json
    */
    public function Equipa_EnviarPing_User(Request $post){
      try{
        if (Auth::user()->user_permission=='GENERAL') return response()->json("Permissões insuficientes." , 200);
        $notifications = new notificationsHandler;
        $notifications->new_notification(
            /*TITLE*/
            $post['data'][1]['value'],
            /*TEXT*/
            $post['data'][2]['value'],
            /*TYPE*/
            'WARNING',
            /*GERAL*/
            '',
            /*TO USER*/
            $post['user_id'],
            /*CREATED BY*/
            'PING @' . Auth::user()->id, null);
            return response()->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json($e->getMessage() , 200);
        }
    }

    /**
    * Faz download do relatório ByLocaisRef
    *
    * @param Request $post
    * @return Excel
    */
    public function ExportTotalExcel(Request $post){
      try {
        $_date = $post->export_date;
        if (!isset($post->export_date)) dd("NOK ARGS");
        $file_name = "EXPORT_TOTAL_UTILIZADORES(".$_date.").xlsx";
        return Excel::download(new \App\Exports\ByLocaisRef($post->export_date), $file_name);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }

    }

    /**
    * Faz download do relatório MonthlyExport
    *
    * @param Request $post
    * @return Excel
    */
    public function ExportMonthyExcel(Request $post){
      try {
        if (!isset($post['month_select'])) dd("NOK ARGS");
        $month = intval($post['month_select']);
        $file_name = "EXPORT_MENSAL_UTILIZADORES(".$month.").xlsx";
        return Excel::download(new \App\Exports\MonthlyExport($month), $file_name);
      } catch (\Exception $e) {
        return response()->json("Argumento inválido." , 200);
      }
    }

    /**
    * Faz download do relatório MessesExport
    *
    * @param Request $post
    * @return Excel
    */
    public function ExportMonthyExcelMesses(Request $post){
      try {
        if (!isset($post['month_select'])) dd("NOK ARGS");
        $month = intval($post['month_select']);
        $file_name = "EXPORT_MENSAL_MESSE(".$month.").xlsx";
        return Excel::download(new \App\Exports\MessesExport($month), $file_name);
      } catch (\Exception $e) {
        return response()->json("Argumento inválido." , 200);
      }
    }

    /**
    * Faz download do relatório PedidosQuantExport
    *
    * @param Request $post
    * @return Excel
    */
    public function ExportQuantExcel(Request $post){
      try {
        $_date = $post->export_quant_date;

        if (!isset($post->export_quant_date)) dd("NOK ARGS");
        $file_name = "EXPORT_DIÁRIO_QUANTITAVOS(".$_date.").xlsx";
        return Excel::download(new \App\Exports\PedidosQuantExport($post->export_quant_date), $file_name);
      } catch (\Exception $e) {
        return response()->json("Argumento inválido." , 200);
      }
    }

    /**
    * Faz download do relatório GeneralExport
    *
    * @param Request $post
    * @return Excel
    */
    public function ExportTotalGeneral(Request $post){
      $_date = $post->export_date_general;
      if (!isset($post->export_date_general)) dd("NOK ARGS");
      $date = explode("|", $_date);
      $file_name = "EXPORT_TOTAL_GERAL(".$post->export_date_general.").xlsx";
      return Excel::download(new \App\Exports\GeneralExport($date), $file_name);
    }

    public function ExportsPage(){
      return view('gestao.all_exports');
    }

}
