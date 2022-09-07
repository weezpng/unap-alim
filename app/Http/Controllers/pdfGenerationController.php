<?php
namespace App\Http\Controllers;

use PDF;
use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\marcacaotable;
use App\Models\users_children;
use App\Models\users_children_subgroups;
use App\Models\user_children_checked_meals;
use \Carbon\Carbon;

/**
 * Lógica de criação de relatórios em formato PDF.
 */
class pdfGenerationController extends Controller
{
    /**
   * @ignore
   */
    public function viewUsersNotConf(Request $post){
        $date = date('Y-m-d');
        $dateNex = date("Y-m-d", strtotime($date . "+ 15 days"));
    }

    /**
     * Gera um array com todas as dentes entre duas datas
     *
     * @param string $first - Primeiro dia
     * @param string $last - Ultimo dia
     *
     * @return array
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
   * @ignore
   */
    public function DeletedTagsByUnit(){

        $today = date('Y-m-d');
        $today =date('Y-m-d');
        $datePrev = date("Y-m-d", strtotime($today. "- ".((new checkSettingsTable)->ADDMAX() - 5)." days"));
        $dateNex = date("Y-m-d", strtotime($today. "+ ".((new checkSettingsTable)->REMOVEMAX() - 1)." days"));

        $unidades = \App\Models\unap_unidades::get()->all();

        $trashed_ini = marcacaotable::onlyTrashed();

        $all_trashed = array();
        $iter = 0;

        $date_range = $this->dateRange($datePrev, $dateNex);

        foreach ($unidades as $key => $unit) {
            $all_trashed[$iter]['unit_name'] = $unit['name'];
            $all_trashed[$iter]['unit_slug'] = $unit['slug'];
            $all_trashed[$iter]['local'] = $unit['local'];

            $all_trashed[$iter]['tf_start'] = $datePrev;
            $all_trashed[$iter]['tf_end'] = $dateNex;
            $all_trashed[$iter]['total_tf'] = marcacaotable::onlyTrashed()
                                ->where('data_marcacao', '<=', $dateNex)
                                ->where('data_marcacao', '>=', $datePrev)
                                ->where('unidade', $unit['slug'])->count();

            foreach ($date_range as $index_date => $date) {
                $total_day_total = marcacaotable::onlyTrashed()->where('data_marcacao', $date)->where('unidade', $unit['slug'])->count();
                $all_trashed[$iter]['dates'][$date]['total'] = $total_day_total;
                $total_day_mil = marcacaotable::onlyTrashed()->where('data_marcacao', $date)->where('unidade', $unit['slug'])->where('civil', 'N')->count();
                $total_day_civ = marcacaotable::onlyTrashed()->where('data_marcacao', $date)->where('unidade', $unit['slug'])->where('civil', 'Y')->count();
                $all_trashed[$iter]['dates'][$date]['mil'] = $total_day_mil;
                $all_trashed[$iter]['dates'][$date]['civ'] = $total_day_civ;
            }

            $iter++;
        }

        dd($all_trashed);



    }

    /**
     * Gera relatório de APENAS marcações eliminadas
     *
     * @param Request $post
     *
     * @return PDF
     */
    public function DeletedTags(Request $post){

        $today =date('Y-m-d');
        $datePrev = date("Y-m-d", strtotime($today. "- ".((new checkSettingsTable)->ADDMAX())." days"));
        $dateNex = date("Y-m-d", strtotime($today. "+ ".((new checkSettingsTable)->REMOVEMAX() - 1)." days"));
        $count_trashed = marcacaotable::onlyTrashed()->where('data_marcacao', '<=', $dateNex)->where('data_marcacao', '>=', $datePrev)->count();
        $trashed_tags = marcacaotable::onlyTrashed()->where('data_marcacao', '<=', $dateNex)->where('data_marcacao', '>=', $datePrev)->get();

        $all_trashed = array();
        $it = 0;
        foreach ($trashed_tags as $key => $trashed) {
            $all_trashed[$it]['tag_date'] = Carbon::parse($trashed['data_marcacao'])->format('d/m/Y');

            if($trashed['meal']=="1REF") $all_trashed[$it]['tag_meal'] = "Pequeno-almoço";
            elseif($trashed['meal']=="2REF") $all_trashed[$it]['tag_meal'] = "Almoço";
            elseif($trashed['meal']=="3REF") $all_trashed[$it]['tag_meal'] = "Jantar";

            $all_trashed[$it]['tag_local'] = \App\Models\locaisref::where('refName', $trashed['local_ref'])->value('localName');
            $all_trashed[$it]['tag_type'] = ($trashed['dieta']=='Y') ? "Dieta" : "Normal";
            $all_trashed[$it]['tagged_at'] = Carbon::parse($trashed['created_at'])->format('d/m/Y H:i');
            $all_trashed[$it]['trashed_at'] = Carbon::parse($trashed['deleted_at'])->format('d/m/Y H:i');

            $created_by = $trashed['created_by'];

            if($created_by==null){
                $all_trashed[$it]['tag_by'] ="Próprio";
            } elseif(str_contains($created_by, "POC")) {
                $pieces = explode("POC@", $created_by);
                $id = $pieces[1];
                while ((strlen((string)$id)) < 8) {
                    $id = 0 . (string)$id;
                }
                $user = User::where('id', $id)->first();

                $all_trashed[$it]['tag_by']['id'] = $id;
                $all_trashed[$it]['tag_by']['posto'] = $user['posto'];
                $all_trashed[$it]['tag_by']['name'] = $user['name'];
            } else {
                $all_trashed[$it]['tag_by'] = $created_by;
            }

            $id = $trashed['NIM'];
            while ((strlen((string)$id)) < 8) {
              $id = 0 . (string)$id;
            }

            $user = User::where('id', $id)->first();
            $all_trashed[$it]['user']['id'] = $id;
            $all_trashed[$it]['user']['posto'] = $user['posto'];
            $all_trashed[$it]['user']['name'] = $user['name'];
            $all_trashed[$it]['user']['colocacao'] = ($user['seccao'] != null) ? $user['seccao'].'/'.$user['unidade'] : $user['unidade'];

            $it++;
        }

        $token = \Str::random(10);

        $me_id = Auth::user()->id;
        while ((strlen((string)$me_id)) < 8) {
            $me_id = 0 . (string)$me_id;
        }

        $generated['id'] = $me_id;
        $generated['nome'] = Auth::user()->name;
        $generated['posto'] = Auth::user()->posto;
        $generated['email'] = Auth::user()->email;
        $generated['at_date'] = date('d/m/Y');
        $generated['at_hour'] = date('H:i:s');
        $generated['token'] = $token;

        $html = view('PdfGenerate.general-tags.trashed-tags', [
            'generated' => $generated,
            'trashed' => $all_trashed,
            'time_start' => Carbon::parse($datePrev)->format('d/m/Y'),
            'time_end' => Carbon::parse($dateNex)->format('d/m/Y'),
            'count' => $count_trashed,
        ]);

        // return $html;

        return PDF::loadHTML($html)->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->download();

    }


    /**
     * Gera relatório de utilizadores CIVIS que não confirmaram refeições mas que teem marcações
     *
     * @param Request $post
     *
     * @return PDF
     */
    public function viewUsersMarcouNotConfirmed(Request $request)
    {

        if (!((new ActiveDirectoryController)->GET_CIVILIANS_REPORT())) abort(403);
        $date = date('Y-m-d');
        $dateNex = date("Y-m-d", strtotime($date . "+ 15 days"));

        # 'ASS.TEC.','ASS.OP.','TEC.SUP.','ENC.OP.','TIA','TIG.1','TIE'

        if ((new ActiveDirectoryController)->GET_STATS_OTHER_UNITS())
        {
            $allCivilianUsers = User::where('posto', 'ASS.TEC.')->orWhere('posto', 'ASS.OP.')
                ->orWhere('posto', 'TEC.SUP.')->orWhere('posto', 'ENC.OP.')
                ->orWhere('posto', 'TIA')->orWhere('posto', 'TIG.1')->orWhere('posto', 'TIE')
                ->get();
            $allCivilianChildrenUsers = users_children::where('childPosto', 'ASS.TEC.')->orWhere('childPosto', 'ASS.OP.')
                ->orWhere('childPosto', 'TEC.SUP.')->orWhere('childPosto', 'ENC.OP.')
                ->orWhere('childPosto', 'TIA')->orWhere('childPosto', 'TIG.1')->orWhere('childPosto', 'TIE')
                ->get();
        }
        else
        {
            $allCivilianUsers = User::where('posto', 'ASS.TEC.')->orWhere('posto', 'ASS.OP.')
                ->orWhere('posto', 'TEC.SUP.')->orWhere('posto', 'ENC.OP.')
                ->orWhere('posto', 'TIA')->orWhere('posto', 'TIG.1')->orWhere('posto', 'TIE')
                ->where('unidade', Auth::user()->unidade)
                ->get();
            $allCivilianChildrenUsers = users_children::where('childPosto', 'ASS.TEC.')->orWhere('childPosto', 'ASS.OP.')
                ->orWhere('childPosto', 'TEC.SUP.')->orWhere('childPosto', 'ENC.OP.')
                ->orWhere('childPosto', 'TIA')->orWhere('childPosto', 'TIG.1')->orWhere('childPosto', 'TIE')
                ->where('childUnidade', Auth::user()->unidade)
                ->get();
        }

        $todosCivis = array();

        foreach ($allCivilianUsers as $user)
        {
            $nim_key = $user->id;
            $todosCivis[$nim_key]['id'] = $user->id;
            $todosCivis[$nim_key]['nome'] = $user->name;
            $todosCivis[$nim_key]['posto'] = $user->posto;
            $todosCivis[$nim_key]['unidade'] = $user->unidade;
            $todosCivis[$nim_key]['parentNIM'] = $user->accountChildrenOf;
            $todosCivis[$nim_key]['email'] = $user->email;
            $todosCivis[$nim_key]['localRefPref'] = $user->localRefPref;

        }

        foreach ($allCivilianChildrenUsers as $user)
        {
            $nim_key = $user->childID;
            $todosCivis[$nim_key]['id'] = $user->childID;
            $todosCivis[$nim_key]['nome'] = $user->childNome;
            $todosCivis[$nim_key]['posto'] = $user->childPosto;
            $todosCivis[$nim_key]['unidade'] = $user->childUnidade;
            $todosCivis[$nim_key]['parentNIM'] = $user->parentNIM;
            $todosCivis[$nim_key]['email'] = $user->childEmail;
            $todosCivis[$nim_key]['localRefPref'] = $user->localRefPref;
        }

        foreach ($todosCivis as $civil)
        {


            $marcaçoes = marcacaotable::orderBy('data_marcacao')->where('NIM', $civil['id'])->where('data_marcacao', '>=', $date)->orderBy('meal')
                ->get();
            $countTags = 0;
            $iteration = 0;

            foreach ($marcaçoes as $ref)
            {
                $iteration++;
                $nim_key = $ref['NIM'];
                $thisMealTag = \App\Models\user_children_checked_meals::where('user', $nim_key)->where('data', $ref['data_marcacao'])->where('ref', $ref['meal'])->where('check', 'Y')->first();

                $todosCivis[$nim_key]['marcacoes'][$iteration]['data'] = $ref['data_marcacao'];
                $todosCivis[$nim_key]['marcacoes'][$iteration]['meal'] = $ref['meal'];
                $todosCivis[$nim_key]['marcacoes'][$iteration]['confirmation'] = (empty($thisMealTag)) ? 0 : 1;
                if (!empty($thisMealTag)) $countTags++;
                $todosCivis[$nim_key]['marcacoes']['total_marcacoes'] = $iteration;
                $todosCivis[$nim_key]['marcacoes']['total_tags'] = $countTags;
            }
            if (array_key_exists("marcacoes", $todosCivis[$civil['id']]))
            {
                if (array_key_exists("total_marcacoes", $todosCivis[$civil['id']]['marcacoes']))
                {
                    $countMarcacoes = $todosCivis[$civil['id']]['marcacoes']['total_marcacoes'];
                    $countTagged = $todosCivis[$civil['id']]['marcacoes']['total_tags'];
                    if ($countMarcacoes >= 0)
                    {

                        if ($countTagged > 0)
                        {
                            unset($todosCivis[$civil['id']]);
                        }
                    }
                    else
                    {
                        unset($countTagged[$civil['id']]);
                    }
                }
            }
            else
            {
                unset($todosCivis[$civil['id']]);
            }
        }
        $html = view('PdfGenerate.civilian_marc_noconf.civilian-start');
        $is_first_run = true;
        foreach ($todosCivis as $nim_key => $civil)
        {
            $id = Auth::user()->id;
            while ((strlen((string)$id)) < 8) {
                $id = 0 . (string)$id;
            }
            $token = \Str::random(10);
            $generated['id'] = $id;
            $generated['nome'] = Auth::user()->name;
            $generated['posto'] = Auth::user()->posto;
            $generated['email'] = Auth::user()->email;
            $generated['at_date'] = date('d/m/Y');
            $generated['at_hour'] = date('H:i:s');
            $generated['token'] = $token;
            $page = 1;
            if ($is_first_run == true)
            {
                $html .= view('PdfGenerate.civilian_marc_noconf.civilian-reportinfo', ['generated' => $generated, ]);
            }
            else
            {
                $html .= view('PdfGenerate.civilian_marc_noconf.civilian-reportinfo-new-user', ['generated' => $generated, ]);
            }

            if ($civil['localRefPref'] == null)
            {
                $local_pref = "NENHUM";
            }
            else
            {
                $local_pref = $civil['localRefPref'];
            }

            $civil_parent = $civil['parentNIM'] . " " . strtoupper(User::where('id', $civil['parentNIM'])->value('posto')) . " " . strtoupper(User::where('id', $civil['parentNIM'])->value('name'));

            $html .= view('PdfGenerate.civilian_marc_noconf.civilian-main', ['user_id' => $nim_key, 'user_name' => strtoupper($civil['nome']) , 'user_posto' => $civil['posto'], 'user_email' => $civil['email'], 'user_localRefPref' => $local_pref, 'user_parent_id' => $civil_parent, 'marcaçoes' => $civil['marcacoes'], 'total_marcacoes' => $civil['marcacoes']['total_marcacoes'], 'total_tags' => $civil['marcacoes']['total_tags'], 'timeperiod' => null]);
            $is_first_run = false;
        }
        PDF::setOptions(['dpi' => 600, 'defaultFont' => 'sans-serif', 'debugCss' => true, 'defaultMediaType' => "print", 'isHtml5ParserEnabled' => true]);
        if (isset($generated))
        {
            $filename = 'RELATÓRIO (UTILIZADORES SEM CONFIRMAÇÕES) (' . $generated['at_date'] . ').pdf';
            return PDF::loadHTML($html)->setPaper('a4', 'portrait')
                ->download($filename);
        }
        else
        {
            return view('messages.error', ['message' => 'Não há nenhum caso de utilizadores com marcações sem confirmações.', 'url' => route('gestão.statsAdmin') ]);
        }

    }

    /**
     * Gera relatório de marcações com informação NOMINAL
     *
     * @param Request $post
     *
     * @return PDF
     */
    public function generateNominalListing(Request $request)
    {
        $local = $request->local;
        $date = date('Y-m-d');
        $maxdate = ((new checkSettingsTable)->REMOVEMAX() - 1);
        $dateNex = date("Y-m-d", strtotime($date . "+ ".$maxdate." days"));
        if ($local == "GERAL")
        {
            $marcaçoes = marcacaotable::orderBy('data_marcacao')->where('data_marcacao', '>', $date)->where('data_marcacao', '<', $dateNex)->orderBy('meal')
                ->orderBy('local_ref')
                ->get()
                ->all();
        }
        else
        {
            $marcaçoes = marcacaotable::where('local_ref', $local)->where('data_marcacao', '>', $date)->where('data_marcacao', '<', $dateNex)->orderBy('data_marcacao')
                ->orderBy('meal')
                ->get()
                ->all();
        }
        foreach ($marcaçoes as $key => $marcacao)
        {
            $new_key = strtotime($marcacao->data_marcacao);
            $key_local = $marcacao->local_ref;
            $key_ref = $marcacao->meal;
            $id = $marcacao->NIM;
            while ((strlen((string)$id)) < 8) {
                $id = 0 . (string)$id;
            }
            $user = User::where('id', $id)->first();
            if (!$user)
            {
                $user = users_children::where('childID', $id)->first();
                if ($user)
                {
                    $user_id = $id;
                    $user_name = $user->childNome;
                    $user_posto = $user->childPosto;
                    $user_unit = $user->childUnidadechildUnidade;
                }
                else
                {
                    $user_id = "";
                    $user_name = "NÃO ENCONTRADO";
                    $user_posto = "";
                    $user_unit = "";
                }
            }
            else
            {
                $user_id = $id;
                $user_name = $user->name;
                $user_posto = $user->posto;
                $user_unit = $user->unidade;
            }
            $marcacoesFormatted[$new_key]['data'] = $marcacao->data_marcacao;
            $marcacoesFormatted[$new_key][$key_local]['LOCAL'] = $key_local;
            $marcacoesFormatted[$new_key][$key_local][$key_ref]['REF'] = $key_ref;
            $marcacoesFormatted[$new_key][$key_local][$key_ref][$key]['USER_ID'] = $user_id;
            $marcacoesFormatted[$new_key][$key_local][$key_ref][$key]['USER_NAME'] = $user_name;
            $marcacoesFormatted[$new_key][$key_local][$key_ref][$key]['USER_POSTO'] = $user_posto;
            $marcacoesFormatted[$new_key][$key_local][$key_ref][$key]['USER_UNIT'] = $user_unit;
        }
        $token = \Str::random(10);



        $me_id = Auth::user()->id;
        while ((strlen((string)$me_id)) < 8) {
            $me_id = 0 . (string)$me_id;
        }

        $generated['id'] = $me_id;
        $generated['nome'] = Auth::user()->name;
        $generated['posto'] = Auth::user()->posto;
        $generated['email'] = Auth::user()->email;
        $generated['at_date'] = date('d/m/Y');
        $generated['at_hour'] = date('H:i:s');
        $generated['token'] = $token;
        $html = null;
        $page = 1;
        $first_run = true;
        $last_date = null;
        $last_local = null;
        $last_ref = null;
        $count = 0;
        $subcount = 1;
        $firstKey = array_key_first($marcacoesFormatted);
        $local = key(array_slice($marcacoesFormatted[$firstKey], 1, 2));
        $ref = key(array_slice($marcacoesFormatted[$firstKey][$local], 1, 2));
        $pedidosAdicionais = array();
        $html0 = view('PdfGenerate.nominal.nominal-full', ['generated' => $generated, 'marcs' => $marcacoesFormatted, 'page' => $page, 'date' => date("d/m/Y", strtotime($marcacoesFormatted[$firstKey]['data'])) , 'ref' => $ref, 'local' => $local, ]);
        foreach ($marcacoesFormatted as $key => $marcacoesData)
        {
            if (is_array($marcacoesData))
            {
                foreach ($marcacoesData as $key => $marcacaoLocal)
                {
                    if (is_array($marcacaoLocal))
                    {
                        $local = $key;
                        foreach ($marcacaoLocal as $key => $marcacaoREF)
                        {
                            if (is_array($marcacaoREF))
                            {
                                $ref = $key;
                                foreach ($marcacaoREF as $key => $marcacaoUsers)
                                {
                                    if (is_array($marcacaoUsers))
                                    {
                                        $subcount++;
                                        if ($last_local != $local || $last_date != $marcacoesData['data'] || $last_ref != $ref)
                                        {
                                            if (!$first_run)
                                            {
                                                $subcount = 1;
                                                $page++;
                                                $html2 = view('PdfGenerate.nominal.nominal-full-restart', ['generated' => $generated, 'page' => $page, 'date' => date("d/m/Y", strtotime($marcacoesData['data'])) , 'ref' => $ref, 'local' => $local, ]);
                                                $last_local = $local;
                                                $last_date = $marcacoesData['data'];
                                                $last_ref = $ref;
                                                $html0 .= $html2;
                                            }
                                        }
                                        if ($subcount == 18)
                                        {
                                            $subcount = 1;
                                            $page++;
                                            $html2 = view('PdfGenerate.nominal.nominal-new-page', ['generated' => $generated, 'page' => $page, 'date' => date("d/m/Y", strtotime($marcacoesData['data'])) , 'ref' => $ref, 'local' => $local, ]);
                                            $html0 .= $html2;
                                        }
                                        $count++;
                                        $html1 = view('PdfGenerate.nominal.nominal-tables', ['id' => $marcacaoUsers['USER_ID'], 'name' => $marcacaoUsers['USER_NAME'], 'posto' => $marcacaoUsers['USER_POSTO'], 'unit' => $marcacaoUsers['USER_UNIT'],

                                        ]);
                                        $html0 .= $html1;
                                    }
                                }
                            }
                        }

                    }
                }
            }
            $first_run = false;
        }

        $html1 = view('PdfGenerate.nominal.nominal-footer', ['totalMarcacoes' => $count]);
        $html0 .= $html1;

        PDF::setOptions(['dpi' => 600, 'defaultFont' => 'sans-serif', 'debugCss' => true, 'defaultMediaType' => "print", 'isHtml5ParserEnabled' => true]);

        if (isset($html0))
        {
            return PDF::loadHTML($html0)->setPaper('a4', 'portrait')->download('invoice.pdf');
        }
        else
        {
            return 0;
        }
    }

    /**
     * Gera UM relatório de marcações de um utilizador com conta s/ login
     *
     * @param Request $post
     *
     * @return PDF
     */
    public function generateChildrenUserReport(Request $request)
    {
        $childrenUser = users_children::where('childID', $request->childID)
            ->first();
        $groupDetails = users_children_subgroups::where('groupID', $childrenUser->childGroup)
            ->first();
        $parentUser = User::where('id', $childrenUser->parentNIM)
            ->first();
        $marcaçoes = marcacaotable::where('NIM', $childrenUser->childID)
            ->orderBy('data_marcacao')
            ->get()
            ->all();
        $taggedRef = user_children_checked_meals::where('user', $childrenUser->childID)
            ->get()
            ->all();
        $filename = 'RELATÓRIO-' . $childrenUser->childID . '-' . date("d.m.y") . '.pdf';
        $pdf = PDF::loadView('PdfGenerate.childrenUserReport', array(
            'user' => $childrenUser,
            'grupo' => $groupDetails,
            'parent' => $parentUser,
            'marcacoes' => $marcaçoes,
            'tagMarcacoes' => $taggedRef
        ));
        return $pdf->download($filename);
    }

    /**
     *  Obter datas de inicio e fim da semana
     *
     * @param string $week
     * @param string @year
     *
     * @return array
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
     * Gera relatório de marcações de um utilizador com conta c/ login
     *
     * @param Request $post
     *
     * @return PDF
     */
    public function generateGeneralUserReport(Request $request)
    {

        $id = $request->id;
        while ((strlen((string)$id)) < 8) {
          $id = 0 . (string)$id;
        }

        $user = \App\Models\User::where('id', $id)->first();

        $civilian_postos = array(
          'ASS.TEC.','ASS.OP.','TEC.SUP.','ENC.OP.','TIA','TIG.1','TIE'
        );

        if ($user)
        {
            $user_info['id'] = $user->id;
            $user_info['name'] = $user->name;
            $user_info['posto'] = $user->posto;
            $user_info['tag_oblig'] = $user->isTagOblig;
            $user_info['local_pref'] = ($user->localRefPref != "") ? $user->localRefPref : "NÃO PREENCHIDO";;
            $user_info['email'] = ($user->email != "") ? $user->email : "NÃO PREENCHIDO";
            if ($user->isAccountChildren == 'Y'){
              $parent = \App\Models\User::where('id', $user->accountChildrenOf)->first();
              $user_info['association'] = "Associado a <b>" . $parent['posto']. ' ' . $parent['name'].'</b>';
            }
            elseif ($user->isAccountChildren == 'WAITING'){
               $parent = \App\Models\User::where('id', $user->accountChildrenOf)->first();
               $user_info['association'] = "Aguarda associação a <b>" . $parent['posto']. ' ' . $parent['name'].'</b>';
            }
            else $user_info['association'] = "Não associado";

            if ($user->posto == "ASS.TEC." || $user->posto == "ASS.OP."
                || $user->posto == "TEC.SUP." || $user->posto == "ENC.OP."
                || $user->posto == "TIA" || $user->posto == "TIG.1"
                || $user->posto == "TIE" || $user->isTagOblig!=null) $user_info['confirms_meals'] = true;
            else $user_info['confirms_meals'] = false;
        }
        else
        {
            $user = \App\Models\users_children::where('childID', $id)->first();
            $user_info['id'] = $user->childID;
            $user_info['name'] = $user->childNome;
            $user_info['posto'] = $user->childPosto;
            $user_info['tag_oblig'] = null;
            $user_info['local_pref'] = "NÃO PREENCHIDO";;
            $user_info['email'] = ($user->childEmail != "") ? $user->childEmail : "NÃO PREENCHIDO";
            $user_info['association'] = "SIM, DE " . $user->parentNIM;
            if ($user->childPosto == "ASS.TEC." || $user->childPosto == "ASS.OP." || $user->childPosto == "TEC.SUP." || $user->childPosto == "ENC.OP." || $user->childPosto == "TIA" || $user->childPosto == "TIG.1" || $user->childPosto == "TIE"
            || $user->isTagOblig!=null) $user_info['confirms_meals'] = true;
            else $user_info['confirms_meals'] = false;

        }

        if ($user_info['local_pref']!="NÃO PREENCHIDO") {
            $local = \App\Models\locaisref::where('refName', $user_info['local_pref'])->first();
            $user_info['local_pref'] = $local['localName'];
        }

        PDF::setOptions(['dpi' => 600, 'defaultFont' => 'sans-serif', 'debugCss' => true, 'defaultMediaType' => "print", 'isHtml5ParserEnabled' => true]);

        $token = \Str::random(10);
        $filename = "RELATÓRIO UTILIZADOR " . $id . " (" . date('d/m/Y') . ")";

        $id = $request->id;

        $me_id = Auth::user()->id;
        while ((strlen((string)$me_id)) < 8) { $me_id = 0 . (string)$me_id; }

        $generated['id'] = $me_id;
        $generated['nome'] = Auth::user()->name;
        $generated['posto'] = Auth::user()->posto;
        $generated['email'] = Auth::user()->email;
        $generated['at_date'] = date('d/m/Y');
        $generated['at_hour'] = date('H:i:s');
        $generated['token'] = $token;
        $date = date('Y-m-d');
        $dateNex = date("Y-m-d", strtotime($date . "+ 12 days"));
        $DATES = $this->dateRange($date, $dateNex);
        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

        $mes_start = date('m', strtotime($DATES[0]));
        $date_start = date('d', strtotime($DATES[0])).' de '.$mes[($mes_start - 1)];

        $count = count($DATES) - 1;
        $mes_end = date('m', strtotime($DATES[$count]));
        $date_end = date('d', strtotime($DATES[$count])).' de '.$mes[($mes_end - 1)];

        $tags = array();
        $confs = array();
        $it = 0;
        foreach ($DATES as $DATE) {

          $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
          $mes_index = date('m', strtotime($DATE));

          $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
          $weekday_number = date('N',  strtotime($DATE));

          $tags[$it]['data'] = date('d', strtotime($DATE)).' de '.$mes[($mes_index - 1)];
          $tags[$it]['weekday'] = $semana[($weekday_number -1)];

          $_1REF = marcacaotable::where('NIM', $user_info['id'])->where('data_marcacao', $DATE)->where('meal', '1REF')->first();
          if ($_1REF!=null) {
            $local = \App\Models\locaisref::where('refName', $_1REF['local_ref'])->first();
            $tags[$it]['1']['local'] = $local['localName'];
          }
          $_2REF = marcacaotable::where('NIM', $user_info['id'])->where('data_marcacao', $DATE)->where('meal', '2REF')->first();
          if ($_2REF!=null) {
            $local = \App\Models\locaisref::where('refName', $_2REF['local_ref'])->first();
            $tags[$it]['2']['local'] = $local['localName'];
          }
          $_3REF = marcacaotable::where('NIM', $user_info['id'])->where('data_marcacao', $DATE)->where('meal', '3REF')->first();
          if ($_3REF!=null) {
            $local = \App\Models\locaisref::where('refName', $_3REF['local_ref'])->first();
            $tags[$it]['3']['local'] = $local['localName'];
          }


          if ($user_info['confirms_meals']==true) {
            $confs[$it]['data'] = date('d', strtotime($DATE)).' de '.$mes[($mes_index - 1)];
            $tags[$it]['weekday'] = $semana[($weekday_number -1)];

            $_1REF = \App\Models\user_children_checked_meals::where('user', $user_info['id'])->where('data', $DATE)->where('ref', '1REF')->first();
            if ($_1REF!=null) {
              $confs[$it]['1'] = 'CONFIRMADA';
            } else {
              $confs[$it]['1'] = 'NÃO';
            }

            $_2REF = \App\Models\user_children_checked_meals::where('user', $user_info['id'])->where('data', $DATE)->where('ref', '2REF')->first();
            if ($_2REF!=null) {
              $confs[$it]['2'] = 'CONFIRMADA';
            } else {
              $confs[$it]['2'] = 'NÃO';
            }

            $_3REF = \App\Models\user_children_checked_meals::where('user', $user_info['id'])->where('data', $DATE)->where('ref', '3REF')->first();
            if ($_3REF!=null) {
              $confs[$it]['3'] = 'CONFIRMADA';
            } else {
              $confs[$it]['3'] = 'NÃO';
            }

          }

          $it++;
        }

        while ((strlen((string)$user_info['id'])) < 8) {
          $user_info['id'] = 0 . (string)$user_info['id'];
        }

        $html = view('PdfGenerate.general-user.general-user-full',[
          'user_info' => $user_info,
          'generated' => $generated,
          'date_start' => $date_start,
          'date_end' => $date_end,
          'marcacoes' => $tags,
          'confirmations' => $confs,
        ]);

        if ($request->ajax()) return PDF::loadHTML($html)->setPaper('a4', 'portrait')->stream();
        else return PDF::loadHTML($html)->setPaper('a4', 'portrait')->download('RELATÓRIO-CONFIRMAÇÕES-' . $id . ".pdf");

    }

    /**
     * Gera relatório de marcações por periodo de tempo, com informação quantitativa
     *
     * @param Request $post
     *
     * @return PDF
     */
    public function generateGeneralTimeframeReport(Request $request)
    {
        if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS()) abort(401);
        $currentYear = date("Y", strtotime('now'));
        $currentWeekInt = date("W", strtotime('now'));
        $currentWeek = $this::getStartAndEndDate($currentWeekInt, $currentYear);
        $nextWeek = $this::getStartAndEndDate(($currentWeekInt + 1) , $currentYear);
        // dd($request->all());
        $date = date('Y-m-d');
        if ($request->timeframe == "ALL")
        {
            $dateNex = date("Y-m-d", strtotime($date . "+ 276 days"));
            $filtroTimeEnd = "MÁXIMO";
        }
        elseif ($request->timeframe == "WEEK")
        {
            $date = $currentWeek['week_start'];
            $dateNex = $currentWeek['week_end'];
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($request->timeframe == "NEXTWEEK")
        {
            $date = $nextWeek['week_start'];
            $dateNex = $nextWeek['week_end'];
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($request->timeframe == "MONTH")
        {
            $date = date('Y-m-01', strtotime($date));
            $dateNex = date('Y-m-t', strtotime($date));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($request->timeframe == "NEXTMONTH")
        {
            $date = date('Y-m-01', strtotime($date));
            $dateNex = date('Y-m-t', strtotime($date));
            $date = strtotime("+1 months", strtotime($date));
            $dateNex = strtotime("+1 months", strtotime($dateNex));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($request->timeframe == "PERSON")
        {
            $date = date('Y-m-d', strtotime($request->customtimeStart));
            $dateNex = date('Y-m-d', strtotime($request->customtimeEnd));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        if ($request->local == "GERAL")
        {
            $local = null;
            $localFiltro = "TODOS";
        }
        else if ($request->local == null)
        {
            if (!(new ActiveDirectoryController)->GET_STATS_OTHER_UNITS())
            {
                $local = \App\Models\unap_unidades::where('slug', Auth::user()->unidade)->value('local');
                $localFiltro = $local;
            }
            else
            {
                abort(500);
            }
        }
        else
        {
            $local = $request->local;
            $localFiltro = $request->local;
        }

        $dateStart = $date;
        $filtroTimeStart = date('d/m/Y', strtotime($date));
        $dateEnd = $dateNex;

        PDF::setOptions(['dpi' => 600, 'defaultFont' => 'sans-serif', 'debugCss' => true, 'defaultMediaType' => "print", 'isHtml5ParserEnabled' => true]);
        $token = \Str::random(10);
        $filename = "RELATÓRIO " . $request->local . " (" . date('d/m/Y') . ")";
        if ($local == null)
        {
            $allMarcaçoes = marcacaotable::orderBy('data_marcacao')->where('data_marcacao', '>=', $dateStart)->where('data_marcacao', '<=', $dateEnd)->get()->all();
        }
        else
        {
            $allMarcaçoes = marcacaotable::orderBy('data_marcacao')->where('data_marcacao', '>=', $dateStart)->where('data_marcacao', '<=', $dateEnd)->where('local_ref', $local)->get()->all();
        }
        $iteration = 0;
        $datas_temp = \App\Models\ementatable::orderBy('data')->where('data', '>=', $dateStart)->where('data', '<=', $dateEnd)->get();
        $datas[] = array();

         foreach ($datas_temp as $key => $data) {
          $datas[$iteration] = $data['data'];
          $iteration++;
        }


        $allRefs[] = array();
        $ementa[] = array();
        $iteration = 0;
        $total1Ref = 0;
        $total2Ref = 0;
        $total3Ref = 0;
        if (empty($datas[0]))
        {
            return 0;
        }
        foreach ($datas as $data)
        {
            $allRefs[$iteration]['data'] = date('d/m/Y', strtotime($data));
            $allRefs[$iteration]['1REF'] = ($local==null) ? marcacaotable::where('meal', '1REF')->where('data_marcacao', $data)->count() : marcacaotable::where('meal', '1REF')->where('data_marcacao', $data)->where('local_ref', $local)->count();
            $notNominalRef1 = ($local == null) ? \App\Models\pedidosueoref::where('meal', '1REF')->where('data_pedido', $data)->get() : \App\Models\pedidosueoref::where('meal', '1REF')->where('data_pedido', $data)->where('local_ref', $local)->get();
            $notNominalRef1_temp = 0;
            foreach ($notNominalRef1 as $key => $REF_1_EXTERNAL) {
              $notNominalRef1_temp += $REF_1_EXTERNAL['quantidade'];
            }
            $notNominalRef1 = $notNominalRef1_temp;
            $allRefs[$iteration]['1REF'] = ($notNominalRef1) ? ($allRefs[$iteration]['1REF'] + $notNominalRef1) : $allRefs[$iteration]['1REF'];

            $allRefs[$iteration]['2REF'] = ($local == null) ? marcacaotable::where('meal', '2REF')->where('data_marcacao', $data)->count() : marcacaotable::where('meal', '2REF')->where('data_marcacao', $data)->where('local_ref', $local)->count();
            $notNominalRef2 = ($local == null) ? \App\Models\pedidosueoref::where('meal', '2REF')->where('data_pedido', $data)->get() : \App\Models\pedidosueoref::where('meal', '2REF')->where('data_pedido', $data)->where('local_ref', $local)->get();
            $notNominalRef2_temp = 0;
            foreach ($notNominalRef2 as $key => $REF_2_EXTERNAL) {
              $notNominalRef2_temp += $REF_2_EXTERNAL['quantidade'];
            }
            $notNominalRef2 = $notNominalRef2_temp;

            $allRefs[$iteration]['2REF'] = ($notNominalRef2) ? ($allRefs[$iteration]['2REF'] + $notNominalRef2) : $allRefs[$iteration]['2REF'];
            $allRefs[$iteration]['3REF'] = ($local==null) ? marcacaotable::where('meal', '3REF')->where('data_marcacao', $data)->count() : marcacaotable::where('meal', '3REF')->where('data_marcacao', $data)->where('local_ref', $local)->count();
            $notNominalRef3 = ($local == null) ? \App\Models\pedidosueoref::where('meal', '3REF')->where('data_pedido', $data)->get() : \App\Models\pedidosueoref::where('meal', '3REF')->where('data_pedido', $data)->where('local_ref', $local)->get();
            $notNominalRef3_temp = 0;
            foreach ($notNominalRef3 as $key => $REF_3_EXTERNAL) {
              $notNominalRef3_temp += $REF_3_EXTERNAL['quantidade'];
            }
            $notNominalRef3 = $notNominalRef3_temp;

            $allRefs[$iteration]['3REF'] = ($notNominalRef3) ? ($allRefs[$iteration]['3REF'] + $notNominalRef3) : $allRefs[$iteration]['3REF'];
            $allRefs[$iteration]['TOTAL'] = (($allRefs[$iteration]['1REF'] + $allRefs[$iteration]['2REF']) + $allRefs[$iteration]['3REF']);
            $total1Ref = ($total1Ref + $allRefs[$iteration]['1REF']);
            $total2Ref = ($total2Ref + $allRefs[$iteration]['2REF']);
            $total3Ref = ($total3Ref + $allRefs[$iteration]['3REF']);
            $iteration++;

        }
        $totalRefs = ($total1Ref + ($total2Ref + $total3Ref));
        $countEntries = count($allRefs);
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
        $generated['token'] = $token;
        if ($countEntries <= 10)
        {
            $html = view('PdfGenerate.general-ref.general-ref-count-full', ['generated' => $generated, 'refs' => $allRefs, 'total1Ref' => $total1Ref, 'total2Ref' => $total2Ref, 'total3Ref' => $total3Ref, 'totalRefs' => $totalRefs, 'localFilter' => $localFiltro, 'timeStartFilter' => $filtroTimeStart, 'timeEndFilter' => $filtroTimeEnd]);
        }
        else
        {
            $start_html = view('PdfGenerate.general-ref.general-ref-count-start', ['generated' => $generated, ]);
            $html = $start_html;
            $page = 1;
            $iteration = 0;
            foreach ($allRefs as $key => $ref)
            {
                $iteration++;
                if ($iteration == 13)
                {
                    $page++;
                    $iteration = 1;
                    $newPage_html = view('PdfGenerate.general-ref.general-ref-count-new-page', ['generated' => $generated, 'key' => $key, 'page' => $page]);
                    $html .= $newPage_html;
                }
                $entry_html = view('PdfGenerate.general-ref.general-ref-count', ['data' => $ref['data'], 'REF1' => $ref['1REF'], 'REF2' => $ref['2REF'], 'REF3' => $ref['3REF'], 'TOTAL' => $ref['TOTAL']]);
                $html .= $entry_html;
            }

            $end_html = view('PdfGenerate.general-ref.general-ref-count-last', ['total1Ref' => $total1Ref, 'total2Ref' => $total2Ref, 'total3Ref' => $total3Ref, 'totalRefs' => $totalRefs, 'localFilter' => $localFiltro, 'timeStartFilter' => $filtroTimeStart, 'timeEndFilter' => $filtroTimeEnd]);
            $html .= $end_html;

        }
        return PDF::loadHTML($html)->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->download();
    }

    /**
     * Gera relatório de marcações feitas por POC's
     *
     * @param Request $post
     *
     * @return PDF
     */
    public function generateMarcacaoPorPOC(Request $request){
        if (!(new ActiveDirectoryController)->VIEW_GENERAL_STATS()) abort(401);
        $currentYear = date("Y", strtotime('now'));
        $currentWeekInt = date("W", strtotime('now'));
        $currentWeek = $this::getStartAndEndDate($currentWeekInt, $currentYear);
        $nextWeek = $this::getStartAndEndDate(($currentWeekInt + 1) , $currentYear);

        $date = date('Y-m-d');
        if ($request->timeframe == "ALL")
        {
            $dateNex = date("Y-m-d", strtotime($date . "+ 276 days"));
            $filtroTimeEnd = "MÁXIMO";
        }
        elseif ($request->timeframe == "WEEK")
        {
            $date = $currentWeek['week_start'];
            $dateNex = $currentWeek['week_end'];
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($request->timeframe == "NEXTWEEK")
        {
            $date = $nextWeek['week_start'];
            $dateNex = $nextWeek['week_end'];
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($request->timeframe == "MONTH")
        {
            $date = date('Y-m-01', strtotime($date));
            $dateNex = date('Y-m-t', strtotime($date));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }
        elseif ($request->timeframe == "NEXTMONTH")
        {
            $date = date('Y-m-01', strtotime( "+1 month", strtotime( $date ) ));
            $dateNex = date('Y-m-t', strtotime( $date ));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));

        }
        elseif ($request->timeframe == "PERSON")
        {
            $date = date('Y-m-d', strtotime($request->customtimeStart));
            $dateNex = date('Y-m-d', strtotime($request->customtimeEnd));
            $filtroTimeEnd = date('d/m/Y', strtotime($dateNex));
        }


        $dateStart = $date;
        $filtroTimeStart = date('d/m/Y', strtotime($date));
        $dateEnd = $dateNex;

        $token = \Str::random(10);
        $filename = "RELATÓRIO POC DE (".$dateStart.") ATÉ (".$dateEnd.").pdf";

        $datas_temp = \App\Models\ementatable::orderBy('data')->where('data', '>=', $dateStart)->where('data', '<=', $dateEnd)->get()->pluck('data')->all();
        $unidades = \App\Models\unap_unidades::get(['slug', 'name', 'local'])->all();
        // PEDIDOS POC
        $refs = array();
        $iteration = 0;

        foreach ($unidades as $key => $unit) {
            $slug = $unit['slug'];
            $refs[$slug]['name'] = $unit['name'];
            $refs[$slug]['local'] = \App\Models\locaisref::where('refName', $unit['local'])->value('localName');

            $POCS_Unidade = \App\Models\User::where('unidade', $slug)->where('user_type', 'POC')->get();
            foreach($POCS_Unidade as $POC){
                $refs[$slug]['POCs'][$POC['id']]['NIM'] = $POC['id'];
                $refs[$slug]['POCs'][$POC['id']]['POSTO'] = $POC['posto'];
                $refs[$slug]['POCs'][$POC['id']]['NOME'] = $POC['name'];

                $POC_Signature = "POC@".$POC['id'];
                foreach ($datas_temp as $key => $entrada_data) {

                    $REF1_CURRENT = (isset($refs[$slug]['TAGS'][$entrada_data]['MARCA'][1])) ? $refs[$slug]['TAGS'][$entrada_data]['MARCA'][1] : 0;
                    $REF2_CURRENT = (isset($refs[$slug]['TAGS'][$entrada_data]['MARCA'][2])) ? $refs[$slug]['TAGS'][$entrada_data]['MARCA'][2] : 0;
                    $REF3_CURRENT = (isset($refs[$slug]['TAGS'][$entrada_data]['MARCA'][3])) ? $refs[$slug]['TAGS'][$entrada_data]['MARCA'][3] : 0;

                    $refs[$slug]['TAGS'][$entrada_data]['MARCA'][1] = $REF1_CURRENT +  \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('created_by', $POC_Signature)->count();
                    $refs[$slug]['TAGS'][$entrada_data]['MARCA'][2] = $REF2_CURRENT + \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('created_by', $POC_Signature)->get()->count();
                    $refs[$slug]['TAGS'][$entrada_data]['MARCA'][3] = $REF3_CURRENT + \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('created_by', $POC_Signature)->count();

                    $All_Pedidos = \App\Models\pedidosueoref::where('data_pedido', $entrada_data)->where('registeredByNIM', $POC['id'])->get(['quantidade', 'meal'])->all();

                    $PEDIDO_1REF = 0;
                    $PEDIDO_2REF = 0;
                    $PEDIDO_3REF = 0;

                    if (!empty($All_Pedidos)) {
                        foreach ($All_Pedidos as $key => $pedido) {
                            if ($pedido['meal'] == "1REF") $PEDIDO_1REF += $pedido['quantidade'];
                            if ($pedido['meal'] == "2REF") $PEDIDO_2REF += $pedido['quantidade'];
                            if ($pedido['meal'] == "3REF") $PEDIDO_3REF += $pedido['quantidade'];
                        }
                    }

                    $REF1_CURRENT = (isset($refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][1])) ? $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][1] : 0;
                    $REF2_CURRENT = (isset($refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][2])) ? $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][2] : 0;
                    $REF3_CURRENT = (isset($refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][3])) ? $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][3] : 0;

                    $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][1] = $REF1_CURRENT + $PEDIDO_1REF;
                    $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][2] = $REF2_CURRENT + $PEDIDO_2REF;
                    $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][3] = $REF3_CURRENT + $PEDIDO_3REF;

                    $TOTAL_REF1 = ($refs[$slug]['TAGS'][$entrada_data]['MARCA'][1] + $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][1]);
                    $TOTAL_REF2 = ($refs[$slug]['TAGS'][$entrada_data]['MARCA'][2] + $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][2]);
                    $TOTAL_REF3 = ($refs[$slug]['TAGS'][$entrada_data]['MARCA'][3] + $refs[$slug]['TAGS'][$entrada_data]['PEDIDO'][3]);

                    $refs[$slug]['TAGS'][$entrada_data]['TOTAL'][1] = $TOTAL_REF1;
                    $refs[$slug]['TAGS'][$entrada_data]['TOTAL'][2] = $TOTAL_REF2;
                    $refs[$slug]['TAGS'][$entrada_data]['TOTAL'][3] = $TOTAL_REF3;

                    // RESET
                    $REF1_CURRENT = 0;
                    $REF2_CURRENT = 0;
                    $REF3_CURRENT = 0;
                    $TOTAL_REF1 = 0;
                    $TOTAL_REF2 = 0;
                    $TOTAL_REF3 = 0;

                }
            }

        }

        // TOTAL
        $allRefs = array();

        foreach ($datas_temp as $key => $entrada_data) {

            $allRefs['QSP'][$entrada_data]['MARCA'][1] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('civil', 'N')->where('local_ref', 'QSP')->count();
            $allRefs['QSP'][$entrada_data]['MARCA'][2]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'QSP')->where('civil', 'N')->where('dieta', 'N')->count();
            $allRefs['QSP'][$entrada_data]['MARCA'][2]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'QSP')->where('civil', 'N')->where('dieta', 'Y')->count();
            $allRefs['QSP'][$entrada_data]['MARCA'][3]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'QSP')->where('civil', 'N')->where('dieta', 'N')->count();
            $allRefs['QSP'][$entrada_data]['MARCA'][3]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'QSP')->where('civil', 'N')->where('dieta', 'Y')->count();

            $allRefs['QSP'][$entrada_data]['CV'][1] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('civil', 'Y')->where('local_ref', 'QSP')->count();
            $allRefs['QSP'][$entrada_data]['CV'][2]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'QSP')->where('civil', 'Y')->where('dieta', 'N')->count();
            $allRefs['QSP'][$entrada_data]['CV'][2]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'QSP')->where('civil', 'Y')->where('dieta', 'Y')->count();
            $allRefs['QSP'][$entrada_data]['CV'][3]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'QSP')->where('civil', 'Y')->where('dieta', 'N')->count();
            $allRefs['QSP'][$entrada_data]['CV'][3]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'QSP')->where('civil', 'Y')->where('dieta', 'Y')->count();

            $allRefs['QSO'][$entrada_data]['MARCA'][1] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('civil', 'N')->where('local_ref', 'QSO')->count();
            $allRefs['QSO'][$entrada_data]['MARCA'][2]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'QSO')->where('civil', 'N')->where('dieta', 'N')->count();
            $allRefs['QSO'][$entrada_data]['MARCA'][2]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'QSO')->where('civil', 'N')->where('dieta', 'Y')->count();
            $allRefs['QSO'][$entrada_data]['MARCA'][3]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'QSO')->where('civil', 'N')->where('dieta', 'N')->count();
            $allRefs['QSO'][$entrada_data]['MARCA'][3]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'QSO')->where('civil', 'N')->where('dieta', 'Y')->count();

            $allRefs['QSO'][$entrada_data]['CV'][1] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('civil', 'Y')->where('local_ref', 'QSO')->count();
            $allRefs['QSO'][$entrada_data]['CV'][2]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'QSO')->where('civil', 'Y')->where('dieta', 'N')->count();
            $allRefs['QSO'][$entrada_data]['CV'][2]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'QSO')->where('civil', 'Y')->where('dieta', 'Y')->count();
            $allRefs['QSO'][$entrada_data]['CV'][3]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'QSO')->where('civil', 'Y')->where('dieta', 'N')->count();
            $allRefs['QSO'][$entrada_data]['CV'][3]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'QSO')->where('civil', 'Y')->where('dieta', 'Y')->count();

            $allRefs['MMANTAS'][$entrada_data]['MARCA'][1] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('civil', 'N')->where('local_ref', 'MMANTAS')->count();
            $allRefs['MMANTAS'][$entrada_data]['MARCA'][2]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'MMANTAS')->where('civil', 'N')->where('dieta', 'N')->count();
            $allRefs['MMANTAS'][$entrada_data]['MARCA'][2]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'MMANTAS')->where('civil', 'N')->where('dieta', 'Y')->count();
            $allRefs['MMANTAS'][$entrada_data]['MARCA'][3]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'MMANTAS')->where('civil', 'N')->where('dieta', 'N')->count();
            $allRefs['MMANTAS'][$entrada_data]['MARCA'][3]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'MMANTAS')->where('civil', 'N')->where('dieta', 'Y')->count();

            $allRefs['MMANTAS'][$entrada_data]['CV'][1] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('civil', 'Y')->where('local_ref', 'MMANTAS')->count();
            $allRefs['MMANTAS'][$entrada_data]['CV'][2]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'MMANTAS')->where('civil', 'Y')->where('dieta', 'N')->count();
            $allRefs['MMANTAS'][$entrada_data]['CV'][2]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'MMANTAS')->where('civil', 'Y')->where('dieta', 'Y')->count();
            $allRefs['MMANTAS'][$entrada_data]['CV'][3]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'MMANTAS')->where('civil', 'Y')->where('dieta', 'N')->count();
            $allRefs['MMANTAS'][$entrada_data]['CV'][3]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'MMANTAS')->where('civil', 'Y')->where('dieta', 'Y')->count();

            $allRefs['MMBATALHA'][$entrada_data]['MARCA'][1] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('civil', 'N')->where('local_ref', 'MMBATALHA')->count();
            $allRefs['MMBATALHA'][$entrada_data]['MARCA'][2]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'MMBATALHA')->where('civil', 'N')->where('dieta', 'N')->count();
            $allRefs['MMBATALHA'][$entrada_data]['MARCA'][2]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'MMBATALHA')->where('civil', 'N')->where('dieta', 'Y')->count();
            $allRefs['MMBATALHA'][$entrada_data]['MARCA'][3]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'MMBATALHA')->where('civil', 'N')->where('dieta', 'N')->count();
            $allRefs['MMBATALHA'][$entrada_data]['MARCA'][3]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'MMBATALHA')->where('civil', 'N')->where('dieta', 'Y')->count();

            $allRefs['MMBATALHA'][$entrada_data]['CV'][1] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '1REF')->where('civil', 'Y')->where('local_ref', 'MMBATALHA')->count();
            $allRefs['MMBATALHA'][$entrada_data]['CV'][2]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'MMBATALHA')->where('civil', 'Y')->where('dieta', 'N')->count();
            $allRefs['MMBATALHA'][$entrada_data]['CV'][2]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '2REF')->where('local_ref', 'MMBATALHA')->where('civil', 'Y')->where('dieta', 'Y')->count();
            $allRefs['MMBATALHA'][$entrada_data]['CV'][3]['NORMAL'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'MMBATALHA')->where('civil', 'Y')->where('dieta', 'N')->count();
            $allRefs['MMBATALHA'][$entrada_data]['CV'][3]['DIETA'] = \App\Models\marcacaotable::where('data_marcacao', $entrada_data)->where('meal', '3REF')->where('local_ref', 'MMBATALHA')->where('civil', 'Y')->where('dieta', 'Y')->count();

            $PEDIDO_1REF_QSP = 0;
            $PEDIDO_2REF_QSP = 0;
            $PEDIDO_3REF_QSP = 0;
            
            $PEDIDO_1REF_QSO = 0;
            $PEDIDO_2REF_QSO = 0;
            $PEDIDO_3REF_QSO = 0;

            $PEDIDO_1REF_ANTAS = 0;
            $PEDIDO_2REF_ANTAS = 0;
            $PEDIDO_3REF_ANTAS = 0;
            
            $PEDIDO_1REF_BATALHA = 0;
            $PEDIDO_2REF_BATALHA = 0;
            $PEDIDO_3REF_BATALHA = 0;

            $All_Pedidos = \App\Models\pedidosueoref::where('data_pedido', $entrada_data)->get(['quantidade', 'meal', 'local_ref'])->all();

            if (!empty($All_Pedidos)) {
                foreach ($All_Pedidos as $key => $pedido) {

                    if ($pedido['meal'] == "1REF" && $pedido['local_ref'] == "QSP") $PEDIDO_1REF_QSP = $PEDIDO_1REF_QSP + $pedido['quantidade'];
                    if ($pedido['meal'] == "2REF" && $pedido['local_ref'] == "QSP") $PEDIDO_2REF_QSP = $PEDIDO_2REF_QSP + $pedido['quantidade'];
                    if ($pedido['meal'] == "3REF" && $pedido['local_ref'] == "QSP") $PEDIDO_3REF_QSP = $PEDIDO_3REF_QSP + $pedido['quantidade'];

                    if ($pedido['meal'] == "1REF" && $pedido['local_ref'] == "QSO") $PEDIDO_1REF_QSO = $PEDIDO_1REF_QSO + $pedido['quantidade'];
                    if ($pedido['meal'] == "2REF" && $pedido['local_ref'] == "QSO") $PEDIDO_2REF_QSO = $PEDIDO_2REF_QSO + $pedido['quantidade'];
                    if ($pedido['meal'] == "3REF" && $pedido['local_ref'] == "QSO") $PEDIDO_3REF_QSO = $PEDIDO_3REF_QSO + $pedido['quantidade'];
                    
                    if ($pedido['meal'] == "1REF" && $pedido['local_ref'] == "MMANTAS") $PEDIDO_1REF_ANTAS = $PEDIDO_1REF_ANTAS + $pedido['quantidade'];
                    if ($pedido['meal'] == "2REF" && $pedido['local_ref'] == "MMANTAS") $PEDIDO_2REF_ANTAS = $PEDIDO_2REF_ANTAS + $pedido['quantidade'];
                    if ($pedido['meal'] == "3REF" && $pedido['local_ref'] == "MMANTAS") $PEDIDO_3REF_ANTAS = $PEDIDO_3REF_ANTAS + $pedido['quantidade'];
                    
                    if ($pedido['meal'] == "1REF" && $pedido['local_ref'] == "MMBATALHA") $PEDIDO_1REF_BATALHA = $PEDIDO_1REF_BATALHA + $pedido['quantidade'];
                    if ($pedido['meal'] == "2REF" && $pedido['local_ref'] == "MMBATALHA") $PEDIDO_2REF_BATALHA = $PEDIDO_2REF_BATALHA + $pedido['quantidade'];
                    if ($pedido['meal'] == "3REF" && $pedido['local_ref'] == "MMBATALHA") $PEDIDO_3REF_BATALHA = $PEDIDO_3REF_BATALHA + $pedido['quantidade'];

                }
            }

            $allRefs['QSP'][$entrada_data]['PEDIDOS'][1] = $PEDIDO_1REF_QSP;
            $allRefs['QSP'][$entrada_data]['PEDIDOS'][2] = $PEDIDO_2REF_QSP;
            $allRefs['QSP'][$entrada_data]['PEDIDOS'][3] = $PEDIDO_3REF_QSP;
            
            $allRefs['QSO'][$entrada_data]['PEDIDOS'][1] = $PEDIDO_1REF_QSO;
            $allRefs['QSO'][$entrada_data]['PEDIDOS'][2] = $PEDIDO_2REF_QSO;
            $allRefs['QSO'][$entrada_data]['PEDIDOS'][3] = $PEDIDO_3REF_QSO;

            $allRefs['MMANTAS'][$entrada_data]['PEDIDOS'][1] = $PEDIDO_1REF_ANTAS;
            $allRefs['MMANTAS'][$entrada_data]['PEDIDOS'][2] = $PEDIDO_2REF_ANTAS;
            $allRefs['MMANTAS'][$entrada_data]['PEDIDOS'][3] = $PEDIDO_3REF_ANTAS;

            $allRefs['MMBATALHA'][$entrada_data]['PEDIDOS'][1] = $PEDIDO_1REF_BATALHA;
            $allRefs['MMBATALHA'][$entrada_data]['PEDIDOS'][2] = $PEDIDO_2REF_BATALHA;
            $allRefs['MMBATALHA'][$entrada_data]['PEDIDOS'][3] = $PEDIDO_3REF_BATALHA;


            $allRefs['QSP'][$entrada_data]['TOTAL'][1] = $PEDIDO_1REF_QSP + $allRefs['QSP'][$entrada_data]['MARCA'][1] + $allRefs['QSP'][$entrada_data]['CV'][1];

            $allRefs['QSP'][$entrada_data]['TOTAL'][2]['NORMAL'] = $PEDIDO_2REF_QSP + $allRefs['QSP'][$entrada_data]['MARCA'][2]['NORMAL'] + $allRefs['QSP'][$entrada_data]['CV'][2]['NORMAL'];
            $allRefs['QSP'][$entrada_data]['TOTAL'][2]['DIETA'] = $allRefs['QSP'][$entrada_data]['MARCA'][2]['DIETA']+ $allRefs['QSP'][$entrada_data]['CV'][2]['DIETA'];
            
            $allRefs['QSP'][$entrada_data]['TOTAL'][3]['NORMAL'] = $PEDIDO_3REF_QSP + $allRefs['QSP'][$entrada_data]['MARCA'][3]['NORMAL'] + $allRefs['QSP'][$entrada_data]['CV'][3]['NORMAL'];
            $allRefs['QSP'][$entrada_data]['TOTAL'][3]['DIETA'] = $allRefs['QSP'][$entrada_data]['MARCA'][3]['DIETA'] + $allRefs['QSP'][$entrada_data]['CV'][3]['DIETA'];

            $allRefs['QSO'][$entrada_data]['TOTAL'][1] = $PEDIDO_1REF_QSO + $allRefs['QSO'][$entrada_data]['MARCA'][1] + $allRefs['QSO'][$entrada_data]['CV'][1];
            $allRefs['QSO'][$entrada_data]['TOTAL'][2]['NORMAL'] = $PEDIDO_2REF_QSO + $allRefs['QSO'][$entrada_data]['MARCA'][2]['NORMAL'] + $allRefs['QSO'][$entrada_data]['CV'][2]['NORMAL'];
            $allRefs['QSO'][$entrada_data]['TOTAL'][2]['DIETA'] = $allRefs['QSO'][$entrada_data]['MARCA'][2]['DIETA'] + $allRefs['QSO'][$entrada_data]['CV'][2]['DIETA'];
            $allRefs['QSO'][$entrada_data]['TOTAL'][3]['NORMAL'] = $PEDIDO_3REF_QSO + $allRefs['QSO'][$entrada_data]['MARCA'][3]['NORMAL'] + $allRefs['QSO'][$entrada_data]['CV'][3]['NORMAL'];
            $allRefs['QSO'][$entrada_data]['TOTAL'][3]['DIETA'] = $allRefs['QSO'][$entrada_data]['MARCA'][3]['DIETA'] + $allRefs['QSO'][$entrada_data]['CV'][3]['DIETA'];

            $allRefs['MMANTAS'][$entrada_data]['TOTAL'][1] = $PEDIDO_1REF_ANTAS + $allRefs['MMANTAS'][$entrada_data]['MARCA'][1] + $allRefs['MMANTAS'][$entrada_data]['CV'][1];
            $allRefs['MMANTAS'][$entrada_data]['TOTAL'][2]['NORMAL'] = $PEDIDO_2REF_ANTAS + $allRefs['MMANTAS'][$entrada_data]['MARCA'][2]['NORMAL'] + $allRefs['MMANTAS'][$entrada_data]['CV'][2]['NORMAL'];
            $allRefs['MMANTAS'][$entrada_data]['TOTAL'][2]['DIETA'] = $allRefs['MMANTAS'][$entrada_data]['MARCA'][2]['DIETA'] + $allRefs['MMANTAS'][$entrada_data]['CV'][2]['DIETA'];
            $allRefs['MMANTAS'][$entrada_data]['TOTAL'][3]['NORMAL'] = $PEDIDO_3REF_ANTAS + $allRefs['MMANTAS'][$entrada_data]['MARCA'][3]['NORMAL'] + $allRefs['MMANTAS'][$entrada_data]['CV'][3]['NORMAL'];
            $allRefs['MMANTAS'][$entrada_data]['TOTAL'][3]['DIETA'] = $allRefs['MMANTAS'][$entrada_data]['MARCA'][3]['DIETA'] + $allRefs['MMANTAS'][$entrada_data]['CV'][3]['DIETA'];

            $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][1] = $PEDIDO_1REF_BATALHA + $allRefs['MMBATALHA'][$entrada_data]['MARCA'][1];

            $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][2]['NORMAL'] = $PEDIDO_2REF_BATALHA + $allRefs['MMBATALHA'][$entrada_data]['MARCA'][2]['NORMAL'];
            $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][2]['DIETA'] = $allRefs['MMBATALHA'][$entrada_data]['MARCA'][2]['DIETA'];
            $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][3]['NORMAL'] = $PEDIDO_3REF_BATALHA + $allRefs['MMBATALHA'][$entrada_data]['MARCA'][3]['NORMAL'];
            $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][3]['DIETA'] = $allRefs['MMBATALHA'][$entrada_data]['MARCA'][3]['DIETA'];

            $allRefs['TOTAL'][$entrada_data]['MARCA'][1] = $allRefs['QSP'][$entrada_data]['TOTAL'][1]
                    + $allRefs['QSO'][$entrada_data]['TOTAL'][1]
                    + $allRefs['MMANTAS'][$entrada_data]['TOTAL'][1]
                    + $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][1];

            $allRefs['TOTAL'][$entrada_data]['MARCA'][2] = $allRefs['QSP'][$entrada_data]['TOTAL'][2]['NORMAL'] + $allRefs['QSO'][$entrada_data]['TOTAL'][2]['NORMAL']
                    + $allRefs['MMANTAS'][$entrada_data]['TOTAL'][2]['NORMAL'] + $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][2]['NORMAL']
                    + $allRefs['QSP'][$entrada_data]['TOTAL'][2]['DIETA'] + $allRefs['QSO'][$entrada_data]['TOTAL'][2]['DIETA']
                    + $allRefs['MMANTAS'][$entrada_data]['TOTAL'][2]['DIETA'] + $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][2]['DIETA'];

            $allRefs['TOTAL'][$entrada_data]['MARCA'][3] = $allRefs['QSP'][$entrada_data]['TOTAL'][3]['NORMAL'] + $allRefs['QSO'][$entrada_data]['TOTAL'][3]['NORMAL']
                    + $allRefs['MMANTAS'][$entrada_data]['TOTAL'][3]['NORMAL'] + $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][3]['NORMAL']
                    + $allRefs['QSP'][$entrada_data]['TOTAL'][3]['DIETA'] + $allRefs['QSO'][$entrada_data]['TOTAL'][3]['DIETA']
                    + $allRefs['MMANTAS'][$entrada_data]['TOTAL'][3]['DIETA'] + $allRefs['MMBATALHA'][$entrada_data]['TOTAL'][3]['DIETA'];


            $allRefs['TOTAL'][$entrada_data]['PEDIDOS'][1] = $PEDIDO_1REF_QSP + $PEDIDO_1REF_QSO + $PEDIDO_1REF_ANTAS + $PEDIDO_1REF_BATALHA;
            $allRefs['TOTAL'][$entrada_data]['PEDIDOS'][2] = $PEDIDO_2REF_QSP + $PEDIDO_2REF_QSO + $PEDIDO_2REF_ANTAS + $PEDIDO_2REF_BATALHA;
            $allRefs['TOTAL'][$entrada_data]['PEDIDOS'][3] = $PEDIDO_3REF_QSP + $PEDIDO_3REF_QSO + $PEDIDO_3REF_ANTAS + $PEDIDO_3REF_BATALHA;


            $allRefs['TOTAL'][$entrada_data]['TOTAL'][1] = $allRefs['TOTAL'][$entrada_data]['MARCA'][1] + $allRefs['TOTAL'][$entrada_data]['PEDIDOS'][1];
            $allRefs['TOTAL'][$entrada_data]['TOTAL'][2] = $allRefs['TOTAL'][$entrada_data]['MARCA'][2] + $allRefs['TOTAL'][$entrada_data]['PEDIDOS'][2];
            $allRefs['TOTAL'][$entrada_data]['TOTAL'][3] = $allRefs['TOTAL'][$entrada_data]['MARCA'][3] + $allRefs['TOTAL'][$entrada_data]['PEDIDOS'][3];

        }

        $reforcos = array();

        foreach ($datas_temp as $key => $entrada_data) {
            $pedidos_data = \App\Models\pedidosueoref::where('data_pedido', $entrada_data)->get(['qty_reforços'])->all();
            $qty = 0;
            foreach ($pedidos_data as $key => $refr) {
                $qty += $refr['qty_reforços'];
            }

            $reforcos[$entrada_data] = $qty;
        }



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
        $generated['token'] = $token;

        $html = view('PdfGenerate.general-ref-seclog.general-ref-full', [
            'generated' => $generated,
            'P_POC' => $refs,
            'P_TOTAL' => $allRefs,
            'P_REFRC' => $reforcos,
            'timeStartFilter' => $filtroTimeStart,
            'timeEndFilter' => $filtroTimeEnd
        ]);

        # return $html;

        return PDF::loadHTML($html)->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->download();

    }

}
