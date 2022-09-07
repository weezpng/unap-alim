@extends('layout.master')
@section('title','GESTÃO DE EMENTA')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Nova ementa</li>
@endsection
@section('page-content')

  @php
  $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
  $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");

  @endphp

<div class="col-md-12">
   <div class="card">
      <div class="card-header">
         <h3 class="card-title">Rever ementa</h3>
      </div>
      <div class="card-body" style="max-height: 90vh;">
         <div class="row">
            <div class="col-md-7" style="overflow-y: auto; height: 85vh;">
               @foreach ($ementaRever as $key0 => $dia)
               <div class="row">
                  <div class="col-md-2">
                     <p style="margin-top: 3rem;">
                     <h6>DATA</h6>

                     @php
                       $mes_index = date('m', strtotime($key0));
                       $weekday_number = date('N',  strtotime($key0));
                     @endphp
                     <strong>
                     {{ date('d', strtotime($key0)) }}
                     {{ $mes[($mes_index - 1)] }}<br>
                     </strong>
                     <span style="font-size: .85rem;">{{ $semana[($weekday_number -1)] }}</span>
                     </p>
                  </div>
                  <div class="col-md-7">
                     <div class="col-md-12">
                        <div class="callout callout-success hide" style="box-shadow: none !important; margin-top: 2rem; z-index: 999;" id="sucessToast{{ $key0 }}">
                           <h5>Publicada!</h5>
                           <p>A ementa para o dia <strong>{{ date('d', strtotime($key0)) }} de {{ $mes[($mes_index - 1)] }}</strong> foi carregada.</p>
                        </div>
                        <div class="callout callout-danger hide" style="box-shadow: none !important; margin-top: 2rem; z-index: 999;" id="errorToast{{ $key0 }}">
                           <h5>Erro</h5>
                           <p id="errorToastMessage{{ $key0 }}"></p>
                        </div>
                        <form class="form-horizontal" method="POST" id="{{ $dia['id'] }}" action="{{ route('gestao.postarEmentaPost') }}">
                           @foreach ($dia as $key1 => $ref)
                           <div class="refReviewDiv">
                              @if ($key1=="almoço")
                              <h5>
                                 <p style="margin-bottom: 2rem;">
                                    ALMOÇO
                                 </p>
                              </h5>
                              @csrf
                              <input type="hidden" value="{{$ref['data']}}" id="data" name="data">
                              <div class="form-group row" style="margin-bottom: 0 !important">
                                 <label for="inputName" class="col-sm-2 col-form-label">Sopa</label>
                                 <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{$ref['sopa']}}" id="ref_soup_almoço" name="ref_soup_almoço" placeholder="Sopa do almoço"><br>
                                 </div>
                              </div>
                              <div class="form-group row" style="margin-bottom: 0 !important">
                                 <label for="inputName" class="col-sm-2 col-form-label">Prato</label>
                                 <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{$ref['prato']}}" id="ref_plate_almoço" name="ref_plate_almoço" placeholder="Prato do almoço"><br>
                                 </div>
                              </div>
                              <div class="form-group row" style="margin-bottom: 0 !important">
                                 <label for="inputName" class="col-sm-2 col-form-label">Sobremesa</label>
                                 <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{$ref['sobremesa']}}" id="ref_dessert_almoço" name="ref_dessert_almoço" placeholder="Sobremesa do almoço"><br>
                                 </div>
                              </div>
                              @elseif ($key1=="jantar")
                              <h5>
                                 <p style="margin-bottom: 2rem;">
                                    JANTAR
                                 </p>
                              </h5>
                              <div class="form-group row" style="margin-bottom: 0 !important">
                                 <label for="inputName" class="col-sm-2 col-form-label">Sopa</label>
                                 <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{$ref['sopa']}}" id="ref_soup_jantar" name="ref_soup_jantar" placeholder="Sopa do jantar"><br>
                                 </div>
                              </div>
                              <div class="form-group row" style="margin-bottom: 0 !important">
                                 <label for="inputName" class="col-sm-2 col-form-label">Prato</label>
                                 <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{$ref['prato']}}" id="ref_plate_jantar" name="ref_plate_jantar" placeholder="Prato do jantar"><br>
                                 </div>
                              </div>
                              <div class="form-group row" style="margin-bottom: 0 !important">
                                 <label for="inputName" class="col-sm-2 col-form-label">Sobremesa</label>
                                 <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{$ref['sobremesa']}}" id="ref_dessert_jantar" name="ref_dessert_jantar" placeholder="Sobremesa do jantar"><br>
                                 </div>
                              </div>
                              @endif
                           </div>
                           @endforeach
                           <div class="form-group row profile-settings-form-svbtn-spacer">
                              <div class="col-sm-12">
                                 <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif">Confirmar</button>
                              </div>
                           </div>
                        </form>
                        <hr style="margin-bottom: .5rem; margin-top: 3rem !important; border-top-color:#9090908c;">
                     </div>
                  </div>
               </div>
               @endforeach
               <a href="{{route('gestao.ementa.index')}}">
                  <div class="col-md-7 offset-md-2">
                     <button class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif hide" id="completeAddingBtn" style="width: 100%; margin-top: 5rem;" >Concluir</button>
                  </div>
               </a>
            </div>
            <div class="col-md-5" style="height: 85vh !important; overflow-y: hidden;">
               @php $file = asset('filesys\ementas\pdf\\'.$pdfFile); @endphp
               <div style="width: 100%;overflow: hidden;">
                  <iframe style="width:100%; position: relative; min-height: 1600px !important; max-height: 1700px !important;" src="{{$file}}" frameborder='0' title="Revisão de ementa" scroll="no"></iframe>
               </div>
            </div>
         </div>
         <!-- /.card-body -->
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   $('.form-horizontal').on('submit', function(e){
     e.preventDefault();
     var form = this;
     var data = $("input[name='data']",form).val();
     var almoço_sopa = $("input[name='ref_soup_almoço']",form).val();
     var almoço_prato = $("input[name='ref_plate_almoço']",form).val();
     var almoço_sobremesa = $("input[name='ref_dessert_almoço']",form).val();
     var jantar_sopa = $("input[name='ref_soup_jantar']",form).val();
     var jantar_prato = $("input[name='ref_plate_jantar']",form).val();
     var jantar_sobremesa = $("input[name='ref_dessert_jantar']",form).val();
     $.ajax({
         type: "POST",
         async: true,
         url: "{{route('gestao.postarEmentaPost')}}",
         data: {
             "_token": "{{ csrf_token() }}",
             data: data,
             almoço_sopa: almoço_sopa,
             almoço_prato: almoço_prato,
             almoço_sobremesa: almoço_sobremesa,
             jantar_sopa: jantar_sopa,
             jantar_prato: jantar_prato,
             jantar_sobremesa: jantar_sobremesa
         },
         success: function (msg) {
             var errorToastName = '#errorToast' + data;
             if (msg != 'success') {
               var errorToastMessage = '#errorToastMessage' + data;
               $(errorToastName).removeClass("hide");
               $(errorToastMessage).text(msg)
               if (msg.includes("já se encontra publicada")) {
                 $(form).animate({
                    padding: "0px",
                   'font-size': "0px",
                   'margin-top':'-' + ($(form).height() - $(errorToastName).outerHeight(true)) + "px"
                 }, 700, function() {
                    $(form).remove();
                    $(errorToastMessage).removeClass("hide");
                });
              }
              var numForms = $('.form-horizontal').length;
              if (numForms == 1) {
                $("#completeAddingBtn").removeClass("hide");
              }
             } else {
               var successToastName = '#sucessToast' + data;
               $(form).animate({
                  padding: "0px",
                 'font-size': "0px",
                 'margin-top':'-' + ($(form).height() - $(successToastName).outerHeight(true)) + "px"
               }, 700, function() {
                  $(form).remove();
                  $(errorToastName).addClass("hide");
                  $(successToastName).removeClass("hide");
              });
              var numForms = $('.form-horizontal').length;
              if (numForms == 1) {
                $("#completeAddingBtn").removeClass("hide");
              }
             }
         }
     });
   })
</script>
@endsection
