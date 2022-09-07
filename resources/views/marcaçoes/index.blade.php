@extends('layout.master')
@section('title','Marcações')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Marcações</li>
<li class="breadcrumb-item active">Marcar refeições</li>
@endsection
@section('page-content')
<div class="modal fade" id="errorAddingModal" tabindex="-1" role="dialog" aria-labelledby="errorAddingModal" aria-hidden="true">
   <div class="modal-dialog" role="document">
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
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      <div class="card-header border-0">
         <h3 class="card-title">Marcar refeição</h3>
         <div class="card-tools">
            <a href="{{route('marcacao.minhas')}}">Rever marcações &nbsp; <i class="fas fa-eye">&nbsp</i></a>
         </div>
      </div>
      <div class="card-body" style="">
         @if (Auth::user()->lock=="N")
         @if (!$MARCACOES_A_DINHEIRO)
         @if(!empty($marcaçoes))
         <table class="table table-striped projects" id="marcainfo">
            <thead>
               <tr>
                  <th style="width: 10%">
                     Data
                  </th>
                  <th style="width: 10%">
                     Refeição
                  </th>
                  <th style="width: 55%">
                     Ementa
                  </th>
                  <th style="width: 10%">
                  </th>
                  <th>
                  </th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  @php
                  $today = date("Y-m-d");
                  $maxdate = date("Y-m-d", strtotime("-".$maxDays." days"));
                  $minDateBefore = date("Y-m-d", strtotime("+".$marcarRefMax." days"));
                  @endphp
                  @foreach($marcaçoes as $refeiçao)

                    @php
                      $weekday_number = date('N',  strtotime($refeiçao['data']));
                      $__isAlmoco = ($refeiçao['meal']=="2REF");
                      $__isUser = (Auth::user()->user_type!="ADMIN" && Auth::user()->user_type!="POC");
                      $__isCivilian = (Auth::user()->posto=="ASS.TEC." || Auth::user()->posto=="ASS.OP."
                        || Auth::user()->posto=="TEC.SUP." || Auth::user()->posto == "ENC.OP." || Auth::user()->posto == "TIA" || Auth::user()->posto == "TIG.1" || Auth::user()->posto == "TIE");
                      if ($weekday_number==6 || $weekday_number==7) $__isWeekend = true;
                      else  $__isWeekend = false;
                    @endphp

                    @if ($__isAlmoco==false || ($__isAlmoco && $__isWeekend) || ($__isAlmoco && $__isCivilian))

                   @if($refeiçao['meal']=="1REF")
                     <td rowspan="3" style="padding: 1.2rem 0 1.2rem 10px !important;">
                        @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($refeiçao['data']));

                        @endphp
                        <strong>
                        {{ date('d', strtotime($refeiçao['data'])) }}
                        {{ $mes[($mes_index - 1)] }}<br>
                        </strong>
                        <span @if($weekday_number=="7" || $weekday_number=="6") style="font-size: .85rem;color: #92b1d1;" @else style="font-size: .85rem;" @endif>{{ $semana[($weekday_number -1)] }}</span>
                     </td>
                  @endif

                  <td style="padding: 1.2rem 0 1.2rem 10px !important;">
                     @if($refeiçao['meal']=="1REF")
                     Pequeno-almoço
                     @elseif($refeiçao['meal']=="3REF")
                     Jantar
                        @if($dieta!=null && $dieta['data_inicio'] <= $refeiçao['data'] && $dieta['data_fim'] >= $refeiçao['data'])
                           <span style="font-size: .9rem; color: #69c0a6;"><br>Dieta</span>
                        @endif
                     @else
                     Almoço
                        @if($dieta!=null && $dieta['data_inicio'] <= $refeiçao['data'] && $dieta['data_fim'] >= $refeiçao['data'])
                           <span style="font-size: .9rem; color: #69c0a6;"><br>Dieta</span>
                        @endif
                     @endif
                  </td>
                  <td>
                     @if($refeiçao['meal']=="2REF" )
                        Sopa: <strong>{{$refeiçao['sopa_almoço']}}</strong> <br>
                        Prato: <strong>{{$refeiçao['prato_almoço']}}</strong> <br>
                        Sobremesa: <strong>{{$refeiçao['sobremesa_almoço']}}</strong>
                     @elseif($refeiçao['meal']=="3REF" )
                        Sopa: <strong>{{$refeiçao['sopa_jantar']}}</strong> <br>
                        Prato: <strong>{{$refeiçao['prato_jantar']}}</strong> <br>
                        Sobremesa: <strong>{{$refeiçao['sobremesa_jantar']}}</strong>
                     @endif
                  </td>
                  <td>
                     @if($refeiçao['marcado']==1)
                        @foreach ($locais as $key => $local)
                           @if ($local['ref']==$refeiçao['local'])
                              <H6 class="text-muted text-sm">{{ $local['nome'] }}</H6>
                           @endif
                        @endforeach
                     @endif
                  </td>
                  <td id="meal{{$refeiçao['data']}}{{$refeiçao['meal']}}">
                     @if($refeiçao['marcado']==1)
                       @php $maxdate = date("Y-m-d", strtotime("-".$maxDays." days", strtotime($refeiçao['data']))); @endphp
                         @if($today<=$maxdate)
                           <form method="POST" action="{{route('marcacao.destroy')}}">
                              @csrf
                              <input type="hidden" id="id" name="id" value="{{$refeiçao['marcacao_id']}}"></input>
                              <button type="button" class="btn btn-sm btn-danger marcar-ref-btn slide-in-blurred-top" onclick="RemoveRef('{{$refeiçao['marcacao_id']}}')"><i class="fas fa-trash"></i>&nbspRemover</button>
                           </form>
                         @else
                           <a class="btn btn-danger btn-sm disabled marcar-ref-btn  slide-in-blurred-top" href="#"><i class="fas fa-ban"></i>&nbspBloqueada</a>
                         @endif
                     @else
                       @if($refeiçao['data']>=$minDateBefore)
                         @php $formid = Str::random(32); @endphp
                         <form method="POST" action="{{route('marcacao.store')}}" id="{{$formid}}" name="{{$formid}}">
                            @csrf
                            <input type="hidden" id="data{{$formid}}" name="data" value="{{$refeiçao['data']}}"></input>
                            <input type="hidden" id="ref{{$formid}}" name="ref" value="{{$refeiçao['meal']}}"></input>
                            <input type="hidden" id="localDeRef{{$formid}}" name="localDeRef" value=""></input>
                            <div class="btn-group marcar-ref-btn">
                               <button type="button" style="width: 130px !important; height: 31px !important;" class="btn btn-sm btn-primary dropdown-toggle dropdown-icon slide-in-blurred-top" data-toggle="dropdown"
                                  aria-expanded="false">
                                  Marcar&nbsp&nbsp&nbsp
                                  <span class="sr-only" style="">Toggle Dropdown</span>
                                  <div class="dropdown-menu dropdown-menu-local" role="menu">
                                     @foreach ($locais as $key => $local)
                                     @if ($local['ref']!=Auth::user()->localRefPref)
                                     <a class="dropdown-item @if($local['estado']=="NOK") disabled-drop @endif"
                                     @if($local['estado']!="NOK" ) onclick="changeLocalAndPost('{{$local['ref']}}', '{{ $formid }}')"@endif>{{$local['nome']}}
                                     </a>
                                     @else
                                     <a class="dropdown-item @if($local['estado']=="NOK") disabled-drop @endif"
                                     @if($local['estado']!="NOK" )onclick="changeLocalAndPost('{{$local['ref']}}', '{{ $formid }}')"@endif><b>Preferencial&nbsp&nbsp</b>{{$local['nome']}}
                                     </a>
                                     @endif
                                     @endforeach
                                  </div>
                               </button>
                            </div>
                         </form>
                       @else
                         <button type="button" class="btn btn-sm btn-warning disabled marcar-ref-btn">Data ultrapassada</button>
                       @endif
                   @endif
                  </td>
                @else
                  @if($refeiçao['meal']=="1REF")
                     <td style="padding: 1.2rem 0 1.2rem 10px !important;">
                     <strong>
                     {{ date('d', strtotime($refeiçao['data'])) }}
                     {{ $mes[($mes_index - 1)] }}<br>
                     </strong>
                     <span style="font-size: .85rem;">{{ $semana[($weekday_number -1)] }}</span>
                     </td>
                  @endif
                  <td style="padding: 1.2rem 0 1.2rem 10px !important;">
                    &nbsp;Almoço
                  </td>
                  <td>
                    Sopa: <strong>{{$refeiçao['sopa_almoço']}}</strong> <br>
                    Prato: <strong>{{$refeiçao['prato_almoço']}}</strong> <br>
                    Sobremesa: <strong>{{$refeiçao['sobremesa_almoço']}}</strong>
                  </td>
                  <td>
                    <H6 class="text-muted text-sm">Marcação por POC</H6>
                    @if($refeiçao['marcado']==1)
                      @foreach ($locais as $key => $local)
                        @if ($local['ref']==$refeiçao['local'])
                        <H6 class="text-muted text-sm">{{ $local['nome'] }}</H6>
                        @endif
                      @endforeach
                    @endif
                  </td>
                  <td>
                  @if($refeiçao['marcado']==1)
                     <h6 class="marc-poc-info slide-in-blurred-top">
                      <i class="fas fa-check"></i>
                     </h6>
                  @else
                     <h6 class="marc-poc-info-not slide-in-blurred-top">
                       <i class="fas fa-times"></i>
                     </h6>
                  @endif

                  </td>
                @endif
               </tr>
               @if($refeiçao['meal']=="3REF" )
               @if(!$loop->last)
               <tr class="marcar-ref-spacer">
               </tr>
               @endif
               @endif
               @endforeach
            </tbody>
         </table>
         @else
         <h6>Nenhuma refeição disponivel para marcação.</h6>
         @endif
         @else
           <p>Você não pode fazer marcações, a sua conta foi marcada para receber as <strong>refeições a dinheiro</strong>.</p>
         @endif
         @else
         <p>A sua conta encontra-se <strong>bloqueada</strong>.</p>
         @endif
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   function changeLocalAndPost(toWhere, formID) {
       var campoID = "localDeRef" + formID;
       var campoData = "data" + formID;
       var refData = "ref" + formID;
       document.getElementById(campoID).value = toWhere.toUpperCase();;
       var data = document.getElementById(campoData).value;
       var ref = document.getElementById(refData).value;
       $.ajax({
           url: "{{route('marcacao.store')}}",
           type: "POST",
           data: {
               "_token": "{{ csrf_token() }}",
               data: data,
               ref: ref,
               localDeRef: toWhere,
           },
           success: function(response) {
               console.log(response);
               if (response) {
                   if (response != 'success') {
                       document.getElementById("errorAddingTitle").innerHTML = "Erro";
                       document.getElementById("errorAddingText").innerHTML = "Erro a fazer marcação.";
                       $("#errorAddingModal").modal()
                   } else {
                      $(document).Toasts('create', {
                        title: "Marcada",
                        subtitle: "",
                        body: "A <b>"+ ref + "</b> para o dia <b>" + data + "</b> foi marcada no <b>" + toWhere + "</b>.",
                        icon: "fas fa-book",
                        autohide: true,
                        autoremove: true,
                        delay: 3500,
                        class: "toast-not",
                     });
                     var content = "#marcainfo";
                      $(content).load(location.href + " " + content);
                   }
               }
           }
       });
   }
</script>

<script>
   function RemoveRef(tag_id) {
       $.ajax({
           url: "{{route('marcacao.destroy')}}",
           type: "POST",
           data: {
            "_token": "{{ csrf_token() }}",
            id: tag_id,
         },
           success: function(response) {
               console.log(response);
               if (response) {
                   if (response != 'success') {
                       document.getElementById("errorAddingTitle").innerHTML = "Erro";
                       document.getElementById("errorAddingText").innerHTML = "Erro a fazer marcação.";
                       $("#errorAddingModal").modal()
                   } else {
                      $(document).Toasts('create', {
                        title: "Marcada",
                        subtitle: "",
                        body: "A marcação foi removida com sucesso.",
                        icon: "fas fa-calendar-times",
                        autohide: true,
                        autoremove: true,
                        delay: 3500,
                        class: "toast-not",
                     });
                    var content = "#marcainfo";
                    $(content).load(location.href + " " + content);
                   }
               }
           }
       });
   }
</script>
@endsection
