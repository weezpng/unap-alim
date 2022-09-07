<?php
namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\marcacaotable;
use App\Models\users_children;
use App\Models\users_children_subgroups;
use Facades\App\Models\user_type_permissions;
use App\Models\express_account_verification_tokens;
use Illuminate\Support\Facades\Hash;

/**
 * Lógica principal de gestão de utilizadores associados.
 */
class superUserHandlerController extends Controller
{

    /** Garante que um NIM têm os 8 carácteres
     * 
     * 
     * @param string NIM a formatar
     * @return string NIM formatado
     */
    public function checkNIMLen($NIM)
    {
        $len = strlen((string)$NIM);
        if ($len < 8)
        {
            return 0 . (string)$NIM;
        }
        return $NIM;
    }

    /**
    * Obter informação de utilizadores e subgrupos num grupo.
    * 
    * 
    * @param string Unidade
    * @param string Grupo
    * @return view
    */
    public function groupManager($unidade, $grupo)
    {
        if ($grupo != "GERAL")
        {
            $usersByGroup = \App\Models\users_children::where('parentNIM', Auth::user()->id)
                ->where('childUnidade', $unidade)->where('childGroup', $grupo)->where('accountVerified', 'Y')
                ->get()
                ->all();
        }
        else
        {
            $usersByGroup = \App\Models\users_children::where('parentNIM', Auth::user()->id)
                ->where('accountVerified', 'Y')
                ->where('childUnidade', $unidade)->get()
                ->all();
        }
        foreach ($usersByGroup as $key => $user)
        {
            $localPref = users_children_subgroups::where('groupID', $user->childGroup)
                ->first()->groupLocalPrefRef;
            if ($user['childGroup'] != null)
            {
                $usersByGroup[$key]['childGroupName'] = users_children_subgroups::where('groupID', $user->childGroup)
                    ->first()->groupName;
                $groupRef = $user->childGroup;
            }
            else
            {
                $usersByGroup[$key]['childGroupName'] = "GERAL";
                $groupRef = "GERAL";
            }
            $groupName = $usersByGroup[$key]['childGroupName'];
            $count = count($usersByGroup);

        }
        return view('gestao.groupManagement', ['groupUnidade' => $unidade, 'groupName' => $groupName, 'groupRef' => $groupRef, 'localPref' => $localPref, 'childUsers' => $usersByGroup, 'howManyUsers' => $count]);
    }

    /**
    * Editar informação de perfil de utilizador sem conta local.
    *
    * @param Request $request
    * @return view
    */
    public function editChildrenUser(Request $request)
    {

        $len = strlen((string)$request->id);
        if ($len < 8)
        {
            $id = 0 . (string)$request->id;
        }
        else
        {
            $id = $request->id;
        }
        $userChidlren = \App\Models\users_children::where('childID', $id)->where('accountVerified', 'Y')
            ->first();
        if ($userChidlren!=null) {
          if ($request->inputUEO)
          {
            $changed = ($userChidlren->childUnidade != $request->inputUEO);
          }
          else
          {
            $changed = false;
          }

          $oldUnidade = $userChidlren->childUnidade;
          if ($changed)
          {
            if ($userChidlren->trocarUnidade == null)
            {
              $userChidlren->trocarUnidade = $request->inputUEO;
            }
          }

          $userChidlren->childNome = $request->inputName;
          $userChidlren->childPosto = $request->inputPosto;
          $userChidlren->childGroup = $request->inputGroup;

          $userChidlren->descriptor = $request->inputFunc;
          $userChidlren->seccao = $request->inputSecc;

          $userChidlren->save();
        } else {
          $user = \App\Models\User::where('id', $id)->where('account_verified', 'Y')->first();
          if ($request->inputUEO)
          {
            $changed = ($user->unidade != $request->inputUEO);
          }
          else
          {
            $changed = false;
          }

          $oldUnidade = $user->unidade;
          if ($changed)
          {
            if ($user->trocarUnidade == null)
            {
              $user->trocarUnidade = $request->inputUEO;
            }
          }
          $user->name = $request->inputName;
          $user->posto = $request->inputPosto;
          $user->accountChildrenGroup = $request->inputGroup;
          $user->descriptor = $request->inputFunc;
          $user->seccao = $request->inputSecc;
          $user->save();

        }
        if ($changed)
        {
            return view('profile.saved', ['changedUnidade' => $changed, 'newUnidade' => $request->inputUEO, 'oldUnidade' => $oldUnidade, 'url' => route('gestão.viewUserChildren', $id) , ]);
        }
        return view('profile.saved', ['url' => route('gestão.viewUserChildren', $id) , ]);
    }

    /**
    * Converter um utilizador sem conta local para um conta com login
    *
    * @param Request $request
    * @return view
    */
    public function addChildrenUser(Request $request)
    {
        $childrenUser = new users_children();
        $childrenUser->parentNIM = Auth::user()->id;
        $childrenUser->parent2nNIM = Auth::user()->accountPartnerPOC;
        $childrenUser->childID = $request->childID;
        $childrenUser->childNome = $request->childNome;
        $childrenUser->childPosto = $request->childPosto;
        $childrenUser->childUnidade = Auth::user()->unidade;
        if ($request->childGrupo == "GERAL")
        {
            $childrenUser->childGroup = null;
        }
        else
        {
            $childrenUser->childGroup = $request->childGrupo;
        }
        $childrenUser->save();
        $message = "O utilizador foi criado. Ficará disponivel após confirmação por um Admistrador.";
        return view('messages.success', ['message' => $message, 'url' => url()->previous() ]);
    }

    /**
    * Criar um grupo de utilizadores.
    *
    * @param Request $request
    * @return redirect
    */
    public function addChildrenGroup(Request $request)
    {
        $ref = hash("crc32b", $request->childGrupoName);
        $newGroup = new \App\Models\users_children_subgroups;
        $newGroup->groupID = $ref;
        $newGroup->parentNIM = Auth::user()->id;
        $newGroup->parent2nNIM = Auth::user()->accountPartnerPOC;
        $newGroup->groupName = $request->childGrupoName;
        $newGroup->groupUnidade = Auth::user()->unidade;

        $newGroup->save();
        return redirect()
            ->route('gestão.associatedUsersAdmin');
    }

    /**
    * Cria um subgrupo dentro de um grupo.
    *
    * @param Request $request
    * @return redirect
    */
    public function addChildrenSubGroup(Request $request)
    {
        $ref = hash("crc32b", $request->childSubGrupoName);
        $newGroup = new \App\Models\users_children_sub2groups;
        $newGroup->subgroupID = $ref;
        $newGroup->parentNIM = Auth::user()->id;
        $newGroup->parent2nNIM = Auth::user()->accountPartnerPOC;
        $newGroup->subgroupName = $request->childSubGrupoName;
        $newGroup->parentGroupID = $request->groupID;
        $newGroup->save();
        return redirect()
            ->route('gerir.grupo', $request->groupID);
    }

    /**
    * Elimina um utilizador sem conta local
    *
    * @param Request $request
    * @return redirect
    */
    public function destroyChildrenUser(Request $request)
    {
        if (!((new ActiveDirectoryController)->DELETE_MEMBERS())) abort(401);
        $user = users_children::where('childID', $request->nim)
            ->where('accountVerified', 'Y')
            ->first();
        $user->delete();
        return redirect()
            ->back();
    }

    /**
    * Retirar utilizador sem conta local de um grupo.
    *
    * @param Request $request
    * @return redirect
    */
    public function retirarChildrenUser(Request $request)
    {

        $user = User::where('id', $request->nimRet)
            ->first();
        $userChildren = users_children::where('childID', $request->nimRet)
            ->where('accountVerified', 'Y')
            ->first();
        if ($user != null)
        {
            $user->accountChildrenGroup = null;
            $user->accountChildrenSubGroup = null;
            $user->updated_by = Auth::user()->id;
            $user->save();
        }
        else
        {
            $userChildren->childGroup = null;
            $userChildren->childSubGroup = null;
            $userChildren->save();
        }
        return redirect()
            ->back();
    }

    /**
    * Retirar utilizador sem conta local de um subgrupo.
    *
    * @param Request $request
    * @return redirect
    */
    public function retirarChildrenUserFromSub(Request $request)
    {
        $user = User::where('id', $request->nimRet)
            ->first();
        $userChildren = users_children::where('childID', $request->nimRet)
            ->where('accountVerified', 'Y')
            ->first();
        if ($user != null)
        {
            $user->accountChildrenSubGroup = null;
            $user->updated_by = Auth::user()->id;
            $user->save();
        }
        else
        {
            $userChildren->childSubGroup = null;
            $userChildren->save();
        }
        return redirect()
            ->back();
    }

    /**
    * Retirar utilizador de um grupo.
    *
    * @param Request $request
    * @return redirect
    */
    public function removeUserFromGroup(Request $request)
    {
        $user = \App\Models\users_children::where('childID', $request->nim)
            ->where('accountVerified', 'Y')
            ->first();
        $user->childGroup = null;
        $user->save();
        return redirect()
            ->back();
    }


    /**
    * Adicionar um utilizador de um grupo.
    *
    * @param Request $request
    * @return redirect
    */
    public function addUserToGroup(Request $request)
    {
        $user = \App\Models\users_children::where('childID', $request->childrenAddToGroup)
            ->where('accountVerified', 'Y')
            ->first();
        $user->childGroup = $request->groupName;
        $user->save();
        return redirect()
            ->back();
    }

    /**
    * Procurar através de NIM utilizadores que podem ser associados a um Admin\POC
    *
    * @param Request $request
    * @return json
    */
    public function searchUserToAdd(Request $request)
    {
        if (auth()->user()->user_type != "POC" && auth()->user()->user_type != "ADMIN") abort(401);
        if ($request->ajax())
        {
            $users = User::where('id', 'LIKE', '%' . $request->search . "%")
                ->get();
            if ($users)
            {
                foreach ($users as $key => $user)
                {
                    if ($user->user_type == "USER" && $user->unidade == Auth::user()->unidade && $user->isAccountChildren == "N")
                    {
                        $id = $user->id;
                        $name = $user->name;
                        $posto = $user->posto;
                        $unidade = $user->unidade;
                        $user_type = $user->user_type;
                        $canAdd = ($user->isAccountChildren == "N");
                    }
                }
                return response()
                    ->json(['id' => $id, 'nome' => $name, 'posto' => $posto, 'unidade' => $unidade, 'canAdd' => $canAdd, 'user_type' => $user_type]);
            }
            return response()->json(['nome' => null]);
        }
    }

    /**
    * Faz um pedido de associação de utilizador
    *
    * @param string NIM do utilizador a associar.
    * @return json
    */

    public function addUserToMe($id)
    {
        $id = $this::checkNIMLen($id);
        $user = User::where('id', $id)->first();
        if ($user->user_type != "USER" || $user->unidade != Auth::user()->unidade || $user->isAccountChildren != "N")
        {
            $returnMessage = "Impossivel adicionar este utilizador. Poderá já estar associado a outro utilizador.
          Confirme também que este faz parte da unidade " . Auth::user()->unidade . ".";
            return view('messages.error', ['message' => $returnMessage, 'url' => url()->previous() , ]);
        }
        $user->accountChildrenOf = Auth::user()->id;
        $user->account2ndChildrenOf = Auth::user()->accountPartnerPOC;
        $user->isAccountChildren = "WAITING";
        $user->updated_by = Auth::user()->id;
        $user->save();
        $returnMessage = "O utilizador foi notificado. Quando " . $user->name . ' confirmar, passará a fazer parte dos seus utilizadores.';
        return view('messages.success', ['message' => $returnMessage, 'url' => url()->previous() , ]);
    }

    /**
    * Faz um pedido de associação de utilizador directamente para um grupo\subgrupo
    *
    * @param string NIM do utilizador a associar.
    * @param string Grupo.
    * @param string Subgrupo.
    * @return json
    */
    public function addUserToMeInGroup($id, $group, $sub)
    {
        $id = $this::checkNIMLen($id);
        $user = User::where('id', $id)->first();
        if ($user->user_type != "USER" || $user->unidade != Auth::user()->unidade || $user->isAccountChildren != "N")
        {
            $returnMessage = "Impossivel adicionar este utilizador. Poderá já estar associado a outro utilizador.
        Confirme também que este faz parte da unidade " . Auth::user()->unidade . ".";
            return view('messages.error', ['message' => $returnMessage, 'url' => url()->previous() , ]);
        }
        $user->accountChildrenOf = Auth::user()->id;
        $user->account2ndChildrenOf = Auth::user()->accountPartnerPOC;
        $user->isAccountChildren = "WAITING";
        $user->accountChildrenGroup = $group;
        if ($sub != 0)
        {
            $user->accountChildrenSubGroup = $sub;
        }
        $user->updated_by = Auth::user()->id;
        $user->save();
        $returnMessage = "O utilizador foi notificado. Quando " . $user->name . ' confirmar, passará a fazer parte dos seus utilizadores.';
        return view('messages.success', ['message' => $returnMessage, 'url' => url()->previous() , ]);
    }

    /**
    * Obter toda a informação de um grupo
    *
    * @param string ID de grupo    
    * @return view
    */
    public function manageGroup($id)
    {
        $group = users_children_subgroups::where('groupID', $id)->first();
        $subgroups = \App\Models\users_children_sub2groups::where('parentGroupID', $group['groupID'])->get()
            ->all();
        if ($group != null)
        {
            foreach ($subgroups as $key => $sub)
            {
                $users = count(User::where('accountChildrenSubGroup', $sub['subgroupID'])->get());
                $childrenusers = count(users_children::where('childSubGroup', $sub['subgroupID'])->where('accountVerified', 'Y')
                    ->get());
                $subgroups[$key]['totalUsers'] = ($users + $childrenusers);
            }
            $allChildren = \App\Models\users_children::where('parentNIM', auth()->user()
                ->id)
                ->orWhere('parent2nNIM', auth()
                ->user()
                ->id)
                ->where('childGroup', $id)->where('childSubGroup', null)
                ->where('accountVerified', 'Y')
                ->get();
            $allUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('account_verified', 'Y')
                ->where('isAccountChildren', 'Y')
                ->where('accountChildrenGroup', $id)->where('accountChildrenSubGroup', null)
                ->get();
            $todosUtilizadores = [];
            foreach ($allChildren as $key => $users)
            {
              if ($users->childGroup==$group['groupID']) {
                $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
                $todosUtilizadores[$users->childID]['id'] = $users->childID;
                $todosUtilizadores[$users->childID]['name'] = $users->childNome;
                $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
                $todosUtilizadores[$users->childID]['unidade'] = $users->childUnidade;
              }
            }
            foreach ($allUsers as $key => $users)
            {
              if ($users->accountChildrenGroup==$group['groupID']) {
                $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
                $todosUtilizadores[$users->id]['id'] = $users->id;
                $todosUtilizadores[$users->id]['name'] = $users->name;
                $todosUtilizadores[$users->id]['posto'] = $users->posto;
                $todosUtilizadores[$users->id]['unidade'] = $users->unidade;
              }
            }
        }
        return view('group_gestao.gestaoGrupo', ["rootUsers" => $todosUtilizadores, "grupo" => $group, "subgrupos" => $subgroups]);
    }

    /**
    * Obter toda a informação de um subgrupo
    *
    * @param string ID de grupo    
    * @param string ID de subgrupo
    * @return view
    */

    public function manageSubGroup($grupo, $subgrupo)
    {
        if ($subgrupo == "0NULL")
        {
            return redirect()->route('gerir.grupo', $grupo);
        }
        $group = users_children_subgroups::where('groupID', $grupo)->first();
        $subgroup = \App\Models\users_children_sub2groups::where('parentGroupID', $grupo)->where('subgroupID', $subgrupo)->first();
        $allChildren = \App\Models\users_children::where('parentNIM', auth()->user()
            ->id)
            ->orWhere('parent2nNIM', auth()
            ->user()
            ->id)
            ->where('accountVerified', 'Y')
            ->where('childGroup', $grupo)->where('childSubGroup', $subgrupo)->get();
        $allUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
            ->orWhere('account2ndChildrenOf', Auth::user()
            ->id)
            ->where('account_verified', 'Y')
            ->where('isAccountChildren', 'Y')
            ->where('accountChildrenGroup', $grupo)->where('accountChildrenSubGroup', $subgrupo)->orderBy('accountChildrenGroup')
            ->orderBy('accountChildrenSubGroup')
            ->get();
        $todosUtilizadores = [];
        foreach ($allChildren as $key => $users)
        {
            if ($users->accountChildrenGroup==$grupo && $$users->childGroup==$subgrupo) {
              $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
              $todosUtilizadores[$users->childID]['id'] = $users->childID;
              $todosUtilizadores[$users->childID]['name'] = $users->childNome;
              $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
              $todosUtilizadores[$users->childID]['unidade'] = $users->childUnidade;
          }
        }
        foreach ($allUsers as $key => $users)
        {
          if ($users->childGroup==$grupo && $$users->accountChildrenSubGroup==$subgrupo) {
            $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
            $todosUtilizadores[$users->id]['id'] = $users->id;
            $todosUtilizadores[$users->id]['name'] = $users->name;
            $todosUtilizadores[$users->id]['posto'] = $users->posto;
            $todosUtilizadores[$users->id]['unidade'] = $users->unidade;
          }
        }
        if ($subgroup == null)
        {
            abort(404);
        }
        return view('group_gestao.gestaoSubGrupo', ["grupo" => $group, "subgrupo" => $subgroup, "users" => $todosUtilizadores, ]);
    }

    /**
    * Retira um utilizador sem conta local de um subgrupo
    *
    * @param Request $request
    * @return redirect
    */
    public function kickFromSubGrupo(Request $request)
    {
        $user = \App\Models\users_children::where('childID', $request->nim)
            ->where('accountVerified', 'Y')
            ->first();
        $user->childGroup = null;
        $user->save();
        return redirect()
            ->back();
    }

    /**
    * Apaga um subgrupo.
    * Utilizadores são movidos para GRUPO\GERAL
    *
    * @param string ID de grupo
    * @param string ID de subgrupo
    * @return redirect
    */
    public function deleteSubGroup($grupo, $subgrupo)
    {
        $subgroup = \App\Models\users_children_sub2groups::where('parentGroupID', $grupo)->where('subgroupID', $subgrupo)->first();
        $allChildren = \App\Models\users_children::where('parentNIM', auth()->user()
            ->id)
            ->orWhere('parent2nNIM', auth()
            ->user()
            ->id)
            ->where('accountVerified', 'Y')
            ->where('childGroup', $grupo)->where('childSubGroup', $subgrupo)->get();
        $allUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
            ->orWhere('account2ndChildrenOf', Auth::user()
            ->id)
            ->where('account_verified', 'Y')
            ->where('isAccountChildren', 'Y')
            ->where('accountChildrenGroup', $grupo)->where('accountChildrenSubGroup', $subgrupo)->orderBy('accountChildrenGroup')
            ->orderBy('accountChildrenSubGroup')
            ->get();
        foreach ($allChildren as $user0)
        {
            $user0->childSubGroup = null;
            $user0->save();
        }
        foreach ($allUsers as $user1)
        {
            $user1->accountChildrenSubGroup = null;
            $user1->save();
        }
        $subgroup->delete();
        return redirect()
            ->route('gerir.grupo', $grupo);
    }

    /**
    * Apaga todos os subgrupos de um grupo.
    * Utilizadores são movidos para GRUPO\GERAL
    *
    * @param string ID de grupo
    * @return redirect
    */
    public function removeAllSubGroups($grupo)
    {
        $subgroups = \App\Models\users_children_sub2groups::where('parentGroupID', $grupo)->get()
            ->all();
        foreach ($subgroups as $group2)
        {
            $fromSub = $group2['subgroupID'];
            $allChildren = \App\Models\users_children::where('parentNIM', auth()->user()
                ->id)
                ->orWhere('parent2nNIM', auth()
                ->user()
                ->id)
                ->where('accountVerified', 'Y')
                ->where('childSubGroup', $fromSub)->get();
            $allUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('account_verified', 'Y')
                ->where('isAccountChildren', 'Y')
                ->where('accountChildrenSubGroup', $fromSub)->orderBy('accountChildrenGroup')
                ->orderBy('accountChildrenSubGroup')
                ->get();
            foreach ($allChildren as $user0)
            {
                $user0->childSubGroup = null;
                $user0->save();
            }
            foreach ($allUsers as $user1)
            {
                $user1->accountChildrenSubGroup = null;
                $user1->save();
            }
            $group2->delete();
        }
        return redirect()
            ->route('gerir.grupo', $grupo);
    }

    /**
    * Apaga um grupo
    * Utilizadores são movidos para GERAL
    *
    * @param string ID de grupo
    * @return redirect
    */
    public function removeGroup($grupoID)
    {
        $subgroups = \App\Models\users_children_sub2groups::where('parentGroupID', $grupo)->get()
            ->all();
        foreach ($subgroups as $group2)
        {
            $fromSub = $group2['subgroupID'];
            $allChildren = \App\Models\users_children::where('parentNIM', auth()->user()
                ->id)
                ->orWhere('parent2nNIM', auth()
                ->user()
                ->id)
                ->where('accountVerified', 'Y')
                ->where('childSubGroup', $fromSub)->get();
            $allUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('account_verified', 'Y')
                ->where('isAccountChildren', 'Y')
                ->where('accountChildrenSubGroup', $fromSub)->orderBy('accountChildrenGroup')
                ->orderBy('accountChildrenSubGroup')
                ->get();
            foreach ($allChildren as $user0)
            {
                $user0->childSubGroup = null;
                $user0->save();
            }
            foreach ($allUsers as $user1)
            {
                $user1->accountChildrenSubGroup = null;
                $user1->save();
            }
            $group2->delete();
        }
        $allChildren = \App\Models\users_children::where('parentNIM', auth()->user()
            ->id)
            ->orWhere('parent2nNIM', auth()
            ->user()
            ->id)
            ->where('accountVerified', 'Y')
            ->where('childGroup', $grupoID)->get();
        $allUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
            ->orWhere('account2ndChildrenOf', Auth::user()
            ->id)
            ->where('account_verified', 'Y')
            ->where('isAccountChildren', 'Y')
            ->where('accountChildrenGroup', $grupoID)->get();
        foreach ($allChildren as $childrenUser)
        {
            $childrenUser->childGroup = null;
            $childrenUser->save();
        }
        foreach ($allUsers as $key => $user)
        {
            $user->accountChildrenGroup = null;
            $user->save();
        }
        $grupo = $subgroups = \App\Models\users_children_subgroups::where('groupID', $grupoID)->first();
        $grupo->delete();
        return redirect()
            ->route('gestão.usersAdmin');
    }

    /**
    * Guarda informação de grupo
    *
    * @param Request $request
    * @return redirect
    */
    public function saveGroupEdit(Request $request)
    {
        $grupo = \App\Models\users_children_subgroups::where('groupID', $request->groupID)
            ->first();
        $grupo->groupName = $request->groupName;
        $grupo->groupLocalPrefRef = $request->inputLocalRefPref;
        $grupo->save();
        return redirect()
            ->route('gerir.grupo', $request->groupID);
    }

    /**
    * Guarda informação de sub-grupo
    *
    * @param Request $request
    * @return redirect
    */
    public function saveSubGroupEdit(Request $request)
    {
        $grupo = \App\Models\users_children_sub2groups::where('subgroupID', $request->subgroupID)
            ->first();
        $grupo->subgroupName = $request->subgroupName;
        $grupo->subgroupLocalPref = $request->subgroupLocalPref;
        $grupo->save();
        return redirect()
            ->back();
    }

    /**
    * Obtem informação de utilizadores que podem prontamente serem adicionados ao sub-grupo, e utilizadores já existentes nesse sub-grupo.
    *
    * @param Request $request
    * @return json
    */
    public function get_users_to_subgroups(Request $request)
    {
        $allChildren = \App\Models\users_children::where('parentNIM', auth()->user()
            ->id)
            ->orWhere('parent2nNIM', auth()
            ->user()
            ->id)
            ->where('accountVerified', 'Y')
            ->where('childGroup', null)
            ->where('childSubGroup', null)
            ->get();
        $allChildrenCurrentGroup = \App\Models\users_children::where('parentNIM', auth()->user()
            ->id)
            ->orWhere('parent2nNIM', auth()
            ->user()
            ->id)
            ->where('accountVerified', 'Y')
            ->where('childGroup', $request->currentGroup)
            ->where('childSubGroup', null)
            ->get();
        $allUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
            ->orWhere('account2ndChildrenOf', Auth::user()
            ->id)
            ->where('account_verified', 'Y')
            ->where('isAccountChildren', 'Y')
            ->where('accountChildrenSubGroup', null)
            ->where('accountChildrenGroup', null)
            ->get();
        $allUsersCurrentGroup = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
            ->orWhere('account2ndChildrenOf', Auth::user()
            ->id)
            ->where('account_verified', 'Y')
            ->where('isAccountChildren', 'Y')
            ->where('accountChildrenGroup', $request->currentGroup)
            ->where('accountChildrenSubGroup', null)
            ->get();
        $todosUtilizadores = [];
        foreach ($allChildren as $key => $users)
        {
            $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
            $todosUtilizadores[$users->childID]['id'] = $users->childID;
            $todosUtilizadores[$users->childID]['name'] = $users->childNome;
            $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
            $todosUtilizadores[$users->childID]['grupoID'] = $users->childGroup;
            $todosUtilizadores[$users->childID]['grupo'] = "NÃO ASSOCIADO";
        }
        foreach ($allUsers as $key => $users)
        {
            $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
            $todosUtilizadores[$users->id]['id'] = $users->id;
            $todosUtilizadores[$users->id]['name'] = $users->name;
            $todosUtilizadores[$users->id]['posto'] = $users->posto;
            $todosUtilizadores[$users->id]['grupoID'] = $users->unidade;
            $todosUtilizadores[$users->id]['grupo'] = "NÃO ASSOCIADO";
        }
        foreach ($allChildrenCurrentGroup as $key => $users)
        {
            $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
            $todosUtilizadores[$users->childID]['id'] = $users->childID;
            $todosUtilizadores[$users->childID]['name'] = $users->childNome;
            $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
            $todosUtilizadores[$users->childID]['grupoID'] = $users->childGroup;
            $todosUtilizadores[$users->childID]['grupo'] = "ASSOCIADO";
        }
        foreach ($allUsersCurrentGroup as $key => $users)
        {
            $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
            $todosUtilizadores[$users->id]['id'] = $users->id;
            $todosUtilizadores[$users->id]['name'] = $users->name;
            $todosUtilizadores[$users->id]['posto'] = $users->posto;
            $todosUtilizadores[$users->id]['grupo'] = "ASSOCIADO";
        }
        return response()
            ->json($todosUtilizadores);
    }

    /**
    * Adicionar um utilizador já  associado a sub-grupo
    *
    * @param Request $request
    * @return json
    */
    public function addUserToSub(Request $request)
    {
        if ($request->ajax())
        {
            try
            {
                if ($request->userType == "CHILDREN")
                {
                    $children = \App\Models\users_children::where('childID', $request->userID)
                        ->where('accountVerified', 'Y')
                        ->first();
                    $children->childGroup = $request->groupID;
                    $children->childSubGroup = $request->subID;
                    $children->save();
                    return response()
                        ->json('success', 200);
                }
                $user = \App\Models\User::where('id', $request->userID)
                    ->first();
                $user->accountChildrenGroup = $request->groupID;
                $user->accountChildrenSubGroup = $request->subID;
                $user->updated_by = Auth::user()->id;
                $user->save();
                return response()
                    ->json('success', 200);
            }
            catch(\Exception $e)
            {
                return response()->json('500', 200);
            }
            return response()
                ->json('405', 200);
        }
        abort(405);
    }

    /**
    * Procura utilizadores associados que podem prontamente serem adicionados a GRUPO
    *
    * @param Request $request
    * @return json
    */
    public function searchFromSuperAddToGroupSearch(Request $request)
    {
        try
        {
            $allChildren = \App\Models\users_children::where('childID', 'LIKE', '%' . $request->search . "%")
                ->where('parentNIM', Auth::user()
                ->id)
                ->where('childGroup', null)
                ->where('accountVerified', 'Y')
                ->get();
            $allUsers = \App\Models\User::where('id', 'LIKE', '%' . $request->search . "%")
                ->where('accountChildrenOf', Auth::user()
                ->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('isAccountChildren', 'Y')
                ->where('accountChildrenGroup', null)
                ->where('account_verified', 'Y')
                ->get();
            $todosUtilizadores = [];
            foreach ($allChildren as $key => $users)
            {
                $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
                $todosUtilizadores[$users->childID]['id'] = $users->childID;
                $todosUtilizadores[$users->childID]['name'] = $users->childNome;
                $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
                $todosUtilizadores[$users->childID]['unidade'] = $users->childUnidade;
            }
            foreach ($allUsers as $key => $users)
            {
                $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
                $todosUtilizadores[$users->id]['id'] = $users->id;
                $todosUtilizadores[$users->id]['name'] = $users->name;
                $todosUtilizadores[$users->id]['posto'] = $users->posto;
                $todosUtilizadores[$users->id]['unidade'] = $users->unidade;
                $todosUtilizadores[$users->id]['verified'] = $users->account_verified;
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
    * Procura de possiveis gestores para o utilizador com sessão iniciada.
    *
    * @param Request $request
    * @return json
    */
    public function searchUserSubGestor(Request $request)
    {
        $possible_new_gestores = \App\Models\User::where('account_verified', 'Y')->where('unidade', Auth::user()
            ->unidade)
            ->where('user_type', 'POC')
            ->orWhere('user_type', 'ADMIN')
            ->where('lock', 'N')
            ->where('isAccountChildren', 'N')
            ->where('id', 'LIKE', '%' . $request->search . "%")
            ->get();
        $todosUtilizadores = array();
        foreach ($possible_new_gestores as $key => $users)
        {
            if ($users->id != Auth::user()
                ->id)
            {
                if (!$users->accountPartnerPOC)
                {
                    $todosUtilizadores[$users->id]['id'] = $users->id;
                    $todosUtilizadores[$users->id]['name'] = $users->name;
                    $todosUtilizadores[$users->id]['posto'] = $users->posto;
                }
            }
        }
        return $todosUtilizadores;
    }

    /**
    * Procura de informação basica de utilizadores associados a utilizador com sessão iniciada
    *
    * @param Request $request
    * @return json
    */
    public function searchUserFromMine(Request $request)
    {
        try
        {
            $allChildren = \App\Models\users_children::where('childID', 'LIKE', '%' . $request->search . "%")
                ->where('parentNIM', Auth::user()
                ->id)
                ->where('accountVerified', 'Y')
                ->get();
            $allUsers = \App\Models\User::where('id', 'LIKE', '%' . $request->search . "%")
                ->where('accountChildrenOf', Auth::user()
                ->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('isAccountChildren', 'Y')
                ->where('account_verified', 'Y')
                ->get();
            $todosUtilizadores = [];
            foreach ($allChildren as $key => $users)
            {
                $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
                $todosUtilizadores[$users->childID]['id'] = $users->childID;
                $todosUtilizadores[$users->childID]['name'] = $users->childNome;
                $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
                $todosUtilizadores[$users->childID]['grupo'] = ($users->childGroup == null) ? null : \App\Models\users_children_subgroups::where('groupID', $users->childGroup)
                    ->first()
                    ->value('groupName');
                $todosUtilizadores[$users->childID]['subgrupo'] = ($users->childSubGroup == null) ? null : \App\Models\users_children_sub2groups::where('subgroupID', $users->childSubGroup)
                    ->first()
                    ->value('subgroupName');
                $todosUtilizadores[$users->childID]['grupoID'] = $users->childGroup;
                $todosUtilizadores[$users->childID]['subgrupoID'] = $users->childSubGroup;
            }
            foreach ($allUsers as $key => $users)
            {
                $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
                $todosUtilizadores[$users->id]['id'] = $users->id;
                $todosUtilizadores[$users->id]['name'] = $users->name;
                $todosUtilizadores[$users->id]['posto'] = $users->posto;
                $todosUtilizadores[$users->id]['unidade'] = $users->unidade;
                $todosUtilizadores[$users->childID]['grupo'] = ($users->accountChildrenGroup == null) ? null : \App\Models\users_children_subgroups::where('groupID', $users->accountChildrenGroup)
                    ->first()
                    ->value('groupName');
                $todosUtilizadores[$users->childID]['subgrupo'] = ($users->accountChildrenSubGroup == null) ? null : \App\Models\users_children_sub2groups::where('subgroupID', $users->accountChildrenSubGroup)
                    ->first()
                    ->value('subgroupName');
                $todosUtilizadores[$users->childID]['grupoID'] = $users->accountChildrenGroup;
                $todosUtilizadores[$users->childID]['subgrupoID'] = $users->accountChildrenSubGroup;
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
    * Faz redirect para o 'tipo' de perfil correct a mostrar para um utilizador (com/sem conta local)
    *
    * @param string tipo de user
    * @param string id do user
    * @return redirect
    */
    public function present_user_definer_type($type, $id)
    {
        if ($type == "CHILDREN")
        {
            return redirect()->route('gestão.viewUserChildren', $id);
        }
        elseif ($type == "UTILIZADOR")
        {
            return redirect()->route('user.profile', $id);
        }
        else
        {
            abort(405);
        }
    }

    /**
    * Adiciona utilizador associado a grupo
    *
    * @param string id do grupo
    * @param string id do utilizador
    * @param string tipo de utilizador
    * @return redirect
    */
    public function addUserToGrupo($grupoID, $userID, $userType)
    {
        if ($userType == "CHILDREN")
        {
            $childrenUser = \App\Models\users_children::where('childID', $userID)->where('parentNIM', Auth::user()
                ->id)
                ->where('accountVerified', 'Y')
                ->first();
            $childrenUser->childGroup = $grupoID;
            $childrenUser->save();
        }
        elseif ($userType == "UTILIZADOR")
        {
            $associatedUser = \App\Models\User::where('id', $userID)->where('accountChildrenOf', Auth::user()
                ->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('isAccountChildren', 'Y')
                ->where('account_verified', 'Y')
                ->first();
            $associatedUser->accountChildrenGroup = $grupoID;
            $associatedUser->updated_by = Auth::user()->id;
            $associatedUser->save();
        }
        else
        {
            abort(405);
        }
        return redirect()
            ->route('gerir.grupo', $grupoID);
    }

    /**
    * Dessassocia um utilizador de Auth::user()
    *
    * @param Request $request
    * @return redirect
    */
    public function desassociarUser(Request $request)
    {
        $user = \App\Models\User::where('id', $request->nimdesassocia)
            ->where('accountChildrenOf', Auth::user()
            ->id)
            ->orWhere('account2ndChildrenOf', Auth::user()
            ->id)
            ->where('isAccountChildren', 'Y')
            ->first();
        $user->isAccountChildren = "N";
        $user->accountChildrenOf = null;
        $user->accountChildrenGroup = null;
        $user->accountChildrenSubGroup = null;
        $user->updated_by = Auth::user()->id;
        $notifications = new notificationsHandler;
        $notifications->new_notification( /*TITLE*/
        'Conta desassociada', /*TEXT*/
        'A sua conta foi desassociada do utilizador ' . Auth::user()->id . '.',
        /*TYPE*/
        'WARNING', /*GERAL*/
        null, /*TO USER*/
        $request->nimdesassocia, /*CREATED BY*/
        'SYSTEM: ACCOUNT DISSOCIATION @' . Auth::user()->id, null);
        $user->save();
        return redirect()
            ->back();
    }

    /**
    * Retira TODOS os utilizadores de um grupo e de um subgrupo.
    * São movidos para o GERAL.
    *
    * @param string grupoID
    * @param string subgrupoID
    * @return redirect
    */
    public function retireUsersFromGroupAll($grupoID, $subgrupoID)
    {
        if ($subgrupoID == "0")
        {
            $childrenUsers = \App\Models\users_children::where('parentNIM', Auth::user()->id)
                ->where('accountVerified', 'Y')
                ->where('childGroup', $grupoID)->where('childSubGroup', null)
                ->get()
                ->all();
            $associatedUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('account_verified', 'Y')
                ->where('accountChildrenGroup', $grupoID)->where('accountChildrenSubGroup', null)
                ->get()
                ->all();
        }
        else if ($subgrupoID == "ALL")
        {
            $childrenUsers = \App\Models\users_children::where('parentNIM', Auth::user()->id)
                ->where('accountVerified', 'Y')
                ->where('childGroup', $grupoID)->get()
                ->all();
            $associatedUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('account_verified', 'Y')
                ->where('accountChildrenGroup', $grupoID)->get()
                ->all();
        }
        else
        {
            $childrenUsers = \App\Models\users_children::where('parentNIM', Auth::user()->id)
                ->where('accountVerified', 'Y')
                ->where('childGroup', $grupoID)->where('childSubGroup', $subgrupoID)->get()
                ->all();
            $associatedUsers = \App\Models\User::where('accountChildrenOf', Auth::user()->id)
                ->orWhere('account2ndChildrenOf', Auth::user()
                ->id)
                ->where('accountChildrenGroup', $grupoID)->where('accountChildrenSubGroup', $subgrupoID)->get()
                ->all();
        }
        foreach ($childrenUsers as $user0)
        {
            $user0->childGroup = null;
            $user0->childSubGroup = null;
            $user0->save();
        }
        foreach ($childrenUsers as $user1)
        {
            $user1->accountChildrenOf = null;
            $user0->accountChildrenGroup = null;
            $user1->updated_by = Auth::user()->id;
            $user1->save();
        }
        return redirect()
            ->back();
    }

    /**
    * A um utilizador sem conta local, é lhe criada uma conta, com toda a informação já existente.
    *
    * @param Request $request
    * @return redirect
    */
    public function convertUser(Request $request)
    {
        $id = $request->childrenID;
        $childrenUser = \App\Models\users_children::where('childID', $id)->first();
        $user = new \App\Models\User;
        $user->id = $childrenUser->childID;
        $user->name = $childrenUser->childNome;
        $user->email = $childrenUser->childEmail;
        $user->password = Hash::make($request->childrenID);
        $user->mustResetPassword = 'Y';
        $user->posto = $childrenUser->childPosto;
        $user->unidade = $childrenUser->childUnidade;
        $user->account_verified = 'Y';
        $user->localRefPref = $childrenUser->localRefPref;
        $user->isAccountChildren = 'Y';
        $user->accountChildrenOf = $childrenUser->parentNIM;
        $user->accountChildrenGroup = $childrenUser->childGroup;
        $user->accountChildrenSubGroup = $childrenUser->childSubGroup;
        $user->verified_at = Carbon::now()
            ->toDateTimeString();
        $user->verified_by = Auth::user()->id;
        $user->save();
        $childrenUser->delete();
        return redirect()
            ->route('viewUserProfile', $id);
    }
}
