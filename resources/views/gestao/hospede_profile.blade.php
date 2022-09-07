@extends('layout.master')
@section('title','Perfil')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Gestão</li>
<li class="breadcrumb-item active">Hóspede</li>
<li class="breadcrumb-item active">{{$hospede['name']}}</li>
@endsection
@section('page-content')
@include('layout.float-btn')
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header p-2">
         <div class="card-tools" style="margin-right: .5rem !important; margin-top: 0.4rem !important;">
            <a href="{{ route('gestao.hospedes') }}">Voltar atrás</a>
         </div>
         <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#info" data-toggle="tab">Informação</a></li>
            <li class="nav-item"><a class="nav-link" href="#marcar" data-toggle="tab">Marcações</a></li>
            <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Definições</a></li>
         </ul>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
         <div class="tab-content">
            <div class="tab-pane swing-in-left-fwd active" id="info">
               <p>
               <h5>HÓSPEDE Nº</h5>
               <h6><b>{{$hospede['id']}}</b></h6>
               </p>
               <p>
               <h5>NOME</h5>
               <h6><b>{{$hospede['name']}}</b></h6>
               </p>
               <p>
               <h5>CONTACTO</h5>
               <h6><b>{{$hospede['contacto']}}</b></h6>
               </p>
               <p>
               <h5>TIPO</h5>
               <h6><b>{{$hospede['type']}}</b></h6>
               </p>
            </div>
            <div class="tab-pane swing-in-left-fwd" id="marcar">
              @if ($allRefs)
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
                        <td>
                           @php
                           $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                           $mes_index = date('m', strtotime($refeiçao['data']));
                           @endphp
                           {{ date('d', strtotime($refeiçao['data'])) }}
                           {{ $mes[($mes_index - 1)] }}
                        </td>
                        <td>
                           @if($refeiçao['meal']=="1REF")
                           1ºRefeição
                           @elseif($refeiçao['meal']=="3REF")
                           3ºRefeição
                           @else
                           2ºRefeição
                           @endif
                        </td>
                        <td>
                           @if($refeiçao['meal']=="1REF")
                           Pequeno-almoço
                           @elseif($refeiçao['meal']=="2REF")
                           Sopa: {{$refeiçao['sopa_almoço']}} <br>
                           Prato: {{$refeiçao['prato_almoço']}} <br>
                           Sobremesa: {{$refeiçao['sobremesa_almoço']}}
                           @elseif($refeiçao['meal']=="3REF")
                           Sopa: {{$refeiçao['sopa_jantar']}} <br>
                           Prato: {{$refeiçao['prato_jantar']}} <br>
                           Sobremesa: {{$refeiçao['sobremesa_jantar']}}
                           @endif
                        </td>
                        <td>
                           @if($refeiçao['marcado']==1)
                           <form method="POST" action="{{route('marcacao.destroy.hospede')}}">
                              @csrf
                              <input type="hidden" id="data" name="data" value="{{$refeiçao['data']}}"></input>
                              <input type="hidden" id="ref" name="ref" value="{{$refeiçao['meal']}}"></input>
                              <input type="hidden" id="user" name="user" value="{{$hospede['fictio_nim']}}"></input>
                              <button style="WIDTH: 100% !important;" type="submit" class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn"><i class="far fa-calendar-times">&nbsp&nbsp&nbsp</i>Desmarcar</button>
                           </form>
                           @else
                           @php $formid = Str::random(32); @endphp
                           <form method="POST" id="{{ $formid }}">
                              @csrf
                              <input type="hidden" id="user{{$formid}}" name="user" value="{{$hospede['fictio_nim']}}"></input>
                              <input type="hidden" id="data{{$formid}}" name="data" value="{{$refeiçao['data']}}"></input>
                              <input type="hidden" id="ref{{$formid}}" name="ref" value="{{$refeiçao['meal']}}"></input>
                              <input type="hidden" id="localDeRef{{$formid}}" name="localDeRef" value=""></input>
                              <button style="margin: 2px !important;color: white !important;" type="submit" class="btn btn-sm btn-primary children-user-context-btn meal-confirmed" onclick="changeLocalAndPost('0', '{{ $formid }}')">Marcar</a>

                           </form>
                           @endif
                        </td>
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
               <h6>Nenhuma data disponível.</h6>
             @endif
            </div>
            <div class="tab-pane swing-in-left-fwd" id="settings">
               <form class="form-horizontal" method="POST" action="{{route('gestao.hospedes.save')}}">
                  {{ csrf_field() }}
                  <div class="form-group row">
                     <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control uppercase-only" id="inputName" name="inputName" placeholder="Nome" value="{{$hospede['name']}}">
                        <input type="hidden" id="id" name="id" value="{{$hospede['id']}}"></input>
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="inputTelf" class="col-sm-2 col-form-label">Tipo</label>
                     <div class="col-sm-10">
                       <select class="custom-select" id="inputType" name="inputType">
                         <option value="MILITAR" @if($hospede['type']=="MILITAR") @endif>Militar</option>
                         <option value="CIVIL" @if($hospede['type']=="CIVIL") @endif>Civil</option>
                       </select>
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="inputTelf" class="col-sm-2 col-form-label">Contacto</label>
                     <div class="col-sm-10">
                        <input type="number" maxlength="9" class="form-control" id="inputCont" name="inputCont" placeholder="Contacto telefónico" value="{{ $hospede['contacto'] }}">
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
                 <form action="{{route('gestao.hospedes.remover')}}" method="POST" enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" id="nim" name="nim" value="{{$hospede['id']}}" readonly>
                     <button type="submit" class="btn btn-danger">Eliminar</button>
                 </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>

@endsection

@section('extra-scripts')
  <script>
     function changeLocalAndPost(toWhere, formID) {
        var user_id = "user" + formID;
        var campoData = "data" + formID;
        var refData = "ref" + formID;
        var campoID = "localDeRef" + formID;
        var uid = "{{$hospede['fictio_nim']}}";
        toWhere = "{{$hospede['local']}}";
        document.getElementById(campoID).value = toWhere.toUpperCase();
        var data = document.getElementById(campoData).value;
        var ref = document.getElementById(refData).value;
        $.ajax({
          url: "{{route('marcacao.store.hospede')}}",
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
                    alert("Erro ao fazer marcação");
                  } else {
                    location.reload(true);
                  }
              }
          }
      });
     }
  </script>
@endsection
