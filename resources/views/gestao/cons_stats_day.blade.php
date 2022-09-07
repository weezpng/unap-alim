@extends('layout.master')
@section('title','Estatisticas')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Estatisticas</li>
<li class="breadcrumb-item active">Dados de consumo diário</li>
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
         <form id="reportFormAlt" name="reportFormAlt" method="POST" action="{{ route('gestão.statsAdminLast') }}">
            <div class="modal-body">
               <!-- POR TIME PERIOD -->
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

<div style="display: none; width: 100%; margin-bottom: 1rem; padding: 0.75rem;" id="toggleText">
   <h5>
      A obter informação...
   </h5>
</div>

@else

  @php
   $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
   $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
   $mes_index = date('m', strtotime($stats['date']));
  @endphp

   <a id="back-to-top" href="#" onclick="printToFile()" class="btn btn-primary back-to-top elevation-4 tooltip" role="button" aria-label="Save to file">
      <i class="fas fa-print"></i>
      <span class="tooltiptext3">Página para impressão</span>
   </a>

   <a id="choose-new" style="margin-bottom: 3rem;" href="{{ route('gestão.statsUnitsRemoved')}}"  class="btn btn-primary back-to-top elevation-4 tooltip" role="button" aria-label="Escolher novo periodo;">
      <i class="fas fa-redo-alt"></i>
      <span class="tooltiptext2">Escolher novo periodo</span>
   </a>

   <div style="display: inline; width: 100%; margin-bottom: 1rem; padding: 0.5rem;">
      <h5>
         <b>{{date('d', strtotime($stats['date'])) }} {{ $mes[($mes_index - 1)] }}</b>
      </h5>
   </div>
   <div style="display: inline; width: 100%; margin-bottom: 1rem; padding: 0.5rem;"><h5 style="margin-bottom: 0;"><b>Total</b></h5><h6>Contagem de marcações e pedidos quantitativos</h6 style="margin-top: 0;"></div>

   <div class="col-sm-6 col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-success"><i class="fas fa-calendar-check"></i></span>
         <div class="info-box-content">
         <span class="info-box-text">Total de refeições</span>
         <span class="info-box-number">{{$total_stats['ped']}} refeições</span>
         </div>
      </div>
   </div>
   <div class="col-sm-6 col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-success"><i class="fas fa-utensils"></i></span>
         <div class="info-box-content">
         <span class="info-box-text">Total consumidas</span>
         <span class="info-box-number">{{$total_stats['cons']}} consumidas</span>
         </div>
      </div>
   </div>

   <div class="col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
         <div class="info-box-content">
            <span class="info-box-text">Taxa de consumo geral</span>
            <div class="progress">
               <div class="progress-bar" style="width: {{$total_stats['perc']}}%"></div>
            </div>
            <span class="progress-description">
            <b @if($total_stats['perc']>100) style="color: #e64c3c;"@elseif($total_stats['perc']<=50) style="color: #e64c3c;" @elseif($total_stats['perc']<=80) style="color: #f29b12;"  @endif>{{$total_stats['perc']}}%</b>
            das refeições <b>pedidas/marcadas</b> foram consumidas
            </span>
         </div>
      </div>
   </div>

   <div style="display: inline; width: 100%; margin-bottom: 1rem; padding: 0.5rem;"><h5><b>Marcações</b></h5></div>
   <div class="col-sm-6 col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-success"><i class="fas fa-calendar-check"></i></span>
         <div class="info-box-content">
         <span class="info-box-text">Total de marcações</span>
         <span class="info-box-number">{{$stats['total_marc']}} marcações</span>
         </div>
      </div>
   </div>

   <div class="col-sm-6 col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-success"><i class="fas fa-utensils"></i></span>
         <div class="info-box-content">
         <span class="info-box-text">Total consumidas</span>
         <span class="info-box-number">{{$stats['total_cons']}} consumidas</span>
         </div>
      </div>
   </div>

   <div class="col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
         <div class="info-box-content">
            <span class="info-box-text">Taxa de consumo geral</span>
            <div class="progress">
               <div class="progress-bar" style="width: {{$stats['total_perc']}}%"></div>
            </div>
            <span class="progress-description">
            <b @if($stats['total_perc']>100) style="color: #e64c3c;" @elseif($stats['total_perc']<=50) style="color: #e64c3c;" @elseif($stats['total_perc']<=80) style="color: #f29b12;" @endif>{{$stats['total_perc']}}%</b>
            das refeições <b>marcadas</b> foram consumidas
            </span>
         </div>
      </div>
   </div>

   <div class="col-md-12" style="margin-bottom: 3rem;">
      <div style="display: inline; width: 100%; margin-bottom: 1rem; padding: 0.5rem;"><h5><b>Unidades</b></h5></div>
      <div class="card card-dark">
         <div class="card-body" style="max-height: none !important;">
            <table class="table table-hover projects" style="margin: 0.6rem !important;">
               <thead>
                  <tr>
                     <th>Data</th>
                     <th></th>
                     <th>Marcações <b>Militares</b></th>
                     <th>Consumidas <b>Militares</b></th>
                     <th></th>
                     <th>Marcações <b>Civis</b></th>
                     <th>Consumidas <b>Civis</b></th>
                     <th></th>
                  </tr>
               </thead>
               <tbody>
               @foreach($stats as $key => $entry)
                  @if(is_array($entry))
                  <tr data-widget="expandable-table" aria-expanded="false">
                        <td>
                           <!-- DATA -->
                              {{ $entry['unit_slug'] }}<br>
                              <b>{{ $entry['unit_name'] }}</b>
                        </td>
                        <td>
                           <!-- Refeição -->
                           <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">Pequeno-almoço</h6>
                           <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">Almoço</h6>
                           <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">Jantar</h6>
                        </td>

                        <td style="font-size: 0.8rem;">
                           <!-- Marcações Militares -->
                           <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">{{ $entry['total']["1REF"]['mil'] }}</h6>
                           <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">{{ $entry['total']["2REF"]['mil'] }}</h6>
                           <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">{{ $entry['total']["3REF"]['mil'] }}</h6>
                        </td>
                        <td>
                           <!-- Consumidas Militares -->
                           <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">{{ $entry['cons']["1REF"]['mil'] }}</h6>
                           <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">{{ $entry['cons']["2REF"]['mil'] }}</h6>
                           <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">{{ $entry['cons']["3REF"]['mil'] }}</h6>
                        </td>
                        <td style="font-size: 0.8rem;">
                           <!-- Percentagens Militares -->
                           <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">
                              @if($entry['perc']["1REF"]['mil']!="NA")
                              <!-- 1ºREF -->
                                 @if($entry['perc']["1REF"]['mil']>100)
                                    <span class="badge bg-danger">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["1REF"]['mil']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["1REF"]['mil']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["1REF"]['mil']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @else
                                    <span class="badge bg-danger"><b>{{$entry['perc']["1REF"]['mil']}}</b> %</span>
                                 @endif
                              @else
                              <span class="badge bg-danger">N/A</span>
                              @endif
                           </h6>
                           <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">
                           <!-- 2ºREF -->
                           @if($entry['perc']["2REF"]['mil']>100)
                                    <span class="badge bg-danger">{{$entry['perc']["2REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["2REF"]['mil']!="NA")
                              @if($entry['perc']["2REF"]['mil']>=90)
                                 <span class="badge bg-primary">{{$entry['perc']["2REF"]['mil']}} %</span>
                              @elseif($entry['perc']["2REF"]['mil']>=70)
                                 <span class="badge bg-success">{{$entry['perc']["2REF"]['mil']}} %</span>
                              @elseif($entry['perc']["2REF"]['mil']>=50)
                                 <span class="badge bg-warning">{{$entry['perc']["2REF"]['mil']}} %</span>
                              @else
                                 <span class="badge bg-danger"><b>{{$entry['perc']["2REF"]['mil']}}</b> %</span>
                              @endif
                           @else
                              <span class="badge bg-danger">N/A</span>
                           @endif
                           </h6>
                           <!-- 3ºREF -->
                           <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">
                              @if($entry['perc']["3REF"]['mil']!="NA")
                                 @if($entry['perc']["3REF"]['mil']>100)
                                    <span class="badge bg-danger">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["3REF"]['mil']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["3REF"]['mil']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["3REF"]['mil']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @else
                                    <span class="badge bg-danger"><b>{{$entry['perc']["3REF"]['mil']}}</b> %</span>
                                 @endif
                              @else
                                 <span class="badge bg-danger">N/A</span>
                              @endif
                           </h6>
                        </td>

                        <td style="font-size: 0.8rem;">
                           <!-- Marcações Civis -->
                           <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">{{ $entry['total']["1REF"]['civ'] }}</h6>
                           <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">{{ $entry['total']["2REF"]['civ'] }}</h6>
                           <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">{{ $entry['total']["3REF"]['civ'] }}</h6>
                        </td>
                        <td>
                           <!-- Consumidas Civis -->
                           <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">{{ $entry['cons']["1REF"]['civ'] }}</h6>
                           <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">{{ $entry['cons']["2REF"]['civ'] }}</h6>
                           <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">{{ $entry['cons']["3REF"]['civ'] }}</h6>
                        </td>
                        <td style="font-size: 0.8rem;">
                           <!-- Percentagens Civis -->
                           <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">
                              @if($entry['perc']["1REF"]['civ']!="NA")
                              <!-- 1ºREF -->
                                 @if($entry['perc']["1REF"]['civ']>100)
                                    <span class="badge bg-danger">{{$entry['perc']["1REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["1REF"]['civ']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["1REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["1REF"]['civ']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["1REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["1REF"]['civ']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["1REF"]['civ']}} %</span>
                                 @else
                                    <span class="badge bg-danger"><b>{{$entry['perc']["1REF"]['civ']}}</b> %</span>
                                 @endif
                              @else
                              <span class="badge bg-danger">N/A</span>
                              @endif
                           </h6>
                           <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">
                           <!-- 2ºREF -->
                           @if($entry['perc']["2REF"]['civ']>100)
                                    <span class="badge bg-danger">{{$entry['perc']["2REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["2REF"]['civ']!="NA")
                              @if($entry['perc']["2REF"]['civ']>=90)
                                 <span class="badge bg-primary">{{$entry['perc']["2REF"]['civ']}} %</span>
                              @elseif($entry['perc']["2REF"]['civ']>=70)
                                 <span class="badge bg-success">{{$entry['perc']["2REF"]['civ']}} %</span>
                              @elseif($entry['perc']["2REF"]['civ']>=50)
                                 <span class="badge bg-warning">{{$entry['perc']["2REF"]['civ']}} %</span>
                              @else
                                 <span class="badge bg-danger"><b>{{$entry['perc']["2REF"]['civ']}}</b> %</span>
                              @endif
                           @else
                              <span class="badge bg-danger">N/A</span>
                           @endif
                           </h6>
                           <!-- 3ºREF -->
                           <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">
                              @if($entry['perc']["3REF"]['civ']!="NA")
                                 @if($entry['perc']["3REF"]['civ']>100)
                                    <span class="badge bg-danger">{{$entry['perc']["3REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["3REF"]['civ']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["3REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["3REF"]['civ']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["3REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["3REF"]['civ']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["3REF"]['civ']}} %</span>
                                 @else
                                    <span class="badge bg-danger"><b>{{$entry['perc']["3REF"]['civ']}}</b> %</span>
                                 @endif
                              @else
                                 <span class="badge bg-danger">N/A</span>
                              @endif
                           </h6>
                        </td>


                     </tr>
                     <tr class="expandable-body d-none">
                        <td colspan="8">
                           @php $weekday_number = $semana[(date('N',  strtotime($stats['date'])) -1)]; @endphp
                           <p style="display: none; margin-botton: 0 !important;">
                              Neste dia, <b>{{ date('d', strtotime($stats['date'])) }} {{ $mes[($mes_index - 1)] }} de {{ date('Y', strtotime($stats['date'])) }}</b>,
                              @if($weekday_number=="7" || $weekday_number=="6") um @else uma @endif <b>{{ $weekday_number }}</b>,
                              na <b>1º refeição</b> houve <b>{{ $entry['total']["1REF"]['mil'] }} marcações por militares</b>, em que <b>{{ $entry['cons']["1REF"]['mil'] }} consumiram</b>,
                              @if($entry['perc']["1REF"]['mil']!="NA")
                                 uma taxa de consumo de &nbsp;
                                 @if($entry['perc']["1REF"]['mil']>100)
                                    <span class="badge bg-danger"  style="FONT-WEIGHT: BOLDER;FONT-SIZE: .9REM;">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["1REF"]['mil']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["1REF"]['mil']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["1REF"]['mil']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @else
                                    <span class="badge bg-danger">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @endif
                              @endif
                              e houve <b>{{ $entry['total']["1REF"]['civ'] }} marcações por civis</b>, em que <b>{{ $entry['cons']["1REF"]['civ'] }} consumiram</b>
                              @if($entry['perc']["1REF"]['mil']!="NA")
                                 , uma taxa de consumo de &nbsp;
                                 @if($entry['perc']["1REF"]['civ']>100)
                                    <span class="badge bg-danger"  style="FONT-WEIGHT: BOLDER;FONT-SIZE: .9REM;">{{$entry['perc']["1REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["1REF"]['civ']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["1REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["1REF"]['civ']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["1REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["1REF"]['civ']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["1REF"]['civ']}} %</span>
                                 @else
                                    <span class="badge bg-danger">{{$entry['perc']["1REF"]['civ']}} %</span>
                                 @endif
                              @endif
                              .
                              <br>
                             Em relação à <b>2º refeição</b> houve <b>{{ $entry['total']["2REF"]['mil'] }} marcações por militares</b>, em que <b>{{ $entry['cons']["2REF"]['mil'] }} consumiram</b>
                              @if($entry['perc']["2REF"]['mil']!="NA")
                              , uma taxa de consumo de &nbsp;
                                 @if($entry['perc']["2REF"]['mil']>100)
                                    <span class="badge bg-danger"  style="FONT-WEIGHT: BOLDER;FONT-SIZE: .9REM;">{{$entry['perc']["2REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["2REF"]['mil']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["2REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["2REF"]['mil']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["2REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["2REF"]['mil']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["2REF"]['mil']}} %</span>
                                 @else
                                    <span class="badge bg-danger">{{$entry['perc']["2REF"]['mil']}} %</span>
                                 @endif
                              @endif
                              e houve <b>{{ $entry['total']["2REF"]['civ'] }} marcações por civis</b>, em que <b>{{ $entry['cons']["2REF"]['civ'] }}
                              consumiram</b>
                              @if($entry['perc']["2REF"]['mil']!="NA")
                              , uma taxa de consumo de &nbsp;
                                 @if($entry['perc']["2REF"]['civ']>100)
                                    <span class="badge bg-danger" style="FONT-WEIGHT: BOLDER;FONT-SIZE: .9REM;">{{$entry['perc']["2REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["2REF"]['civ']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["2REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["2REF"]['civ']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["2REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["2REF"]['civ']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["2REF"]['civ']}} %</span>
                                 @else
                                    <span class="badge bg-danger">{{$entry['perc']["2REF"]['civ']}} %</span>
                                 @endif
                              @endif
                              .
                           <br>
                              Finalmente, na <b>3º refeição</b> houve <b>{{ $entry['total']["3REF"]['mil'] }} marcações por militares</b>
                              @if($entry['perc']["3REF"]['mil']!="NA")
                              ,em que <b>{{ $entry['cons']["3REF"]['mil'] }} consumiram</b>, uma taxa de consumo de &nbsp;
                                 @if($entry['perc']["3REF"]['mil']>100)
                                    <span class="badge bg-danger">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["3REF"]['mil']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["3REF"]['mil']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["3REF"]['mil']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @else
                                    <span class="badge bg-danger">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @endif
                              @endif
                              e houve <b>{{ $entry['total']["3REF"]['civ'] }} marcações por civis</b>, em que <b>{{ $entry['cons']["3REF"]['civ'] }} consumiram</b>
                              @if($entry['perc']["3REF"]['mil']!="NA")
                              , uma taxa de consumo de &nbsp;
                                 @if($entry['perc']["3REF"]['civ']>100)
                                    <span class="badge bg-danger">{{$entry['perc']["3REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["3REF"]['civ']>=90)
                                    <span class="badge bg-primary">{{$entry['perc']["3REF"]['civ']}} %</span>
                                 @elseif($entry['perc']["3REF"]['civ']>=70)
                                    <span class="badge bg-success">{{$entry['perc']["3REF"]['mil']}} %</span>
                                 @elseif($entry['perc']["3REF"]['civ']>=50)
                                    <span class="badge bg-warning">{{$entry['perc']["3REF"]['civ']}} %</span>
                                 @else
                                    <span class="badge bg-danger">{{$entry['perc']["3REF"]['civ']}} %</span>
                                 @endif
                              @endif
                              .
                           </p>
                        </td>
                     </tr>
                  @endif
               @endforeach
               </tbody>
            </table>
         </div>
      </div>

   </div>
   <div style="display: inline; width: 100%; margin-bottom: 1rem; padding: 0.5rem;"><h5><b>Pedidos quantitativos</b></h5></div>
   <div class="col-sm-6 col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-success"><i class="fas fa-calendar-check"></i></span>
         <div class="info-box-content">
         <span class="info-box-text">Total de pedidos</span>
         <span class="info-box-number">{{$pedidos['total_pedido']}} marcações</span>
         </div>
      </div>
   </div>

   <div class="col-sm-6 col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-success"><i class="fas fa-utensils"></i></span>
         <div class="info-box-content">
         <span class="info-box-text">Total consumidas</span>
         <span class="info-box-number">{{$pedidos['total_cons']}} consumidas</span>
         </div>
      </div>
   </div>

   <div class="col-12">
      <div class="info-box shadow-none">
         <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
         <div class="info-box-content">
            <span class="info-box-text">Taxa de consumo geral</span>
            <div class="progress">
               <div class="progress-bar" style="width: {{$pedidos['total_perc']}}%"></div>
            </div>
            <span class="progress-description">
            <b @if($pedidos['total_perc']>100) style="color: #e64c3c;" @elseif($pedidos['total_perc']<=50) style="color: #e64c3c;" @elseif($pedidos['total_perc']<=80) style="color: #f29b12;" @endif>{{$pedidos['total_perc']}}%</b> das refeições <b>pedidas</b> foram consumidas
            </span>
         </div>
      </div>
   </div>
   <div class="col-md-12">
   <div style="display: inline; width: 100%; margin-bottom: 1rem; padding: 0.5rem;"><h5><b>Locais de refeição</b></h5></div>
   <div class="card card-dark">
   <div class="card-body" style="max-height: none !important;">
   <table class="table table-hover projects" style="margin: 0.6rem !important;">
      <thead>
         <tr>
            <th>Data</th>
            <th></th>
            <th>Refeições <b>Pedidas</b></th>
            <th>Refeições <b>Consumidas</b></th>
            <th></th>
         </tr>
      </thead>
      <tbody>
      @foreach($pedidos as $key => $entry)
         @if(is_array($entry))
                  <tr>
                     <td>
                     <!-- DATA -->
                        <b>{{ $entry["local_name"] }}</b>
                     </td>
                     <td>
                        <!-- Refeição -->
                        <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">Pequeno-almoço</h6>
                        <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">Almoço</h6>
                        <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">Jantar</h6>
                     </td>
                     <td style="font-size: 0.8rem;">
                        <!-- Pedidos  -->
                        <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">{{ $entry['total']["1REF"] }}</h6>
                        <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">{{ $entry['total']["2REF"] }}</h6>
                        <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">{{ $entry['total']["3REF"]}}</h6>
                     </td>
                     <td>
                        <!-- Consumidas -->
                        <h6 style="font-size: 0.8rem; margin-top: 0.5rem;">{{ $entry['cons']["1REF"] }}</h6>
                        <h6 style="font-size: .8rem; margin: 0.7rem 0 0.7rem;">{{ $entry['cons']["2REF"] }}</h6>
                        <h6 style="font-size: 0.8rem; margin-bottom: 0.5rem;">{{ $entry['cons']["3REF"]}}</h6>
                     </td>
                     <td style="font-size: 1.2rem;">
                        <!-- Percentagens  -->
                        <h6 style="font-size: 1.2rem; margin-top: 0.5rem;">
                           @if($entry['perc']["1REF"]!="NA")
                           <!-- 1ºREF -->
                              @if($entry['perc']["1REF"]>100)
                                 <span class="badge bg-danger">{{$entry['perc']["1REF"]}} %</span>
                              @elseif($entry['perc']["1REF"]>=90)
                                 <span class="badge bg-primary">{{$entry['perc']["1REF"]}} %</span>
                              @elseif($entry['perc']["1REF"]>=70)
                                 <span class="badge bg-success">{{$entry['perc']["1REF"]}} %</span>
                              @elseif($entry['perc']["1REF"]>=50)
                                 <span class="badge bg-warning">{{$entry['perc']["1REF"]}} %</span>
                              @else
                                 <span class="badge bg-danger"><b>{{$entry['perc']["1REF"]}}</b> %</span>
                              @endif
                           @else
                           <span class="badge bg-danger">N/A</span>
                           @endif
                        </h6>
                        <h6 style="font-size: 1.2rem; margin: 0.7rem 0 0.7rem;">
                        <!-- 2ºREF -->
                        @if($entry['perc']["2REF"]>100)
                           <span class="badge bg-danger">{{$entry['perc']["2REF"]}} %</span>
                        @elseif($entry['perc']["2REF"]!="NA")
                           @if($entry['perc']["2REF"]>=90)
                              <span class="badge bg-primary">{{$entry['perc']["2REF"]}} %</span>
                           @elseif($entry['perc']["2REF"]>=70)
                              <span class="badge bg-success">{{$entry['perc']["2REF"]}} %</span>
                           @elseif($entry['perc']["2REF"]>=50)
                              <span class="badge bg-warning">{{$entry['perc']["2REF"]}} %</span>
                           @else
                              <span class="badge bg-danger"><b>{{$entry['perc']["2REF"]}}</b> %</span>
                           @endif
                        @else
                           <span class="badge bg-danger">N/A</span>
                        @endif
                        </h6>
                        <!-- 3ºREF -->
                        <h6 style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                           @if($entry['perc']["3REF"]!="NA")
                              @if($entry['perc']["3REF"]>100)
                                 <span class="badge bg-danger">{{$entry['perc']["3REF"]}} %</span>
                              @elseif($entry['perc']["3REF"]>=90)
                                 <span class="badge bg-primary">{{$entry['perc']["3REF"]}} %</span>
                              @elseif($entry['perc']["3REF"]>=70)
                                 <span class="badge bg-success">{{$entry['perc']["3REF"]}} %</span>
                              @elseif($entry['perc']["3REF"]>=50)
                                 <span class="badge bg-warning">{{$entry['perc']["3REF"]}} %</span>
                              @else
                                 <span class="badge bg-danger"><b>{{$entry['perc']["3REF"]}}</b> %</span>
                              @endif
                           @else
                              <span class="badge bg-danger">N/A</span>
                           @endif
                        </h6>
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
