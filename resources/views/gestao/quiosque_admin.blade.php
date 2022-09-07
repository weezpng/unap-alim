@extends('layout.master')
@section('title','Entradas quiosque')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Quiosque</li>
@endsection
@section('page-content')
<div class="modal puff-in-center" id="GenerateQR" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-qrcode"> </i> &nbsp; Gerar códigos QR &nbsp; </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
          <div class="modal-body">
            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-8 col-form-label">Códigos pedidos por utilizadores</label>
               <div class="col-sm-4">
                  <a href="{{ route('gestao.qrs.mass') }}">
                     <button type="submit" class="btn btn-sm btn-dark marcar-ref-btn" style="margin-top: calc(0.375rem + 1px);">Gerar</button> 
                  </a>
               </div>
            </div>
            <hr style="border-top-color: #6c757d;">

            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-8 col-form-label">Código Candidatos (PCS)</label>
               <div class="col-sm-4">
                  <a href="{{ route('gestao.qrs.pcs') }}">
                     <button type="submit" class="btn btn-sm btn-dark marcar-ref-btn" style="margin-top: calc(0.375rem + 1px);">Gerar</button> 
                  </a>
               </div>
            </div>

            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-8 col-form-label">Código Dia de Defesa Nacional</label>
               <div class="col-sm-4">
                  <a href="{{ route('gestao.qrs.ddn') }}">
                     <button type="submit" class="btn btn-sm btn-dark marcar-ref-btn" style="margin-top: calc(0.375rem + 1px);">Gerar</button> 
                  </a>
               </div>
            </div>

            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-8 col-form-label">Código Outros Pedidos</label>
               <div class="col-sm-4">
                  <a href="{{ route('gestao.qrs.dlg') }}">
                     <button type="submit" class="btn btn-sm btn-dark marcar-ref-btn" style="margin-top: calc(0.375rem + 1px);">Gerar</button> 
                  </a>
               </div>
            </div>


         </div>         
      </div>
    </div>
  </div>
@if ($MASS_QR_GENERATE)


@endif



<div class="col-md-12">
   <div class="card">
     <div class="card-header border-0">
        <div class="d-flex justify-content-between">
           <h3 class="card-title">Entradas por dia</h3>
           <div class="card-tools" style="margin-right: 0 !important;">
               @if ($MASS_QR_GENERATE)
                  <a href="#"  data-toggle="modal" data-target="#GenerateQR" style="margin: 0 .5rem 0 .65rem;"> &nbsp; Gerar códigos QR &nbsp; <i class="fas fa-qrcode"></i></a>
               @endif
              &nbsp;
              <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>              
           </div>
        </div>
     </div>
      <div class="card-body" style="max-height: 77vh;">
      @if(!empty($info))
         @foreach($info as $key => $info_entry)
            <div id="accordion">
               <div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}" class="collapsed" aria-expanded="false">
                        {{ $info_entry['date'] }}
                     </a>                     
                     </h4>
                     <div class="card-tools" style="margin-right: 0 !important;">
                        &nbsp;
                        <button type="button" class="btn btn-tool" onclick="createPDF('{{$key}}', '{{ $info_entry['date'] }}')"><i class="fas fa-print"></i>    
                     </div>
                  </div>
                  <div id="collapse{{$key}}" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                        <table class="table table-striped projects" id='{{$key}}'>
                           <thead>
                              <tr>
                                 <th>
                                    Utilizador
                                 </th>
                                 <th style="width: 15%">
                                    Refeição
                                 </th>
                                 <th style="width: 15%">
                                    Refeição marcada
                                 </th>
                                 <th style="width: 15%">
                                    Local
                                 </th>                                 
                                 <th style="width: 15%">
                                    Entrada no quiosque
                                 </th>
                              </tr>
                           </thead>
                           <tbody>
                              @foreach($info_entry as $row => $user_entry)
                                 @if (is_array($user_entry))
                                 <tr>
                                    <td>
                                       <h6>{{ $user_entry['POSTO'] }}</h6>
                                       <h6 style="display: none;">{{$user_entry['NIM']}}</h6>
                                       @if($user_entry['NOME']=='Diligência' || $user_entry['NOME']=='Dia de Defesa Nacional' || $user_entry['NOME']=='Provas de Classificação e Seleção' ||  $user_entry['NOME']=='Desconhecido')
                                          <h5 style="text-transform: uppercase;"> {{ $user_entry['NOME'] }} </h5>
                                       @else
                                       <h5><a href="{{route('user.profile', $user_entry['NIM'])}}"> {{ $user_entry['NOME'] }}</a></h5>
                                       @endif
                                    </td>
                                    <td>
                                       {{ $user_entry['REF'] }}
                                    </td>
                                    <td> 
                                    @if($user_entry['NOME']=='Diligência' || $user_entry['NOME']=='Dia de Defesa Nacional' || $user_entry['NOME']=='Provas de Classificação e Seleção' ||  $user_entry['NOME']=='Desconhecido')

                                    @else
                                       <div class="custom-control custom-checkbox">
                                          <input class="custom-control-input" type="checkbox" id="customCheckbox3" disabled="" @if($user_entry['MARCADA']=="1") checked @endif>
                                          <label for="customCheckbox3" class="custom-control-label"></label>
                                          @if($user_entry['MARCADA']=="1") Sim @else Não @endif
                                       </div>
                                    @endif
                                    </td>
                                    <td>
                                       {{ $user_entry['LOCAL'] }}
                                    </td>
                                    <td>
                                       <b>{{ $user_entry['REGISTADO_TIME'] }}</b> 
                                    </td>
                                 </tr>
                                 @endif
                              @endforeach
                           </tbody>
                        </table>
                        </div>
                     </div>
                  </div>
               </div>
               @endforeach     
            @else
               <h6>Nenhuma entrada de quiosque.</h6>
            @endif        
      </div>
      <!-- /.card-body -->
   </div>
</div>
@endsection

@section('extra-scripts')
<script>
    function createPDF(id, date) {
        var sTable = document.getElementById(id).outerHTML;
        var style = "<style>";
        style = style + "table {width: 100%;font: 17px Calibri;}";
        style = style + "h3 {width: 100%;font: 24px Calibri; margin-top: 1rem;}";
        style = style + "span {display: block; font-size: 18px;}";
        style = style + "a {text-decoration: none;}";        
        style = style + "table, th, td {border: solid 1px #DDD; border-collapse: collapse;";
        style = style + "padding: 2px 3px;text-align: center;}";
        style = style + "h6 { margin: 0; margin-top: 0.2rem; display: block !important;} h5 {margin: 0.2rem; }";
        style = style + "</style>";
        var win = window.open('', '', 'height=1024,width=2048');
        win.document.write('<html><head>');
        win.document.write('<title>Entradas no quiosque a ' +date+ '</title>');   // <title> FOR PDF HEADER.
        win.document.write(style);          // ADD STYLE INSIDE THE HEAD TAG.
        win.document.write('</head>');
        win.document.write('<h3>Entradas no quiosque<span>' +date+ '</span></h3>')
        win.document.write('<body>');
        win.document.write(sTable);         // THE TABLE CONTENTS INSIDE THE BODY TAG.
        win.document.write('</body></html>');
        win.document.close(); 	// CLOSE THE CURRENT WINDOW.
        win.print();    // PRINT THE CONTENTS.
    }
</script>

@endsection