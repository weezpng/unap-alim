<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Models\User;
use App\Models\unap_unidades;

/**
 * Lógica do sistema de marcações POC.
 */
class POC_Controller extends Controller
{
/**
   * Página de index do sistema de férias para POC's
   * 
   * @return view
   */
  public function poc_ferias_index(){

    return "Não implementado";

    if (Auth::user()->user_type!='POC') abort(403);
    $unidade_to_check = Auth::user()->unidade;
    
    if ($unidade_to_check=="UnAp/CmdPess" ||  $unidade_to_check=="UnAp/CmdPess/QSO") {
      $users = User::where('unidade', 'UnAp/CmdPess')->orWhere('unidade', 'UnAp/CmdPess/QSO');

    } elseif ($unidade_to_check=="MMBatalha" || $unidade_to_check=="MMAntas") {
      $users = User::where('unidade', 'MMBatalha')->orWhere('unidade', 'MMAntas');
    } else {
      $users = User::where('unidade', $unidade_to_check);
    }

    $users = $users->where('lock', 'N')->where('account_verified', 'Y')->orderBy('seccao')->orderBy('posto')->get()->all();

    $__todosUtilizadores = array();
    $it = 0;
    $today = date('Y-m-d');

    foreach ($users as $key => $_user) {
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

    return view('gestao.poc_ferias', [
      'users' => $__todosUtilizadores,
      'minDay' => $minDay,
    ]);
  }

  /**
   * Altera o local de refeição de uma marcação.
   * 
   * @param Request $Request
   * 
   * @return json
   */
    public function changeLocRef(Request $Request){
      try {
        if (!$post->ajax()) abort(500);
        $marcacao = \App\Models\marcacaotable::where('data_marcacao', $post->ChangeLc_data)
          ->where('NIM', $post->ChangeLc_uid)
          ->where('meal', '2REF')->first();
        if (!$marcacao) return response()->json('Não foi possivel encontrar esta marcação na base de dados.', 200);
        $marcacao->local_ref = $post->sltNewLcl;
        $marcacao->save();
        $response = 'success,'.$post->sltNewLcl;
        return response()->json($response, 200);
      } catch (\Exception $e) {
          return response()->json($e->getMessage(), 200);
      }
    }

    /**
     * Marca a 2º refeição para os utilizadores selecionados no Centro POC
     * 
     * @param Request $Request
     * 
     * @return json
     */
    public function marcar_2ref(Request $request){
      try {
          if (!$request->ajax()) abort(405);
          $_data = $request["data"][0]["value"];
          $_users = $request["data"];

          foreach ($_users as $key => $_array_entry) {
            if ($_array_entry['name']=="date") {
              unset($_users[$key]);
            }
          }

          foreach ($_users as $key => $_user_entry) {
            $_user_id = $_user_entry["value"];

            while ((strlen((string)$_user_id)) < 8) {
              $_user_id = 0 . (string)$_user_id;
            }

            $local = User::where('id', $_user_id)->first();
            $slug = $local['unidade'];
            $slug = \App\Models\unap_unidades::where('slug', $slug)->first();
            $local = $slug['local'];

          $refs_dinheiro_entries = \App\Models\users_tagged_conf::where('registered_to', $_user_id)
            ->where('data_inicio', '<=', $_data)
            ->where('data_fim', '>=', $_data)
            ->get()->all();

            $marcação_exists = \App\Models\marcacaotable::where('NIM', $_user_id)
              ->where('data_marcacao', $_data)
              ->where('local_ref', $local)
              ->where('meal', '2REF')
              ->get()->all();

            if (empty($marcação_exists) && empty($refs_dinheiro_entries)) {
              $marcaçoes = new \App\Models\marcacaotable;
              $marcaçoes->NIM = $_user_id;
              $marcaçoes->data_marcacao = $_data;
              $marcaçoes->local_ref = $local;
              $marcaçoes->meal = "2REF";
              $marcaçoes->unidade = Auth::user()->unidade;
              $marcaçoes->created_by = "POC@" . Auth::user()->id;

              $dieta = \App\Models\Dietas::where('NIM', $_user_id)->first();
              if ($dieta) {
                if ($_data > $dieta['data_inicio'] && $_data < $dieta['data_fim'] ) {
                  $marcaçoes->dieta = "Y";
                }
              }

              $marcaçoes->save();
            } else {
              continue;
            }
          }

          return response()->json('success', 200);
      } catch (\Exception $e) {
            return response()->json("error", 200);
      }
    }

  /**
   * Remove uma marcação a partir do centro POC
   * 
   * @param Request $Request
   * 
   * @return json
   */
    public function remove_2ref(Request $request){
      try {
        if (!$request->ajax()) abort(500);

        $marcacao = \App\Models\marcacaotable::where('data_marcacao', $request->data)
          ->where('NIM', $request->user)
          ->where('meal', '2REF')->first();

        if (!$marcacao){
          $NIM = $request->user;
          while ((strlen((string)$NIM)) < 8) {
            $NIM = 0 . (string)$NIM;
          }
          $marcacao = \App\Models\marcacaotable::where('data_marcacao', $request->data)
            ->where('NIM', $NIM)
            ->where('meal', '2REF')->first();
        }

        if (!$marcacao) return response()->json('Não foi possivel encontrar esta marcação na base de dados.', 200);
        $marcacao->delete();
        return response()->json('success', 200);
      } catch (\Exception $e) {
          return response()->json($e->getMessage(), 200);
      }
    }

    
    /**
     * @ignore
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
    * POC encarregue de marcar 2REFS
    *
    * Obter informação de utilizadores na mesma unidade que POC:Auth::user()
    * Redirect pagina com informação
    */


    /** 
   * Mostra a página de marcações Centro POC. 
   * 
   * 
   * @return view
   */
    public function control_center_index(){

      if (Auth::user()->user_type!='POC') abort(403);
      $unidade_to_check = Auth::user()->unidade;
      
      if ($unidade_to_check=="UnAp/CmdPess" ||  $unidade_to_check=="UnAp/CmdPess/QSO") {
        $users = User::where('unidade', 'UnAp/CmdPess')->orWhere('unidade', 'UnAp/CmdPess/QSO');

      } elseif ($unidade_to_check=="MMBatalha" || $unidade_to_check=="MMAntas") {
        $users = User::where('unidade', 'MMBatalha')->orWhere('unidade', 'MMAntas');
      } else {
        $users = User::where('unidade', $unidade_to_check);
      }

      $users = $users->where('lock', 'N')->where('account_verified', 'Y')->orderBy('seccao')->orderBy('posto')->get()->all();

      $today = date('Y-m-d');
      $max_date = date('Y-m-d',strtotime($today. ' + 1 months'));
      $dates = $this->dateRange($today, $max_date);

      $ementa = \App\Models\ementaTable::where('data', '>', $today)->where('data', '<', $max_date)->get()->all();
      $ementa_formatted = array();
      $it = 0;
      $it_sec = 0;

      foreach ($dates as $key => $date) {

        $weekday_number = date('N',  strtotime($date));
        if ($weekday_number==6 || $weekday_number==7) continue;
        
        $ementa = \App\Models\ementatable::where('data', $date)->first();      
        $ementa_formatted[$it]['data'] = $date ;

        if($ementa){
          $ementa_formatted[$it]['id'] = $ementa['id'] ;
          $ementa_formatted[$it]['sopa_almoço'] = $ementa['sopa_almoço'];
          $ementa_formatted[$it]['prato_almoço'] = $ementa['prato_almoço'];
          $ementa_formatted[$it]['sobremesa_almoço'] = $ementa['sobremesa_almoço'];
        } else {
          $ementa_formatted[$it]['id'] = "NOTPUBLISHED".rand(1, 200);
          $ementa_formatted[$it]['sopa_almoço'] = 'Não publicado';
          $ementa_formatted[$it]['prato_almoço'] = 'Não publicado';
          $ementa_formatted[$it]['sobremesa_almoço'] = 'Não publicado';
        }

        foreach ($users as $key => $usr) {

          $NIM = $usr['id'];
          while ((strlen((string)$NIM)) < 8) {
            $NIM = 0 . (string)$NIM;
          }

          $ausente_entries = \App\Models\Ferias::where('to_user', $NIM)
            ->where('data_inicio', '<=', $date)->where('data_fim', '>', $date)
            ->get()->all();

          if (empty($ausente_entries)) {

            $refs_dinheiro_entries = \App\Models\users_tagged_conf::where('registered_to', $NIM)
              ->where('data_inicio', '<=', $date)
              ->where('data_fim', '>=', $date)
              ->get()->all();

              $account_not_locked = $usr['lock']=="N";

              if ($usr['posto'] != "ASS.TEC."
              && $usr['posto'] != "ASS.OP."
              && $usr['posto'] != "TEC.SUP."
              && $usr['posto'] != "'ENC.OP."
              && $usr['posto'] != 'TIA'
              && $usr['posto'] != "TIG.1"
              && $usr['posto'] != "TIE"){
                $account_not_not_civil = true;
              } else {
                $account_not_not_civil = false;
              }

              if(!empty($refs_dinheiro_entries)) continue;
              if (empty($refs_dinheiro_entries) && ($account_not_locked && $account_not_not_civil)) {

                $_is_marcado = \App\Models\marcacaotable::where('data_marcacao', $date)
                  ->where('meal', '2REF')->where('NIM', $NIM)->first();

                if (!$_is_marcado) {
                  $ementa_formatted[$it]['users_available'][$it_sec]['id'] = $NIM;
                  $ementa_formatted[$it]['users_available'][$it_sec]['name'] = $usr['name'];
                  $ementa_formatted[$it]['users_available'][$it_sec]['descriptor'] = $usr['descriptor'];
                  $ementa_formatted[$it]['users_available'][$it_sec]['seccao'] = $usr['seccao'];
                  $ementa_formatted[$it]['users_available'][$it_sec]['posto'] = $usr['posto'];
                  $ementa_formatted[$it]['users_available'][$it_sec]['unidade'] = $usr['unidade'];
                  $ementa_formatted[$it]['users_available'][$it_sec]['localRefPref'] = $usr['localRefPref'];
                  $it_sec++;
                } else {
                  $ementa_formatted[$it]['users_tagged'][$it_sec]['id'] = $NIM;
                  $ementa_formatted[$it]['users_tagged'][$it_sec]['name'] = $usr['name'];
                  $ementa_formatted[$it]['users_tagged'][$it_sec]['descriptor'] = $usr['descriptor'];
                  $ementa_formatted[$it]['users_tagged'][$it_sec]['seccao'] = $usr['seccao'];
                  $ementa_formatted[$it]['users_tagged'][$it_sec]['posto'] = $usr['posto'];
                  $ementa_formatted[$it]['users_tagged'][$it_sec]['unidade'] = $usr['unidade'];
                  $ementa_formatted[$it]['users_tagged'][$it_sec]['localRefPref'] = $usr['localRefPref'];
                  $ementa_formatted[$it]['users_tagged'][$it_sec]['localRefActual'] = $_is_marcado['local_ref'];
                  $it_sec++;
                }
              }
          }
        }

        $ementa_formatted[$it]['users_total_count'] = count($users);
        $ementa_formatted[$it]['users_available_count'] = (isset($ementa_formatted[$it]['users_available'])) ? count($ementa_formatted[$it]['users_available']) : 0 ;
        $ementa_formatted[$it]['users_marcados_count'] = (isset($ementa_formatted[$it]['users_tagged'])) ? count($ementa_formatted[$it]['users_tagged']) : 0 ;
        $it++;
        $it_sec = 0;

      }

      $locaisRef = \App\Models\locaisref::get()->all();
      $locaisAvailable = [];
      foreach ($locaisRef as $key => $local) {
        $locaisAvailable[$local->refName]['nome'] = $local->localName;
        $locaisAvailable[$local->refName]['ref'] = $local->refName;
        $locaisAvailable[$local->refName]['estado'] = $local->status;
      }

      return view('POC.control-center', [
        'entries' => $ementa_formatted,
        'locais' => $locaisAvailable,
        'ADD_MAX' => ((new checkSettingsTable)->ADDMAX()),
        'REMOVE_MAX' => ((new checkSettingsTable)->REMOVEMAX()),
      ]);
    }
}
