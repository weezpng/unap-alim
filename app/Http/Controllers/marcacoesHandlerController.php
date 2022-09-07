<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

/**
 * Controlador principal de marcações de refeições.
 */
class marcacoesHandlerController extends Controller
{

    /**
   * Formata um NIM para conter os 8 carácteres necessários.
   *
   * @param string $NIM NIM a formatar
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
     * Ver página de marcações
     *
     * @return view
     */
    public function verMinhasMarcacoes()
    {
        $today = date("Y-m-d");
        $max_date = date('Y-m-d', strtotime($today. ' + 1 months'));       

        $marcaçoes = \App\Models\marcacaotable::where('NIM', Auth::user()->id)
            ->where('data_marcacao', '>=', $today)
            ->where('data_marcacao', '<=', $max_date)->orderBy('data_marcacao')
            ->orderBy('meal', 'DESC')
            ->get()
            ->all();

        $ementaTable = \App\Models\ementatable::orderBy('data')->get();
        $datasMarcadas = array();
        $marcacaoEmenta = array();
        foreach ($marcaçoes as $marcaçao)
        {
            $datasMarcadas[$marcaçao->id]   = $marcaçao->data_marcacao;
            $refeiçaoMarcada[$marcaçao->id] = $marcaçao->meal;
        }

        foreach ($datasMarcadas as $i => $dateToAdd)
        {
            $emt_tb = $ementaTable->where('data', '=', $dateToAdd)->first();
            (array)$marcacaoEmenta[$i]['meal'] = $refeiçaoMarcada[$i];

            if ($emt_tb != null) {
                $marcacaoEmenta[$i] = $ementaTable->where('data', '=', $dateToAdd)->first();
            } else {
                $marcacaoEmenta[$i]["sopa_almoço"] = "A definir";
                $marcacaoEmenta[$i]["prato_almoço"] = "A definir";
                $marcacaoEmenta[$i]["sobremesa_almoço"] = "A definir";
                $marcacaoEmenta[$i]["sopa_jantar"] = "A definir";
                $marcacaoEmenta[$i]["prato_jantar"] = "A definir";
                $marcacaoEmenta[$i]["sobremesa_jantar"] = "A definir";
            }            
        }
        
        $maxdate = ((new checkSettingsTable)->REMOVEMAX());
        $maxDateAdd = ((new checkSettingsTable)->ADDMAX());

        $locaisRef = \App\Models\locaisref::get()->all();
        $locaisAvailable = [];
        foreach ($locaisRef as $key => $local) {
          $locaisAvailable[$local->refName]['nome'] = $local->localName;
          $locaisAvailable[$local->refName]['ref'] = $local->refName;
          $locaisAvailable[$local->refName]['estado'] = $local->status;
        }

        return view('marcaçoes.minhasmaracacoes', ['marcaçoes' => $marcaçoes, 'ementa' => $marcacaoEmenta, 'maxDays' => $maxdate, 'marcarRefMax' => $maxDateAdd, 'locais' => $locaisAvailable]);
    }

    /**
     * Efectua uma marcação de refeição para Auth::user
     *
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        try
        {
            $NIM = $this::checkNIMLen(auth()->user()->id);
            $data_marcacao = $request->data;
            $meal = $request->ref;
            $marcaçao = new \App\Models\marcacaotable;
            $marcaçao->NIM = $NIM;
            $marcaçao->data_marcacao = $data_marcacao;
            $marcaçao->meal = $meal;
            $marcaçao->local_ref = $request->localDeRef;
            $marcaçao->unidade = auth()->user()->unidade;
            $marcaçao->created_by = $NIM;

            if (Auth::user()->posto=="ASS.TEC." || Auth::user()->posto=="ASS.OP." || Auth::user()->posto=="TEC.SUP." ||
            Auth::user()->posto == "ENC.OP." || Auth::user()->posto == "TIA" ||Auth::user()->posto == "TIG.1" || Auth::user()->posto == "TIE"){
                $marcaçao->civil = 'Y';
            } else {
                $marcaçao->civil = 'N';
            }

            $dieta = \App\Models\Dietas::where('NIM', $NIM)
                      ->where('data_inicio', '<', $request->data)->where('data_fim', '>', $request->data)
                      ->first();

            if ($dieta!=null) $marcaçao->dieta = "Y";
            else $marcaçao->dieta = "N";

            if(\App\Models\marcacaotable::where('NIM', $NIM)->where('data_marcacao', $data_marcacao)->where('meal', $meal)->where('local_ref', $request->localDeRef)->first()==null){
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
     * Altera o locao de refeição de uma marcação
     *
     * @param Request $request
     * @return json
     */
    public function change_ref(Request $request)
    {
        try
        {
            $marcaçao = \App\Models\marcacaotable::where('id', $request->id)->first();
            $marcaçao->local_ref = $request->to;
            $marcaçao->save();

            $local = \App\Models\locaisref::where('refName', $request->to)->value('localName');
            return response()->json($local, 200);
        }
        catch(\Exception $e)
        {
            return response()->json('error', 200);
        }
    }

   /**
     * Efectua uma marcação de refeição para um ChildrenUser.
     *
     * @param Request $request
     * @return json
     */
    public function store_children(Request $request)
    {
        try
        {
            $marcaçao = new \App\Models\marcacaotable;
            $marcaçao->NIM = $request->user;
            $marcaçao->data_marcacao = $request->data;
            $marcaçao->meal = $request->ref;
            $marcaçao->local_ref = $request->localDeRef;

            $dieta = \App\Models\Dieta::where('NIM', checkNIMLen($request->user))->first();
            if ($dieta) {
              if ($request->data > $dieta['data_inicio'] && $request->data < $dieta['data_fim'] ) {
                $marcaçoes->dieta = "Y";
              }
            }

            $marcaçao->save();
            return response()
                ->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json($e->getMessage(), 200);
        }
    }

    /**
     * Remove uma marcação de refeição
     *
     * @param Request $request
     * @return json
     */
    public function destroy(Request $request)
    {
        try
        {
            $marcaçao = \App\Models\marcacaotable::find($request->id);
            $marcaçao->delete();
            return response()->json('success', 200);
        } catch(\Exception $e) {
            return response()->json('error', 200);
        }
    }

    /**
     * Obter SUBGRUPOS em GRUPO para marcação em MASSA
     *
     * @param Request $request
     * @return array
     */
    public function getSubsInGroup(Request $request)
    {
        $subgroups = \App\Models\users_children_sub2groups::where('parentGroupID', $request->group)
            ->get();
        $output = [];
        foreach ($subgroups as $sub)
        {
            $output[$sub
                ->subgroupID] = $sub->subgroupName;
        }
        return $output;
    }

    /**
     * Obter informação de Marcações para cada utilizador sem conta associado
     *
     * @param string data
     * @param string refeição
     * @param array utilizadores
     * @return array
     */
    public function assoChildUsersMarcadaRef($data, $meal, $users)
    {
        $usersArray = [];
        foreach ($users as $key => $usr)
        {
            $marcaçao = \App\Models\marcacaotable::where('NIM', $key)->where('data_marcacao', $data)->where('meal', $meal)->first();
            $usersArray[$key]['id'] = $usr['id'];
            $usersArray[$key]['name'] = $usr['name'];
            $usersArray[$key]['posto'] = $usr['posto'];
            $usersArray[$key]['type'] = $usr['type'];
            $usersArray[$key]['localPref'] = $usr['localPref'];
            $usersArray[$key]['marcado'] = ($marcaçao == null) ? "0" : "1";
        }
        return $usersArray;
    }

    /**
     * Obter informação de grupo\subgrupo selecionado.
     * Obter ementa para dias onde a marcação é possivel.
     * Se não houver seleção de grupo ou subgrupo, devolver página para fazer essa seleção
     *
     * @return view
     */
    public function verMarcacoesSelGrupo(){
        $groups = \App\Models\users_children_subgroups::where('parentNIM', Auth::user()->id)
        ->orWhere('parent2nNIM', Auth::user())
        ->where('groupUnidade', Auth::user()->unidade)
        ->get()->all();

        return view('marcaçoes.marcacaomassa', [
          'groupRef' => null,
          'subRef' => null,
          'groupName' => null,
          'marcacoes' => null,
          'locais' => null,
          'localPref' => null,
          'groups' => $groups,
          'maxDays' => null,
          'marcarRefMax' => null,
          'users' => null
        ]);
    }

    /**
     * Ver todas as marcações em massa já feitas
     *
     * @param Request $request
     * @return view
     */
    public function verMarcacoesEmMassa(Request $request)
    {
          if (!$request->all()) {
            $groups = \App\Models\users_children_subgroups::where('parentNIM', Auth::user()->id)
            ->orWhere('parent2nNIM', Auth::user())
            ->where('groupUnidade', Auth::user()->unidade)
            ->get()->all();

            return view('marcaçoes.marcacaomassa', [
              'groupRef' => null,
              'subRef' => null,
              'groupName' => null,
              'marcacoes' => null,
              'locais' => null,
              'localPref' => null,
              'groups' => $groups,
              'maxDays' => null,
              'marcarRefMax' => null,
              'users' => null
            ]);
          }
          $sub = ($request->inputSubGroup == "GERAL") ? null : $request->inputSubGroup;
          if ($sub)
          {
              $allChildren = \App\Models\users_children::where('childGroup', $request->inputGroup)
                  ->where('childSubGroup', $request->inputSubGroup)
                  ->get();
              $allUsers = \App\Models\User::where('accountChildrenGroup', $request->inputGroup)
                  ->where('accountChildrenSubGroup', $request->inputSubGroup)
                  ->where('isTagOblig', null)
                  ->get();
          }
          else
          {
              $allChildren = \App\Models\users_children::where('childGroup', $request->inputGroup)
                  ->where('childSubGroup', null)
                  ->get();
              $allUsers = \App\Models\User::where('accountChildrenGroup', $request->inputGroup)
                  ->where('accountChildrenSubGroup', null)
                  ->get();
          }

          $today = date("Y-m-d");
          $ementaTable = \App\Models\ementatable::orderBy('data')->where('data', '>', $today)->get()
              ->all();
          $todosUtilizadores = [];
          foreach ($allChildren as $key => $users)
          {
              $todosUtilizadores[$users->childID]['type'] = "CHILDREN";
              $todosUtilizadores[$users->childID]['id'] = $users->childID;
              $todosUtilizadores[$users->childID]['name'] = $users->childNome;
              $todosUtilizadores[$users->childID]['posto'] = $users->childPosto;
              $todosUtilizadores[$users->childID]['localPref'] = $users->localRefPref;
          }
          foreach ($allUsers as $key => $users)
          {
              $todosUtilizadores[$users->id]['type'] = "UTILIZADOR";
              $todosUtilizadores[$users->id]['id'] = $users->id;
              $todosUtilizadores[$users->id]['name'] = $users->name;
              $todosUtilizadores[$users->id]['posto'] = $users->posto;
              $todosUtilizadores[$users->id]['localPref'] = $users->localRefPref;
          }
          $ementaPopulated = [];
          $index = 0;
          foreach ($ementaTable as $key => $entryEmenta)
          {
              $counter = 0;
              $ementaPopulated[$index]['id'] = $entryEmenta->id;
              $ementaPopulated[$index]['data'] = $entryEmenta->data;
              for ($i = 0;$i < 3;$i++)
              {
                  $counterRef = 0;
                  if ($i == 0)
                  {
                      $ementaPopulated[$index]['refs']['1REF']['id'] = $entryEmenta->id;
                      $ementaPopulated[$index]['refs']['1REF']['USERS'] = $this::assoChildUsersMarcadaRef($entryEmenta->data, "1REF", $todosUtilizadores);
                      foreach ($ementaPopulated[$index]['refs']['1REF']['USERS'] as $usr)
                      {
                          if ($usr['marcado'] === "1")
                          {
                              $counterRef = $counterRef + 1;
                          }
                      }
                      $ementaPopulated[$index]['refs']['1REF']['count'] = $counterRef;
                      $counter = ($counter + $counterRef);
                      $counterRef = 0;
                  }
                  else if ($i == 1)
                  {
                      $ementaPopulated[$index]['refs']['2REF']['id'] = $entryEmenta->id;
                      $ementaPopulated[$index]['refs']['2REF']['sopa'] = $entryEmenta->sopa_almoço;
                      $ementaPopulated[$index]['refs']['2REF']['prato'] = $entryEmenta->prato_almoço;
                      $ementaPopulated[$index]['refs']['2REF']['sobremesa'] = $entryEmenta->sobremesa_almoço;
                      $ementaPopulated[$index]['refs']['2REF']['USERS'] = $this::assoChildUsersMarcadaRef($entryEmenta->data, "2REF", $todosUtilizadores);
                      foreach ($ementaPopulated[$index]['refs']['2REF']['USERS'] as $usr)
                      {
                          if ($usr['marcado'] === "1")
                          {
                              $counterRef = $counterRef + 1;
                          }
                      }
                      $counter = ($counter + $counterRef);
                      $ementaPopulated[$index]['refs']['2REF']['count'] = $counterRef;
                      $counterRef = 0;
                  }
                  else if ($i == 2)
                  {
                      $ementaPopulated[$index]['refs']['3REF']['id'] = $entryEmenta->id;
                      $ementaPopulated[$index]['refs']['3REF']['sopa'] = $entryEmenta->sopa_jantar;
                      $ementaPopulated[$index]['refs']['3REF']['prato'] = $entryEmenta->prato_jantar;
                      $ementaPopulated[$index]['refs']['3REF']['sobremesa'] = $entryEmenta->sobremesa_jantar;
                      $ementaPopulated[$index]['refs']['3REF']['USERS'] = $this::assoChildUsersMarcadaRef($entryEmenta->data, "3REF", $todosUtilizadores);
                      foreach ($ementaPopulated[$index]['refs']['3REF']['USERS'] as $usr)
                      {
                          if ($usr['marcado'] === "1")
                          {
                              $counterRef = $counterRef + 1;
                          }
                      }
                      $ementaPopulated[$index]['refs']['3REF']['count'] = $counterRef;
                      $counter = ($counter + $counterRef);
                      $counterRef = 0;
                  }
              }
              $ementaPopulated[$index]['count'] = $counter;
              $counter = 0;
              $index++;
          }
          $maxdate = ((new checkSettingsTable)->REMOVEMAX());
          $maxDateAdd = ((new checkSettingsTable)->ADDMAX());
          $locaisRef = \App\Models\locaisref::get()->all();
          if ($sub == null)
          {
              $localPref = \App\Models\users_children_subgroups::where('groupID', $request->inputGroup)
                  ->first()
                  ->value('groupLocalPrefRef');
              $subName = "GERAL";
              $subRef = "0NULL";
          }
          else
          {
              $localPref = \App\Models\users_children_sub2groups::where('subgroupID', $sub)->first()
                  ->value('subgroupLocalPref');
              $subName = \App\Models\users_children_sub2groups::where('subgroupID', $sub)->first()
                  ->value('subgroupName');
              $subRef = $request->inputSubGroup;
          }
          $groupName = \App\Models\users_children_subgroups::where('groupID', $request->inputGroup)
              ->first()
              ->value('groupName');
          $name = $groupName . ' / ' . $subName;
          $groups = \App\Models\users_children_subgroups::where('parentNIM', Auth::user()->id)
              ->where('groupUnidade', Auth::user()
              ->unidade)
              ->get()
              ->all();
          return view('marcaçoes.marcacaomassa', ['groupRef' => $request->inputGroup, 'subRef' => $subRef, 'groupName' => $name, 'marcacoes' => $ementaPopulated, 'locais' => $locaisRef, 'localPref' => $localPref, 'groups' => $groups, 'maxDays' => $maxdate, 'marcarRefMax' => $maxDateAdd, 'users' => $todosUtilizadores]);
    }

    /**
     * Efectuar uma marcação em massa para um grupo inteiro.
     *
     * @param Request $request
     * @return json
     */
    public function marcarParaGrupo(Request $request)
    {
        try
        {
            $IDsToMarcar = $request->IDs;
            foreach ($IDsToMarcar as $key => $id)
            {
                $marcaçao = new \App\Models\marcacaotable;
                $marcaçao->NIM = $id;
                $marcaçao->data_marcacao = $request->dateForGroup;
                $marcaçao->meal = $request->mealForGroup;
                $marcaçao->local_ref = $request->localDeRef;

                $dieta = \App\Models\Dieta::where('NIM', checkNIMLen($id))->first();
                if ($dieta) {
                  if ($request->dateForGroup > $dieta['data_inicio'] && $request->dateForGroup < $dieta['data_fim'] ) {
                    $marcaçoes->dieta = "Y";
                  }
                }

                $marcaçao->save();
            }
            return response()
                ->json('success', 200);
        }
        catch(\Exception $e)
        {
            return response()->json('error', 200);
        }
    }

    /**
    * Ver pedidos de marcações quantitativas feitos por o grupo de permissões do utilizador
    *
    * @return view
    */
    public function marcacoesNotNominalIndex()
    {
        if (!(new ActiveDirectoryController)->MEALS_TO_EXTERNAL()) abort(403);

        $date_current = date("Y-m-d");
        if (Auth::user()->user_permission == 'TUDO') {
          $users = \App\Models\User::all();
        } else {
          $users = \App\Models\User::where('user_permission', Auth::user()->user_permission)->get()->all();
        }

        $meus_pedidos = array();
        $it = 0;

        foreach ($users as $key => $usr) {
          $pedido = \App\Models\pedidosueoref::where('registeredByNIM', $usr['id'])
              ->where('data_pedido', '>=', $date_current)
              ->orderBy('data_pedido', 'ASC')
              ->orderBy('meal', 'ASC')
              ->orderBy('local_ref')
              ->get()->all();
          if (empty($pedido)) continue;
          foreach ($pedido as $key => $pd) {
            $NIM = $pd['registeredByNIM'];
            if (str_contains($NIM, '@')) {
              $pieces = explode("@", $NIM);
              $NIM = $pieces[0];
            }
            while ((strlen((string)$NIM)) < 8) {
                $NIM = 0 . (string)$NIM;
            }

            $USER_REQ = \App\Models\User::where('id', $NIM)->get(['name', 'posto'])->first();
            if ($USER_REQ==null) {
              $name = "";
              $posto = "";
              $NIM = "";
            } else {
              $name = $USER_REQ['name'];
              $posto = $USER_REQ['posto'];
            }

            if ($pd['motive']=="DDN") {
              $motivo = "Dia de Defesa Nacional";
            } elseif ($pd['motive']=="PCS") {
              $motivo = "Provas de Classificação e Seleção";
            } elseif ($pd['motive']=="DILIGENCIA") {
              $motivo = "Diligência";
            } else {
              $motivo = $pd['motive'];
            }

            $meus_pedidos[$pd['data_pedido']][$pd['id']]['id']               = $pd['id'];
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['local_ref']        = $pd['local_ref'];
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['local_desc']       = \App\Models\locaisRef::where('refName', $pd['local_ref'])->value('localName');
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['data_pedido']      = $pd['data_pedido'];
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['meal']             = $pd['meal'];
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['motive']           = $motivo;
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['quantidade']       = $pd['quantidade'];
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['qty_reforços']     = $pd['qty_reforços'];
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['nim']              = $NIM;
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['posto']            = $posto;
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['name']             = $name;
            $meus_pedidos[$pd['data_pedido']][$pd['id']]['unidade']          = $pd['unidade'];
            $it++;
          }
        }

        $userperm = Auth::user()->user_permission;

        $perm_descriptor;

        switch ($userperm) {
          case 'ALIM':
            $perm_descriptor = "por o grupo  de Alimentação";
            break;
          case 'PESS':
            $perm_descriptor = "por o grupo de Pessoal";
            break;
          case 'LOG':
            $perm_descriptor = "por o grupo de Logística";
            break;
          case 'MESSES':
            $perm_descriptor = "por o grupo das Messes";
            break;
          case 'GCSEL':
            $perm_descriptor = "por o grupo do Gabinete de Classificação e Seleção";
            break;
          case 'CCS':
            $perm_descriptor = "por o grupo da Companhia de Comando e Serviços";
            break;
          case 'GENERAL':
            $perm_descriptor = "por o grupo geral";
            break;
          case 'TUDO':
            $perm_descriptor = "totais";
            break;
          default:
            $perm_descriptor = "por um grupo desconhecido (".$userperm.")";
            break;
        }

        $locaisRef  = \App\Models\locaisref::get()->all();
        $maxdate    = ((new checkSettingsTable)->REMOVEMAX());
        $maxDateAdd = ((new checkSettingsTable)->ADDMAX());
        $pedidos    = array();

        return view('marcaçoes.marcacoes_quant', [
            'meus_pedidos'  => $meus_pedidos,
            'locals'        => $locaisRef,
            'maxDays'       => $maxdate,
            'minDaysMarcar' => $maxDateAdd,
            'perm_desc'     => $perm_descriptor
        ]);
    }

    /**
    * Cria novo pedido de marcações quantitativas.
    *
    * @param Request $request
    * @return redirect
    */
    public function marcacoesNotNominal_add(Request $request)
    {
        #dd($request->all());
        if (!(new ActiveDirectoryController)->MEALS_TO_EXTERNAL()) abort(403);
        $dates = explode(" até ", $request->dateRangePicker);
        $date0 = date('Y-m-d', strtotime($dates[0]));
        $date1 = date('Y-m-d', strtotime($dates[1]));
        $date0 = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
        $date1 = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');
        $DeferenceInDays = \Carbon\Carbon::parse($date0)->diffInDays($date1);
        $meals = array();

        if ($request->check_pqalmoco)   $meals[0] = "1REF";
        if ($request->check_almoco)     $meals[1] = "2REF";
        if ($request->check_jantar)     $meals[2] = "3REF";

        $reforços = (isset($request->reforços)) ? $request->reforços : 0;
        $razao = ($request->reason=="OUTROS") ? $request->reason_2 : $request->reason;

        if ($DeferenceInDays > 0)
        {
            for ($i = 0;$i <= $DeferenceInDays;$i++)
            {
                foreach ($meals as $key => $meal)
                {
                    $novoPedido = new \App\Models\pedidosueoref;
                    $novoPedido->quantidade = $request->quantidade;
                    $novoPedido->local_ref = $request->local;
                    $novoPedido->data_pedido = date('Y-m-d', strtotime($date0 . ' + ' . ($i) . ' days'));
                    $novoPedido->meal = $meal;
                    $novoPedido->registeredByNIM = Auth::user()->id;
                    $novoPedido->motive = $razao;
                    $novoPedido->qty_reforços = $request->reforços;
                    $novoPedido->save();
                }
            }
        }
        else
        {
            foreach ($meals as $key => $meal)
            {
                $novoPedido = new \App\Models\pedidosueoref;
                $novoPedido->quantidade = $request->quantidade;
                $novoPedido->local_ref = $request->local;
                $novoPedido->data_pedido = date('Y-m-d', strtotime($date0));
                $novoPedido->meal = $meal;
                $novoPedido->registeredByNIM = Auth::user()->id;
                $novoPedido->motive = $razao;
                $novoPedido->qty_reforços = $request->reforços;
                $novoPedido->save();
            }
        }

        return redirect()->route('marcacao.non_nominal');
    }

    /**
    * Ver página inicial de conf. de marcações para faturação a Auth::user()
    *
    * @return view
    */
    public function conf_index()
    {
      $__isCivilian =( Auth::user()->posto == "ASS.TEC." || Auth::user()->posto != "ASS.OP." || Auth::user()->posto != "TEC.SUP."
      || auth()->user()->posto == "ENC.OP." || auth()->user()->posto == "TIA" || auth()->user()->posto == "TIG.1" || auth()->user()->posto == "TIE");

        if (!$__isCivilian)
        {
          if (Auth::user()->isTagOblig == null) {
            return view('messages.error', ['message' => 'Você não necessita confirmar refeiçoes.', 'url' => route('index') , ]);
          } else {
            $__token = Auth::user()->isTagOblig;
            $__from = \App\Models\users_tagged_conf::where('id', $__token)->value('data_inicio');
            $__until = \App\Models\users_tagged_conf::where('id', $__token)->value('data_fim');

            $marcaçao = \App\Models\marcacaotable::where('NIM', Auth::user()->id)
            ->where('data_marcacao', '>=', $__from)
            ->where('data_marcacao', '<=', $__until)
            ->orderBy('data_marcacao')
            ->get();
          }

        }

        if (!isset($__token)) {
          $marcaçao = \App\Models\marcacaotable::where('NIM', Auth::user()->id)
          ->where('data_marcacao', '>=', date('Y-m-d'))
          ->orderBy('data_marcacao')
          ->get();
        }

        $marcacoesSigned = array();
        $iteration = 0;
        foreach ($marcaçao as $key => $ref)
        {
            $confirmacaoRef = \App\Models\user_children_checked_meals::where('data', $ref['data_marcacao'])->where('ref', $ref['meal'])->where('user', Auth::user()
                ->id)
                ->where('check', 'Y')
                ->first();
            $marcacoesSigned[$iteration]['id'] = $ref['id'];
            $marcacoesSigned[$iteration]['data_marcacao'] = $ref['data_marcacao'];
            $marcacoesSigned[$iteration]['meal'] = $ref['meal'];
            if ($ref['meal'] != "1REF")
            {
                if ($ref['meal'] == "2REF")
                {
                    $marcacoesSigned[$iteration]['sopa'] = \App\Models\ementatable::where('data', $ref['data_marcacao'])->value('sopa_almoço');
                    $marcacoesSigned[$iteration]['prato'] = \App\Models\ementatable::where('data', $ref['data_marcacao'])->value('prato_almoço');
                    $marcacoesSigned[$iteration]['sobremesa'] = \App\Models\ementatable::where('data', $ref['data_marcacao'])->value('sobremesa_almoço');
                }
                else
                {
                    $marcacoesSigned[$iteration]['sopa'] = \App\Models\ementatable::where('data', $ref['data_marcacao'])->value('sopa_jantar');
                    $marcacoesSigned[$iteration]['prato'] = \App\Models\ementatable::where('data', $ref['data_marcacao'])->value('prato_jantar');
                    $marcacoesSigned[$iteration]['sobremesa'] = \App\Models\ementatable::where('data', $ref['data_marcacao'])->value('sobremesa_jantar');
                }
            }
            $marcacoesSigned[$iteration]['local_ref'] = $ref['local_ref'];
            $marcacoesSigned[$iteration]['confirmada'] = ($confirmacaoRef != null) ? 'Y' : 'N';
            $iteration++;
        }
        return view('marcaçoes.confirm', ['marcaçoes' => $marcacoesSigned]);
    }

    /**
    * Adicionar uma conf. de refeição para faturação
    *
    * @param Request $request
    * @return redirect
    */
    public function conf_post(Request $request)
    {
        $marcaçao = \App\Models\marcacaotable::where('NIM', Auth::user()->id)
            ->where('id', $request->id)
            ->first();
        $data = $marcaçao['data_marcacao'];
        $meal = $marcaçao['meal'];

        $confirmarRef = new \App\Models\user_children_checked_meals;
        $confirmarRef->data = $data;
        $confirmarRef->ref = $meal;
        $confirmarRef->check = 'Y';
        $confirmarRef->user = Auth::user()->id;
        $confirmarRef->save();

        return redirect()
            ->route('confirmacoes.index');

    }

    /**
    * Remover uma conf. de refeição para faturação
    *
    * @param Request $request
    * @return redirect
    */
    public function marcacoesNotNominal_destroy(Request $request)
    {
        if (!(new ActiveDirectoryController)->MEALS_TO_EXTERNAL()) abort(403);
        $pedido = \App\Models\pedidosueoref::where('registeredByNIM', Auth::user()->id)
            ->where('id', $request->id)
            ->first();
        $pedido->delete();
        return redirect()
            ->route('marcacao.non_nominal');
    }

    /**
    * Marca uma marcação de refeição para um hóspede
    *
    * @param Request $request
    * @return json
    */
    public function hospede_marcar(Request $request){
      try
      {
          $marcaçao = new \App\Models\marcacaotable;
          $marcaçao->NIM = $request->user;
          $marcaçao->data_marcacao = $request->data;
          $marcaçao->meal = $request->ref;
          $marcaçao->local_ref = $request->localDeRef;
          $marcaçao->save();
          return response()
              ->json('success', 200);
      }
      catch(\Exception $e)
      {
          return response()->json('error', 200);
      }
    }

    /**
    * Remove uma marcação de refeição para um hóspede
    *
    * @param Request $request
    * @return json
    */
    public function hospede_desmarcar(Request $request)
    {
        $marcaçao = \App\Models\marcacaotable::where('data_marcacao', $request->data)
            ->where('NIM', $request->user)
            ->where('meal', $request->ref);
        $marcaçao->delete();
        return redirect()->back();
    }
}
