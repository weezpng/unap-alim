@extends('layout.master')
@section('extra-links')
<link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
@endsection
@section('title','Perfil')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Utilizadores</li>
<li class="breadcrumb-item active">{{$user['id']}}</li>
@endsection
@section('page-content')
@include('layout.float-btn')
@if ($USERS_NEED_FATUR)
  @if($user['posto']!="ASS.OP." && $user['posto']!="ASS.TEC." && $user['posto']!="TEC.SUP.")
      <div class="modal puff-in-center" id="marcarParaConf" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class=" modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Marcar utilizador</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form action="{{ route('user.tagoblg') }}" method="POST" enctype="multipart/form-data">
                  <div class="modal-body">
                    <p>Marcar este utilizador para confirmar as refeições tomadas para faturação.
                      @if ($user['already_tagged'])
                           Isto irá sobrescrever a entrada actual.
                      @endif

                    </p>

                     @csrf
                     <input type="hidden" name="user_ID" value="{{ $user['id'] }}">
                     <div class="form-group row" id="customTimeInput" name="customTimeInput">
                        <label for="reportLocalSelect" class="col-sm-5 col-form-label">Periodo de tempo</label>
                        <div class="col-sm-7">
                           <div class="input-group">
                              <div class="input-group-prepend">
                                 <span class="input-group-text">
                                 <i class="far fa-calendar-alt"></i>
                                 </span>
                              </div>
                              <input type="text" placeholder="Periodo de tempo" required class="form-control float-right" name="dateRangePicker" id="dateRangePicker" data-toggle="dateRangePicker" data-target="#dateRangePicker">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-warning">Concluir</button>
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
    @endif
  @endif
<div class="modal puff-in-center" id="convertToUser" tabindex="-1" role="dialog" aria-labelledby="newGroupModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Converter para conta</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('childrenUser.convert')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <p>
                  Tem a certeza que pretende continuar?         
               </p>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Converter</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header p-2">
         <div class="card-tools" style="margin-right: .5rem !important; margin-top: 0.4rem !important;">
            <a href="{{ url()->previous() }}">Voltar atrás</a>
         </div>
         <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#info" data-toggle="tab">Informação</a></li>
            <li class="nav-item"><a class="nav-link" href="#marcar" data-toggle="tab">Marcações</a></li>
            @if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP.")
            <li class="nav-item"><a class="nav-link" href="#stats" data-toggle="tab">Confirmações</a></li>
            @endif
            <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Definições</a></li>
         </ul>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
         <div class="tab-content">
            <div class="tab-pane swing-in-left-fwd active" id="info">
               <p>
               <h5>NIM</h5>
               <h6><b>{{$user['id']}}</b></h6>
               </p>
               <p>
               <h5>NOME</h5>
               <h6><b>{{$user['name']}}</b></h6>
               </p>
               <p>
               <h5>POSTO</h5>
               <h6><b>{{$user['posto']}}</b></h6>
               </p>
               <p>
               <h5>GRUPO</h5>
               <h6><b>{{$group}}\ {{$subgroup}}</b></h6>
               </p>
               @if ($user['already_tagged'])
                 <p>
                   <br>
                 <h6><b><i style="color: #d14351" class="fas fa-exclamation mr-1"></i>&nbsp; A receber refeições a dinheiro.</b></h6>
                 </p>
               @endif
            </div>
            <div class="tab-pane swing-in-left-fwd" id="marcar">
               @if($user['already_tagged']==false)
               <table class="table table-striped projects">
                  <thead>
                     <tr>
                        <th style="width: 15%">
                           Data
                        </th>
                        <th style="width: 10%">
                           Refeição
                        </th>
                        <th style="width: 65%">
                           Ementa
                        </th>
                        <th style="width: 10%">
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        @php
                        $today = date("Y-m-d");
                        @endphp
                        @foreach($allRefs as $refeiçao)
                        @if($refeiçao['data']>=$today)
                        @if ($refeiçao['meal']!="2REF")
                        <td>
                           @php
                           $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                           $mes_index = date('m', strtotime($refeiçao['data']));
                           @endphp
                           {{ date('d', strtotime($refeiçao['data'])) }}
                           {{ $mes[($mes_index - 1)] }}
                        </td>
                        <td style="padding: 1rem 0 1rem 0 !important;">
                           @if($refeiçao['meal']=="1REF")
                           Pequeno-almoço
                           @elseif($refeiçao['meal']=="3REF")
                           Jantar
                           @endif
                        </td>
                        <td>
                           @if($refeiçao['meal']=="3REF")
                           Sopa: <strong>{{$refeiçao['sopa_jantar']}}</strong> <br>
                           Prato: <strong>{{$refeiçao['prato_jantar']}}</strong> <br>
                           Sobremesa: <strong>{{$refeiçao['sobremesa_jantar']}}</strong>
                           @endif
                        </td>
                        <td>
                           @if($refeiçao['marcado']==1)
                           @php $maxdate = date("Y-m-d", strtotime("-".$maxDays." days", strtotime($refeiçao['data']))); @endphp
                           @if($today<=$maxdate)
                           <form method="POST" action="{{route('marcacao.children.destroy')}}">
                              @csrf
                              <input type="hidden" id="data" name="data" value="{{$refeiçao['data']}}"></input>
                              <input type="hidden" id="ref" name="ref" value="{{$refeiçao['meal']}}"></input>
                              <input type="hidden" id="user" name="user" value="{{$user['id']}}"></input>
                              <button type="submit" class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn meal-confirmed"><i class="far fa-calendar-times">&nbsp&nbsp&nbsp</i>Desmarcar</button>
                           </form>
                           @else
                           <button class="btn btn-danger smallbtn disabled remove-ref-btn user-children-btn" href="#"><i class="fas fa-lock">&nbsp&nbsp&nbsp</i>Bloqueada</button>
                           @endif
                           @else
                           @php
                           $maxtdateMarcar = date('Y-m-d', strtotime("+".$maxDaysMarcar." days"));
                           @endphp
                           @if($refeiçao['data']>=$maxtdateMarcar)
                           @php $formid = Str::random(32); @endphp
                           <form method="POST" id="{{ $formid }}">
                              @csrf
                              <input type="hidden" id="user{{$formid}}" name="user" value="{{$user['id']}}"></input>
                              <input type="hidden" id="data{{$formid}}" name="data" value="{{$refeiçao['data']}}"></input>
                              <input type="hidden" id="ref{{$formid}}" name="ref" value="{{$refeiçao['meal']}}"></input>
                              <input type="hidden" id="localDeRef{{$formid}}" name="localDeRef" value=""></input>
                              <div class="btn-group">
                                 <button type="button" class="btn btn-sm btn-primary  user-children-btn dropdown-toggle dropdown-icon" data-toggle="dropdown"
                                    aria-expanded="false">
                                    Marcar&nbsp&nbsp&nbsp
                                    <span class="sr-only" style="">Toggle Dropdown</span>
                                    <div class="dropdown-menu dropdown-menu-local-child" role="menu">
                                       @foreach ($locais as $key => $local)
                                       <a class="dropdown-item @if($local['status']=="NOK") disabled-drop @endif"
                                       @if($local['status']!="NOK" )onclick="changeLocalAndPost('{{$local['refName']}}', '{{ $formid }}')"@endif>{{$local['localName']}}
                                       </a>
                                       @endforeach
                                    </div>
                                 </button>
                              </div>
                           </form>
                           @else
                           <button type="button" class="btn btn-secondary smallbtn disabled remove-ref-btn user-children-btn"><i class="fas fa-lock">&nbsp&nbsp&nbsp</i>Data ultrapassada</button>
                           @endif
                           @endif
                        </td>
                      @endif
                     </tr>
                     @if($refeiçao['meal']=="3REF")
                     @if(!$loop->last)
                     <tr class="marcar-ref-spacer">
                        <td><strong>Data</strong></td>
                        <td><strong>Refeição</strong></td>
                        <td><strong>Ementa</strong></td>
                        <td></td>
                     </tr>
                     @endif
                     @endif
                     @endif
                     @endforeach
                  </tbody>
               </table>
               @else
               <p>
                 <h6><b><i style="color: #d14351" class="fas fa-exclamation mr-1"></i>&nbsp; Não pode fazer marcações para este utilizador.</b></h6>
                 </p>
               @endif
            </div>
            @if ($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP.")
            <div class="tab-pane swing-in-left-fwd" id="stats">
               @if(!$marcadas->isEmpty())
               <form method="POST" action="{{ route('generate.general.user.Report') }}">
                  @csrf
                  <input type="hidden" id="id" name="id" value="{{ $user['id'] }}">
                  <button type="submit" class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn generate-confirmed-meals-report @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif"><i class="fas fa-file-invoice"></i>&nbsp&nbsp&nbspGerar relatório</button>
               </form>


               <table class="table table-striped" style="margin-bottom: 0 !important;">
                  <thead>
                     <tr>
                        <th style="width: 1%">
                        </th>
                        <th>
                           Data
                        </th>
                        <th style="width: 20%">
                           Refeição
                        </th>
                        <th style="width: 40%">
                           Ementa
                        </th>
                        <th>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($marcadas as $marcaçao)
                     @php

                       $refeiçaoEmMarcação = $ementa[$marcaçao['id']];
                       $isMarcada = $marcadasVerificadas[$marcaçao['id']];
                     @endphp
                     <tr>
                        <td>
                           @if(isset($isMarcada['data']))
                           @if($isMarcada['data']==$refeiçaoEmMarcação['data'] && $isMarcada['meal']==$marcaçao['meal'])
                           @if($isMarcada['check']=="Y")
                           @php $refMarcada = 1; @endphp
                           <i class="fas fa-check nav-icon"></i>
                           @else
                           @php $refMarcada = 0; @endphp
                           <i class="fas fa-times nav-icon"></i>
                           @endif
                           @else
                           @php $refMarcada = 0; @endphp
                           <i class="fas fa-times"></i>
                           @endif
                           @else
                           @php $refMarcada = 0; @endphp
                           <i class="fas fa-times"></i>
                           @endif
                        </td>
                        <td class="project_progress">
                           @php
                           $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                           $mes_index = date('m', strtotime($refeiçaoEmMarcação['data']));
                           @endphp
                           <p class="p_noMargin">
                              {{ date('d', strtotime($refeiçaoEmMarcação['data'])) }}
                              {{ $mes[($mes_index - 1)] }}
                           </p>
                        </td>
                        <td>
                           <p class="p_noMargin">
                              @if($marcaçao['meal']=='1REF')
                              1ºRefeição
                              @elseif($marcaçao['meal']=='3REF')
                              3ºRefeição
                              @else
                              2ºRefeição
                              @endif
                           </p>
                        </td>
                        <td>
                           @if($marcaçao['meal']=='1REF')
                           <p class="p_noMargin">
                              Pequeno almoço</b>
                           </p>
                           @else
                           <p class="p_noMargin">
                              Prato: <b>@if($marcaçao['meal']=='3REF'){{ $refeiçaoEmMarcação['prato_jantar'] }}@else {{ $refeiçaoEmMarcação['prato_almoço'] }}@endif</b>
                           </p>
                           <small>
                              <p class="p_noMargin">
                                 Sopa: <b>@if($marcaçao['meal']=='3REF'){{ $refeiçaoEmMarcação['sopa_jantar'] }}@else {{ $refeiçaoEmMarcação['sopa_almoço'] }}@endif</b>
                              </p>
                              <p class="p_noMargin">
                                 Sobremesa: <b>@if($marcaçao['meal']=='3REF'){{ $refeiçaoEmMarcação['sobremesa_jantar'] }}@else {{ $refeiçaoEmMarcação['sobremesa_almoço'] }}@endif</b>
                              </p>
                           </small>
                           @endif
                        </td>
                        <td class="project-actions text-right">
                           @if($refMarcada==1)
                           <form method="POST" action="{{route('gestão.untagMealChildrenUser')}}">
                              @csrf
                              <input type="hidden" id="meal" name="meal" value="{{$marcaçao['meal']}}"></input>
                              <input type="hidden" id="data" name="data" value="{{$refeiçaoEmMarcação['data']}}"></input>
                              <input type="hidden" id="user" name="user" value="{{$user['id']}}"></input>
                              <button type="submit" class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn meal-confirmed @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif-untag "><i class="far fa-times-circle"></i>&nbsp&nbsp&nbspAnular confirmação</button>
                           </form>
                           @else
                           <form method="POST" action="{{route('gestão.tagMealChildrenUser')}}">
                              @csrf
                              <input type="hidden" id="meal" name="meal" value="{{$marcaçao['meal']}}"></input>
                              <input type="hidden" id="data" name="data" value="{{$refeiçaoEmMarcação['data']}}"></input>
                              <input type="hidden" id="user" name="user" value="{{$user['id']}}"></input>
                              <button style="width: 200px !important;" type="submit" class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn"><i class="far fa-check-circle"></i>&nbsp&nbsp&nbspConfirmar</button>
                           </form>
                           @endif
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>

               @else
                  <h5>Este utilizador não tem refeições marcadas.</h5>
               @endif
            </div>
            @endif
            <div class="tab-pane swing-in-left-fwd" id="settings">
               <form class="form-horizontal" method="POST" action="{{route('gestão.editChildrenUser')}}">
                  {{ csrf_field() }}
                  <div class="form-group row">
                     <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control uppercase-only" id="inputName" name="inputName" placeholder="Nome" value="{{$user['name']}}">
                        <input type="hidden" id="id" name="id" value="{{$user['id']}}"></input>
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="inputTelf" class="col-sm-2 col-form-label">Secção</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputSecc" name="inputSecc" placeholder="Secção deste utilizador dentro da unidade" value="{{ $user['seccao'] }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputTelf" class="col-sm-2 col-form-label">Função</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputFunc" name="inputFunc" placeholder="Função deste utilizador" value="{{ $user['descriptor'] }}">
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="inputEmail" class="col-sm-2 col-form-label">Posto</label>
                     <div class="col-sm-10">
                        <select class="custom-select" id="inputPosto" name="inputPosto">
                        <option value="ASS.TEC." @if($user['posto']=="ASS.TEC.") selected @endif>ASS.TEC.</option>
                        <option value="ASS.OP." @if($user['posto']=="ASS.OP.") selected @endif>ASS.OP.</option>
                        <option value="TEC.SUP." @if($user['posto']=="TEC.SUP.") selected @endif>TEC.SUP.</option>
                        <option value="SOLDADO" @if($user['posto']=="SOLDADO") selected @endif)>SOLDADO</option>
                        <option value="2ºCABO" @if($user['posto']=="2ºCABO") selected @endif)>2º CABO</option>
                        <option value="1ºCABO" @if($user['posto']=="1ºCABO") selected @endif>1º CABO</option>
                        <option value="CABO-ADJUNTO" @if($user['posto']=="CABO-ADJUNTO") selected @endif>CABO-ADJUNTO</option>
                        <option value="2ºFURRIEL" @if($user['posto']=="2ºFURRIEL") selected @endif>2º FURRIEL</option>
                        <option value="FURRIEL" @if($user['posto']=="FURRIEL") selected @endif>FURRIEL</option>
                        <option value="2ºSARGENTO" @if($user['posto']=="2ºSARGENTO") selected @endif>2º SARGENTO</option>
                        <option value="1ºSARGENTO" @if($user['posto']=="1ºSARGENTO") selected @endif>1º SARGENTO</option>
                        <option value="SARGENTO-AJUDANTE" @if($user['posto']=="SARGENTO-AJUDANTE") selected @endif>SARGENTO-AJUDANTE</option>
                        <option value="SARGENTO-CHEFE" @if($user['posto']=="SARGENTO-CHEFE") selected @endif>SARGENTO-CHEFE</option>
                        <option value="SARGENTO-MOR" @if($user['posto']=="SARGENTO-MOR") selected @endif>SARGENTO-MOR</option>
                        <option value="ASPIRANTE" @if($user['posto']=="ASPIRANTE") selected @endif>ASPIRANTE</option>
                        <option value="ALFERES" @if($user['posto']=="ALFERES") selected @endif>ALFERES</option>
                        <option value="TENENTE" @if($user['posto']=="TENENTE") selected @endif>TENENTE</option>
                        <option value="CAPITAO" @if($user['posto']=="CAPITAO") selected @endif>CAPITÃO</option>
                        <option value="MAJOR" @if($user['posto']=="MAJOR") selected @endif>MAJOR</option>
                        <option value="TENENTE-CORONEL" @if($user['posto']=="TENENTE-CORONEL") selected @endif>TENENTE-CORONEL</option>
                        <option value="CORONEL" @if($user['posto']=="CORONEL") selected @endif>CORONEL</option>
                        <option value="BRIGADEIRO-GENERAL" @if($user['posto']=="BRIGADEIRO-GENERAL") selected @endif>BRIGADEIRO-GENERAL</option>
                        <option value="MAJOR-GENERAL" @if($user['posto']=="MAJOR-GENERAL") selected @endif>MAJOR-GENERAL</option>
                        <option value="TENENTE-GENERAL" @if($user['posto']=="TENENTE-GENERAL") selected @endif>TENENTE-GENERAL</option>
                        <option value="GENERAL" @if($user['posto']=="GENERAL") selected @endif>GENERAL</option>
                        </select>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputExperience" class="col-sm-2 col-form-label">Unidade</label>
                     <div class="col-sm-10">
                        <select class="custom-select" id="inputUEO" @if($user['trocarUnidade']!=null) disabled @endif  name="inputUEO">
                        @if($user['unidade']==null)
                        <option disabled selected>Selecione uma unidade</option>
                        @endif
                        @foreach ($unidades as $key => $unidadePossivel)
                        <option @if($user['unidade']==$unidadePossivel->slug) selected @endif value="{{ $unidadePossivel->slug }}">{{ $unidadePossivel->name }}</option>
                        @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputExperience" class="col-sm-2 col-form-label">Grupo</label>
                     <div class="col-sm-10">
                        <select class="custom-select" id="inputGroup"  name="inputGroup">
                        <option @if($user['groupID']==null) selected @endif value="">GERAL</option>
                        @if($possibleGroups!=null)
                        @foreach($possibleGroups as $grupo)
                        <option @if($grupo['ref']==$user['groupID']) selected @endif value="{{$grupo['ref']}}">{{$grupo['nome']}}</option>
                        @endforeach
                        @endif
                        </select>
                     </div>
                  </div>
                  <div class="form-group row profile-settings-form-svbtn-spacer">
                     <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif">Guardar</button>
                     </div>
                  </div>
               </form>
               <div class="form-group row profile-settings-form-svbtn-spacer" style="margin-top: 3rem !important;">
                  <div class="offset-sm-2 col-sm-10">
                     <h4 style="margin-bottom: 1rem;font-size: 1.25rem;">Ferramentas</h4>
                     @if ($user['type']=="CHILDREN")
                       <button class="btn btn-primary" data-toggle="modal" data-target="#convertToUser" id="convertToUserBtn">Converter para conta</button>
                     @endif
                     @if ($USERS_NEED_FATUR)
                         @if($user['posto']!="ASS.OP." && $user['posto']!="ASS.TEC." && $user['posto']!="TEC.SUP." && $user['already_tagged']==false)
                         <a href="#" data-toggle="modal" data-target="#marcarParaConf">
                           <button type="submit" style="margin: .2rem; margin-right: 1.5rem !important;" class="btn btn-warning">Marcar para confirmações</button>
                         </a>
                        @endif
                     @endif
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')

  @php $minDay = date('d/m/Y'); @endphp
  <script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/daterangepicker.js')}}"></script>
  <script>
     $(document).ready(function() {
       $('#dateRangePicker').daterangepicker({
         format: 'DD/MM/YYYY',
         startDate: '{{ $minDay }}',
         separator: " até ",
         showDropdowns: false,
         timePicker: false,
         opens: 'center',
         singleDatePicker: false,
         showRangeInputsOnCustomRangeOnly: false,
         applyClass: 'rangePickerApplyBtn',
         cancelClass: 'rangePickerCancelBtn',
         minDate : '{{ $minDay }}',
         parentEl: $('#marcarParaConf'),
         locale: {
           cancelLabel: 'Limpar',
           applyLabel: 'Aplicar',
           fromLabel: 'DE',
           toLabel: 'ATÉ',
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
  </script>

<script>
   function changeLocalAndPost(toWhere, formID) {
      var user_id = "user" + formID;
      var campoData = "data" + formID;
      var refData = "ref" + formID;
      var campoID = "localDeRef" + formID;
      var uid = document.getElementById(user_id).value;
      document.getElementById(campoID).value = toWhere.toUpperCase();
      var data = document.getElementById(campoData).value;
      var ref = document.getElementById(refData).value;
      $.ajax({
        url: "{{route('marcacao.store.children')}}",
        type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            data: data,
            ref: ref,
            localDeRef: toWhere,
            user: uid,
        },
        success: function(response) {
            console.log(response);
            if (response) {
                if (response != 'success') {
                    document.getElementById("errorAddingTitle").innerHTML = "Erro";
                    document.getElementById("errorAddingText").innerHTML = "Erro a fazer marcação.";
                    $("#errorAddingModal").modal()
                } else {
                    var form_id = "#"+formID;
                    var trHTML = '<button class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif smallbtn disabled remove-ref-btn user-children-btn slide-in-blurred-top"><i class="fas fa-check">&nbsp;&nbsp;&nbsp;</i>Marcada</button>' ;
                    $(form_id).parents('td').append(trHTML);
                    $(form_id).remove();
                }
            }
        }
    });
   }
</script>
@endsection
