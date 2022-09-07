@extends('layout.master')
@section('title','Estatisticas de consumo')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Estatisticas por Unidade</li>
@endsection

@section('extra-links')
<link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
@endsection

@section('page-content')

@if(isset($data) && $data==false)

<div class="modal puff-in-center" id="reportModalAlt" tabindex="-1" role="dialog" aria-labelledby="reportModalAlt" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="overlay fade-in-fwd" id="reportModalOverlayAlt">
            <i class="fas fa-2x fa-sync fa-spin"></i>
         </div>
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Obter dados</h5>
         </div>
         <form id="reportFormAlt" name="reportFormAlt" method="POST" action="{{ route('gestão.AdminUnitLoad') }}">
            <div class="modal-body">
               <!-- POR TIME PERIOD -->
               @csrf
               <input type="hidden" id="date" name="date" >
                 <div class="form-group row" id="" name="">
                   <label for="reportLocalSelect" class="col-sm-5 col-form-label">Unidade</label>
                   <div class="col-sm-7">
                      <select autofocus required class="custom-select" name="unitSelect" id="unitSelect">
                        <option selected disabled value="0">Selecione uma unidade</option>
                        @foreach ($unidades as $key => $unit)
                          <option value="{{ $unit['slug'] }}">{{ $unit['name'] }}</option>
                        @endforeach
                      </select>
                   </div>
                 </div>
                 <div class="form-group row" id="customTimeInput2" name="customTimeInput2">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Selecione a data</label>
                  <div class="col-sm-7">
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text">
                           <i class="far fa-calendar-alt"></i>
                           </span>
                        </div>
                        <input type="text" autocomplete="off" class="form-control float-right" id="dateRangePicker2" data-toggle="dateRangePicker2" data-target="#dateRangePicker2">
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="generateBtnAlt"  style="width: 6rem;display: none;">Gerar</button>
               <a href="{{ url()->previous() }}">
                  <button type="button" class="btn btn-dark" >Voltar atrás</button>
               </a>
            </div>
         </form>
      </div>
   </div>
</div>

<div style="display: none; width: 100%; margin-bottom: 1rem; padding: 0.5rem;" id="toggleText">
   <h5>
      A obter informação...
   </h5>
</div>

@else

  @php
   $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
   $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
   $mes_index = date('m', strtotime($date));
  @endphp

   <a id="back-to-top" href="#" onclick="printToFile()" class="btn btn-primary back-to-top elevation-4 tooltip" role="button" aria-label="Save to file">
      <i class="fas fa-print"></i>
      <span class="tooltiptext3">Página para impressão</span>
   </a>

   <a id="choose-new" style="margin-bottom: 3rem;" href="{{ route('gestão.AdminUnit')}}"  class="btn btn-primary back-to-top elevation-4 tooltip" role="button" aria-label="Escolher novo periodo;">
      <i class="fas fa-redo-alt"></i>
      <span class="tooltiptext2">Nova seleção</span>
   </a>

   <div style="display: inline; width: 100%; padding: 0.5rem; padding-bottom: 0;">
      <h5>
         <b>{{date('d', strtotime($date)) }} {{ $mes[($mes_index - 1)] }}</b><br />
      </h5>
   </div>

   <div style="display: inline; width: 100%; margin-bottom: 1rem; padding: 0.5rem; padding-top: 0;">
     <h5><b>{{ $unit['name'] }}</b></h5>
   </div>

   <div class="col-md-12" style="margin-bottom: 3rem;">
      <div class="card card-dark">
         <div class="card-body" style="max-height: none !important;">
            <table class="table table-hover projects" style="margin: 0.6rem !important;">
               <thead>
                  <tr>
                     <th style="width: 10rem;">UTILIZADORES COM MARCAÇÃO</th>
                     <th style="width: 2rem;"></th>
                     <th style="width: 50px;">1ºRefeição</th>
                     <th style="width: 50px;">2ºRefeição</th>
                     <th style="width: 50px;">3ºRefeição</th>
                  </tr>
               </thead>
               <tbody>
               @foreach($marcadas as $key => $entry)
                  @if(is_array($entry))
                    <tr >
                      <td>
                        @php
                          $temp_id = strval($entry['NIM']);
                          while ((strlen((string)$temp_id)) < 8) {
                            $temp_id = 0 . (string)$temp_id;
                          }

                          $filename = "assets/profiles/".$temp_id . ".PNG";
                          $filename_jpg = "assets/profiles/".$temp_id . ".JPG";
                        @endphp
                        @if (file_exists(public_path($filename)))
                           <a href="{{ route('user.profile',  $entry['NIM']) }}">
                            <div style="display: inline-block; width: 5rem;">
                              <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;" />
                            </div>
                          </a>
                        @elseif (file_exists(public_path($filename_jpg)))
                          <a href="{{ route('user.profile',  $entry['NIM']) }}">
                             <div style="display: inline-block; width: 5rem;">
                               <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename_jpg) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;">
                             </div>
                           </a>
                        @else

                         @if($entry['POSTO']=="ASS.TEC." || $entry['POSTO']=="ASS.OP." || $entry['POSTO']=="TEC.SUP."||
                               $entry['POSTO'] == "ENC.OP." || $entry['POSTO'] == "TIA" ||$entry['POSTO'] == "TIG.1" || $entry['POSTO'] == "TIE")
                               @php
                                  $filename = "assets/icons/CIVIL.PNG";
                               @endphp
                          @else
                              @php
                                 $filename = "assets/icons/MILITAR.PNG";
                              @endphp

                          @endif
                            <a href="{{ route('user.profile',  $entry['NIM']) }}">
                            <div style="display: inline-block; width: 5rem;">
                              <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;">
                            </div>
                          </a>
                        @endif

                        <span style="padding: 0.75rem; display: inline-block; vertical-align: top;">
                          <a style="font-size: 1.15rem !important;" href="{{ route('user.profile', $entry['NIM']) }}">
                            <h6 style="display: inline; margin-top: 1rem;">{{ $entry['NAME'] }} (<span class="text-muted text-center text-sm">{{ $entry['NIM'] }}</span>)</h6>
                          </a>
                        </span>

                      </td>
                      <td class="uppercase-only">
                        <span style="padding: 1rem; padding-left: .25rem; padding-right: 1.5rem;">
                          @if ($entry['POSTO'] != "ASS.TEC." && $entry['POSTO'] != "ASS.OP." && $entry['POSTO'] != "TEC.SUP." && $entry['POSTO'] != "SOLDADO" && $entry['POSTO'] != "ENC.OP" && $entry['POSTO'] != "TEC.SUP"&& $entry['POSTO'] != "TIA" && $entry['POSTO'] != "TIG.1"&& $entry['POSTO'] != "TIE" && $entry['POSTO'] != "")
                            @if (Auth::check() && Auth::user()->dark_mode=='Y')
                              @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" .$entry['POSTO'] . ".png"; @endphp
                            @else
                               @php $filename2 = "assets/icons/postos/TRANSPARENT/" .$entry['POSTO'] . ".png"; @endphp
                            @endif
                            <img style="max-width: 3.5rem; object-fit: scale-down;" src="{{ asset($filename2) }}">
                          @else
                           <span style="font-size: .75rem;"><strong>{{ $entry['POSTO'] }}</strong></span>
                         @endif
                        </span>
                      </td>

                      <td>
                        <h6 style="margin-bottom: .2rem; font-size: .95rem;">Pequeno-almoço<br /></h6>
                        @if (isset($entry['1REF']['MEAL']))
                            @if($entry['1REF']['CONSUMIDA']=='Y')
                             <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Marcada</span> <br />
                             <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Consumida</span>
                            @else
                            <span style="font-size: .85rem; width: 7rem;" class="badge bg-warning"><strong>Marcada</strong></span> <br />
                              <span style="font-size: .85rem; width: 7rem;" class="badge bg-warning"><strong>Não</strong> consumida</span>
                            @endif
                        @else
                          @if($entry['1REF']['CONSUMIDA']=='Y')
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-danger"><strong>Não</strong> marcada</span>  <br />
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-danger"><strong>Consumida</strong></span>
                          @else
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Não marcada</span>  <br />
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Não consumida</span>
                          @endif
                        @endif
                      </td>

                      <td>
                        <h6 style="margin-bottom: .2rem; font-size: .95rem;">Almoço<br /></h6>
                        @if (isset($entry['2REF']['MEAL']))
                            @if($entry['2REF']['CONSUMIDA']=='Y')
                             <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Marcada</span> <br />
                             <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Consumida</span>
                            @else
                            <span style="font-size: .85rem; width: 7rem;" class="badge bg-warning"><strong>Marcada</strong></span> <br />
                              <span style="font-size: .85rem; width: 7rem;" class="badge bg-warning"><strong>Não</strong> consumida</span>
                            @endif
                        @else
                          @if($entry['2REF']['CONSUMIDA']=='Y')
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-danger"><strong>Não</strong> marcada</span>  <br />
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-danger"><strong>Consumida</strong></span>
                          @else
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Não marcada</span>  <br />
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Não consumida</span>
                          @endif
                        @endif
                      </td>

                      <td>
                        <h6 style="margin-bottom: .2rem; font-size: .95rem;">Jantar<br /></h6>
                        @if (isset($entry['3REF']['MEAL']))
                            @if($entry['3REF']['CONSUMIDA']=='Y')
                             <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Marcada</span> <br />
                             <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Consumida</span>
                            @else
                            <span style="font-size: .85rem; width: 7rem;" class="badge bg-warning"><strong>Marcada</strong></span> <br />
                              <span style="font-size: .85rem; width: 7rem;" class="badge bg-warning"><strong>Não</strong> consumida</span>
                            @endif
                        @else
                          @if($entry['3REF']['CONSUMIDA']=='Y')
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-danger"><strong>Não</strong> marcada</span>  <br />
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-danger"><strong>Consumida</strong></span>
                          @else
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Não marcada</span>  <br />
                           <span style="font-size: .85rem; width: 7rem;" class="badge bg-success">Não consumida</span>
                          @endif
                        @endif
                      </td>

                    </tr>
                  @endif
               @endforeach
               </tbody>
            </table>
         </div>
      </div>

   </div>

@endif
@endsection

@section('extra-scripts')
@if(isset($data) && $data==false)
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/daterangepicker.js')}}"></script>
<script>

var date;

$( document ).ready(function() {
   $('#reportModalAlt').modal({backdrop: 'static', keyboard: false});
   $('#reportModalOverlayAlt').removeClass( "overlay" );
   $('#reportModalOverlayAlt').hide();
});

$('#dateRangePicker2').on('apply.daterangepicker', function(ev, picker) {
   if (picker.startDate.format('YYYY-MM-DD')!=null) {
      date = picker.startDate.format('YYYY-MM-DD');
      $("#generateBtnAlt").css("display", "block");
      $('#date').val(date);
   }
});
var todayDate = new Date().getDate();

@if(strtotime('H:i')>=strtotime('21:00'))
   var maxDate = new Date(new Date().setDate(todayDate));
@else
   var maxDate = new Date(new Date().setDate(todayDate - 1));
@endif

$(document).ready(function() {
     $('#dateRangePicker2').daterangepicker({
       format: 'DD/MM/YYYY',
       autoclose: true,
       viewMode: 'days',
       startDate: moment(),
       showDropdowns: false,
       timePicker: false,
       opens: 'center',
       singleDatePicker: true,
       maxDate: maxDate,
       locale: {
         "daysOfWeek": [
             "D",
             "S",
             "T",
             "Q",
             "Q",
             "S",
             "S"
         ],
         monthNames: [
             "Janeiro",
             "Fevereiro",
             "Março",
             "Abril",
             "Maio",
             "Junho",
             "Julho",
             "Agosto",
             "Setembro",
             "Outubro",
             "Novembro",
             "Dezembro"
         ],
         firstDay: 1
       }
     },
   );
   });

   $("#reportFormAlt").submit(function(e) {
      $('#reportModalOverlayAlt').show();
      $('#reportModalOverlayAlt').addClass( "overlay" );
      $('#toggleText').attr( "style", "display: inline !important;" );
   });


</script>

@else
<script>
   function sleep(milliseconds) {
   const date = Date.now();
   let currentDate = null;
   do {
      currentDate = Date.now();
   } while (currentDate - date < milliseconds);
   }

   function printToFile(){
      let mywindow = window.open('', 'PRINT', 'height=1240,width=1754,top=10,left=100');
      mywindow.document.write(`<html><head><link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
      <link rel="stylesheet" href="{{asset('assets/custom/ionicons.min.css')}}">
      <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
      <link rel="stylesheet" href="{{asset('assets/custom/custom.exp.css')}}">
      <link rel="stylesheet" href="{{asset('assets/custom/SourceSansPro.css')}}">
      <link rel="stylesheet" href="{{asset('assets/custom/custom_print.css')}}">
      <title>Estatisticas de consumo</title>`);
      mywindow.document.write('</head><body>');
      mywindow.document.write(document.getElementById('tarbody').innerHTML);
      mywindow.document.write('</body></html>');
      mywindow.document.close();
      mywindow.focus();
      sleep(5000);
      mywindow.print();
   }
</script>
@endif
@endsection
