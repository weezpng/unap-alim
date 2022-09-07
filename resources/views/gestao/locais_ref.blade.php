@extends('layout.master')
@section('title','Gestão de locais de refeição')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Gestão</li>
<li class="breadcrumb-item active">Plataforma</li>
<li class="breadcrumb-item active">Locais</li>
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

<div class="modal puff-in-center" id="createLocalRefModal" tabindex="-1" role="dialog" aria-labelledby="createLocalRefModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-plus-square"></i>&nbsp;&nbsp; Criar novo local</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.locais.save')}}" method="POST" enctype="multipart/form-data" id="createLocal">
            <div class="modal-body">

              <div class="form-group row">
                 <label for="inputName" class="col-sm-2 col-form-label">Sigla</label>
                 <div class="col-sm-10">
                    <input required type="text" class="form-control" id="inputSigla" name="inputSigla" autocomplete="off" placeholder="Sigla do local">
                 </div>
              </div>

               <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                  <div class="col-sm-10">
                     <input required type="text" class="form-control" id="inputName" name="inputName" autocomplete="off" placeholder="Nome do local">
                  </div>
               </div>

               <div class="form-group row">
                  <label for="inputTelf" class="col-sm-2 col-form-label">Estado</label>
                  <div class="col-sm-10">
                    <select required class="custom-select" id="inputStatus" name="inputStatus">
                      <option selected disabled value="0">Selecione o estado</option>
                      <option value="OK">OK</option>
                      <option value="NOK">INOP</option>
                    </select>
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

<div class="modal puff-in-center" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-minus-square"></i>&nbsp;&nbsp; Remover local de refeição</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.locais.delete')}}" method="POST" enctype="multipart/form-data" id="removeH">
            <div class="modal-body">
               <p>Tem a certeza que pretende remover o local <b><span id="localNameRemove"></span></b>?<br>
                  Esta ação é <b>irreversível</b>, e terá efeitos imediatos.<br>O local irá deixar de aparecer em todas as estatisticas.
               </p>
            </div>
            <div class="modal-footer">
               <button type="button" onclick="removeLocalRef();" class="btn btn-danger">Remover</button>
               <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
            </div>
         </form>
      </div>
   </div>
</div>

  <div class="modal puff-in-center" id="editLocalModal" tabindex="-1" role="dialog" aria-labelledby="createUserModal" aria-hidden="true">
     <div class=" modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="editLocalLabel"><i class="fas fa-edit">&nbsp;</i>&nbsp;Editar local</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <form action="{{route('gestão.locais.edit')}}" method="POST" enctype="multipart/form-data" id="editLocal">
              <div class="modal-body">
                 <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                    <div class="col-sm-10">
                       <input required type="text" class="form-control" id="inputName1" name="inputName" placeholder="Nome">
                    </div>
                 </div>

                 <div class="form-group row">
                    <label for="inputTelf" class="col-sm-2 col-form-label">Estado</label>
                    <div class="col-sm-10">
                      <select required class="custom-select" id="inputStatus1" name="inputStatus">
                        <option value="0">Selecione o estado</option>
                        <option value="OK">OK</option>
                        <option value="NOK">INOP</option>
                      </select>
                    </div>
                 </div>

              </div>
              <div class="modal-footer">
                 <button type="button" onclick="submitEditForm();" class="btn btn-primary" style="min-width: 6rem;">Guardar</button>
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
                <h3 class="card-title"></h3>
                <div class="card-tools">
                    <a href="#" id="AddLocal" data-toggle="modal" data-target="#createLocalRefModal"><i class="fas fa-plus-square"></i> &nbsp; Adicionar local</a>
                    &nbsp;&nbsp;
                    <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            Abaixo encontram-se listados os locais de refeição atualmente definidos.
            <table class="table table-striped projects" style="margin-top: 1rem;" id="locals">
                <thead>
                    <tr>
                        <th style="width: 2%">

                        </th>
                        <th style="width: 40%">
                            Nome
                        </th>
                        <th style="width: 10%">
                            Sigla
                        </th>
                        <th style="width: 8%" class="text-center">
                            Estado
                        </th>
                        <th style="width: 20%">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locais as $key => $local)
                    <tr>
                        <td>
                            #{{ $local['id'] }}
                        </td>
                        <td>
                            {{ $local['localName'] }}
                        </td>
                        <td>
                            {{ $local['refName'] }}
                        </td>

                        <td class="project-state">
                          @if($local['status']=="OK")
                            <span class="badge badge-success">OK</span>
                          @else
                            <span class="badge badge-danger">INOP</span>
                          @endif
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-primary btn-sm slide-in-blurred-top" href="#" onclick="showEditModal('{{ $local['id'] }}','{{ $local['localName'] }}', '{{ $local['status'] }}');">
                                <i class="fas fa-pencil-alt">
                                </i>
                                &nbsp;Editar
                            </a>
                            &nbsp;
                            <a class="btn btn-danger btn-sm slide-in-blurred-top" href="#" onclick="showRemoveModal('{{ $local['id'] }}','{{ $local['localName'] }}');">
                                <i class="fas fa-trash">
                                </i>
                                &nbsp;Remover
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
</div>
@endsection
@section('extra-scripts')
<script type="text/javascript">

  var T_ID;
  var T_NAME;
  var TR_ID;
  var TR_NAME;

  function showEditModal(id, name, status){
    $("#inputName1").val(name);
    $("#inputStatus1").val(status);
    $("#editLocalModal").modal()
    T_ID = id;
    T_NAME = name;
  }

  function showRemoveModal(id, name){
    $("#localNameRemove").text(name);
    $("#delModal").modal()
    TR_ID = id;
    TR_NAME = name;
  }

  function submitEditForm(){
    var data = $("#editLocal").serializeArray();
    $.ajax({
        url: "{{route('gestão.locais.edit')}}",
        type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            id : T_ID,
            data: data,
        },
        success: function(response) {
           $("#editLocalModal").modal('hide');
            if (response) {
                if (response != 'success') {
                    document.getElementById("errorAddingTitle").innerHTML = "Erro";
                    document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro a guardar alterações ao local de refeição.";
                    $("#errorAddingModal").modal()
                } else {
                  $(document).Toasts('create', {
                    title: "Guardado",
                    subtitle: "",
                    body: "Foram guardadas as alterações feitas ao local de refeição '" + T_NAME + "'.",
                    icon: "fas fa-edit",
                    autohide: true,
                    autoremove: true,
                    delay: 3500,
                    class: "toast-not",
                  });
                  $("#locals").load(location.href + " " + "#locals");
                }
            }
        }
    });
  }

  function submitForm(){
    var data = $("#createLocal").serializeArray();
    $.ajax({
        url: "{{route('gestão.locais.save')}}",
        type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            data: data,
        },
        success: function(response) {
           $("#createLocalRefModal").modal('hide');
            if (response) {
                if (response != 'success') {
                    document.getElementById("errorAddingTitle").innerHTML = "Erro";
                    document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro a guardar o local de refeição.";
                    $("#errorAddingModal").modal()
                } else {
                  $(document).Toasts('create', {
                    title: "Criado",
                    subtitle: "",
                    body: "Foi criado o local de refeição '" + data[1]['value'] + "'.",
                    icon: "fas fa-plus-square",
                    autohide: true,
                    autoremove: true,
                    delay: 3500,
                    class: "toast-not",
                  });
                  $("#locals").load(location.href + " " + "#locals");
                }
            }
        }
    });
  }

  function removeLocalRef(){
    $.ajax({
        url: "{{route('gestão.locais.delete')}}",
        type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            id : TR_ID,
        },
        success: function(response) {
           $("#delModal").modal('hide');
            if (response) {
                if (response != 'success') {
                    document.getElementById("errorAddingTitle").innerHTML = "Erro";
                    document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro a eliminar o local de refeição.";
                    $("#errorAddingModal").modal()
                } else {
                  $(document).Toasts('create', {
                    title: "Eliminado",
                    subtitle: "",
                    body: "Foi eliminado com sucesso o local '" + TR_NAME + "'.",
                    icon: "fas fa-minus-square",
                    autohide: true,
                    autoremove: true,
                    delay: 3500,
                    class: "toast-not",
                  });
                  $("#locals").load(location.href + " " + "#locals");
                }
            }
        }
    });
  }

</script>
@endsection
