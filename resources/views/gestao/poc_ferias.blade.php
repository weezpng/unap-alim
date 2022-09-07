@extends('layout.master')
@section('extra-links')
<link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
@endsection
@section('title','Férias dos utilizadores')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Férias dos utilizadores</li>
@endsection

@section('page-content')
<div class="modal puff-in-center" id="deleteFeriasModal" tabindex="-1" role="dialog" aria-labelledby="deleteFeriasModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="deleteFeriasModalLabel">Remover férias</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.destroyUser')}}" method="POST" enctype="multipart/form-data" id="removeFeriasForm">
            <div class="modal-body">
               <p>Tem a certeza que pretende remover esta entrada de férias?<br>
                  Esta ação é <b>irreversível</b>.
               </p>
               @csrf
               <input type="hidden" id="id_ferias" name="id_ferias" readonly>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-danger">Remover</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="modal puff-in-center" id="errorAddingModal" tabindex="-1" role="dialog" aria-labelledby="errorAddingModal" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="errorAddingTitle" name="errorAddingTitle"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p id="errorAddingText" name="errorAddingText"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>

<div class="modal puff-in-center" id="addVacation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Marcar férias</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestao.ferias.add')}}" method="POST" enctype="multipart/form-data" id="addFerias">
            <div class="modal-body">              
               @csrf
               <div class="form-group row" id="customTimeInput" name="customTimeInput">
                  <label for="reportLocalSelect" class="col-sm-3 col-form-label">Datas</label>
                  <div class="col-sm-9">
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text">
                           <i class="far fa-calendar-alt"></i>
                           </span>
                        </div>
                        <input type="text" placeholder="Data de inicio a data de apresentação" required class="form-control float-right" name="dateRangePicker"
                          id="dateRangePicker" data-toggle="dateRangePicker" data-target="#dateRangePicker">

                          <input type="hidden" value="" id="user_id_add" name="user_id">
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Marcar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="col-md-12">
   <div class="card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif card-outline" style="height: auto;">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Consulta</h3>
            <div class="card-tools" style="margin-right: 0 !important; margin-top: 0.4rem !important;">
              <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </div>
         </div>
      </div>
      <div class="card-body">
        <div class="ferias_search_parent">
          <div class="form-group row">
              <label for="reportLocalSelect" class="col-sm-2 col-form-label">Procurar utilizador</label>
              <div class="col-sm-5">
                <div class="input-group-append">
                  <input id="userSearchFeriasSearchBar"
                  @if (Auth::user()->dark_mode=='Y')
                    style="background-color: #3f474e; border-right-width: 1px; color: white;"
                  @else
                    style="border-right-width: 1px;"
                  @endif name="userSearchFeriasSearchBar" class="form-control form-control-navbar" maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="search" placeholder="NIM" aria-label="NIM">
                </div>
              </div>
           </div>
        </div>
        @foreach ($users as $it => $user)
          @php
              $NIM = $user['NIM'];
              while ((strlen((string)$NIM)) < 8) {
                  $NIM = 0 . (string)$NIM;
              }
          @endphp
        <div class="card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif user-card" id="user{{ $user['NIM'] }}" style="box-shadow: none !important; margin-top: 1rem !important; margin-bottom: 1rem !important; ">
           <div class="card-header border-0">
              <div class="d-flex justify-content-between">
                 <h3 class="card-title">{{$NIM}}&nbsp;{{$user['POSTO']}}&nbsp;{{$user['NOME']}}</h3>                 
              </div>
           </div>
           <div class="card-body" style="padding-left: 1rem !important; padding-right: .5rem !important;" name="{{ $user['POSTO'] }}{{ $user['NIM'] }}">

             @php
               $filename="assets/profiles/" .$NIM . ".JPG" ;
               $filename_png="assets/profiles/" .$NIM . ".PNG" ;
             @endphp

             @if (file_exists(public_path($filename)))
               <img src="{{ asset($filename) }}" class="elevation-2 img-circle" alt="User Image" style="width: 6rem; height: 6rem ; object-fit: cover; margin-left: 0.5rem; margin-top: 0.5rem;">
             @elseif(file_exists(public_path($filename_png)))
               <img src="{{ asset($filename_png) }}" class="elevation-2 img-circle" alt="User Image" style="width: 6rem; height: 6rem ; object-fit: cover; margin-left: 0.5rem; margin-top: 0.5rem;">
             @else

               <img src="https://cpes-wise2/Unidades/Fotos/{{ $NIM }}.jpg" class="elevation-2 img-circle" alt="User Image" style="width: 6rem; height: 6rem ; object-fit: cover; margin-left: 0.5rem; margin-top: 0.5rem;">
             @endif

              <div style="margin: 1.5rem auto; margin-top: 0.5rem;">
               @if($user['POSTO']==NULL)
                  <h6 style="display: inline-block; vertical-align: super;">Posto N/D</h6>
                @elseif ($user['POSTO'] != "ASS.TEC." && $user['POSTO'] != "ASS.OP." && $user['POSTO'] != "TEC.SUP."
                && $user['POSTO'] != "ENC.OP." && $user['POSTO'] != "TIA" && $user['POSTO'] != "TIG.1" && $user['POSTO'] != "TIE" && $user['POSTO'] != "SOLDADO")
                 @if (Auth::check() && Auth::user()->dark_mode=='Y')
                   @php $filename2 = "assets/icons/POSTOs/TRANSPARENT_WHITE/" . $user['POSTO'] . ".png"; @endphp
                 @else
                    @php $filename2 = "assets/icons/POSTOs/TRANSPARENT/" . $user['POSTO'] . ".png"; @endphp
                 @endif
                 <img style="width: 3rem; height: 3rem; object-fit: scale-down; margin-top: -2rem; display: inline-block;" src="{{ asset($filename2) }}">
                @else
                  <h6 style="display: inline-block; vertical-align: super;">{{ $user['POSTO'] }}</h6>
               @endif
               <div style="display: inline-block; width: 50rem; margin-top: .5rem;">
                 <h4 style="line-height: 1.8rem; vertical-align: text-top; margin-left: 0.5rem; margin-bottom: 0;">{{ $user['NOME'] }}
                   <br>
                   <h6 style="margin-left: .5rem; vertical-align: top;">NIM <strong>{{$NIM}}</strong><br></h6>
                 </h4>

               </div>
               <div style="float: right; margin-right: 2rem; margin-top: 1rem;">
                 <button type="submit" style="width: 10rem !important;"  data-toggle="modal" data-target="#addVacation" class="btn smallbtn
                 @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn addVac" data-id="{{$user['NIM']}}">
                   <i class="far fa-calendar-plus">&nbsp&nbsp&nbsp</i>
                   Marcar
                 </button>
               </div>
               <h6 style="margin-top: 1rem;" class="text-muted">{{$user['SECCAO']}}<br>{{$user['DESCRIPTOR']}}</h6>
             </div>
             <div class="row" id="userFerias{{ $user['NIM'] }}">
               @if (isset($user['FERIAS']))
                 @foreach ($user['FERIAS'] as $key => $ferias_entry)
                   <div class="ferias_entry_parent" id="feriasEntry{{ $ferias_entry['id'] }}">
                     <div class="ferias_entry_icon">
                       <i class="fas fa-calendar-alt"></i>
                     </div>
                     <div class="ferias_entry_details">
                       @php
                       $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                       $mes_index_start = date('m', strtotime($ferias_entry['data_inicio']));
                       $mes_index_end = date('m', strtotime($ferias_entry['data_fim']));
                       @endphp
                       <h6 class="text-sm text-mute">Início &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h6><h6 class="text-sm text-mute ferias_entry_spacer"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Apresentação</h6><br>
                       <h5>{{ date('d', strtotime($ferias_entry['data_inicio'])) }}  {{ $mes[($mes_index_start - 1)] }}</h5> &nbsp;
                       <h5 class="ferias_entry_spacer">{{ date('d', strtotime($ferias_entry['data_fim'])) }} {{ $mes[($mes_index_end - 1)] }}</h5>
                     </div>
                     <div class="ferias_entry_button_parent">
                       <button type="button" data-id="{{ $ferias_entry['id'] }}"  data-toggle="modal" data-target="#deleteFeriasModal"
                       class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn ferias_entry_button" style="opacity: 1 !important;">
                         <i class="far fa-calendar-times">&nbsp&nbsp&nbsp</i>
                         Desmarcar
                       </button>
                     </div>
                   </div>
                 @endforeach
               @else
                 <h6>Este utilizador atualmente não tem nenhuma entrada de férias</h6>
               @endif
             </div>
         </div>
       </div>
       @endforeach

     </div>
   </div>
</div>
@endsection

@section('extra-scripts')
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/daterangepicker.js')}}"></script>

<script>
   $(document).on('click','.ferias_entry_button',function(){
        let id = $(this).attr('data-id');
        $('#id_ferias').val(id);
   });
</script>

<script>
   $(document).on('click','.addVac',function(){
        let id = $(this).attr('data-id');
        $('#user_id_add').val(id);
   });
</script>

<script>

var startDate;
var endDate;

   $(document).ready(function() {
  
     $('#dateRangePicker').daterangepicker({
       format: 'DD/MM/YYYY',
       startDate: '{{ $minDay }}',
       separator: " até ",
       showDropdowns: true,
       timePicker: false,
       opens: 'center',
       singleDatePicker: false,
       showRangeInputsOnCustomRangeOnly: false,
       applyClass: 'rangePickerApplyBtn',
       cancelClass: 'rangePickerCancelBtn',
       minDate : '{{ $minDay }}',
       parentEl: $('#addVacation'),
       locale: {
         cancelLabel: 'Limpar',
         applyLabel: 'Aplicar',
         fromLabel: 'INICIO',
         toLabel: 'APRESENTAÇÃO',
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
       function(start, end) {
        startDate = start;
        endDate = end;

       })
    });

$("#removeFeriasForm").submit(function(e) {
      e.preventDefault();
      var ferias_ID = document.getElementById("id_ferias").value;
       $.ajax({
           url: "{{route('gestao.ferias.remove')}}",
           type: "POST",
           data: {
               "_token": "{{ csrf_token() }}",
               id: ferias_ID
           },
           success: function(response) {
               if (response) {
                   if (response != 'success') {
                    document.getElementById("errorAddingTitle").innerHTML = "Erro";
                    document.getElementById("errorAddingText").innerHTML = response;
                    $("#errorAddingModal").modal()
                    $('#deleteFeriasModal').modal('toggle');
                   } else {
                     $('#deleteFeriasModal').modal('toggle');
                     document.getElementById("feriasEntry" + ferias_ID).remove();
                   }
               }
           }
       });
 });

  $("#addFerias").submit(function(e) {
        e.preventDefault();
        var timeframe = startDate.format('D/MM/YYYY') + ' até ' + endDate.format('D/MM/YYYY');
        var user = document.getElementById("user_id_add").value;
         $.ajax({
             url: "{{route('gestao.ferias.add')}}",
             type: "POST",
             data: {
                 "_token": "{{ csrf_token() }}",
                 dateRangePicker: timeframe,
                 user_id: user,
             },
             success: function(response) {
                 if (response) {
                     if (response != 'success') {
                      document.getElementById("errorAddingTitle").innerHTML = "Erro";
                      document.getElementById("errorAddingText").innerHTML = response;
                      $("#errorAddingModal").modal()
                      $('#addVacation').modal('toggle');
                     } else {
                       var divName = "#userFerias" + user;
                       $(divName).load(location.href + " " + divName);
                       $('#addVacation').modal('toggle');
                     }
                 }
             }
         });
   });
</script>

<script>
  $('#userSearchFeriasSearchBar').on("paste keyup",function(){
    $value=$(this).val();
    var elementID = "#user" + parseInt($value, 10);
    if ($(elementID).length > 0) {
      var els = document.getElementsByClassName("user-card");
      for(var i = 0; i < els.length; i++)
      {
        els[i].style.display = 'none';
      }
        $(elementID).toggle();
      } else {
        var els = document.getElementsByClassName("user-card");
        for(var i = 0; i < els.length; i++)
        {
          els[i].style.display = 'block';
        }
      }
  }).delay(500);
  </script>
@endsection
