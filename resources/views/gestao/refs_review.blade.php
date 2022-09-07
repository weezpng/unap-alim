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
            <div class="col-md-12" style="overflow-y: auto; height: 85vh;">
              
               
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
