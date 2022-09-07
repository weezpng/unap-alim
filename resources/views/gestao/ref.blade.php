@extends('layout.master')
@section('title','Gestão de ementa')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Ementa</li>
@endsection
@section('extra-links')
<link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
@endsection
@section('page-content')
@if($ADD_EMENTA)
<div class="modal puff-in-center" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Nova ementa</i></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestao.ementa.novaEmenta')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <div class="custom-file">
                  <input type="file" class="custom-file-input" id="customFile" name="customFile" accept=".xls, .xlsm">
                  <label class="custom-file-label" for="customFile">Carregar ementa (formato EXCEL)</label>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Carregar</button>
               <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="modal puff-in-center" id="tradeDaysModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Trocar ementa</i></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
            <div class="modal-body">
              <h6>Tem a certeza que pretende trocar estas ementas?</h6>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-primary" onclick="completeTrade('2REF');">Almoço</button>
               <button type="button" class="btn btn-primary" onclick="completeTrade('3REF');">Jantar</button>
               <button type="button" class="btn btn-primary" onclick="completeTrade('BOTH');">Ambos</button>
               <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
            </div>
      </div>
   </div>
</div>

<div class="modal puff-in-center" id="newEntry" tabindex="-1" role="dialog" aria-labelledby="newEntryLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Nova entrada de ementa</i></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestao.ementa.create')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <input type="hidden" id="date" name="date" >
               <div class="form-group row" id="customTimeInput2" name="customTimeInput2">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Selecione a data</label>
                  <div class="col-sm-7">
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text">
                           <i class="far fa-calendar-alt"></i>
                           </span>
                        </div>
                        <input type="text" autocomplete="off" required class="form-control float-right" id="dateRangePicker2" data-toggle="dateRangePicker2" data-target="#dateRangePicker2">
                     </div>
                  </div>
               </div>

               <div class="form-group row" id="customTimeInput2" name="customTimeInput2">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Almoço</label>
                  <div class="col-sm-7">
                        <input type="text" autocomplete="off" required class="form-control float-right" id="SopaAlm" name="SopaAlm" placeholder="Sopa">
                  </div>
                  <div class="col-sm-7 offset-sm-5" style="margin-top: .25rem;">
                        <input type="text" autocomplete="off" required class="form-control float-right" id="PratoAlm" name="PratoAlm" placeholder="Prato">
                  </div>
                  <div class="col-sm-7 offset-sm-5" style="margin-top: .25rem;">
                        <input type="text" autocomplete="off" required class="form-control float-right" id="SobremesaAlm" name="SobremesaAlm" placeholder="Sobremesa">
                  </div>
               </div>

               <div class="form-group row" id="customTimeInput2" name="customTimeInput2">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Jantar</label>
                  <div class="col-sm-7">
                        <input type="text" autocomplete="off" required class="form-control float-right" id="SopaJantar" name="SopaJantar" placeholder="Sopa">
                  </div>
                  <div class="col-sm-7 offset-sm-5" style="margin-top: .25rem;">
                        <input type="text" autocomplete="off" required class="form-control float-right" id="PratoJantar" name="PratoJantar" placeholder="Prato">
                  </div>
                  <div class="col-sm-7 offset-sm-5" style="margin-top: .25rem;">
                        <input type="text" autocomplete="off" required class="form-control float-right" id="SobremesaJantar" name="SobremesaJantar" placeholder="Sobremesa">
                  </div>
               </div>

            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Carregar</button>
               <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>

@endif
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Alterar ementa</h3>
            <div class="card-tools">
               @if ($ADD_EMENTA)
               <a href="#" id="newEmentaModalBtn" data-toggle="modal" data-target="#exampleModal">Carregar nova ementa &nbsp; <i class="fas fa-plus-square"></i></a>&nbsp;&nbsp;&nbsp;
               <a href="#" id="PublishNewEmentaEntry" data-toggle="modal" data-target="#newEntry">Nova entrada &nbsp; <i class="fas fa-calendar-plus"></i></a>
               @endif
               &nbsp;&nbsp;&nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
               </button>
            </div>
         </div>
      </div>
      <div class="card-body" style="overflow-y: auto; max-height: 70vh;">
         @if($EDIT_EMENTA)
         @if ($ementaTable)
         <table class="table table-striped projects">
            <thead>
               <tr>
                  <th style="width: 12.5%">
                     DATA
                  </th>
                  <th style="width: 12.5%">
                     REFEIÇÃO
                  </th>
                  <th>
                     EMENTA
                  </th>
               </tr>
            </thead>
            <tbody>
               @php
               $today = date("Y-m-d");
               $maxtdate = date('Y-m-d', strtotime("+15 days"));
               @endphp
               @foreach($ementaTable as $key => $ref)
               @if($ref['data']>=$today && $ref['data']<=$maxtdate)
                 @if($ref['meal']!="1REF" )
               <tr>
                  @if($ref['meal']=="2REF")
                    <td rowspan="2" style="padding: 2rem !important; background-color: #31373d !important;">
                       @php
                       $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                       $semana  = array("Segunda-Feira","Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                       $mes_index = date('m', strtotime($ref['data']));
                       $weekday_number = date('N',  strtotime($ref['data']));
                       @endphp
                       <strong>
                         {{ date('d', strtotime($ref['data'])) }}
                         {{ $mes[($mes_index - 1)] }}
                       </strong>
                       <br>
                       <span style="font-size: .85rem;">{{ $semana[($weekday_number -1)] }}</span>
                    </td>
                  @endif
                  <td style="padding: 2rem !important; background-color: #343a40; !important;">
                    <strong>
                      @if($ref['meal']=="3REF" )
                        Jantar
                      @elseif($ref['meal']=="2REF" )
                        Almoço
                      @endif
                    </strong>
                  </td>
                  <td style="background-color: #343a40; !important;">
                     @if($ref['meal']==="2REF" )
                     <form method="POST" action="{{route('gestao.ementa.update.meal0')}}" style="background-color: #343a40; !important;" id="formal{{$ref['id']}}">
                        @csrf
                        <input type="hidden" class="form-control" value=" {{$ref['id']}}" id="ref_id" name="ref_id">
                        <input type="text" class="form-control" value=" {{$ref['sopa_almoço']}}" id="ref_soup" name="ref_soup" placeholder="Sopa do almoço" style="margin: .5rem 0 .5rem;">
                        <input type="text" class="form-control" value=" {{$ref['prato_almoço']}}" id=ref_plate"" name="ref_plate" placeholder="Prato do almoço" style="margin: .5rem 0 .5rem;">
                        <input type="text" class="form-control" value=" {{$ref['sobremesa_almoço']}}" id="ref_dessert" name="ref_dessert" placeholder="Sobremesa do almoço" style="margin: .5rem 0 .5rem;">
                        <button type="button" onclick="updateAlmoco('{{$ref['id']}}');" class="btn btn-sm btn-primary edit-ementa-btn">Guardar</button>
                     </form>
                     <br>
                     @elseif($ref['meal']==="3REF" )
                     <form method="POST" action="{{route('gestao.ementa.update.meal1')}}"  style="background-color: #343a40; !important;" id="formja{{$ref['id']}}">
                        @csrf
                        <input type="hidden" class="form-control" value=" {{$ref['id']}}" id="ref_id" name="ref_id">
                        <input type="text" class="form-control" value=" {{$ref['sopa_jantar']}}" id="ref_soup" name="ref_soup" placeholder="Sopa do jantar" style="margin: .5rem 0 .5rem;">
                        <input type="text" class="form-control" value=" {{$ref['prato_jantar']}}" id="ref_plate" name="ref_plate" placeholder="Prato do jantar" style="margin: .5rem 0 .5rem;">
                        <input type="text" class="form-control" value=" {{$ref['sobremesa_jantar']}}" id=ref_dessert"" name="ref_dessert" placeholder="Sobremesa do jantar" style="margin: .5rem 0 .5rem;">
                        <button type="button" onclick="updateJantar('{{$ref['id']}}')" class="btn btn-sm btn-primary edit-ementa-btn">Guardar</button>
                     </form>
                     <br>
                     @else
                       Pequeno-almoço
                     @endif
                  </td>
               </tr>
              @endif
               @if($ref['meal']=="3REF" )
               @if(!$loop->last)
               <tr class="editar-ref-spacer">
                 @if(array_key_exists(($key +1), $ementaTable))
                 <td style="border-top: none;background-color: #31373d  !important;"></td>
                 <td style="border-top: none;background-color: #31373d  !important;"></td>
                 <td style="border-top: none;background-color: #31373d  !important;">

                   @php
                    $new_day = date('Y-m-d', strtotime( $ref['data'] . " +1 days"));
                    $mes_2  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                    $semana_2  = array("Segunda-Feira","Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                    $mes_index_2 = date('m', strtotime($new_day));
                    $weekday_number_2 = date('N',  strtotime($new_day));
                   @endphp

                   <button class="btn btn-sm btn-dark tooltip" style="float: right; margin: .5rem; background-color: #31373d !important; position: static !important;" onclick="storeDate(this);"
                            data-date={{ $ref['data'] }} data-toggle="modal" data-target="#tradeDaysModal">
                            <i class="fa-solid fa-arrows-up-down"></i>&nbsp; TROCAR EMENTAS
                            <span class="tooltiptext2" style="left: -55px !important; width: 195px !important;">
                              Trocar as ementas de <b>{{ date('d', strtotime($ref['data'])) }} {{ $mes[($mes_index - 1)] }} </b>
                              para <b>{{ date('d', strtotime($new_day)) }} {{ $mes_2[($mes_index_2 - 1)] }}</b>
                            </span>
                   </button>
                 </td>
                 @endif
               </tr>
               @endif
               @endif
               @endif
               @endforeach
            </tbody>
         </table>
         @else
         Nenhuma ementa publicada
         @endif
         @else
           A sua conta não tem permissões para alterar a ementa.<br>
           Pode no entanto consultar a ementa actual <a href="{{ route('ementa.index') }}">aqui</a>.
         @endif
      </div>
      <!-- /.card-body -->
   </div>
</div>
@endsection
@if($ADD_EMENTA)
@section('extra-scripts')
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script type="text/javascript">
   $(document).ready(function() {
       bsCustomFileInput.init();
   });
</script>

<script>
  function updateAlmoco(id){
    var form_id = "#formal"+id;
    var data = $(form_id).serializeArray();
    $.ajax({
        type: "POST",
        url: "{{route('gestao.ementa.update.meal0')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            data,
        },
        success: function (msg) {
            if (msg != 'success') {
              if (msg == 'same') {
                $(document).Toasts('create', {
                  title: "Sem alteração",
                  subtitle: "",
                  body: "Não efetuou nenhuma alteração nesta ementa.",
                  icon: "fa-solid fa-pen-to-square",
                  autohide: false,
                  autoremove: false,
                  delay: 3500,
                  class: "toast-not",
                });
              } else {
                $(document).Toasts('create', {
                  title: "Erro",
                  subtitle: "",
                  body: "Ocorreu um erro:" + msg,
                  icon: "fa-solid fa-pen-to-square",
                  autohide: false,
                  autoremove: false,
                  delay: 3500,
                  class: "toast-not",
                });
              }
            } else {
              $(document).Toasts('create', {
                title: "Alterada!",
                subtitle: "",
                body: "A ementa foi alterada com sucesso.",
                icon: "fa-solid fa-pen-to-square",
                autohide: true,
                autoremove: true,
                delay: 3500,
                class: "toast-not",
              });
            }
        }
    });
  }
</script>


<script>
  function updateJantar(id){
    var form_id = "#formja"+id;
    var data = $(form_id).serializeArray();
    $.ajax({
        type: "POST",
        url: "{{route('gestao.ementa.update.meal1')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            data,
        },
        success: function (msg) {
            if (msg != 'success') {
              if (msg == 'same') {
                $(document).Toasts('create', {
                  title: "Sem alteração",
                  subtitle: "",
                  body: "Não efetuou nenhuma alteração nesta ementa.",
                  icon: "fa-solid fa-pen-to-square",
                  autohide: false,
                  autoremove: false,
                  delay: 3500,
                  class: "toast-not",
                });
              } else {
                $(document).Toasts('create', {
                  title: "Erro",
                  subtitle: "",
                  body: "Ocorreu um erro:" + msg,
                  icon: "fa-solid fa-pen-to-square",
                  autohide: false,
                  autoremove: false,
                  delay: 3500,
                  class: "toast-not",
                });
              }
            } else {
              $(document).Toasts('create', {
                title: "Alterada!",
                subtitle: "",
                body: "A ementa foi alterada com sucesso.",
                icon: "fa-solid fa-pen-to-square",
                autohide: true,
                autoremove: true,
                delay: 3500,
                class: "toast-not",
              });
            }
        }
    });
  }
</script>

<script>

$('#dateRangePicker2').on('apply.daterangepicker', function(ev, picker) {
   if (picker.startDate.format('YYYY-MM-DD')!=null) {
      date = picker.startDate.format('YYYY-MM-DD');
      $("#generateBtnAlt").css("display", "block");
      $('#date').val(date);
   }
});
var todayDate = new Date().getDate();
var maxDate = new Date(new Date().setDate(todayDate + {{$MAX}}));

var tradeDateTarget;

function storeDate(current){
  tradeDateTarget = $(current).data("date");
}

function completeTrade(meal){
  $("#tradeDaysModal").modal('hide');
  $.ajax({
      type: "POST",
      url: "{{route('gestao.tradeementa.nextday')}}",
      async: true,
      data: {
          "_token": "{{ csrf_token() }}",
          date: tradeDateTarget,
          meal: meal,
      },
      success: function (msg) {
          if (msg != 'success') {
            $(document).Toasts('create', {
              title: "Erro a trocar ementas",
              subtitle: "",
              body: "Ocorreu um erro:" + msg,
              icon: "fa-solid fa-arrows-up-down",
              autohide: false,
              autoremove: false,
              delay: 3500,
              class: "toast-not",
            });
          } else {
            $(document).Toasts('create', {
              title: "Trocadas!",
              subtitle: "",
              body: "As ementas foram trocadas! Recarrege a página para ver as alterações.",
              icon: "fa-solid fa-arrows-up-down",
              autohide: true,
              autoremove: true,
              delay: 3500,
              class: "toast-not",
            });
          }
      }
  });
}


$(document).ready(function() {
     $('#dateRangePicker2').daterangepicker({
       format: 'DD/MM/YYYY',
       autoclose: true,
       viewMode: 'days',
       startDate: maxDate,
       showDropdowns: false,
       timePicker: false,
       opens: 'center',
       singleDatePicker: true,
       minDate: maxDate,
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

   $('#dateRangePicker2').on('apply.daterangepicker', function(ev, picker) {
   if (picker.startDate.format('YYYY-MM-DD')!=null) {
      date = picker.startDate.format('YYYY-MM-DD');
      $("#generateBtnAlt").css("display", "block");
      $('#date').val(date);
   }
});

  @if(Session::has('message'))
   $(document).Toasts('create', {
      title: "Criada",
      subtitle: "",
      body: "{{ session('message') }}",
      icon: "fas fa-calendar-plus",
      autohide: true,
      autoremove: true,
      delay: 3500,
      class: "toast-not",
   });
  @endif
</script>
@endsection
@endif
