<?php
namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Mail\ementaChanged;
use Illuminate\Support\Facades\Mail;

/**
 * Funcionalidades de gestão da ementa.
 */
class ementaHandlerController extends Controller
{

    /**
     * Troca a ementa de dois dias.
     * @param Request $request
     *
     * @return json
     */
    public function tradeEmentaNextDay(Request $request){
      try {

        $next_date = date('Y-m-d', strtotime("+1 day", strtotime($request->date)));
        $current = \App\Models\ementatable::where('data', $request->date)->first();
        $next = \App\Models\ementatable::where('data', $next_date)->first();
        $temp_current = array();

        if ($request->meal=='BOTH') {

          $temp_current['sopa_almoço'] =      $current['sopa_almoço'];
          $temp_current['prato_almoço'] =     $current['prato_almoço'];
          $temp_current['sobremesa_almoço'] = $current['sobremesa_almoço'];

          $temp_current['sopa_jantar'] =      $current['sopa_jantar'];
          $temp_current['prato_jantar'] =     $current['prato_jantar'];
          $temp_current['sobremesa_jantar'] = $current['sobremesa_jantar'];

          $current['sopa_almoço'] =      $next['sopa_almoço'];
          $current['prato_almoço'] =     $next['prato_almoço'];
          $current['sobremesa_almoço'] = $next['sobremesa_almoço'];

          $current['sopa_jantar'] =      $next['sopa_jantar'];
          $current['sopa_jantar'] =      $next['prato_jantar'];
          $current['sobremesa_jantar'] = $next['sobremesa_jantar'];
          $current['edited_by'] =        Auth::user()->id;
          $current->save();

          $next['sopa_almoço'] =      $temp_current['sopa_almoço'];
          $next['prato_almoço'] =     $temp_current['prato_almoço'];
          $next['sobremesa_almoço'] = $temp_current['sobremesa_almoço'];

          $next['sopa_jantar'] =      $temp_current['sopa_jantar'];
          $next['sopa_jantar'] =      $temp_current['prato_jantar'];
          $next['sobremesa_jantar'] = $temp_current['sobremesa_jantar'];
          $next['edited_by'] =        Auth::user()->id;
          $next->save();

        } elseif ($request->meal=='2REF') {

          $temp_current['sopa_almoço'] =      $current['sopa_almoço'];
          $temp_current['prato_almoço'] =     $current['prato_almoço'];
          $temp_current['sobremesa_almoço'] = $current['sobremesa_almoço'];

          $current['sopa_almoço'] =      $next['sopa_almoço'];
          $current['prato_almoço'] =     $next['prato_almoço'];
          $current['sobremesa_almoço'] = $next['sobremesa_almoço'];
          $current->save();

          $next['sopa_almoço'] =      $temp_current['sopa_almoço'];
          $next['prato_almoço'] =     $temp_current['prato_almoço'];
          $next['sobremesa_almoço'] = $temp_current['sobremesa_almoço'];
          $next->save();

        } elseif  ($request->meal=='3REF') {

          $temp_current['sopa_jantar'] =      $current['sopa_jantar'];
          $temp_current['prato_jantar'] =     $current['prato_jantar'];
          $temp_current['sobremesa_jantar'] = $current['sobremesa_jantar'];

          $current['sopa_jantar'] =      $next['sopa_jantar'];
          $current['prato_jantar'] =      $next['prato_jantar'];
          $current['sobremesa_jantar'] = $next['sobremesa_jantar'];
          $current['edited_by'] =        Auth::user()->id;
          $current->save();

          $next['sopa_jantar'] =      $temp_current['sopa_jantar'];
          $next['prato_jantar'] =      $temp_current['prato_jantar'];
          $next['sobremesa_jantar'] = $temp_current['sobremesa_jantar'];
          $next['edited_by'] =        Auth::user()->id;
          $next->save();
        }

        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
        $mes_index = date('m', strtotime($request->date));
        $date_not_1 = date('d', strtotime($request->date)).' '.$mes[($mes_index - 1)];

        $mes_index_2 = date('m', strtotime($next_date));
        $date_not_2 = date('d', strtotime($next_date)).' '.$mes[($mes_index_2 - 1)];

        $notifications = new notificationsHandler;
        $notifications->new_notification(
        /*TITLE*/
        'Nova ementa',
        /*TEXT*/
        'Foram trocadas as ementas de ' . $date_not_1 . ' e '.$date_not_2.'.',
        /*TYPE*/
        'NORMAL',
        /*GERAL*/
        'HELPDESK,ADMINS,SUPERS,USERS',
        /*TO USER*/
        '',
        /*CREATED BY*/
        'NEW EMENTA @' . Auth::user()->id, $request->data);

        return response()->json('success', 200);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }

    }

    /**
    * Carregar ementa para um dia com informação já formatada de ficheiro Excel
    * @param Request $request
    *
    * @return json
    */
    public function postEmentaEntry(Request $request)
    {
        if (!((new ActiveDirectoryController)->ADD_EMENTA())) abort(401);

        try
        {
            if (\App\Models\ementatable::where('data', $request->data)->first())
            {
              \App\Models\ementatable::where('data', $request->data)->first()->delete();
            } else {
              $meals = array();
              $locals = array();
  
              $locals[0] = "QSP";
              $locals[1] = "QSO";
  
              $meals[0] = "1REF";
              $meals[1] = "2REF";
              $meals[2] = "3REF";
  
              foreach ($locals as $key => $local) {
                foreach ($meals as $key => $meal)
                {
  
                  if ($local=="QSP") {
                    if ($weekday_number > 6) $qty = ((new checkSettingsTable)->SvcSemanaQSP());
                    else $qty = ((new checkSettingsTable)->SvcFDSemanaQSP());
                  } else {
                    if ($weekday_number > 6) $qty = ((new checkSettingsTable)->SvcSemanaQSO());
                    else $qty = ((new checkSettingsTable)->SvcFDSemanaQSO());
                  }
  
                  $novoPedido = new \App\Models\pedidosueoref;
                  $novoPedido->quantidade = $qty;
                  $novoPedido->local_ref = $local;
                  $novoPedido->data_pedido = $request->data;
                  $novoPedido->meal = $meal;
                  $novoPedido->registeredByNIM = Auth::user()->id . "@System";
                  $novoPedido->motive = "(AUTO)PESSOAL DE SERVIÇO";
                  $novoPedido->save();
  
                }
              }
            }

            foreach ($request->all() as $item)
            {
                if ($item == null || $item == "Leite, Café, Chá, Muesli, Corn Flakes, Queijo, Fiambre, Paio, Doce, Manteiga, Iogurte" || str_contains($item, "#") || str_contains($item, "=D"))
                {
                    throw new \Exception("Por favor confirme a ementa!");
                }
            }

            $newRef = new \App\Models\ementatable;
            $newRef->data = $request->data;
            $newRef->sopa_almoço = $request->almoço_sopa;
            $newRef->prato_almoço = $request->almoço_prato;
            $newRef->sobremesa_almoço = $request->almoço_sobremesa;
            $newRef->sopa_jantar = $request->jantar_sopa;
            $newRef->prato_jantar = $request->jantar_prato;
            $newRef->sobremesa_jantar = $request->jantar_sobremesa;
            $newRef->created_by = Auth::user()->id;
            $newRef->save();

            $weekday_number = date('N', strtotime($request->data));
            $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
            $mes_index = date('m', strtotime($request->data));
            $date = date('d', strtotime($request->data)).' '.$mes[($mes_index - 1)];

            $notifications = new notificationsHandler;
            $notifications->new_notification(
            /*TITLE*/
            'Nova ementa',
            /*TEXT*/
            'Foi publicada a ementa para dia ' . $date . '.',
            /*TYPE*/
            'NORMAL',
            /*GERAL*/
            'HELPDESK,ADMINS,SUPERS,USERS',
            /*TO USER*/
            '',
            /*CREATED BY*/
            'NEW EMENTA @' . Auth::user()->id, $request->data);
          
          return response()->json('success', 200);

        }
          catch(\Exception $e)
        {
            return response()->json($e->getMessage() , 200);
        }
    }

    /**
    * Atualiza a ementa de almoço de um dia
    * @param Request $request
    *
    * @return json
    */
    public function updateAlmoço(Request $request)
    {
        try {
          $data = $request['data'];

          $id = $data[1]['value'];
          $ref_soup = $data[2]['value'];
          $ref_plate = $data[3]['value'];
          $ref_dessert = $data[4]['value'];
          $ref = \App\Models\ementatable::where('id', $id)->first();

          $__date = $ref['data'];
          $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
          $mes_index = date('m', strtotime($__date));

          if (($ref_soup == $ref->sopa_almoço) && ($ref_plate == $ref->prato_almoço) && ($ref_dessert == $ref->sobremesa_almoço)) {
            return response()->json('same', 200);
          }

          $ref->sopa_almoço = $ref_soup;
          $ref->prato_almoço = $ref_plate;
          $ref->sobremesa_almoço = $ref_dessert;
          $ref->edited_by = Auth::user()->id;
          $ref->save();

          $data['data_alteracao'] = date('d', strtotime($__date)).' '.$mes[($mes_index - 1)];

          $notifications = new notificationsHandler;
          $notifications->new_notification(
            /*TITLE*/
          'Alteração de ementa',
          /*TEXT*/
          'O almoço para o dia ' . $data['data_alteracao'] . ' foi alterado.',
          /*TYPE*/
          'NORMAL',
          /*GERAL*/
          'HELPDESK,ADMINS,SUPERS,USERS',
          /*TO USER*/
          '', /*CREATED BY*/
          'SYSTEM: UPDATE ALMOÇO @' . Auth::user()->id, $ref->data);
          $users = \App\Models\User::where('user_permission', 'ALIM')
            ->orWhere('user_permission', 'LOG')
            ->orWhere('user_permission', 'TUDO')
            ->orWhere('user_permission', 'CCS')
            ->get();

          $data['by'] = Auth::user()->id . ' ' . Auth::user()->posto . ' ' . strtoupper(Auth::user()->name);
          foreach ($users as $key => $usr) {
            try {
              $EMAIL = $usr['email'];
              if ($EMAIL!=null) {
                $data['posto'] = $usr['posto'];
                $data['nome'] = strtoupper($usr['name']);
                Mail::to($usr['id'])->send(new ementaChanged($data));
              }
            } catch (\Exception $e) { continue; }
          }
          return response()->json('success', 200);
        } catch (\Exception $e) {
          return response()->json($e->getMessage(), 200);
        }
    }


    /**
    * Atualiza a ementa de jantar de um dia
    * @param Request $request
    *
    * @return json
    */
    public function updateJantar(Request $request)
    {
      try {
        $data = $request['data'];

        $id = $data[1]['value'];
        $ref_soup = $data[2]['value'];
        $ref_plate = $data[3]['value'];
        $ref_dessert = $data[4]['value'];

          $ref = \App\Models\ementatable::where('id', $id)->first();
          $__date = $ref['data'];
          $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
          $mes_index = date('m', strtotime($__date));

          if (($ref_soup == $ref->sopa_jantar) && ($ref_plate == $ref->prato_jantar) && ($ref_dessert == $ref->sobremesa_jantar)) {
            return response()->json('same', 200);
          }

          $ref->sopa_jantar = $ref_soup;
          $ref->prato_jantar = $ref_plate;
          $ref->sobremesa_jantar = $ref_dessert;
          $ref->edited_by = Auth::user()->id;
          $ref->save();

          $data['data_alteracao'] = date('d', strtotime($__date)).' '.$mes[($mes_index - 1)];

          $notifications = new notificationsHandler;
          $notifications->new_notification(
            /*TITLE*/
          'Alteração de ementa',
          /*TEXT*/
          'O jantar para o dia ' . $data['data_alteracao']  . ' foi alterado.',
          /*TYPE*/
          'NORMAL',
          /*GERAL*/
          'HELPDESK,ADMINS,SUPERS,USERS',
          /*TO USER*/
          '',
          /*CREATED BY*/
          'SYSTEM: UPDATE JANTAR @' . Auth::user()->id, $ref->data);
          $users = \App\Models\User::where('user_permission', 'ALIM')
            ->orWhere('user_permission', 'LOG')
            ->orWhere('user_permission', 'CCS')
            ->orWhere('user_permission', 'TUDO')
            ->get();

          $data['by'] = Auth::user()->id . ' '. Auth::user()->posto .' ' . strtoupper(Auth::user()->name);
          foreach ($users as $key => $usr) {
            try {
              $EMAIL = $usr['email'];
              if ($EMAIL!=null) {
                $data['posto'] = $usr['posto'];
                $data['nome'] = strtoupper($usr['name']);
                Mail::to($usr['id'])->send(new ementaChanged($data));
              }
            } catch (\Exception $e) { continue; }
          }
          return response()->json('success', 200);
      } catch (\Exception $e) {
        return response()->json($e->getMessage(), 200);
      }

    }

    /**
    * Publica a ementa a partir da view já formatada com informação do ficheiro Excel.
    * @param Request $request
    *
    * @return redirect
    */
    public function CreateEmentaEntry(Request $request){
      if (!((new ActiveDirectoryController)->ADD_EMENTA())) abort(401);

      if (\App\Models\ementatable::where('data', $request->date)->first())
      {
        \App\Models\ementatable::where('data', $request->date)->first()->delete();
      } else {
        
      $meals = array();
      $locals = array();

      $locals[0] = "QSP";
      $locals[1] = "QSO";

      $meals[0] = "1REF";
      $meals[1] = "2REF";
      $meals[2] = "3REF";

      foreach ($locals as $key => $local) {
        foreach ($meals as $key => $meal)
        {

          if ($local=="QSP") {
            if ($weekday_number != 6 || $weekday_number != 0) $qty = ((new checkSettingsTable)->SvcSemanaQSP());
            else $qty = ((new checkSettingsTable)->SvcFDSemanaQSP());
          } else {
            if ($weekday_number != 6 || $weekday_number != 0) $qty = ((new checkSettingsTable)->SvcSemanaQSO());
            else $qty = ((new checkSettingsTable)->SvcFDSemanaQSO());
          }

          $novoPedido = new \App\Models\pedidosueoref;
          $novoPedido->quantidade = $qty;
          $novoPedido->local_ref = $local;
          $novoPedido->data_pedido = $request->date;
          $novoPedido->meal = $meal;
          $novoPedido->registeredByNIM = Auth::user()->id . "@System";
          $novoPedido->motive = "(AUTO)PESSOAL DE SERVIÇO";
          $novoPedido->save();

        }
      }
      }

      $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
      $mes_index = date('m', strtotime($request->date));
      $date = date('d', strtotime($request->date)).' '.$mes[($mes_index - 1)];

      $newRef = new \App\Models\ementatable;
      $newRef->data = $request->date;
      $newRef->sopa_almoço = $request->SopaAlm;
      $newRef->prato_almoço = $request->PratoAlm;
      $newRef->sobremesa_almoço = $request->SobremesaAlm;
      $newRef->sopa_jantar = $request->SopaJantar;
      $newRef->prato_jantar = $request->PratoJantar;
      $newRef->sobremesa_jantar = $request->SobremesaJantar;
      $newRef->created_by = Auth::user()->id;
      $newRef->save();

      $notifications = new notificationsHandler;
      $notifications->new_notification(
        /*TITLE*/
        'Nova ementa',
        /*TEXT*/
        'Foi publicada a ementa para dia ' . $date . '.',
        /*TYPE*/
        'NORMAL',
        /*GERAL*/
        'HELPDESK,ADMINS,SUPERS,USERS',
        /*TO USER*/
        '',
        /*CREATED BY*/
        'NEW EMENTA @' . Auth::user()->id, $request->date
      );

      return redirect()->route('gestao.ementa.index')->with(
        'message', 'Foi publicada esta entrada de ementa.'
      );
    }

    /**
    * Carrega ficheiro Excel, lê e formata informação, preenche view com dados e devolve.
    * @param Request $request
    *
    * @return View
    */
    public function newEmenta(Request $request)
    {
        if (!((new ActiveDirectoryController)->ADD_EMENTA())) abort(401);
        $ementaArr = Excel::toArray([], $request['customFile']);
        $date = $ementaArr[0][12][0];
        $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date))->format('Y-m-d');
        // GUARDAR UMA COPIA
        $filename = "EMENTA DE " . $date . " A " . Carbon::parse($date)->addDays(6)->format('Y-m-d') . '.xls';
        $filepath = Storage::disk('ementa_uploads')->putFileAs('ementas', $request->file('customFile') , $filename);
        $pathstring = '/filesys/' . $filepath;
        $file = Storage::disk('ementa_uploads')->getDriver()
            ->getAdapter()
            ->getPathPrefix() . "ementas\\" . $filename;
        // Converter para PDF
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
        $spreadsheet = $reader->load($file);
        $filePDFName = "EMENTA DE " . $date . " A " . Carbon::parse($date)->addDays(6)
            ->format('Y-m-d') . ".pdf";
        $pdf_path = Storage::disk('ementa_uploads')->get("ementas\\pdf") . $filePDFName;
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Dompdf');
        $writer->save($pdf_path);
        $newCopyFile = public_path("filesys\\ementas\pdf\\") . $filePDFName;
        if (!copy($pdf_path, $newCopyFile)) abort(500);
        $almoçoSopaIndex = 9;
        $almoçoPratoIndex = 10;
        $almoçoSobremesaIndex = 9;
        $jantarSopaIndex = 12;
        $jantarPratoIndex = 13;
        $jantarSobremesaIndex = 12;

        for ($i = 1;$i < 8;$i++)
        {
            $ementaEntry[$date]['id'] = 'ENTRY' . $i;
            $ementaEntry[$date]['pequeno']['ref'] = "1REF";
            $ementaEntry[$date]['almoço']['id'] = 'ALMOÇO' . $i;
            $ementaEntry[$date]['almoço']['data'] = $date;
            $ementaEntry[$date]['almoço']['ref'] = "2REF";
            $ementaEntry[$date]['almoço']['sopa'] = ucwords($ementaArr[0][$almoçoSopaIndex][3]);
            $ementaEntry[$date]['almoço']['prato'] = ucwords($ementaArr[0][$almoçoPratoIndex][3]);
            $ementaEntry[$date]['almoço']['sobremesa'] = ucwords($ementaArr[0][$almoçoSobremesaIndex][4]);
            $ementaEntry[$date]['jantar']['id'] = 'JANTAR' . $i;
            $ementaEntry[$date]['jantar']['data'] = $date;
            $ementaEntry[$date]['jantar']['ref'] = "3REF";
            $ementaEntry[$date]['jantar']['sopa'] = ucwords(\Str::contains($ementaArr[0][$jantarSopaIndex][3], "=D") ? $ementaArr[0][$almoçoSopaIndex][3] : $ementaArr[0][$jantarSopaIndex][3]);
            $ementaEntry[$date]['jantar']['prato'] = ucwords($ementaArr[0][$jantarPratoIndex][3]);
            $ementaEntry[$date]['jantar']['sobremesa'] = ucwords($ementaArr[0][$jantarSobremesaIndex][4]);

            $date = Carbon::parse($date)->addDays(1)->format('Y-m-d');
            $almoçoSopaIndex = $almoçoSopaIndex + 6;
            $almoçoPratoIndex = $almoçoPratoIndex + 6;
            $almoçoSobremesaIndex = $almoçoSobremesaIndex + 6;
            $jantarSopaIndex = $jantarSopaIndex + 6;
            $jantarPratoIndex = $jantarPratoIndex + 6;
            $jantarSobremesaIndex = $jantarSobremesaIndex + 6;

            // CORREÇÃO FIM-DE-SEMANA
            // SABADO
            if ($jantarSopaIndex == 42)       $jantarSopaIndex--;
            if ($jantarPratoIndex == 43)      $jantarPratoIndex--;
            if ($jantarPratoIndex == 43)      $jantarPratoIndex--;
            if ($jantarSobremesaIndex == 42)  $jantarSobremesaIndex--;

            // DOMINGO
            if ($almoçoSopaIndex == 45)       $almoçoSopaIndex--;
            if ($almoçoPratoIndex == 46)      $almoçoPratoIndex--;
            if ($almoçoSobremesaIndex == 45)  $almoçoSobremesaIndex--;
            if ($jantarSopaIndex == 47)       $jantarSopaIndex--;
            if ($jantarPratoIndex == 48)      $jantarPratoIndex--;
            if ($jantarSobremesaIndex == 47)  $jantarSobremesaIndex--;

        }
        return view('gestao.ref_review', ['ementaRever' => $ementaEntry, 'pdfFile' => $filePDFName]);
    }
}
