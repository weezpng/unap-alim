@extends('layout.master')
@section('title', $grupo['groupName'] . " \ " . $subgrupo['subgroupName'])
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">{{ $grupo['groupName'] }}</li>
<li class="breadcrumb-item active">{{ $subgrupo['subgroupName'] }}</li>
@endsection
@section('page-content')
<div class="modal puff-in-center" id="retirarChildrenUserModal" tabindex="-1" role="dialog" aria-labelledby="retirarChildrenUserModal" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Retirar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.retirarChildrenUserFromSub')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <p class="p_noMargin">Tem a certeza que pretende retirar este utilizador?</p>
               <p>O utilizador <b>não será removido</b>, apenas será retirado deste sub-grupo e movido para o <b>GERAL</b> no grupo "<b>{{ $grupo['groupName'] }}</b>".</p>
               @csrf
               <input type="hidden" id="nimRet" name="nimRet" readonly>
               <input type="hidden" id="nameRet" name="nameRet" readonly>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-warning">Retirar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="removeChildrenUserModal" tabindex="-1" role="dialog" aria-labelledby="removeChildrenUserModal" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Remover utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.destroyChildrenUser')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <p class="p_noMargin">Tem a certeza que pretende remover este utilizador?</p>
               <p>Este utilizador <b>será removido definitivamente</b>.</p>
               @csrf
               <input type="hidden" id="nim" name="nim" readonly>
               <input type="hidden"id="name" name="name" readonly>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-danger">Remover</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="width: 40vw;margin-left: -5vw;">
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Adicionar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="window.location.reload();">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body" style="overflow-y: auto; max-height: 30vw !important;">
            <table class="table results hide" id="resultsTable" name="resultsTable">
               <thead>
                  <tr>
                     <th style="width: 20%">
                        NIM
                     </th>
                     <th style="width: 30%">
                        NOME
                     </th>
                     <th style="width: 20%">
                        POSTO
                     </th>
                     <th style="width: 10%">
                     </th>
                     <th style="width: 20%">
                     </th>
                  </tr>
               </thead>
               <tbody>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">{{ $subgrupo['subgroupName'] }}</h3>
            <div class="card-tools" style="margin-right: 0 !important; margin-top: 0.4rem !important;">
               <a href="{{ route('gerir.grupo', $grupo['groupID']) }}" style="margin-right: 0 !important;">Ver grupo</a>
               &nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </div>
         </div>
      </div>
      <div class="card-body">
         <form class="form-horizontal" method="POST" action="{{ route('gerir.subgrupo.edit') }}">
            {{ csrf_field() }}
            <input type="hidden" id="subgroupID" name="subgroupID" value="{{ $subgrupo['subgroupID'] }}">
            <div class="form-group row">
               <label for="inputName2" class="col-sm-2 col-form-label">Nome do subgrupo</label>
               <div class="col-sm-10">
                  <input type="text" class="form-control" id="subgroupName" name="subgroupName" placeholder="Nome do subgrupo" value="{{ $subgrupo['subgroupName'] }}">
               </div>
            </div>
            <div class="form-group row">
               <label for="inputExperience" class="col-sm-2 col-form-label">Local preferencial de refeição</label>
               <div class="col-sm-10">
                  <select class="custom-select" id="subgroupLocalPref"  name="subgroupLocalPref">
                     @if($subgrupo['subgroupLocalPref']==null)
                     <option disabled selected>Selecione um local preferencial</option>
                     @endif
                     <option @if($subgrupo['subgroupLocalPref']=="QSP") selected @endif value="QSP">Quartel da Serra do Pilar</option>
                     <option @if($subgrupo['subgroupLocalPref']=="QSO") selected @endif value="QSO">Quartel de Santo Ovídio</option>
                     <option @if($subgrupo['subgroupLocalPref']=="MESSE ANTAS") selected @endif value="MESSE ANTAS">Messe das Antas</option>
                     <option @if($subgrupo['subgroupLocalPref']=="MESSE BATALHA") selected @endif value="MESSE BATALHA">Messe da Batalha</option>
                  </select>
               </div>
            </div>
            <div class="form-group row profile-settings-form-svbtn-spacer">
               <div class="offset-sm-2 col-sm-10">
                  <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif">Guardar</button>
               </div>
            </div>
         </form>
      </div>
   </div>
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Utilizadores</h3>
            <div class="card-tools" style="margin-right: 0 !important; margin-top: 0.4rem !important;">
               <a href="#" data-toggle="modal" data-target="#newUserModal"  id="viewUsersAvailableToAdd" >Adicionar utilizador</a>
               &nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </div>
         </div>
      </div>
      <div class="card-body" style="overflow-y: auto; max-height: 25rem;">
         @if(isset($users) && $users!=null)
         <table class="table table-striped projects">
            <thead>
               <tr>
                  <th style="width: 15%">
                     NIM
                  </th>
                  <th style="width: 15%">
                     NOME
                  </th>
                  <th style="width: 15%">
                     POSTO
                  </th>
                  <th style="width: 55%">
                     TIPO DE CONTA
                  </th>
                  <th>
                  </th>
               </tr>
            </thead>
            <tbody>
               @foreach($users as $user)
               <tr>
                  <td>
                     <i class="fas fa-user"></i>&nbsp&nbsp
                     {{ $user['id'] }}
                  </td>
                  <td class="uppercase-only">
                     {{ $user['name'] }}
                  </td>
                  <td>
                     {{ $user['posto'] }}
                  </td>
                  <td>
                     {{ $user['type'] }}
                  </td>
                  <td>
                     <a style="margin: 2px !important;" type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('gestão.viewUserChildren', $user['id'])}}">Gerir</a>
                     @if($user['type']=="UTILIZADOR")
                     <button style="margin: 2px !important;" type="submit" data-id="{{ $user['id'] }}"
                        data-toggle="modal" data-target="#disassiateUserModal" class="btn btn-sm btn-danger children-user-context-btn desassociar">Desassociar</button>
                     @else
                     <button style="margin: 2px !important;" type="submit" data-id="{{ $user['id'] }}" data-name="- {{ $user['posto'] }} - {{ $user['name'] }}"
                        data-toggle="modal" data-target="#removeChildrenUserModal" class="btn btn-sm btn-danger children-user-context-btn delete">Remover</button>
                     @endif
                     <button style="margin: 2px !important;" type="submit" data-id="{{ $user['id'] }}" data-name="- {{ $user['posto'] }} - {{ $user['name'] }}"
                        data-toggle="modal" data-target="#retirarChildrenUserModal" class="btn btn-sm btn-warning children-user-context-btn retirar">Retirar</button>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
         @else
         <h6>Nenhum utilizador dentro do grupo.</h6>
         @endif
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   $(document).on('submit', 'form', function(e) {
     var form = this;
     if (this.className=="add-usr-grp") {
     e.preventDefault();
     var userID = $("input[name='userID']",form).val();
     var userType = $("input[name='userType']",form).val();
     var groupID = "{{ $grupo['groupID'] }}";
     var subID = "{{ $subgrupo['subgroupID'] }}";
     $.ajax({
         type: "POST",
         async: true,
         url: "{{route('super.addUsersToSub')}}",
         data: {
             "_token": "{{ csrf_token() }}",
             userID: userID,
             userType: userType,
             groupID: groupID,
             subID: subID,
         },
         success: function (msg) {
           var btn = $( ":button" , form );
           if (msg=="success") {
             btn.addClass( "disabled" );
             btn.html('Adicionado');
           } else {
             btn.addClass("disabled");
             btn.addClass("disabled-btn");
             btn.html('ERRO');
           }

         }
       });
     }
   });

   $('#viewUsersAvailableToAdd').click(function() {
     $('#resultsTable').empty();
     $.ajax({
         type : 'get',
         async: true,
         url : "{{route('super.useradd.tosub.get')}}",
         data:{
           'currentGroup': "{{ $grupo['groupID'] }}"
         },
       success:function(data){
           $("#resultsTable").removeClass("hide");
           var trHTML = '';
           $.each(data, function (i, item) {
               trHTML += '<tr><td>' + item.id + '</td><td>' + item.name + '</td><td>' + item.posto
               + '</td><td>' + item.grupo+ '</td><td><form class="add-usr-grp" action="{{route('super.addUsersToSub')}}" id="' + item.id + item.type +  '">'
               + '<input type="hidden" id="userID" name="userID" value="' + item.id + '" />'
               + '<input type="hidden" id="userType" name="userType" value="' + item.type + '" />'
               + '<button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif text-sm puff-in-center">Adicionar</button></td></form></tr>';
           });
           $('#resultsTable').append(trHTML);
         }
     });
   })
</script>
<script>
   $(document).on('click','.delete',function(){
        let id = $(this).attr('data-id');
        let name = $(this).attr('data-name');
        $('#nim').val(id);
        $('#name').val(name);
   });
</script>
<script>
   $(document).on('click','.retirar',function(){
        let id = $(this).attr('data-id');
        let name = $(this).attr('data-name');
        $('#nimRet').val(id);
        $('#nameRet').val(name);
   });
</script>
@endsection
