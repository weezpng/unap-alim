<?php
namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Facades\App\Models\user_type_permissions;

use App\Mail\sendQRCodeToSelf;
use App\Mail\QRCodeSecPessRequest;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Lógica de páginas 'top-directory'
 */
class URLHandler extends Controller
{

  /**
   * @ignore
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
   * Força login a partir da conta que o utilizador tem iniciada no seu computador.
   *
   * @return void
   */

  public function login_RDE(){
    if(isset($_SERVER['AUTH_USER']) && $_SERVER['AUTH_USER']!=""){
      $temp_id = explode('\\', $_SERVER['AUTH_USER']);
      $ID = $temp_id[1];

      $USER = User::where('id', $ID)->first();
      if($USER){
        $login_internal = array("id" => $ID, "password" => $ID);

          if (! Auth::attempt($login_internal)) {

            throw ValidationException::withMessages([
                'id' => 'Erro interno, por favor contacte o CCI/UnAp',
            ]);
          }
          $user = \App\Models\User::where('id', $ID)->first();

          if ($user->account_verified=='N') return redirect()->back()->withErrors(['A sua conta ainda não se encontra activada.']);

          $user['last_login'] = now();
          $user->save();

          Auth::logoutOtherDevices($ID);
          return redirect()->route('index');
        } else {
          throw ValidationException::withMessages([
            'id' => "Utilizador não registado na aplicação.",
        ]);
      }
    } else {
      throw ValidationException::withMessages([
        'id' => "Autenticação Express não disponível.",
    ]);
    }
  }

  /**
   * Mostra a página inicial da plataforma.
   *
   * @return view
   */
    public function index()
    {
      if (Auth::check()) {
        $unidade = \App\Models\unap_unidades::where('slug', Auth::user()->unidade)->first();
        $local_ref = \App\Models\locaisref::where('refName', $unidade['local'])->first();
        $usersAvailableToAdd = array();
        $partner = null;

        if (Auth::user()->accountPartnerPOC) {
          $partner = User::where('id', Auth::user()->accountPartnerPOC)->first();
        }
        if (Auth::user()->user_type=="USER" && Auth::user()->accountChildrenOf==null) {
          $usersAvailableToAdd = User::where('user_type', "POC")->orWhere('user_type', "ADMIN")
            ->where('trocarUnidade', null)
            ->where('unidade', Auth::user()->unidade)
            ->where('account_verified', 'Y')
            ->get()->all();
        } elseif (Auth::user()->user_type=="HELPDESK" && Auth::user()->accountChildrenOf==null) {
          $usersAvailableToAdd = User::where('user_type', "POC")->orWhere('user_type', "ADMIN")
            ->where('trocarUnidade', null)
            ->where('unidade', Auth::user()->unidade)
            ->where('account_verified', 'Y')
            ->get()->all();
        }

        $currentWeekInt = date("W", strtotime('now'));
        $currentYear = date("Y", strtotime('now'));
        $lastWeek = $this::getStartAndEndDate($currentWeekInt, $currentYear);
        $date = $lastWeek['week_start'];
        $dateNex = $lastWeek['week_end'];
        $dates = $this::dateRange($date, $dateNex);

        $_user_id = Auth::user()->id;
        while ((strlen((string)$_user_id)) < 8) {
            $_user_id = 0 . (string)$_user_id;
        }

        $refs = array();
        foreach ($dates as $key => $dt) {
          $refs[$key]['DATA'] = $dt;
          $refs[$key]['TAGS'] = \App\Models\marcacaotable::where('NIM', $_user_id)->where('data_marcacao', $dt)->count();
          $refs[$key]['CONF'] = \App\Models\entradasquiosque::where('NIM', $_user_id)->where('REGISTADO_DATE', $dt)->count();
        }

        $lastWeek = $this::getStartAndEndDate($currentWeekInt - 1, $currentYear);
        $date = $lastWeek['week_start'];
        $dateNex = $lastWeek['week_end'];
        $dates = $this::dateRange($date, $dateNex);

        $refs_2 = array();
        foreach ($dates as $key => $dt) {
          $refs_2[$key]['DATA'] = $dt;
          $refs_2[$key]['TAGS'] = \App\Models\marcacaotable::where('NIM', $_user_id)->where('data_marcacao', $dt)->count();
          $refs_2[$key]['CONF'] = \App\Models\entradasquiosque::where('NIM', $_user_id)->where('REGISTADO_DATE', $dt)->count();
        }

        return view('welcome', [
          'unidade' => $unidade,
          'local_ref' => $local_ref,
          'partner' => $partner,
          'REFS' => $refs,
          'REFS_LAST' => $refs_2,
          'users' => $usersAvailableToAdd,
        ]);
      }
      return view('welcome');
    }

    /**
     * Mostrar mensagem de conta bloqueada
     *
     * @return view
     */
    public function accountLocked(){
      if (Auth::user()->lock=='N') return redirect()->back();
      return view('messages.locked');
    }


    /**
     * Mostrar página de ementa.
     *
     * @return view
     */
    public function ementa_index(){
      $today = date("Y-m-d");
      $ementaTable = \App\Models\ementatable::orderBy('data')->where('data', '>=', $today)->get();
      $ementaFormatadaDia = $this::formatEmenta($ementaTable);
        return view('ementa.index', [
          'meals' => $ementaFormatadaDia
        ]);
    }

    /**
    * Verificar se dia e refeição se já se encontram na tabela de marcações.
    *
    * @param string haystack
    * @param string data
    * @param string refeição
    * @return bool
    */
    public function verificarEmMarcacoes($haystack, $data, $meal)
    {
      foreach ($haystack as $key => $entry) {
        if ($entry['data']==$data && $entry['meal']==$meal) {
          return true;
        }
      }
      return false;
    }

    /**
    * Formata ementa para mostrar na página de ementa e de marcações
    *
    * @param array tabela
    * @return array
    */
    public function formatEmenta($table){
      $iteration = 0;
      $iterationRefNext = "1REF";
      $ementaFormatadaDia = [];
      foreach ($table as $key => $dayEntry) {
        if ($iterationRefNext == "1REF")
        {
          $ementaFormatadaDia[$iteration]['id'] = $dayEntry->id;
          $ementaFormatadaDia[$iteration]['data'] = $dayEntry->data;
          $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
          $ementaFormatadaDia[$iteration]['marcado']="0";
          $ementaFormatadaDia[$iteration]['local']="0";
          $iteration = $iteration + 1;
          $iterationRefNext = "2REF";
        }
        if ($iterationRefNext == "2REF")
        {
          $ementaFormatadaDia[$iteration]['id'] = $dayEntry->id;
          $ementaFormatadaDia[$iteration]['data'] = $dayEntry->data;
          $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
          $ementaFormatadaDia[$iteration]['sopa_almoço'] = $dayEntry->sopa_almoço;
          $ementaFormatadaDia[$iteration]['prato_almoço'] = $dayEntry->prato_almoço;
          $ementaFormatadaDia[$iteration]['sobremesa_almoço'] = $dayEntry->sobremesa_almoço;
          $ementaFormatadaDia[$iteration]['marcado']="0";
          $ementaFormatadaDia[$iteration]['local']="0";
          $iteration = $iteration + 1;
          $iterationRefNext = "3REF";
        }
        if ($iterationRefNext == "3REF")
        {
          $ementaFormatadaDia[$iteration]['id'] = $dayEntry->id;
          $ementaFormatadaDia[$iteration]['data'] = $dayEntry->data;
          $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
          $ementaFormatadaDia[$iteration]['sopa_jantar'] = $dayEntry->sopa_jantar;
          $ementaFormatadaDia[$iteration]['prato_jantar'] = $dayEntry->prato_jantar;
          $ementaFormatadaDia[$iteration]['sobremesa_jantar'] = $dayEntry->sobremesa_jantar;
          $ementaFormatadaDia[$iteration]['marcado']="0";
          $ementaFormatadaDia[$iteration]['local']="0";
          $iteration = $iteration + 1;
          $iterationRefNext = "1REF";
        }
      }
      return (empty($ementaFormatadaDia)) ? null : $ementaFormatadaDia;
    }

    /**
    * Página inicial de edição de ementa
    *
    * @return view
    */
    public function gestao_ementa_index(){
      if(!((new ActiveDirectoryController)->EDIT_EMENTA())) abort(401);
      $maxtdate = date('Y-m-d', strtotime("+15 days"));
      $ementaTable = \App\Models\ementatable::where('data', '>=', date('Y-m-d'))->where('data', '<', $maxtdate)->orderBy('data', 'asc')->get()->all();

      #dd($this::formatEmenta($ementaTable));
      return view('gestao.ref', [
        'ementaTable' => $this::formatEmenta($ementaTable),
        'MAX' => (new checkSettingsTable)->ADDMAX(),
      ]);
    }

    /**
    * Pagina inicial de marcações
    *
    * @return view
    */
    public function marcacaoIndex()
    {
        $today = date("Y-m-d");
        $maxdate = ((new checkSettingsTable)->REMOVEMAX());
        $maxDateAdd = ((new checkSettingsTable)->ADDMAX());

        $datefirst = date("Y-m-d",strtotime($today."+ ".$maxDateAdd." days"));        
        $dateNex = date('Y-m-d', strtotime($datefirst.'+2 months'));

        $marcaçoes = \App\Models\marcacaotable::where('NIM', Auth::user()->id)
          ->orderBy('data_marcacao')
          ->get();

        $locaisRef = \App\Models\locaisref::get()->all();
        $locaisAvailable = [];
        $datasMarcadas = [];
        foreach ($marcaçoes as $i => $marcaçao)
        {
          $datasMarcadas[$i]['data'] = $marcaçao->data_marcacao;
          $datasMarcadas[$i]['meal'] = $marcaçao->meal;
          $datasMarcadas[$i]['local'] = $marcaçao->local_ref;
        }
        foreach ($locaisRef as $key => $local) {
          $locaisAvailable[$local->refName]['nome'] = $local->localName;
          $locaisAvailable[$local->refName]['ref'] = $local->refName;
          $locaisAvailable[$local->refName]['estado'] = $local->status;
        }

        $ementaFormatadaDia = array();
        $dates = $this->dateRange($datefirst, $dateNex);
        $iteration = 0;
        $iterationRefNext = "1REF";

        foreach ($dates as $key => $date) {
          $ementa = \App\Models\ementatable::where('data', $date)->first();

          if ($ementa) {
            if ($iterationRefNext == "1REF")
            {
              $ementaFormatadaDia[$iteration]['id'] = $ementa->id;
              $ementaFormatadaDia[$iteration]['data'] = $ementa->data;
              $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
              $ementaFormatadaDia[$iteration]['marcado']="0";
              $ementaFormatadaDia[$iteration]['local']="0";
              $iteration = $iteration + 1;
              $iterationRefNext = "2REF";
            }
            if ($iterationRefNext == "2REF")
            {
              $ementaFormatadaDia[$iteration]['id'] = $ementa->id;
              $ementaFormatadaDia[$iteration]['data'] = $ementa->data;
              $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
              $ementaFormatadaDia[$iteration]['sopa_almoço'] = $ementa->sopa_almoço;
              $ementaFormatadaDia[$iteration]['prato_almoço'] = $ementa->prato_almoço;
              $ementaFormatadaDia[$iteration]['sobremesa_almoço'] = $ementa->sobremesa_almoço;
              $ementaFormatadaDia[$iteration]['marcado']="0";
              $ementaFormatadaDia[$iteration]['local']="0";
              $iteration = $iteration + 1;
              $iterationRefNext = "3REF";
            }
            if ($iterationRefNext == "3REF")
            {
              $ementaFormatadaDia[$iteration]['id'] = $ementa->id;
              $ementaFormatadaDia[$iteration]['data'] = $ementa->data;
              $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
              $ementaFormatadaDia[$iteration]['sopa_jantar'] = $ementa->sopa_jantar;
              $ementaFormatadaDia[$iteration]['prato_jantar'] = $ementa->prato_jantar;
              $ementaFormatadaDia[$iteration]['sobremesa_jantar'] = $ementa->sobremesa_jantar;
              $ementaFormatadaDia[$iteration]['marcado']="0";
              $ementaFormatadaDia[$iteration]['local']="0";
              $iteration = $iteration + 1;
              $iterationRefNext = "1REF";
            }

          } else {

            if ($iterationRefNext == "1REF")
            {
              $ementaFormatadaDia[$iteration]['id'] = null;
              $ementaFormatadaDia[$iteration]['data'] = $date;
              $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
              $ementaFormatadaDia[$iteration]['marcado']="0";
              $ementaFormatadaDia[$iteration]['local']="0";
              $iteration = $iteration + 1;
              $iterationRefNext = "2REF";
            }

            if ($iterationRefNext == "2REF")
            {
              $ementaFormatadaDia[$iteration]['id'] = null;
              $ementaFormatadaDia[$iteration]['data'] = $date;
              $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
              $ementaFormatadaDia[$iteration]['sopa_almoço'] = 'Não publicado';
              $ementaFormatadaDia[$iteration]['prato_almoço'] = 'Não publicado';
              $ementaFormatadaDia[$iteration]['sobremesa_almoço'] = 'Não publicado';
              $ementaFormatadaDia[$iteration]['marcado']="0";
              $ementaFormatadaDia[$iteration]['local']="0";
              $iteration = $iteration + 1;
              $iterationRefNext = "3REF";
            }

            if ($iterationRefNext == "3REF")
            {
              $ementaFormatadaDia[$iteration]['id'] = null;
              $ementaFormatadaDia[$iteration]['data'] = $date;
              $ementaFormatadaDia[$iteration]['meal'] = $iterationRefNext;
              $ementaFormatadaDia[$iteration]['sopa_jantar'] = 'Não publicado';
              $ementaFormatadaDia[$iteration]['prato_jantar'] = 'Não publicado';
              $ementaFormatadaDia[$iteration]['sobremesa_jantar'] = 'Não publicado';
              $ementaFormatadaDia[$iteration]['marcado']="0";
              $ementaFormatadaDia[$iteration]['local']="0";
              $iteration = $iteration + 1;
              $iterationRefNext = "1REF";
            }

          }
        }

        if ($ementaFormatadaDia) {
          foreach ($ementaFormatadaDia as $key => $refPorDia) {
            $get_ferias = \App\Models\Ferias::where('to_user', Auth::user()->id)
            ->where('data_inicio', '<=', $refPorDia['data'])
            ->where('data_fim', '>', $refPorDia['data'])
            ->first();
            if($get_ferias!=null) unset($ementaFormatadaDia[$key]);
            if ($this::verificarEmMarcacoes($datasMarcadas, $refPorDia['data'], $refPorDia['meal'])) {
              $ementaFormatadaDia[$key]['marcado']="1";
              $ementaFormatadaDia[$key]['local']=\App\Models\marcacaotable::where('NIM', Auth::user()->id)
                ->where('data_marcacao', $refPorDia['data'])->where('meal', $refPorDia['meal'])->first()->local_ref;
              $ementaFormatadaDia[$key]['marcacao_id']=\App\Models\marcacaotable::where('NIM', Auth::user()->id)
                ->where('data_marcacao', $refPorDia['data'])->where('meal', $refPorDia['meal'])->first()->id;
            }
          }
        }

        $dieta = \App\Models\Dietas::where('NIM', Auth::user()->id)->first();
        
        return view('marcaçoes.index',[
          'marcaçoes' => $ementaFormatadaDia,
          'dieta' => $dieta,
          'locais' => $locaisAvailable,
          'maxDays' => $maxdate,
          'marcarRefMax' => $maxDateAdd
      ]);
    }

  /**
  * Marca uma notificação como ja vista
  *
  * @param Request $request
  * @return string
  */
  public function notification_check_seen(Request $request){
    $notification = \App\Models\notification_table::where('id', $request->notificationID)->first();
    $action  = \App\Models\pending_actions::where('notification_id', $request->notificationID)->where('is_valid', 'Y')->first();
    if ($notification->notification_geral==null) {
      if ($action==null) {
        $notification->notification_seen = 'Y';
        $notification->save();
        return "NOTIFICATION".$request->notificationID;
      }
    }
  }

  /**
  * Ocultar uma notificação de Auth::User()
  *
  * @param Request $post
  * @return json
  */
  public function notification_del_toUser(Request $post){
    if ($post->ajax())
    {
      try {
        $NIM = Auth::user()->id;
        while ((strlen((string)$NIM)) < 8) {
          $NIM = 0 . (string)$NIM;
        }
        $this_user = User::where('id', $NIM)->first();
        $current_nots_dism = $this_user->dismissed_nots;
        $this_user->dismissed_nots = $current_nots_dism.$post['notID'].';';
        $this_user->save();
        return response()->json('success', 200);
      } catch (\Throwable $th) {
        return response()->json($e->getMessage() , 200);
      }
    }
    return response()->json("Erro interno de servidor." , 200);
  }

  /**
  * Activar ou desactivar DARK MODE para uma janela sem sessão iniciada.
  *
  * @param Request $post
  * @return json
  */
  public function toggle_dark_mode_noauth(Request $request){
    if ($request->ajax())
    {
      try {
        $_d_mode = session('dark_mode_inauth');
        if($_d_mode==null || $_d_mode=="off") session(['dark_mode_inauth' => 'on']);
        else  session(['dark_mode_inauth' => 'off']);
        return response()->json('success', 200);
      } catch (\Throwable $th) {
        return response()->json($e->getMessage() , 200);
      }
    }
    return response()->json("Erro interno de servidor." , 200);
  }


  /**
  * Faz download do código QR do utilizador com sessão iniciada
  *
  * @return download
  */
  public function qr_download(){
    $NIM = Auth::user()->id;
    while ((strlen((string)$NIM)) < 8) {
        $NIM = 0 . (string)$NIM;
    }
    $file = public_path('assets\profiles\QRS\qrcode_'.$NIM.'.png');
    $file_name = "QR_" . $NIM . ".png";
    $headers = array(
      'Content-Type: image/png',
    );

    if(!file_exists($file)){
      $image = \QrCode::size(200)->format('svg')->margin(1)->generate($NIM, public_path('assets\profiles\QRS\qrcode_'.$NIM.'.svg'));
      exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));
    }

    exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));
    return \Response::download($file, $file_name, $headers);

  }

  /**
  * Envia email com o código QR para o email introduzido pelo o utilizador
  *
  * @param Request $post
  * @return redirect
  */
  public function send_mail(Request $post){
    $NIM = Auth::user()->id;
    while ((strlen((string)$NIM)) < 8) {
        $NIM = 0 . (string)$NIM;
    }
    $file_name = "assets\profiles\QRS\qrcode_".$NIM.".png";
    $headers = array(
      'Content-Type: image/png',
    );

    if(!file_exists($file_name)){
      $image = \QrCode::size(200)->format('svg')->margin(1)->generate($NIM, public_path('assets\profiles\QRS\qrcode_'.$NIM.'.svg'));
      exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));
    }

    exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));
    if (!filter_var($post->mail, FILTER_VALIDATE_EMAIL))
    {
        throw new Exception('Email inválido.');
    }
    $data['posto'] = Auth::user()->posto;
    $data['nome'] = Auth::user()->name;
    \Mail::to($post->mail)->send(new sendQRCodeToSelf($data, $file_name));

    return redirect()->back();
  }


  /**
  * Faz um pedido de impressão de código QR
  *
  * @return redirect
  */
  public function qr_code_request(){
    $NIM = Auth::user()->id;
    while ((strlen((string)$NIM)) < 8) {
        $NIM = 0 . (string)$NIM;
    }

    $pedido = new \App\Models\QRsGerados();
    $pedido->NIM = $NIM;
    $pedido->save();


    $file = public_path('\assets\profiles\QRS\qrcode_'.$NIM.'.png');
    $file_name = "assets\profiles\QRS\qrcode_".$NIM.".png";
    $headers = array(
      'Content-Type: image/png',
    );
    if(!file_exists($file_name)){
        $image = \QrCode::size(200)->format('svg')->margin(1)->generate($NIM, public_path('assets\profiles\QRS\qrcode_'.$NIM.'.svg'));
        exec(public_path('assets\profiles\QRS\overwatcher\openfile.bat'));
    }
    $email = "cpess.unap.pessoal@exercito.pt";
    $email = config('app.PESSOAL_EMAIL');
    $data['posto'] = Auth::user()->posto;
    $data['nome'] = Auth::user()->name;
    $data['nim'] = Auth::user()->id;
    \Mail::to($email)->send(new QRCodeSecPessRequest($data, $file_name));
    return redirect()->back();
  }

  /**
  * Página de videos-tutorial
  *
  * @return view
  */
  public function FAQ(){
    return view('helpdesk.faq');
  }

  /**
  * Ver página de informação de entradas no quiosque do utilizador com sessão iniciada
  *
  * @return view
  */
  public function viewQuiosqueInfo(){

    $today = date("Y-m-d");
    $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
    $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
    $formatted = array();
    $id = Auth::user()->id;

    while ((strlen((string)$id)) < 8) {
      $id = 0 . (string)$id;
    }

        $new_Date = date('Y-m-d', strtotime($today . ' -15 days'));
        $entradas = \App\Models\entradasQuiosque::where('REGISTADO_DATE',  '>', $new_Date)
        ->where('NIM', $id)
        ->orderBy('REGISTADO_DATE')
        ->orderBy('REF', 'ASC')
        ->get()->all();
        if (!empty($entradas)) {
            $mes_index = date('m', strtotime($new_Date));
            foreach ($entradas as $key => $entry) {
                $formatted[$key]['DATE'] = date('d', strtotime($entry['REGISTADO_DATE'])) . ' ' . $mes[($mes_index - 1)];
                $formatted[$key]['REF'] = ($entry['REF']=="2REF") ? "Almoço" : "Jantar";

                if ($entry['LOCAL']=="QSP") $formatted[$key]['LOCAL']="Quartel da Serra do Pilar";
                elseif ($entry['LOCAL']=="QSO") $formatted[$key]['LOCAL']="Quartel de Santo Ovídeo";
                elseif ($entry['LOCAL']=="MMANTAS") $formatted[$key]['LOCAL']="Messe das Antas";
                elseif ($entry['LOCAL']=="MMBATALHA") $formatted[$i][$key]['LOCAL']="Messe da Batalha";
                else $formatted[$i][$key]['LOCAL']="";

                $formatted[$key]['MARCADA'] = ($entry['MARCADA']=="false") ? "0" : "1";

                $mes_index = date('m', strtotime($entry['REGISTADO_DATE']));
                $formatted[$key]['REGISTADO_DATE'] = date('d', strtotime($entry['REGISTADO_DATE'])) . " " . $mes[($mes_index - 1)];
                $formatted[$key]['REGISTADO_TIME'] = $entry['REGISTADO_TIME'];

            }
        }

    return view('profile.quiosque_entradas', [
        'info' => $formatted,
    ]);
}
}
