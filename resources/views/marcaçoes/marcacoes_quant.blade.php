@extends('layout.master')
@section('extra-links')
<link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
@endsection
@section('title','Marcações')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Marcações</li>
<li class="breadcrumb-item active">Marcações quantitativas</li>
@endsection
@section('page-content')
@php
$today = date("Y-m-d");
if (!$SHORT_PERIOD_REMOVAL) {
$minDay = date('d/m/Y', strtotime($today. ' + '.$minDaysMarcar.' days'));
} else {
$minDay = date('d/m/Y', strtotime($today. ' + 3 days'));
}
@endphp
<div class="modal puff-in-center" id="addExternalMeal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Criar pedido quantítativo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('marcacao.non_nominal_add')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <div class="form-group row">
                  <label for="inputName" class="col-sm-5 col-form-label">Quantidade</label>
                  <div class="col-sm-7">
                     <input type="number" required class="form-control" id="quantidade" autocomplete="off" name="quantidade" placeholder="Quantidade de refeições">
                  </div>
               </div>
               <div class="form-group row">

                  <label for="inputName" class="col-sm-5 col-form-label">Razão</label>
                  <div class="col-sm-7">
                       <select required class="form-control" id="reason" name="reason" >
                           <option selected disabled value="0">Razão do pedido</option>
                           <option value="DDN">Dia de Defesa Nacional</option>
                           <option value="PCS">Provas de Classificação e Seleção</option>
                           <option value="DILIGENCIA">Diligência</option>
                           <option value="OUTROS">Outros</option>

                       </select>
                  </div>
                  <div class="col-sm-7 offset-sm-5" id="desc">
                     <input type="text" required class="form-control" id="reason_2" name="reason_2" value="0" placeholder="Escreva um descritivo para o pedido" style="margin-top: .25rem;">
                  </div>
               </div>
               <div class="form-group row" id="customTimeInput" name="customTimeInput">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Datas</label>
                  <div class="col-sm-7">
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text">
                           <i class="far fa-calendar-alt"></i>
                           </span>
                        </div>
                        <input type="text" placeholder="Periodo de tempo" autocomplete="off" required class="form-control float-right" name="dateRangePicker" id="dateRangePicker" data-toggle="dateRangePicker" data-target="#dateRangePicker">
                     </div>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="inputName" class="col-sm-5 col-form-label">Refeições</label>
                  <div class="col-sm-7">
                     <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="check_pqalmoco" name="check_pqalmoco">
                        <label for="check_pqalmoco" class="custom-control-label">Pequeno-almoço</label>
                     </div>
                     <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="check_almoco" name="check_almoco">
                        <label for="check_almoco" class="custom-control-label">Almoço</label>
                     </div>
                     <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="check_jantar" name="check_jantar">
                        <label for="check_jantar" class="custom-control-label">Jantar</label>
                     </div>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="inputName" class="col-sm-5 col-form-label">Reforços</label>
                  <div class="col-sm-7">
                     <input type="number" required class="form-control" id="reforços" autocomplete="off" name="reforços" placeholder="Quantidade de reforços">
                  </div>
               </div>
               <div class="form-group row" id="localInput">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Local</label>
                  <div class="col-sm-7">
                        <select required class="custom-select" id="local" name="local">
                           <option selected disabled value="0">Selecione o local</option>
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
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Concluir</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      <div class="card-header border-0">
         <h3 class="card-title">Marcações quantitativas</h3>
         <div class="card-tools">
            <a href="#" data-toggle="modal" data-target="#addExternalMeal">Novo pedido &nbsp; <i class="fas fa-calendar-plus">&nbsp;</i></a>
            &nbsp;&nbsp;&nbsp;
            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
         </div>
      </div>
      <div class="card-body">
         @if ($meus_pedidos)
         <h6>Lista de pedidos feitos {{ $perm_desc }}</h6>
         @foreach($meus_pedidos as $key => $entry)
         <h5 style="margin-top: 2rem; padding-top: 1rem;">
            <strong>
               @php
               $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
               $mes_index = date('m', strtotime($key));
               @endphp
               {{ date('d', strtotime($key)) }} {{ $mes[($mes_index - 1)] }} de {{ date('Y', strtotime($key)) }}
            </strong>
         </h5>
         <table class="table table-striped projects" style="margin-top: 1.5rem; padding-bottom: .5rem; border-bottom: 2px solid #6c757d;">
            <thead>
               <tr>
                  <th style="width: 18%">
                     Local
                  </th>
                  <th style="width: 15%">
                     Refeição
                  </th>
                  <th style="width: 17%">
                     Razão
                  </th>
                  <th colspan="2"  style="width: 22%">
                     Quantidade
                  </th>
                  <th>
                    Pedido por
                  </th>
                  <th>
                  </th>
               </tr>
            </thead>
            <tbody>
               @foreach ($entry as $key0 => $pedido)
               <tr>
                  <td style="border-top: none !important;">
                     @if ($pedido['local_ref']=="QSP")
                        Quartel da <strong>Serra do Pilar</strong>
                     @elseif ($pedido['local_ref']=="QSO")
                        Quartel de <strong>Santo Ovídio</strong>
                     @elseif ($pedido['local_ref']=="MMANTAS")
                        Messe Militar das <strong>Antas</strong>
                     @elseif ($pedido['local_ref']=="MMBATALHA")
                        Messe Militar da <strong>Batalha</strong>
                     @endif
                  </td>
                  <td style="border-top: none !important;">
                     <strong>
                     @if ($pedido['meal']=="1REF")
                        Pequeno-almoço
                     @elseif ($pedido['meal']=="2REF")
                        Almoço
                     @elseif ($pedido['meal']=="3REF")
                        Jantar
                     @endif
                     <br /><span style="font-size: .9rem;">{{ date('d', strtotime($key)) }} {{ $mes[($mes_index - 1)] }} de {{ date('Y', strtotime($key)) }}</span>
                     </strong>
                  </td>
                  <td style="border-top: none !important;">
                  @if ($pedido['motive']=="DDN")
                     Dia de Defesa Nacional
                  @elseif($pedido['motive']=="PCS")
                     Provas de Classificação e Seleção
                  @else
                     {{ $pedido['motive'] }}
                  @endif
                  </td>
                  <td style="border-top: none !important;">
                    @if($pedido['quantidade']>0)
                      @if ($pedido['quantidade']==1)
                        <strong>{{ $pedido['quantidade'] }}&nbsp;</strong>refeição
                      @else
                        <strong>{{ $pedido['quantidade'] }}&nbsp;</strong>refeições
                      @endif
                    @else
                        <strong>Sem&nbsp;</strong>refeições pedidas
                    @endif
                  </td>
                  <td style="border-top: none !important;">
                  @if($pedido['qty_reforços']>0)
                    @if ($pedido['qty_reforços']==1)
                      <strong>{{ $pedido['qty_reforços'] }}&nbsp;</strong>reforço
                    @else
                      <strong>{{ $pedido['qty_reforços'] }}&nbsp;</strong>reforços
                    @endif
                  @else
                  <strong>Sem&nbsp;</strong>reforços pedidos
                  @endif
                  </td>
                  <td  style="border-top: none !important;">
                    @if(Auth::user()->dark_mode=='Y')
                      <img src="{{ asset('assets/icons/postos/TRANSPARENT_WHITE/'.$pedido['posto'].'.png') }}" style="display: inline-block; margin-top: -1rem !important; width: 3.5rem; height: 3.5rem; padding: 0.5rem; margin: 0.5rem; object-fit: scale-down;"/>
                    @else
                      <img src="{{ asset('assets/icons/postos/TRANSPARENT/'.$pedido['posto'].'.png') }}" style="display: inline-block; margin-top: -1rem !important; width: 3.5rem; height: 3.5rem; padding: 0.5rem; margin: 0.5rem; object-fit: scale-down;"/>
                    @endif
                    <div style="display: inline-block; height: 3.5rem; ">
                      <br />
                      <span style="font-size: .9rem;">{{ $pedido['nim'] }}</span><br />
                      {{ $pedido['name'] }}
                    </div>
                  </td>
                  <td class="project-actions text-right" style="border-top: none !important;">
                    @if ($SHORT_PERIOD_TAGS)
                      @php
                        $maxDays = 1;
                      @endphp
                    @endif

                     @php $maxdate = date("Y-m-d", strtotime("-".$maxDays." days", strtotime($pedido['data_pedido']))); @endphp
                     @if($today<=$maxdate)
                     <form method="POST" action="{{route('marcacao.non_nominal_remove')}}">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{$pedido['id']}}"></input>
                        <button type="submit" class="btn btn-sm btn-danger remove-ref-btn"><i class="fas fa-trash"></i>&nbspRemover</button>
                     </form>
                     @else
                       <a class="btn btn-danger btn-sm disabled remove-ref-btn" href="#"><i class="fas fa-ban"></i>&nbspBloqueada</a>
                     @endif

                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
         @endforeach
         @else
         <h6>Actualmente, você não tem nenhum pedido de refeição não nominal.</h6>
         @endif
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/daterangepicker.js')}}"></script>

@if ($SHORT_PERIOD_TAGS)
  @php
    $minDay = date("d-m-Y", strtotime(date('Y-m-d') . "+ 1 days"));
  @endphp
@endif
@if ($ZERO_PERIOD_TAGS)
  @php
      $minDay = date("d-m-Y", strtotime(date('Y-m-d')));
  @endphp
@endif
<script>
   $(document).ready(function() {
      $("#desc").hide();
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
         parentEl: $('#addExternalMeal'),
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

   $("#reason").change(function() {
      if($("#reason").val()=="OUTROS"){
         $("#desc").show();
         $("#reason_2").val("");
      } else {
         $("#desc").hide();
         $("#reason_2").val("0");
      }
   });
</script>
@endsection
