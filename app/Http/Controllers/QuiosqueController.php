<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Lógica do lado de servidor dos pontos de quiosque
 *
 */
class QuiosqueController extends Controller
{

    /**
     * Faz login de um utilizador no sistema 'Gestor LITE'
     *
     * @param Request $request
     * @deprecated
     * @return string
     */
    public function LoginSysQuiosque(Request $post){
        try {
            $ad_url = "exercito.local";
            $user_id = "exercito\\" . $post['NIM'];
            $user_pw = $post['PW'];
            if (empty($post['PW']) || $post['PW']==null) {
                return "401"; // PW INCORRECTA
            }
            $ldap = ldap_connect($ad_url);
            if ($bind = ldap_bind($ldap, $user_id , $user_pw)) {
                $user = \App\Models\User::where('id', $post['NIM'])->first();
                $user_local = \App\Models\unap_unidades::where('slug',  $user['unidade'])->value('local');
                return "AUTHOK;" . $user['posto'] . ";". $user['name'] . ";" . $user['user_type'].';'.$user_local;
            }
        } catch (\Throwable $th) {
            $exists =\App\Models\User::where('id', $post['NIM'])->first();
            if ($exists==null) {
                return "404"; // USER NAO EXISTE
            } else {
                return "401"; // PW INCORRECTA
            }
        }
    }

    /**
   * Devolve os locais de refeição atualmnente definidos como 'OK'
   *
   * @return string
   */
    public function GetLocaisRef(){
        try {
          $locais = \App\Models\locaisRef::where('status', 'OK')->get(['localName', 'refName'])->all();
          $count_total = count($locais);
          $return = "";
          $count = 1;
          foreach ($locais as $key => $lc) {
            $return = $return.$lc['refName'].':'.$lc['localName'];
            if ($count!=$count_total) {
              $return = $return.';';
            }
            $count++;
          }
          return $return;
        } catch (\Exception $e) {
          return "500";
        }
    }

        /**
   * Devolve se o local enviado é uma Messe
   *
   * @param Request $request
   * @return string
   */
    public function IsLocalMesse(Request $post){
        try {
          $local = \App\Models\locaisRef::where('status', 'OK')->where('refName', $post['LOCAL'])->get('localName')->first();
          if (str_contains(strtoupper($local['localName']), 'MESSE')) return "TRUE";
          else return "FALSE";
        } catch (\Exception $e) {
          return "?";
        }
    }

    /**
    * Devolve a quantidade de marcações para uma refeição, num local a uma data.
    *
    * @param Request $request
    * @return string
    */
    public function GetNumberTags(Request $post){
      try {
        if (!$post['LOCAL']) return "LOCAL";
        $ref = $this->GetMeal();
        $tags = \App\Models\marcacaotable::where('data_marcacao', date('Y-m-d'))->where('local_ref', $post['LOCAL'])->where('meal', $ref)->count();
        return $tags;
      } catch (\Exception $e) {
        return "?";
      }
    }

    /**
   * Devolve a quantidade de marcações + os pedidos quantitavos para uma refeição, num local a uma data.
   *
   * @param Request $request
   * @return string
   */
    public function GetNumberConfecionadas(Request $post){
      try {
        if (!$post['LOCAL']) return "LOCAL";
        $ref = $this->GetMeal();
        $tags = \App\Models\marcacaotable::where('data_marcacao', date('Y-m-d'))->where('local_ref', $post['LOCAL'])->where('meal', $ref)->count();
        $pedidos = \App\Models\pedidosueoref::where('data_pedido', date('Y-m-d'))->where('local_ref', $post['LOCAL'])->where('meal', $ref)->get();

        $ped_qty = 0;
        foreach ($pedidos as $_pd_key => $_pd) {
          $ped_qty += $_pd['quantidade'];
        }

        return ($tags + $ped_qty);
      } catch (\Exception $e) {
        return "?";
      }
    }

    /**
    * Devolve a quantidade de entradas c/marcação para uma refeição, num local a uma data.
    *
    * @param Request $request
    * @return string
    */
    public function GetNumberEntriesMarcada(Request $post){
      try {
        if (!$post['LOCAL']) return "LOCAL";
        $ref = $this->GetMeal();
        $tags = \App\Models\entradasQuiosque::where('REGISTADO_DATE', date('Y-m-d'))
          ->where('LOCAL', $post['LOCAL'])
          ->where('REF', $ref)
          ->where('MARCADA', 'true')->get();

        $count = 0;
        foreach ($tags as $key => $entry) {
          if ($entry['NIM']=="DILIGENCIA" || $entry['NIM']=="PCS" || $entry['NIM']=="DDN") $count += $entry['qty'];
          else $count++;
        }

        return $count;
      } catch (\Exception $e) {
        return "?";
      }
    }

    /**
    * Devolve a quantidade de entradas s/marcação para uma refeição, num local a uma data.
    *
    * @param Request $request
    * @return string
    */
    public function GetNumberEntriesNaoMarcadas(Request $post){
      try {
        if (!$post['LOCAL']) return "LOCAL";
        $ref = $this->GetMeal();
        $tags = \App\Models\entradasQuiosque::where('REGISTADO_DATE', date('Y-m-d'))
          ->where('LOCAL', $post['LOCAL'])
          ->where('REF', $ref)
          ->where('MARCADA', 'false')->get();

        $count = 0;
        foreach ($tags as $key => $entry) {
          if ($entry['NIM']=="DILIGENCIA" || $entry['NIM']=="PCS" || $entry['NIM']=="DDN") $count += $entry['qty'];
          else $count++;
        }

        return $count;
      } catch (\Exception $e) {
        return "?";
      }
    }

    /**
   * Devolve se está em periodo de refeição ou fora de horas
   *
   * @param Request $request
   * @return string
   */
    public function IsTime(Request $post){
        if (isset($post['LOCAL']) && !empty($post['LOCAL'])) {
            try {
              $ref = $this::GetMeal();
              if ($ref=="0") return "FORA_HORAS";
              else return "OK";
            } catch (\Throwable $th) {
                return "ERRO";
            }
        }
        return "401";
    }

    /**
   * Devolve qual hórario de refeição é atualmente
   *
   * @return string
   */
    public function GetMeal(){

      $_1ref_start_db = \App\Models\HorariosRef::where('meal', '1REF')->first();
      $_1ref_start_db =  $_1ref_start_db['time_start'];
      $_1ref_end_db = \App\Models\HorariosRef::where('meal', '1REF')->first();
      $_1ref_end_db = $_1ref_end_db['time_end'];

      $pq_almoco_start = \Carbon\Carbon::createFromFormat('H:i', substr($_1ref_start_db, 0, 5))->format('H:i');
      $pq_almoco_end = \Carbon\Carbon::createFromFormat('H:i', substr($_1ref_end_db, 0, 5))->format('H:i');

      $_2ref_start_db = \App\Models\HorariosRef::where('meal', '2REF')->first();
      $_2ref_start_db = $_2ref_start_db['time_start'];
      $_2ref_end_db = \App\Models\HorariosRef::where('meal', '2REF')->first();
      $_2ref_end_db = $_2ref_end_db['time_end'];

      $lunch_start = \Carbon\Carbon::createFromFormat('H:i', substr($_2ref_start_db, 0, 5))->format('H:i');
      $lunch_end = \Carbon\Carbon::createFromFormat('H:i', substr($_2ref_end_db, 0, 5))->format('H:i');

      $_3ref_start_db = \App\Models\HorariosRef::where('meal', '3REF')->first();
      $_3ref_start_db = $_3ref_start_db['time_start'];
      $_3ref_end_db = \App\Models\HorariosRef::where('meal', '3REF')->first();
      $_3ref_end_db = $_3ref_end_db['time_end'];

      $dinner_start = \Carbon\Carbon::createFromFormat('H:i', substr($_3ref_start_db, 0, 5))->format('H:i');
      $dinner_end = \Carbon\Carbon::createFromFormat('H:i', substr($_3ref_end_db, 0, 5))->format('H:i');

      $now = \Carbon\Carbon::createFromFormat('H:i', date('H:i'))->format('H:i');

      if  (($now >= $dinner_start) && ($now <= $dinner_end))             return "3REF";
      elseif (($now >= $lunch_start) && ($now <= $lunch_end))            return "2REF";
      else if (($now >= $pq_almoco_start) && ($now <= $pq_almoco_end))   return "1REF";
      else                                                               return "0";

    }

    /**
     * Devolve hóspedes alojados no quarto inserido, no local selecionado
     *
     * @param Request $post
     * @return string
     */
    public function GetHospQuarto(Request $post){
      if ($post['Q']>909 || $post['Q']<601) {
        return "?";
      }
      $hospedes = \App\Models\hospede::where('quarto', $post['Q'])->where('local', $post['LOCAL'])->get(['id', 'name', 'type', 'type_temp']);
      $info = "";
      foreach ($hospedes as $key => $hosp) {
                  //           0                1                 2                    3
          $info = $info.$hosp['id'].";".$hosp['name'].";".$hosp['type'].";".$hosp['type_temp'];
          if (isset($hospedes[$key+1])) {
            $info=$info.'|';
          }
      }
      return $info;
    }

    /**
     * Devolve informação de um utilizador.
     * Se não for um utilizador, mas sim PCS, DDN ou Diligencia, devolve informação consoante isso.
     *
     * @param Request $post
     * @return string
     */
    public function GetNIM(Request $request){
        $id = $request['NIM'];
        if ($id=="PCS" || $id=="DILIGENCIA" || $id=="DDN") {
            if ($id=="PCS") {
                $name = "Provas de classificação e Seleção";
            } else if ($id=="DDN") {
                $name = "Dia de Defesa Nacional";
            } else if ($id=="DILIGENCIA") {
                $name = "Outros pedidos";
            } else {
                $name = "Desconhecido";
            }
            //      0         1                 2
            return $id.';'.$name.';'."Entrada quantitativa";
        } else {
            while ((strlen((string)$id)) < 8) {
                $id = 0 . (string)$id;
            }
            $user = \App\Models\User::where('id', $id)->first();
                //  0              1                  2                   3                   4
            return $id.';'.$user['name'].';'.$user['posto'].';'.$user['unidade'].';'.$user['seccao'];
        }
    }

    /**
     * Devolve informação de marcação de um utilizador, para uma refeição, num local de refeição, numa data.
     * Se não for um utilizador, mas sim PCS, DDN ou Diligencia, devolve informação consoante isso.
     *
     * @param Request $post
     * @return string
     */
    public function GetTag(Request $post){

        try {

            $id = $post['NIM'];

            $ref = $this::GetMeal();
            if ($ref=="0") return "FORA_HORAS";

            if ($id!="PCS" && $id!="DILIGENCIA" && $id!="DDN") {
                while ((strlen((string)$id)) < 8) {
                    $id = 0 . (string)$id;
                }
            }

            $entrada = \App\Models\entradasQuiosque::where('NIM', $id)->where('REF', $ref)->first();
            if ($entrada) {
                $qty = ($entrada['QTY']==null) ? "null" : $entrada['QTY'];
                            //  0                      1                          2                             3           4
                return $entrada['id'].";".$entrada['QUIOSQUE_IP'].";".$entrada['REGISTADO_TIME'].";".$entrada['MARCADA'].";".$qty;
            }

        } catch (\Throwable $th) {
            return "500";
        }
    }

    /**
     *
     * NOVO SISTEMA QUIOSQUE
     * SISTEMA CLIENTE <--> GESTAO
     *
     */

     /**
     * Obter informação detalhada de um utilizador.
     * Se não for um utilizador, mas sim PCS, DDN ou Diligencia, devolve informação consoante isso.
     *
     * @param Request $post
     * @return string
     */
    public function getDetailsFromID(Request $post){
        try {
            $entrada = \App\Models\entradasQuiosque::where('id', $post['EID'])->first();
            if ($entrada['NIM']=="PCS" || $entrada['NIM']=="DILIGENCIA" || $entrada['NIM']=="DDN") {
                // ARRAY            0                  1                         2           3               4
                $info = $entrada['QTY'].';'."ENTRADA QUANTITATIVA".';'.$entrada['NIM'].';'."null".';'.$post['EID'];
            } else {
                $user = \App\Models\User::where('id', $entrada['NIM'])->first();
                // ARRAY        0                 1                2                    3                  4
                $info = $user['posto'].';'.$user['name'].';'.$user['id'].';'.$entrada['MARCADA'].';'.$post['EID'];
            }

            $entrada->VIEWED_GESTOR = 'Y';
            $entrada->save();
            return $info;
        } catch (\Throwable $th) {
            return "ERRO";
        }
    }

    /**
     * Devolve IDs de entradas em base de dados da refeição atual.
     *
     * @param Request $post
     * @return string
     */
    public function GetIDs(Request $post){
        try {
          $ref = $this::GetMeal();
          if ($ref=="0") return "FORA_HORAS";

            $info = "";
            $entradas = \App\Models\entradasQuiosque::where('REGISTADO_DATE', date('Y-m-d'))
            ->where('LOCAL', $post['LOCAL'])->where('REF', $ref)
            ->get()->all();

            foreach ($entradas as $key => $entry) {
                if(strlen($info)==0){
                    $info = $entry['id'];
                } else {
                    $info = $info . ";" . $entry['id'];
                }
            }
            return $info;
        } catch (\Throwable $th) {
            return "ERRO";
        }
    }


     # POST CANDIDATOS // DDN
     # NOVO SISTEMA APENAS

     /**
     * Cria uma entrada de quiosque para entradas de DDN, PCS ou Diligências
     *
     * @param Request $post
     * @return string
     */
    public function PostSpecial(Request $post){
         try {

           $ref = $this::GetMeal();
           if ($ref=="0") return "FORA_HORAS";

            $entrada_quiosque = new \App\Models\entradasQuiosque;
            $entrada_quiosque->NIM = $post['TYPE'];
            $entrada_quiosque->QUIOSQUE_IP = $post['FROM'];
            $entrada_quiosque->REGISTADO_DATE = date('Y-m-d');
            $entrada_quiosque->REGISTADO_TIME = date('H:i:s');
            $entrada_quiosque->REF = $ref;
            $entrada_quiosque->LOCAL = $post['LOCAL'];
            $entrada_quiosque->QTY = $post['QTY'];
            $entrada_quiosque->save();
            return "OK";

         } catch (\Throwable $th) {
            return "ERRO";
         }
    }

    /**
     * Cria uma entrada de quiosque para entradas de utilizadores.
     *
     * @param Request $post
     * @return string
     */
    public function ClientEntry(Request $post){

        try {

            $ref = $this::GetMeal();
            if ($ref=="0") return "FORA_HORAS";

            $marcaçoes = \App\Models\marcacaotable::where('NIM', $user['id'])->where('data_marcacao', date('Y-m-d'))->where('local_ref', $post['LOCAL'])->where('meal', $ref)->first();

            $entrada = \App\Models\entradasQuiosque::where('NIM', $post['NIM'])
                ->where('REGISTADO_DATE', date('Y-m-d'))
                ->where('REF', $ref)
                ->first();

            if($entrada!=null){ return "ALREADY_TAGGED"; }

            if ($marcaçoes==null) {
                $marcada = "false";
                // AO PASSAR A OF-DIA CONFIRMAR AS REFEIÇOES, UNCOMENT:
                // return $user['posto'].';'.$user['name'].';'.$post['NIM'].';'.$marcada.';';
            }
            else {
                $marcada = "true";
            }
            $entrada_quiosque = new \App\Models\entradasQuiosque;
            $entrada_quiosque->NIM = $post['NIM'];
            $entrada_quiosque->QUIOSQUE_IP = $post['FROM'];
            $entrada_quiosque->REGISTADO_DATE = date('Y-m-d');
            $entrada_quiosque->REGISTADO_TIME = date('H:i:s');
            $entrada_quiosque->REF = $ref;
            $entrada_quiosque->UNIDADE = $user['unidade'];
            $entrada_quiosque->LOCAL = $post['LOCAL'];
            $entrada_quiosque->MARCADA = $marcada;
            $info = $user['posto'].';'.$user['name'].';'.$post['NIM'].';'.$marcada.';'.$entrada_quiosque['id'];
            if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP."
            || $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" || $user['posto'] == "TIG.1" || $user['posto'] == "TIE") {
                $tagRef = new \App\Models\user_children_checked_meals;
                $tagRef->data = date('Y-m-d');
                $tagRef->ref = $ref;
                $tagRef->check = "Y";
                $entrada_quiosque->CIVIL = 'Y';
                $tagRef->user = $user['id'];
                $tagRef->save();

                $CONFIRM = new \App\Models\user_children_checked_meals;
                $CONFIRM->data = date('Y-m-d');
                $CONFIRM->ref = $ref;
                $CONFIRM->user = $post['NIM'];
                $CONFIRM->check = 'Y';
                $CONFIRM->created_by = 'QUIOSQUE';
                $CONFIRM->save();

            } else {
                $entrada_quiosque->CIVIL = 'N';
            }
            $entrada_quiosque->save();

            return $info;

        } catch (\Throwable $th) {
           return "ERRO" ;
        }
     }





     # SISTEMA QUIOSQUE ANTIGO
     # SISTEMA CLIENTE-GESTÃO IN ONE


     /**
     * Cria uma confirmação de consumo de refeição
     *
     * @deprecated
     * @param Request $post
     * @return string
     */
    public function TagConfirmation(Request $post){

      try {
        $user = \App\Models\User::where('id', $post['NIM'])->first();

        $ref = $this::GetMeal();
        if ($ref=="0") return "FORA_HORAS";

        $marcaçoes = \App\Models\marcacaotable::where('NIM', $user['id'])->where('data_marcacao', date('Y-m-d'))->where('local_ref', $post['LOCAL'])>where('meal', $ref)->first();

        $entrada_quiosque = new \App\Models\entradasQuiosque;
        $entrada_quiosque->NIM = $post['NIM'];
        $entrada_quiosque->QUIOSQUE_IP = $post['FROM'];
        $entrada_quiosque->REGISTADO_DATE = date('Y-m-d');
        $entrada_quiosque->REGISTADO_TIME = date('H:i:s');
        $entrada_quiosque->REF = $ref;
        $entrada_quiosque->LOCAL = $post['LOCAL'];
        $entrada_quiosque->MARCADA = "false";
        $entrada_quiosque->UNIDADE = $user['unidade'];
        if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP."
            || $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" || $user['posto'] == "TIG.1" || $user['posto'] == "TIE") {
            $entrada_quiosque->CIVIL = 'Y';

            $CONFIRM = new \App\Models\user_children_checked_meals;
            $CONFIRM->data = date('Y-m-d');
            $CONFIRM->ref = $ref;
            $CONFIRM->user = $post['NIM'];
            $CONFIRM->check = 'Y';
            $CONFIRM->created_by = 'QUIOSQUE';
            $CONFIRM->save();

        } else {
            $entrada_quiosque->CIVIL = 'N';
        }

        $entrada_quiosque->save();
        return "OK";
      } catch (\Throwable $th) {
          return "ERRO";
      }
    }

    /**
     * Cria uma entrada de quiosque de um hóspede
     *
     * @param Request $post
     * @return string
     */
    public function CheckTagHosp(Request $post){
      try {

          $ref = $this::GetMeal();
          if ($ref=="0") return "FORA_HORAS";

          $hosp_detail = \App\Models\hospede::where('id', $post['UID'])->first();
          $marcaçoes = \App\Models\marcacaotable::where('NIM', $hosp_detail['fictio_nim'])
            ->where('data_marcacao', date('Y-m-d'))
            ->where('local_ref', $post['LOCAL'])
            ->where('meal', $ref)->first();

          $marcada = ($marcaçoes == null) ? "false" : "true";

          $info = $hosp_detail['type'].';'.$hosp_detail['name'].';'.$hosp_detail['fictio_nim'].';'.$marcada.';';

          $entrada = \App\Models\entradasQuiosque::where('NIM', $hosp_detail['fictio_nim'])
              ->where('REGISTADO_DATE', date('Y-m-d'))
              ->where('REF', $ref)
              ->first();

          if($entrada!=null){ return "ALREADY_TAGGED"; }

          $entrada_quiosque = new \App\Models\entradasQuiosque;
          $entrada_quiosque->NIM = $hosp_detail['fictio_nim'];
          $entrada_quiosque->QUIOSQUE_IP = $post['FROM'];
          $entrada_quiosque->REGISTADO_DATE = date('Y-m-d');
          $entrada_quiosque->REGISTADO_TIME = date('H:i:s');
          $entrada_quiosque->REF = $ref;
          $entrada_quiosque->UNIDADE = $post['LOCAL'];
          if ($hosp_detail['type']=="CIVIL") {
              $entrada_quiosque->CIVIL = 'Y';
              $tagRef = new \App\Models\user_children_checked_meals;
              $tagRef->data = date('Y-m-d');
              $tagRef->ref = $ref;
              $tagRef->check = "Y";
              $tagRef->user = $hosp_detail['fictio_nim'];
              $tagRef->save();

          } else {
              $entrada_quiosque->CIVIL = 'N';
          }
          $entrada_quiosque->LOCAL = $post['LOCAL'];
          $entrada_quiosque->MARCADA = $marcada;
          $entrada_quiosque->save();

          return $info;

      } catch (\Exception $e) {
          return "ERRO";
      }

    }

    /**
    * Cria uma entrada de quiosque de um utilizador, e se aplicavel, cria uma entrada de confirmação de consumo para faturação.
    *
    * @param Request $post
    * @return string
    */
    public function checkAndTag(Request $post){
        try {

            date_default_timezone_set('Europe/Lisbon');

            $user = \App\Models\User::where('id', $post['NIM'])->first();

            $ref = $this::GetMeal();
            if ($ref=="0") return "FORA_HORAS";

            $marcaçoes = \App\Models\marcacaotable::where('NIM', $user['id'])->where('data_marcacao', date('Y-m-d'))->where('local_ref', $post['LOCAL'])->where('meal', $ref)->first();


            if ($marcaçoes==null) {
                $marcada = "false";
                $dieta = "false";
                // AO PASSAR A OF-DIA CONFIRMAR AS REFEIÇOES, UNCOMENT:
                // return $user['posto'].';'.$user['name'].';'.$post['NIM'].';'.$marcada.';';
            }
            else {
                $marcada = "true";
                $dieta = $marcaçoes->dieta;
            }
            //              0                   1                2            3
            $info = $user['posto'].';'.$user['name'].';'.$post['NIM'].';'.$marcada.';'.$dieta;

            $entrada = \App\Models\entradasQuiosque::where('NIM', $post['NIM'])
                ->where('REGISTADO_DATE', date('Y-m-d'))
                ->where('REF', $ref)
                ->first();

            if($entrada!=null){ return "ALREADY_TAGGED"; }

            $entrada_quiosque = new \App\Models\entradasQuiosque;
            $entrada_quiosque->NIM = $post['NIM'];
            $entrada_quiosque->QUIOSQUE_IP = $post['FROM'];
            $entrada_quiosque->REGISTADO_DATE = date('Y-m-d');
            $entrada_quiosque->REGISTADO_TIME = date('H:i:s');
            $entrada_quiosque->REF = $ref;
            $entrada_quiosque->UNIDADE = $user['unidade'];
            if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP."
                || $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" || $user['posto'] == "TIG.1" || $user['posto'] == "TIE") {
                $entrada_quiosque->CIVIL = 'Y';

                $tagRef = new \App\Models\user_children_checked_meals;
                $tagRef->data = date('Y-m-d');
                $tagRef->ref = $ref;
                $tagRef->check = "Y";
                $tagRef->user = $user['id'];
                $tagRef->save();

            } else {
                $entrada_quiosque->CIVIL = 'N';
            }
            $entrada_quiosque->LOCAL = $post['LOCAL'];
            $entrada_quiosque->MARCADA = $marcada;
            $entrada_quiosque->save();

            return $info;

        } catch (\Throwable $th) {
            return "ERRO";
        }
    }
}
