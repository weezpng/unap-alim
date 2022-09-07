@extends('layout.master')
@section('icon','fas fa-user-tag')
@section('title','Gestão de hóspedes')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Hóspedes</li>
@endsection
@section('page-content')

<div class="modal puff-in-center" id="errorAddingModal" tabindex="-1" role="dialog" aria-labelledby="errorAddingModal" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="errorAddingTitle" name="errorAddingTitle"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body" style="padding: 1.25rem !important;">
            <p id="errorAddingText" name="errorAddingText"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>

<div class="modal puff-in-center" id="QRs" tabindex="-1" role="dialog" aria-labelledby="QRs" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-qrcode"></i>&nbsp; Códigos QR</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <h6>Selecione abaixo que impressos de código QR irá ser necessário.</h6>
            <button type="button" id="toggleVistos" class="btn btn-dark text-xs" style="width: 100%; margin-bottom: 15px; margin-top: 24px;" 
            onclick="ToggleVistos();">Desselecionar todos</button>
            <div style="height: 35vh; overflow-y: auto;">
               <form method="POST" enctype="multipart/form-data" id="requestQRS">
                  @foreach($rooms as $key => $room)
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text" style="border: none;">
                              <input type="checkbox" id="selRooms" checked class="mr-1" name="ROOMs[]" value="{{ $key }}">
                           </span>                     
                        </div>
                        <input type="text" style="font-size: 1.2rem;padding: .5rem; padding-left: 0; border: none; margin-left: 1rem; height: 2.5rem;" class="form-control" readonly
                        value="{{ $room }}">
                     </div>
                  @endforeach
               </form>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-primary @else btn-secondary @endif" onclick="requestQRs();">Pedir impressos</button>            
            <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>

<div class="modal puff-in-center" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-user-minus"></i>&nbsp; Remover hóspede</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestao.hospedes.remover')}}" method="POST" enctype="multipart/form-data" id="removeH">
            <div class="modal-body">
               <p>Tem a certeza que pretende remover este hóspede?<br>
                  Esta ação é <b>irreversível</b>.
               </p>
               @csrf
               <input type="hidden" id="nim" name="nim" readonly>
               <input type="hidden" id="name" name="name" readonly>
            </div>
            <div class="modal-footer">
               <button type="button" onclick="removeHospede();" class="btn btn-danger">Remover</button>
               <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="modal puff-in-center" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-user-plus"></i>&nbsp; Criar hóspede</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestao.hospedes.new')}}" method="POST" enctype="multipart/form-data" id="createHospede">
            <div class="modal-body">
               @csrf
               <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                  <div class="col-sm-10">
                     <input required type="text" class="form-control" id="inputName" name="inputName" placeholder="Nome">
                  </div>
               </div>

               <div class="form-group row">
                  <label for="inputTelf" class="col-sm-2 col-form-label">Tipo</label>
                  <div class="col-sm-10">
                    <select required class="custom-select" id="inputType" name="inputType">
                      <option selected disabled value="0">Selecione o tipo</option>
                      <option value="MILITAR">Militar</option>
                      <option value="CIVIL">Civil</option>
                    </select>
                  </div>
                  <div class="offset-sm-2 col-sm-10" style="margin-top: .5rem;">
                    <select required class="custom-select" id="inputTypePermTemp" name="inputTypePermTemp">
                      <option selected disabled value="0">Selecione o tipo</option>
                      <option value="TEMP">Temporário</option>
                      <option value="PERM">Permanente</option>
                    </select>
                  </div>
               </div>

               <div class="form-group row">
                  <label for="inputTelf" class="col-sm-2 col-form-label">Contacto</label>
                  <div class="col-sm-10">
                     <input required type="number" class="form-control" id="inputCont" name="inputCont" maxlength="9" placeholder="Contacto telefónico">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="inputTelf" class="col-sm-2 col-form-label">Quarto</label>
                  <div class="col-sm-10">
                     <input required type="number" class="form-control" id="inputRoom" name="inputRoom" maxlength="3" min="601" max="909" placeholder="Quarto (601-909)">
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" onclick="submitForm();" class="btn btn-primary" style="min-width: 6rem;">Criar</button>
               <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="col-md-12">
<div class="card">
	<div class="card-header p-2">
      <div class="d-flex justify-content-between">
         <h3 class="card-title" style="margin: 0.6rem 0.8rem;">Hóspedes</h3>
         <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#TEMP" data-toggle="tab" style="margin-right: .5rem;">Temporários</a></li>
            <li class="nav-item"><a class="nav-link" href="#PERM" data-toggle="tab">Permanentes</a></li>       
            <li class="nav-item"><a class="nav-link" href="{{ route('gestao.hospedes.marccenter') }}" style="cursor: pointer;"><i class="fas fa-calendar-alt"></i> &nbsp;Centro de marcações</a></li>
            <li class="nav-item"><a class="nav-link" data-target="#QRs" data-toggle="modal" style="cursor: pointer;"><i class="fas fa-qrcode"></i>&nbsp; Códigos QR</a></li>
         </ul>
      </div>
	</div>
	<div class="card-body">
      <div class="tab-content">
         <div class="tab-pane active" id="TEMP">
            <h5 style="width: 70%; display: inline-block;">
               <i style="padding: 0.5rem;" class="fas fa-user-clock"></i>
               Hóspedes <b>temporários</b>
            </h5>       
            <button class="btn text-sm btn-sm btn-primary" data-toggle="modal" data-target="#createUserModal" style="dsplay: inline-block; float: right; opacity: 1 !important;">Criar novo hóspede</button>
            @if (!@empty($hospedes_temp))
               <table class="table table-striped projects"style="margin-top: 0.5rem;">      
                  <thead>
                     <td>Nome</td>
                     <td>Tipo</td>
                     <td>Contacto</td>
                     <td>Quarto</td>
                     <td></td>
                  </thead>            
                  <tbody>
                     @foreach($hospedes_temp as $hospede)
                     <tr>
                        <td style="padding: .75rem !important;">                        
                           <i style="padding: 0.7rem;" class="fas fa-user-clock"></i>
                           &nbsp;&nbsp;<a href="{{ route('gestao.hospedes.profile',  $hospede['id']) }}">&nbsp;{{ $hospede['name'] }}</a>
                        </td>
                        <td class="uppercase-only" >
                           {{ $hospede['type'] }}
                        </td>
                        <td>                           
                           {{ $hospede['contacto'] }}
                        </td>
                        <td>                           
                           @if(isset($hospede['quarto']) && $hospede['quarto']!=null) {{ $hospede['quarto'] }} @else Não atribuido @endif
                        </td>
                        <td style="text-align: right">
                           <a style="margin: 2px !important; opacity: 1 !important;" type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('gestao.hospedes.profile', $hospede['id'])}}">Gerir</a>
                           <button style="margin: 2px !important; opacity: 1 !important;" type="submit" data-id="{{ $hospede['id'] }}" data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-danger children-user-context-btn delete">Remover</button>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            @else
               <h6>Atualmente não existe nenhum hospede temporário.</h6>
            @endif


         </div>

         <div class="tab-pane" id="PERM">
            <h5 style="width: 70%; display: inline-block;">
               <i style="padding: 0.5rem;" class="fas fa-user-shield"></i>
               Hóspedes <b>permanentes</b>
            </h5>
            <button class="btn text-sm btn-sm btn-primary" data-toggle="modal" data-target="#createUserModal" style="display: block; float: right; opacity: 1 !important;">Criar novo hóspede</button>
               @if (!@empty($hospedes_perm))
               <table class="table table-striped projects"style="margin-top: 0.5rem;">      
                        <thead>
                           <td>Nome</td>
                           <td>Tipo</td>
                           <td>Contacto</td>
                           <td>Quarto</td>
                           <td></td>
                        </thead>            
                        <tbody>
                           @foreach($hospedes_perm as $hospede)
                           <tr>
                              <td style="padding: .75rem !important;">                        
                                 <i style="padding: 0.7rem;" class="fas fa-user-clock"></i>
                                 &nbsp;&nbsp;<a href="{{ route('gestao.hospedes.profile',  $hospede['id']) }}">&nbsp;{{ $hospede['name'] }}</a>
                              </td>
                              <td class="uppercase-only" >
                                 {{ $hospede['type'] }}
                              </td>
                              <td>                           
                                 {{ $hospede['contacto'] }}
                              </td>
                              <td>                           
                                 @if(isset($hospede['quarto']) && $hospede['quarto']!=null) {{ $hospede['quarto'] }} @else Não atribuido @endif
                              </td>
                              <td style="text-align: right">
                                 <a style="margin: 2px !important;" type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('gestao.hospedes.profile', $hospede['id'])}}">Gerir</a>
                                 <button style="margin: 2px !important;" type="submit" data-id="{{ $hospede['id'] }}" data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-danger children-user-context-btn delete">Remover</button>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
            @else
               <h6>Atualmente não existe nenhum hospede permanente.</h6>
            @endif
         </div>

      </div>
	</div>
</div>
</div>
@endsection
@section('extra-scripts')
<script>
   function submitForm(){
      $.ajax({
         type: 'POST',
         url: "{{route('gestao.hospedes.new')}}",
         data: $('#createHospede').serialize(),
         success:function(response){
            $("#createUserModal").modal('hide');
            if (response=='success') {                                    
               $(document).Toasts('create', {
                  title: "Criado",
                  subtitle: "",
                  body: "O hospede <b>" + $('#inputName').val() + "</b> foi criado.",
                  icon: "fas fa-user-circle",
                  autohide: true,
                  autoremove: true,
                  delay: 3500,
                  class: "toast-not",
               });
               var content = "#" + $('#inputTypePermTemp').val();
               $(content).load(location.href + " " + content);  
            } else {
               document.getElementById("errorAddingTitle").innerHTML = "Erro";
               document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro ao criar o hóspede. <b>"+response+"</b>";
               $("#errorAddingModal").modal()
            }                       
         },
      })      
   }

   function removeHospede(){
      $.ajax({
         type: 'POST',
         url: "{{route('gestao.hospedes.remover')}}",
         data: $('#removeH').serialize(),
         success:function(response){
            $("#exampleModal").modal('hide');
            $("#PERM").load(location.href + " " + "#PERM");  
            $("#TEMP").load(location.href + " " + "#TEMP");  
            if (response=='success') {
               $(document).Toasts('create', {
                  title: "Removido",
                  subtitle: "",
                  body: "O hospede <b>" + $('#inputName').val() + "</b> foi removido.",
                  icon: "fas fa-user-slash",
                  autohide: true,
                  autoremove: true,
                  delay: 3500,
                  class: "toast-not",
               });               
            } else {
               document.getElementById("errorAddingTitle").innerHTML = "Erro";
               document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro ao remover o hóspede";
               $("#errorAddingModal").modal()
            }                       
         },
      })      
   }

   var toggled = true;
   function ToggleVistos(){
      $('.mr-1').each(function(){
         $(this).attr('checked', (!toggled));
      });
      if (toggled==true) {        
        $('#toggleVistos').text("Selecionar todos");
      } else {
        $('#toggleVistos').text("Desselecionar todos");        
      }              
      toggled = !toggled;     
   }
</script>
<script>
   function requestQRs(){
      var data = $("#requestQRS").serializeArray();
      $.ajax({
          url: "{{route('gestao.hospedes.QRs')}}",
          type: "POST",
          data: {
              "_token": "{{ csrf_token() }}",
              data: data,
          },
          success: function(response) {
               $("#QRs").modal('hide');
              if (response) {
                  if (response != 'success') {
                      document.getElementById("errorAddingTitle").innerHTML = "Erro";
                      document.getElementById("errorAddingText").innerHTML = "Erro ao pedir os códigos QR.";
                      $("#errorAddingModal").modal()
                  } else {
                    $(document).Toasts('create', {
                      title: "Pedido",
                      subtitle: "",
                      body: "Os códigos QR foram pedidos com sucesso.",
                      icon: "fas fa-qrcode",
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
   $(document).on('click','.delete',function(){
        let id = $(this).attr('data-id');
        let name = $(this).attr('data-name');
        $('#nim').val(id);
        $('#name').val(name);
   });
</script>
@endsection
