@extends('layout.master')
@section('extra-links')
<link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
@endsection
@section('title','Estatisticas')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Estatisticas</li>
<li class="breadcrumb-item active">Dados por dia/refeição</li>
@endsection
@section('page-content')
<div class="modal slide-in-bck-center" style="z-index: 1060 !important; background: #38393a;" id="errorAddingModal" tabindex="-1" role="dialog" aria-labelledby="errorAddingModal" aria-hidden="true">
   <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
<div class="modal puff-in-center" id="searchUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="overlay fade-in-fwd" id="searchUserModalOverlay">
            <i class="fas fa-2x fa-sync fa-spin"></i>
         </div>
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Procurar</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-2 col-form-label">NIM</label>
               <div class="col-sm-10">
                  <div class="input-group input-group-sm">
                     @csrf
                     <input type="number" class="form-control form-control-navbar" maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="search"
                        placeholder=" Procurar NIM" aria-label="Procurar NIM" id="procurarInput" name="procurarInput">
                     <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit" style="border: 1px solid #ced4da; border-left-width: 0;">
                        <i class="fas fa-search"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <table class="table results hide" id="resultadosProcura" name="Resultados">
               <tbody>
                  <tr style="border-top: 0px;">
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="nameSearch" name="nameSearch">
                        <a href="#" id="idSearch">
                        NOME
                        </a>
                     </td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="postoSearch" name="postoSearch">POSTO</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="grupoSearch" name="grupoSearch">GRUPO</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="subgrupoSearch" name="subgrupoSearch">SUBGRUPO</td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
@if(Auth::user()->user_permission=="TUDO" || Auth::user()->user_permission=="LOG")
<div class="modal puff-in-center" id="reportModalAlt" tabindex="-1" role="dialog" aria-labelledby="reportModalAlt" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="overlay fade-in-fwd" id="reportModalOverlayAlt">
            <i class="fas fa-2x fa-sync fa-spin"></i>
         </div>
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Gerar relatório</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form id="reportFormAlt" name="reportFormAlt" method="POST">
            <div class="modal-body">
               <!-- POR TIME PERIOD -->
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Período de tempo</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="timeframeSelectAlt" name="timeframeSelectAçt">
                        <option selected disabled value="0">Selecione o período de tempo</option>
                        <option value="PERSON">Personalizado</option>
                        <option value="ALL">Tudo disponivel actualmente</option>
                        <option value="WEEK">Esta semana</option>
                        <option value="NEXTWEEK">Próxima semana</option>
                        <option value="MONTH">Este mês</option>
                        <option value="NEXTMONTH">Próximo mês</option>
                     </select>
                  </div>
               </div>
               <div class="form-group row" id="customTimeInput2" name="customTimeInput2" style="display: none">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Período personalizado</label>
                  <div class="col-sm-7">
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text">
                           <i class="far fa-calendar-alt"></i>
                           </span>
                        </div>
                        <input type="text" class="form-control float-right" id="dateRangePicker2" data-toggle="dateRangePicker2" data-target="#dateRangePicker2">
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="generateBtnAlt"  style="width: 6rem;display: none;">Gerar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endif
<div class="modal puff-in-center" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModal" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="overlay fade-in-fwd" id="reportModalOverlay">
            <i class="fas fa-2x fa-sync fa-spin"></i>
         </div>
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Gerar relatório</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form id="reportForm" name="reportForm" method="POST">
            <div class="modal-body">
               <!-- POR TIME PERIOD -->
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Período de tempo</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="timeframeSelect" name="timeframeSelect">
                        <option selected disabled value="0">Selecione o período de tempo</option>
                        <option value="PERSON">Personalizado</option>
                        <option value="ALL">Tudo disponivel actualmente</option>
                        <option value="WEEK">Esta semana</option>
                        <option value="NEXTWEEK">Próxima semana</option>
                        <option value="MONTH">Este mês</option>
                        <option value="NEXTMONTH">Próximo mês</option>
                     </select>
                  </div>
               </div>
               <div class="form-group row" id="customTimeInput" name="customTimeInput" style="display: none">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Período personalizado</label>
                  <div class="col-sm-7">
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text">
                           <i class="far fa-calendar-alt"></i>
                           </span>
                        </div>
                        <input type="text" class="form-control float-right" id="dateRangePicker" data-toggle="dateRangePicker" data-target="#dateRangePicker">
                     </div>
                  </div>
               </div>
               @if ($GET_STATS_OTHER_UNITS)
               <div class="form-group row" id="localInput" style="display: none">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Local</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="localRef" name="localRef">
                        <option selected disabled value="0">Selecione o local</option>
                        <option value="GERAL">Todos</option>
                        @foreach ($locals as $key => $local)
                        @if($local['status']!="NOK")
                        <option value="{{$local['refName']}}">{{$local['localName']}}</option>
                        @else
                        <option disabled value="{{$local['refName']}}">{{$local['localName']}}</option>
                        @endif
                        @endforeach
                     </select>
                  </div>
               </div>
               @endif
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="generateBtn"  style="width: 6rem;display: none;">Gerar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
@if ($GET_STATS_NOMINAL)
<div class="modal puff-in-center" id="reportModalNominal" tabindex="-1" role="dialog" aria-labelledby="reportModalNominal" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="overlay fade-in-fwd" id="reportModalNominalOverlay">
            <i class="fas fa-2x fa-sync fa-spin"></i>
         </div>
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Gerar listagem nominal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form id="reportFormNominal" name="reportFormNominal" method="POST">
            <div class="modal-body">
               <p>
                  Extrair listagem nominal até dia <strong>{{ date("d/m/Y", strtotime($MIN_DATE . "- 1 days")) }}</strong> (data em que já não é possivel a remoção de marcações).
               </p>
               @if ($GET_STATS_OTHER_UNITS)
               <div class="form-group row" id="localInputNominal" style="margin-top: 2rem">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Local</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="localRefNominal" name="localRefNominal">
                        <option selected disabled value="0">Selecione o local</option>
                        <option value="GERAL">Todos</option>
                        @foreach ($locals as $key => $local)
                        @if($local['status']!="NOK")
                        <option value="{{$local['refName']}}">{{$local['localName']}}</option>
                        @else
                        <option disabled value="{{$local['refName']}}">{{$local['localName']}}</option>
                        @endif
                        @endforeach
                     </select>
                  </div>
               </div>
               @else
               <h6>A exportar listagem nominal para {{ $myLocal }}</h6>
               <input type="hidden" id="localRefNominal" name="localRefNominal" value="{{ $myLocal }}" readonly>
               @endif
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="generateBtnNominal" style="width: 6rem;display: none;">Gerar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endif

<div class="col-sm-12">
  <h5 style="padding-bottom: 1rem;" class="m-0 text-dark subtitle">Gerar relatórios</h5>
</div>

@if(Auth::user()->user_permission=="TUDO" || Auth::user()->user_permission=="LOG")
<button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn generate-confirmed-meals-report btn-dark2 stats-generate-reports" data-toggle="modal" data-target="#reportModalAlt" >
<i class="fas fa-file-invoice">&nbsp&nbsp&nbsp</i>Relatório de pedidos por POC</button>
@endif
<button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn generate-confirmed-meals-report btn-dark2 stats-generate-reports" data-toggle="modal" data-target="#reportModal" >
<i class="fas fa-file-invoice">&nbsp&nbsp&nbsp</i>Relatório por periodo de tempo</button>
@if ($GET_STATS_NOMINAL)
<button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn generate-confirmed-meals-report btn-dark2 stats-generate-reports" data-toggle="modal" data-target="#searchUserModal" >
<i class="fas fa-file-invoice">&nbsp&nbsp&nbsp</i>Relatório por utilizador</button>
<button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn generate-confirmed-meals-report btn-dark2 stats-generate-reports" data-toggle="modal" data-target="#reportModalNominal" >
<i class="fas fa-file-invoice">&nbsp&nbsp&nbsp</i>Listagem nominal</button>
<!--
<a href="{{route('generate.general.removed.tags')}} ">
  <button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn generate-confirmed-meals-report btn-dark2 stats-generate-reports">
  <i class="fas fa-file-invoice">&nbsp&nbsp&nbsp</i>Marcações eliminadas</button>
</a>
-->
@endif
@if ($GET_CIVILIANS_REPORT)
<a href="{{ route('generate.general.faturation.all') }}">
<button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn generate-confirmed-meals-report btn-dark2 stats-generate-reports" >
<i class="fas fa-file-invoice">&nbsp&nbsp&nbsp</i>Utilizadores sem confirmações</button>
</a>
@endif
@php
$mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
$semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
$mes_index = date('m', strtotime($MAX_DATE));
@endphp
<div class="col-sm-12">
  <h5 style="padding-bottom:2rem; padding-top: 2rem;" class="m-0 text-dark subtitle">Dados de marcações</h5>
</div>
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <h3 class="card-title">Gráficos de marcações</h3>
         <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </button>
         </div>
      </div>
      <div class="card-body scale-up-ver-top">
         <!--<h6 style="padding-bottom: 1rem;">Refeições marcadas por dia</h6>-->
         @php
          $stack = array();
        @endphp

        @if($GET_STATS_OTHER_UNITS)

          <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
             <div class="card-header border-0">
                <h3 class="card-title">Quartel da Serra do Pilar</h3>
                <div class="card-tools">
                 <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
             </div>
             <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
               @foreach($REFS as $REF)
                 @php
                 $REF_SPECIFIC_LOCAL = $REF['QSP'];
                 $mes_index = date('m', strtotime($REF['DATA']));
                 @endphp
                 <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                     <div class="card-header border-0">
                       <h3 class="card-title">{{ date('d', strtotime($REF['DATA'])) }} {{ $mes[($mes_index - 1)] }}</h3>
                       <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                       </div>
                     </div>
                     <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
                       @foreach($REF_SPECIFIC_LOCAL as $key => $REFS_ISOLATED)
                       <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                         <div class="card-header border-0">
                           <h3 class="card-title">@if($key=="1REF") Pequeno-almoço @elseif($key=="2REF") Almoço @else Jantar @endif</h3>
                           <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                           </div>
                         </div>
                         <div class="card-body scale-up-ver-top" style="max-height: none !important;">
                           <h6>Total de refeições: {{ $REFS_ISOLATED['TOTAL'] }} </h6>
                             @php
                               $token = rand(00000000, 99999999);
                               $stack[$token] = $REFS_ISOLATED;
                             @endphp
                           <div style="height: 250px; ">
                             <canvas id="generalPieChart{{$token}}" style="height: 29vh;">
                             </canvas>
                           </div>
                         </div>
                     </div>
                       @endforeach
                     </div>
                 </div>
                 @endforeach
             </div>
          </div>

          <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
             <div class="card-header border-0">
                <h3 class="card-title">Quartel de Santo Ovídio</h3>
                <div class="card-tools">
                 <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
             </div>
             <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
               @foreach($REFS as $REF)
                 @php
                 $REF_SPECIFIC_LOCAL = $REF['QSO'];
                 $mes_index = date('m', strtotime($REF['DATA']));
                 @endphp
                 <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                     <div class="card-header border-0">
                       <h3 class="card-title">{{ date('d', strtotime($REF['DATA'])) }} {{ $mes[($mes_index - 1)] }}</h3>
                       <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                       </div>
                     </div>
                     <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
                       @foreach($REF_SPECIFIC_LOCAL as $key => $REFS_ISOLATED)
                       <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                         <div class="card-header border-0">
                           <h3 class="card-title">@if($key=="1REF") Pequeno-almoço @elseif($key=="2REF") Almoço @else Jantar @endif</h3>
                           <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                           </div>
                         </div>
                         <div class="card-body scale-up-ver-top" style="max-height: none !important;">
                           <h6>Total de refeições: {{ $REFS_ISOLATED['TOTAL'] }} </h6>
                             @php
                               $token = rand(00000000, 99999999);
                               $stack[$token] = $REFS_ISOLATED;
                             @endphp
                           <div style="height: 250px; ">
                             <canvas id="generalPieChart{{$token}}" style="height: 29vh;">
                             </canvas>
                           </div>
                         </div>
                     </div>
                       @endforeach
                     </div>
                 </div>
                 @endforeach
             </div>
          </div>
          <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
             <div class="card-header border-0">
                <h3 class="card-title">Messe Militar das Antas</h3>
                <div class="card-tools">
                 <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
             </div>
             <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
               @foreach($REFS as $REF)
                 @php
                 $REF_SPECIFIC_LOCAL = $REF['MMANTAS'];
                 $mes_index = date('m', strtotime($REF['DATA']));
                 @endphp
                 <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                     <div class="card-header border-0">
                       <h3 class="card-title">{{ date('d', strtotime($REF['DATA'])) }} {{ $mes[($mes_index - 1)] }}</h3>
                       <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                       </div>
                     </div>
                     <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
                       @foreach($REF_SPECIFIC_LOCAL as $key => $REFS_ISOLATED)
                       <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                         <div class="card-header border-0">
                           <h3 class="card-title">@if($key=="1REF") Pequeno-almoço @elseif($key=="2REF") Almoço @else Jantar @endif</h3>
                           <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                           </div>
                         </div>
                         <div class="card-body scale-up-ver-top" style="max-height: none !important;">
                           <h6>Total de refeições: {{ $REFS_ISOLATED['TOTAL'] }} </h6>
                             @php
                               $token = rand(00000000, 99999999);
                               $stack[$token] = $REFS_ISOLATED;
                             @endphp
                           <div style="height: 250px; ">
                             <canvas id="generalPieChart{{$token}}" style="height: 29vh;">
                             </canvas>
                           </div>
                         </div>
                     </div>
                       @endforeach
                     </div>
                 </div>
                 @endforeach
             </div>
          </div>

          <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
             <div class="card-header border-0">
                <h3 class="card-title">Messe Militar da Batalha</h3>
                <div class="card-tools">
                 <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
             </div>
             <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
               @foreach($REFS as $REF)
                 @php
                 $REF_SPECIFIC_LOCAL = $REF['MMBATALHA'];
                 $mes_index = date('m', strtotime($REF['DATA']));
                 @endphp
                 <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                     <div class="card-header border-0">
                       <h3 class="card-title">{{ date('d', strtotime($REF['DATA'])) }} {{ $mes[($mes_index - 1)] }}</h3>
                       <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                       </div>
                     </div>
                     <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
                       @foreach($REF_SPECIFIC_LOCAL as $key => $REFS_ISOLATED)
                       <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                         <div class="card-header border-0">
                           <h3 class="card-title">@if($key=="1REF") Pequeno-almoço @elseif($key=="2REF") Almoço @else Jantar @endif</h3>
                           <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                           </div>
                         </div>
                         <div class="card-body scale-up-ver-top" style="max-height: none !important;">
                           <h6>Total de refeições: {{ $REFS_ISOLATED['TOTAL'] }} </h6>
                             @php
                               $token = rand(00000000, 99999999);
                               $stack[$token] = $REFS_ISOLATED;
                             @endphp
                           <div style="height: 250px; ">
                             <canvas id="generalPieChart{{$token}}" style="height: 29vh;">
                             </canvas>
                           </div>
                         </div>
                     </div>
                       @endforeach
                     </div>
                 </div>
                 @endforeach
             </div>
          </div>

        @elseif (Auth::user()->user_permission=="MESSES")


          <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
             <div class="card-header border-0">
                <h3 class="card-title">Messe Militar das Antas</h3>
                <div class="card-tools">
                 <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
             </div>
             <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
               @foreach($REFS as $REF)
                 @php
                 $REF_SPECIFIC_LOCAL = $REF['MMANTAS'];
                 $mes_index = date('m', strtotime($REF['DATA']));
                 @endphp
                 <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                     <div class="card-header border-0">
                       <h3 class="card-title">{{ date('d', strtotime($REF['DATA'])) }} {{ $mes[($mes_index - 1)] }}</h3>
                       <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                       </div>
                     </div>
                     <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
                       @foreach($REF_SPECIFIC_LOCAL as $key => $REFS_ISOLATED)
                       <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                         <div class="card-header border-0">
                           <h3 class="card-title">@if($key=="1REF") Pequeno-almoço @elseif($key=="2REF") Almoço @else Jantar @endif</h3>
                           <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                           </div>
                         </div>
                         <div class="card-body scale-up-ver-top" style="max-height: none !important;">
                           <h6>Total de refeições: {{ $REFS_ISOLATED['TOTAL'] }} </h6>
                             @php
                               $token = rand(00000000, 99999999);
                               $stack[$token] = $REFS_ISOLATED;
                             @endphp
                           <div style="height: 250px; ">
                             <canvas id="generalPieChart{{$token}}" style="height: 29vh;">
                             </canvas>
                           </div>
                         </div>
                     </div>
                       @endforeach
                     </div>
                 </div>
                 @endforeach
             </div>
          </div>

          <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
             <div class="card-header border-0">
                <h3 class="card-title">Messe Militar da Batalha</h3>
                <div class="card-tools">
                 <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
             </div>
             <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
               @foreach($REFS as $REF)
                 @php
                 $REF_SPECIFIC_LOCAL = $REF['MMBATALHA'];
                 $mes_index = date('m', strtotime($REF['DATA']));
                 @endphp
                 <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                     <div class="card-header border-0">
                       <h3 class="card-title">{{ date('d', strtotime($REF['DATA'])) }} {{ $mes[($mes_index - 1)] }}</h3>
                       <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                       </div>
                     </div>
                     <div class="card-body scale-up-ver-top" style="background-color: #343a40; max-height: none !important;">
                       @foreach($REF_SPECIFIC_LOCAL as $key => $REFS_ISOLATED)
                       <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card">
                         <div class="card-header border-0">
                           <h3 class="card-title">@if($key=="1REF") Pequeno-almoço @elseif($key=="2REF") Almoço @else Jantar @endif</h3>
                           <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                           </div>
                         </div>
                         <div class="card-body scale-up-ver-top" style="max-height: none !important;">
                           <h6>Total de refeições: {{ $REFS_ISOLATED['TOTAL'] }} </h6>
                             @php
                               $token = rand(00000000, 99999999);
                               $stack[$token] = $REFS_ISOLATED;
                             @endphp
                           <div style="height: 250px; ">
                             <canvas id="generalPieChart{{$token}}" style="height: 29vh;">
                             </canvas>
                           </div>
                         </div>
                     </div>
                       @endforeach
                     </div>
                 </div>
                 @endforeach
             </div>
          </div>

        @endif

      </div>
   </div>
<!--
 <a href="{{ route('gestão.statsUnitsRemoved') }}" >
   <button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif generate-confirmed-meals-report btn-dark2 stats-generate-reports" style="margin-bottom: 1.5vh !important;">
     <i class="fas fa-cookie-bite">&nbsp&nbsp&nbsp</i>Estatísticas de consumo diário</button>
 </a>

 <a href="{{ route('gestão.statsRemoved') }}" >
   <button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif generate-confirmed-meals-report btn-dark2 stats-generate-reports" style="margin-bottom: 1.5vh !important;">
     <i class="fas fa-cookie-bite">&nbsp&nbsp&nbsp</i>Estatísticas de consumo</button>
   </a>

 <a href="{{ route('gestão.AdminUnit') }}" >
   <button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif generate-confirmed-meals-report btn-dark2 stats-generate-reports" style="margin-bottom: 1.5vh !important;">
     <i class="fas fa-cookie-bite">&nbsp&nbsp&nbsp</i>Estatísticas de consumo por unidade</button>
 </a>
-->
</div>
@endsection
@section('extra-scripts')
<script src="{{asset('adminlte/plugins/chart.js/Chart.min.js')}}"></script>
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/daterangepicker.js')}}"></script>
<script type="text/javascript">
   $(window).on('load', function(){
     $('#reportModalOverlay').removeClass( "overlay" );
     $('#reportModalOverlay').hide()
     $('#searchUserModalOverlay').removeClass( "overlay" );
     $('#searchUserModalOverlay').hide()
     $('#searchUserModalOverlay').removeClass( "overlay" );
     $('#searchUserModalOverlay').hide()
     $('#reportModalNominalOverlay').removeClass( "overlay" );
     $('#reportModalNominalOverlay').hide()
     $('#reportModalOverlayAlt').removeClass( "overlay" );
     $('#reportModalOverlayAlt').hide()
   });

   var startDate;
   var endDate;

   @if ($GET_STATS_OTHER_UNITS)
   $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
       if (picker.startDate.format('YYYY-MM-DD')!=null && picker.endDate.format('YYYY-MM-DD')!=null) {
         startDate = picker.startDate.format('YYYY-MM-DD');
         endDate = picker.endDate.format('YYYY-MM-DD');
         $("#localInput").css("display", "flex");
       }
    });
   @else
    $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
        if (picker.startDate.format('YYYY-MM-DD')!=null && picker.endDate.format('YYYY-MM-DD')!=null) {
          startDate = picker.startDate.format('YYYY-MM-DD');
          endDate = picker.endDate.format('YYYY-MM-DD');
          $("#generateBtn").css("display", "block");
        }
     });
   @endif

   $('#dateRangePicker2').on('apply.daterangepicker', function(ev, picker) {
        if (picker.startDate.format('YYYY-MM-DD')!=null && picker.endDate.format('YYYY-MM-DD')!=null) {
          startDate = picker.startDate.format('YYYY-MM-DD');
          endDate = picker.endDate.format('YYYY-MM-DD');
          $("#generateBtnAlt").css("display", "block");
        }
     });

   $(document).ready(function() {
     $('#dateRangePicker').daterangepicker({
       format: 'DD/MM/YYYY',
       startDate: moment(),
       separator: " até ",
       showDropdowns: false,
       timePicker: false,
       opens: 'center',
       singleDatePicker: false,
       showRangeInputsOnCustomRangeOnly: false,
       applyClass: 'rangePickerApplyBtn',
       cancelClass: 'rangePickerCancelBtn',
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
   $('#dateRangePicker2').daterangepicker({
       format: 'DD/MM/YYYY',
       startDate: moment(),
       separator: " até ",
       showDropdowns: false,
       timePicker: false,
       opens: 'center',
       singleDatePicker: false,
       showRangeInputsOnCustomRangeOnly: false,
       applyClass: 'rangePickerApplyBtn',
       cancelClass: 'rangePickerCancelBtn',
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

   $('#timeframeSelectAlt').on('change', function() {
    if (this.value=="PERSON") {
       $("#localInput").css("display", "none");
       $("#customTimeInput2").css("display", "flex");
     } else {
        $("#customTimeInput2").css("display", "none");
        $("#generateBtnAlt").css("display", "block");
     }
   });

   $("#reportFormAlt").submit(function(e) {
   $('#reportModalOverlayAlt').show();
   $('#reportModalOverlayAlt').addClass( "overlay" );
   e.preventDefault();
   var timeframe = $( "select#timeframeSelectAlt" ).val();
   $.ajax({
       url: "{{route('generate.general.timeframe.ALT')}}",
       type: "POST",
       data: {
           "_token": "{{ csrf_token() }}",
           timeframe: timeframe,
           customtimeStart: startDate,
           customtimeEnd: endDate
       },
       xhrFields: {
         responseType: 'blob'
       },
       success: function(response){
         $('#reportModalOverlayAlt').removeClass( "overlay" );
         $('#reportModalOverlayAlt').hide()
         var title = "Relatório-"+timeframe+"-Logis-"+Date.now()+".pdf";
         var blob = new Blob([response], {type: 'application/pdf'});
           if (blob.size<=20) {
             document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
             document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
             $("#errorAddingModal").modal()
             return;
           } else {
              var link = document.createElement('a');
             link.href = window.URL.createObjectURL(blob);
             link.download = title;
             link.click();
           }
       }
     });
   });

   $("#reportForm").submit(function(e) {
   $('#reportModalOverlay').show();
   $('#reportModalOverlay').addClass( "overlay" );
   e.preventDefault();
   var timeframe = $( "select#timeframeSelect" ).val();
   var local = $( "select#localRef" ).val();
   $.ajax({
       url: "{{route('generate.general.timeframe.Report')}}",
       type: "POST",
       data: {
           "_token": "{{ csrf_token() }}",
           timeframe: timeframe,
           local: local,
           customtimeStart: startDate,
           customtimeEnd: endDate
       },
       xhrFields: {
         responseType: 'blob'
       },
       success: function(response){
         $('#reportModalOverlay').removeClass( "overlay" );
         $('#reportModalOverlay').hide()
         var title = "Relatório-"+timeframe+"-"+local+"-"+Date.now()+".pdf";
         var blob = new Blob([response], {type: 'application/pdf'});
           if (blob.size<=20) {
             document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
             document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
             $("#errorAddingModal").modal()
             return;
           } else {
             var link = document.createElement('a');
             link.href = window.URL.createObjectURL(blob);
             link.download = title;
             link.click();
           }
       }
     });
   });
</script>
<script>
   $("#reportFormNominal").submit(function(e) {
     $('#reportModalNominalOverlay').show();
     $('#reportModalNominalOverlay').addClass( "overlay" );
     e.preventDefault();
     var local = $( "#localRefNominal" ).val();
     $.ajax({
         url: "{{route('generate.general.nominal')}}",
         type: "POST",
         data: {
             "_token": "{{ csrf_token() }}",
             local: local,
         },
         xhrFields: {
           responseType: 'blob'
         },
         success: function(response){
           $('#reportModalNominalOverlay').removeClass( "overlay" );
           $('#reportModalNominalOverlay').hide()
           var title = "Relatório-Nominal-"+local+"-"+Date.now()+".pdf";
           var blob = new Blob([response], {type: 'application/pdf'});
             if (blob.size<=35) {
               document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
               document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
               $("#errorAddingModal").modal()
               return;
             } else {
               var link = document.createElement('a');
               link.href = window.URL.createObjectURL(blob);
               link.download = title;
               link.click();
             }
         }, error: function(response){
           $('#reportModalNominalOverlay').removeClass( "overlay" );
           $('#reportModalNominalOverlay').hide()
           document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
           document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
           $("#errorAddingModal").modal()
         }
       });
     });

</script>
<script>
   $('#procurarInput').on("paste keyup",function(){
     $('#resultadosProcura').empty();
     $value=$(this).val();
     if (!$value) {
       $("#resultadosProcura").addClass("hide");
       $("#nameSearch").html("");
       $("#postoSearch").html("");
       $("#grupoSearch").html("");
       $("#subgrupoSearch").html("");
       $("#idSearch").attr("href", "#");
       return false;
     }
     $value=$(this).val();
       $.ajax({
           type : 'get',
           url : "{{route('report.search.user')}}",
           data:{
             'search': $value
           },
         success:function(data){
           $('#resultadosProcura').empty();
           $("#resultadosProcura").removeClass("hide");
           var trHTML = '';
           $.each(data, function (i, item) {
             btnHTML = '<button class="stats-possible-reports userReport btn btn-sm btn-dark" name="' + item.name + '" id="' + item.id + '" style="opacity: 1 !important;">Gerar</button>';
             trHTML += '<tr>'
             + '<td ><a href="/gestao/user/type/' + item.type + '/' + item.id  + '">' + item.id + '</a> </td>'
             + '<td class="uppercase-only">' + item.name + '</td>'
             + '<td>' + item.posto + '</td>'
             + '<td>' + btnHTML + '</td>'
             + '</tr>';
           });
           $('#resultadosProcura').append(trHTML);
       }
     });
   }).delay(500);
</script>
<script>
   $(document).on('click', '.userReport', function(event) {
     $('#searchUserModalOverlay').show()
     $('#searchUserModalOverlay').addClass( "overlay" );
     event.stopPropagation();
     event.stopImmediatePropagation();
     var id = this.id;
     var name = this.name;
     $.ajax({
         url: "{{route('generate.general.user.Report')}}",
         type: "POST",
         data: {
             "_token": "{{ csrf_token() }}",
             id: id,
         },
         xhrFields: {
           responseType: 'blob'
         },
         success: function(response){
           $('#searchUserModalOverlay').removeClass( "overlay" );
           $('#searchUserModalOverlay').hide()
           var title = "Relatório-"+id+"-"+name+"-"+Date.now()+".pdf";
           var blob = new Blob([response], {type: 'application/pdf'});
             if (blob.size<=20) {
               document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
               document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>O utilizador NIM <b>"+id+"</b> não tem nenhuma marcação para os proximos 15 dias.";
               $("#errorAddingModal").modal()
               return;
             } else {
               var link = document.createElement('a');
               link.href = window.URL.createObjectURL(blob);
               link.download = title;
               link.click();
             }
         }
       });
     });
</script>
@if ($GET_STATS_OTHER_UNITS==false && $GET_STATS_NOMINAL==true)
<script>
   $(window).on('load', function(){
     $("#generateBtnNominal").css("display", "block");
   });
</script>
@endif
@if ($GET_STATS_OTHER_UNITS)
<script>
   $('#timeframeSelect').on('change', function() {
     if (this.value=="PERSON") {
       $("#localInput").css("display", "none");
       $("#customTimeInput").css("display", "flex");
     } else {
       $("#customTimeInput").css("display", "none");
       $("#localInput").css("display", "flex");
     }
   });
   $('#localRef').on('change', function() {
     $("#generateBtn").css("display", "block");
   });
   $('#localRefNominal').on('change', function() {
     $("#generateBtnNominal").css("display", "block");
   });
</script>
@else
<script>
   $('#timeframeSelect').on('change', function() {
     if (this.value=="PERSON") {
       $("#localInput").css("display", "none");
       $("#customTimeInput").css("display", "flex");
     } else {
       $("#customTimeInput").css("display", "none");
       $("#generateBtn").css("display", "block");
     }
   });
</script>
@endif

@foreach($stack as $key => $data)
<script>
  var canvas = document.getElementById("generalPieChart{{$key}}");
  canvas.height = 250;
  var ctx = canvas.getContext('2d');
  Chart.defaults.global.defaultFontColor = 'white';
  Chart.defaults.global.defaultFontSize = 16;
  var data = {
      labels: ["Normal", "Dietas", "Pedidos quantitativos"],
        datasets: [
          {
              fill: true,
              backgroundColor: [ '#bd5caf', '#bd3a3a', '#28b8b0'],
              data: ["{{ $data['NORMAL'] }}", "{{ $data['DIETAS'] }}", "{{ $data['PEDIDOS'] }}"],
              borderColor:	['black', 'black', 'black'],
              borderWidth: [1,1,1]
          }
      ]
  };
  var options = {
      responsive: true,
      maintainAspectRatio: false,

  };
  var myBarChart = new Chart(ctx, {
      type: 'pie',
      data: data,
      options: options
  });
</script>


@endforeach
@endsection
