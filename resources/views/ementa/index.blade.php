@extends('layout.master')
@section('title','Ementa')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Ementa</li>
<li class="breadcrumb-item active">Atual</li>
@endsection
@section('page-content')
<div class="col-md-12">
<a id="back-to-top" href="#" onclick="printToFile()" class="btn btn-primary back-to-top elevation-4" role="button" aria-label="Save to file">
  <i class="fas fa-print"></i>
   </a>
   <div class="card">
     <div class="card-header border-0">
        <div class="d-flex justify-content-between">
           <h3 class="card-title">Ementa</h3>
           <div class="card-tools" style="margin-right: 0 !important;">
              &nbsp;
              <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
           </div>
        </div>
     </div>
      <div class="card-body" style="overflow-y: scroll; max-height: 77vh;">
         @if($meals!=null)
         <table class="table projects">
            <thead>
               <tr>
                  <th style="width: 10%">
                     Data
                  </th>
                  <th style="width: 15%">
                     Refeição
                  </th>
                  <th>
                     Ementa
                  </th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  @foreach($meals as $refeiçao)
                  @if($refeiçao['meal']=="1REF")
                  <td rowspan="2">
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($refeiçao['data']));
                        $weekday_number = date('N',  strtotime($refeiçao['data']));
                     @endphp
                     <strong>
                     {{ date('d', strtotime($refeiçao['data'])) }}
                     {{ $mes[($mes_index - 1)] }}<br>
                     </strong>
                     <span @if($weekday_number=="7" || $weekday_number=="6") style="font-size: .85rem;color: #92b1d1;" @else style="font-size: .85rem;" @endif>{{ $semana[($weekday_number -1)] }}</span>
                  </td>
                  @else
                  <td>
                     @if($refeiçao['meal']=="2REF")
                        <b>Almoço</b>
                     @elseif($refeiçao['meal']=="3REF")
                        <b>Jantar</b>
                     @endif
                  </td>
                  <td>
                     @if($refeiçao['meal']=="2REF")
                        Sopa: <strong>{{$refeiçao['sopa_almoço']}}</strong> <br>
                        Prato: <strong>{{$refeiçao['prato_almoço']}}</strong> <br>
                        Sobremesa: <strong>{{$refeiçao['sobremesa_almoço']}}</strong>
                     @elseif($refeiçao['meal']=="3REF")
                        Sopa: <strong>{{$refeiçao['sopa_jantar']}}</strong> <br>
                        Prato: <strong>{{$refeiçao['prato_jantar']}}</strong> <br>
                        Sobremesa: <strong>{{$refeiçao['sobremesa_jantar']}}</strong>
                     @endif
                  </td>
               </tr>
                  @if($refeiçao['meal']=="3REF" )
                     @if(!$loop->last)
                     <tr class="marcar-ref-spacer">
                     </tr>
                     @endif
                  @endif
               @endif
               @endforeach
            </tbody>
         </table>
         @else
         <h6>Ementa não publicada.</h6>
         @endif
      </div>
      <!-- /.card-body -->
   </div>
</div>
@endsection

@section('extra-scripts')
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
      <title>Ementa</title>`);
      mywindow.document.write('</head><body>');
      mywindow.document.write(document.getElementById('tarbody').innerHTML.replaceAll("collapsed-card ", ""));
      mywindow.document.write('</body></html>');
      mywindow.document.close();
      mywindow.focus();
      sleep(4000);
      mywindow.print();

   }
</script>
@endsection
