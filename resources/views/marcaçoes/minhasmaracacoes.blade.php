@extends('layout.master')
@section('title','Marcações')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Marcações</li>
<li class="breadcrumb-item active">Minhas marcações</li>
@endsection
@section('page-content')
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      <div class="card-header border-0">
         <h3 class="card-title">Minhas marcações</h3>
         <div class="card-tools">
            @if (!$MARCACOES_A_DINHEIRO && Auth::user()->lock=="N")
              <a href="{{route('marcacao.index')}}">Marcar refeições &nbsp <i class="fas fa-calendar-plus">&nbsp</i></a>
            @endif
         </div>
      </div>
      <div class="card-body">
         @if (Auth::user()->lock=="N")
         @if (!$MARCACOES_A_DINHEIRO)
         @if (!empty($marcaçoes))
         <table class="table projects">
            <thead>
               <tr>
                  <th>
                     Data
                  </th>
                  <th>
                     Refeição
                  </th>

                  <th style="width: 40%">
                     Ementa
                  </th>
                  <th>
                     Local
                  </th>

               </tr>
            </thead>
            <tbody>
               @php
               $today = date("Y-m-d");
               $skip=0;
               @endphp
               @foreach($marcaçoes as $key => $marcaçao)
               @php
                 $__isUser = (Auth::user()->user_type!="ADMIN" && Auth::user()->user_type!="POC");
                 $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                 $semana  = array("Segunda-Feira","Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                 $mes_index = date('m', strtotime($marcaçao['data_marcacao']));
                 $weekday_number = date('N',  strtotime($marcaçao['data_marcacao']));
                 $refeiçaoEmMarcação = $ementa[$marcaçao['id']];
                 $__isCivilian = (Auth::user()->posto=="ASS.TEC." || Auth::user()->posto=="ASS.OP." || Auth::user()->posto=="TEC.SUP." || Auth::user()->posto == "ENC.OP." || Auth::user()->posto == "TIA" || Auth::user()->posto == "TIG.1" || Auth::user()->posto == "TIE");
                 if ($weekday_number==6 || $weekday_number==7) $__isWeekend = true;
                 else  $__isWeekend = false;
               @endphp
               <tr id="tag{{$marcaçao['id']}}">
                  @php
                    $next_key_0 =$key+2;
                    $next_key_1 =$key+1;
                    $next_key_0_exits = array_key_exists($next_key_0, $marcaçoes);
                    $next_key_1_exits = array_key_exists($next_key_1, $marcaçoes);
                  @endphp

                  @if ($skip==0)
                    <td class="project_progress"
                      @if ($next_key_0_exits && ($marcaçoes[$next_key_0]['data_marcacao']==$marcaçao['data_marcacao']))
                        rowspan="3" @php $skip = 3 @endphp
                      @elseif ($next_key_1_exits && ($marcaçoes[$next_key_1]['data_marcacao']==$marcaçao['data_marcacao']))
                        rowspan="2" @php $skip = 2 @endphp
                      @endif
                    >
                       <p class="p_noMargin">
                          <strong>
                          {{ date('d', strtotime($marcaçao['data_marcacao'])) }}
                          {{ $mes[($mes_index - 1)] }}
                          </strong><br>
                          <span style="font-size: .85rem;">{{ $semana[($weekday_number -1)] }}</span>
                       </p>
                    </td>
                  @endif
                  @if ($skip!=0)
                    @php
                      $skip--;
                    @endphp
                  @endif

                  <td>
                     <p class="p_noMargin">
                        @if($marcaçao['meal']=='1REF' )
                           Pequeno-almoço
                        @elseif($marcaçao['meal']=='3REF' )
                           Jantar
                           @if($marcaçao['dieta']=='Y')
                              <span style="font-size: .9rem; color: #69c0a6;">(Dieta)</span>
                           @endif
                        @else
                           Almoço
                           @if($marcaçao['dieta']=='Y')
                              <span style="font-size: .9rem; color: #69c0a6;">(Dieta)</span>
                           @endif
                        @endif
                     </p>
                  </td>
                  <td>
                     @if($marcaçao['meal']!='1REF' )
                     <p class="p_noMargin">
                        Prato: <b>
                        @if($marcaçao['meal']=='3REF' ){{ $refeiçaoEmMarcação['prato_jantar'] }}@else {{ $refeiçaoEmMarcação['prato_almoço'] }}
                        @endif</b>
                     </p>
                     <small>
                        <p class="p_noMargin">
                           Sopa: <b>
                           @if($marcaçao['meal']=='3REF' ){{ $refeiçaoEmMarcação['sopa_jantar'] }}@else {{ $refeiçaoEmMarcação['sopa_almoço'] }}
                           @endif</b>
                        </p>
                        <p class="p_noMargin">
                           Sobremesa: <b>
                           @if($marcaçao['meal']=='3REF' ){{ $refeiçaoEmMarcação['sobremesa_jantar'] }}@else {{ $refeiçaoEmMarcação['sobremesa_almoço'] }}
                           @endif</b>
                        </p>
                     </small>
                     @endif
                  </td>
                  <td id="local_ref{{$marcaçao['id']}}">
                     {{ \App\Models\locaisref::where('refName', $marcaçao['local_ref'])->first()->value('localName')}}
                  </td>
                  <td class="project-actions text-right" style="padding: 1rem !important; padding-right: 0 !important;">

                     @php $maxdate = date("Y-m-d", strtotime("-".$maxDays." days", strtotime($marcaçao['data_marcacao']))); @endphp
                       @if($today<=$maxdate)
                         <form method="POST" action="{{route('marcacao.destroy')}}" >
                           @csrf
                           <input type="hidden" id="id" name="id" value="{{$marcaçao['id']}}"></input>
                           <button type="button" class="btn btn-sm btn-danger remove-ref-btn" onclick="DeletePost('{{$marcaçao['id']}}');"><i class="fas fa-trash"></i>&nbsp;Remover</button>
                         </form>
                       @else
                        <h6 class="marc-poc-info-not slide-in-blurred-top" style="width: 90% !important;">
                           Data ultrapassada
                        </h6>
                       @endif

                       @php $maxdate_change = date("Y-m-d", strtotime("-1 days", strtotime($marcaçao['data_marcacao']))); @endphp
                       @if($today<=$maxdate_change)
                         <form method="POST" action="{{route('marcacao.change')}}" style="margin-top: 5px;">
                           @csrf
                           <input type="hidden" id="id" name="id" value="{{$marcaçao['id']}}"></input>
                           <div class="btn-group remove-ref-btn">
                               <button type="button" style="width: 130px !important; height: 31px !important;" class="btn btn-sm btn-primary dropdown-toggle dropdown-icon" data-toggle="dropdown"
                                  aria-expanded="false">
                                  Alterar local&nbsp&nbsp&nbsp
                                  <span class="sr-only" style="">Toggle Dropdown</span>
                                  <div class="dropdown-menu" role="menu" style="tranform: translate3d(0rem, 30px, 0px) !important;">
                                     @foreach ($locais as $key => $local)
                                     @if ($local['ref']!=Auth::user()->localRefPref)
                                          <a class="dropdown-item @if($local['estado']=="NOK") disabled-drop @endif"
                                          @if($local['estado']!="NOK" ) onclick="changeLocalAndPost('{{$local['ref']}}', '{{ $marcaçao['id'] }}')"@endif>{{$local['nome']}}
                                          </a>
                                       @else
                                          <a class="dropdown-item @if($local['estado']=="NOK") disabled-drop @endif"
                                          @if($local['estado']!="NOK" )onclick="changeLocalAndPost('{{$local['ref']}}', '{{ $marcaçao['id'] }}')"@endif><b>Preferencial&nbsp&nbsp</b>{{$local['nome']}}
                                          </a>
                                     @endif
                                     @endforeach
                                  </div>
                               </button>
                            </div>
                         </form>
                       @endif

                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
         @else
         <h6>Você não tem nenhuma marcação.<br />Carregue <a href="{{route('marcacao.index')}}">aqui</a> para fazer marcações.</h6>
         @endif
          @else
            <p>Você não pode fazer marcações, a sua conta foi marcada para <strong>refeições a dinheiro</strong>.</p>
          @endif
         @else
         <p>A sua conta encontra-se <strong>bloqueada</strong>.</p>
         @endif
      </div>
      <!-- /.card-body -->
   </div>
   <!-- /.card -->
</div>
@endsection

@section('extra-scripts')
<script>
   function changeLocalAndPost(toWhere, tag_id) {
      $.ajax({
         url: "{{route('marcacao.change')}}",
         type: "POST",
         data: {
            "_token": "{{ csrf_token() }}",
            id: tag_id,
            to: toWhere,
         },
         success: function(response) {
            if (response) {
               if (response == 'error') {
                  document.getElementById("errorAddingTitle").innerHTML = "Erro";
                  document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro a alterar o local de refeição.";
                  $("#errorAddingModal").modal()
               } else {
                var local_tag = "local_ref" + tag_id;
                 document.getElementById(local_tag).innerHTML = response;
                 $(document).Toasts('create', {
                    title: "Alterado",
                    subtitle: "",
                    body: "O local da refeição foi alterado para <b>" + response + "</b>.",
                    icon: "fas fa-book",
                    autohide: true,
                    autoremove: true,
                    delay: 3500,
                    class: "toast-not",
                 });
               }
            }
         }
      });
   }
</script>
<script>
   function DeletePost(tag_id){
      $.ajax({
         url: "{{route('marcacao.destroy')}}",
         type: "POST",
         data: {
            "_token": "{{ csrf_token() }}",
            id: tag_id,
         },
         success: function(response) {
            if (response) {
               if (response != 'success') {
                  document.getElementById("errorAddingTitle").innerHTML = "Erro";
                  document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro a remover a marcação.<br>"+response;
                  $("#errorAddingModal").modal()
               } else {
                  var marc_entry = "#tag" + tag_id;
                  $(marc_entry).remove();
                  $(document).Toasts('create', {
                     title: "Removida",
                     subtitle: "",
                     body: "A marcação foi removida com sucesso.",
                     icon: "fas fa-calendar-times",
                     autohide: true,
                     autoremove: true,
                     delay: 3500,
                     class: "toast-not",
                  });
               }
            }
         }
      });
   }
</script>
@endsection
