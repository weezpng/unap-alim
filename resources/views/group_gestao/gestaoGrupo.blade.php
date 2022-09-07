@extends('layout.master')
@section('title', $grupo['groupName'])
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Grupo</li>
<li class="breadcrumb-item active">{{ $grupo['groupName'] }}</li>
@endsection
@section('page-content')
<div class="modal puff-in-center" id="newGroupModal" tabindex="-1" role="dialog" aria-labelledby="newGroupModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Adicionar sub-grupo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.addChildrenSubGroup')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <h6 style="margin-bottom: 1.5rem;">Insira um nome para o novo sub-grupo.</h6>
               @csrf
               <div class="form-group row">
                  <label for="inputName2" class="col-sm-2 col-form-label">Nome</label>
                  <div class="col-sm-10">
                     <input required type="hidden" id="groupID" name="groupID" value="{{ $grupo['groupID'] }}">
                     <input required type="text" maxlength="30" class="form-control" id="childSubGrupoName" name="childSubGrupoName" placeholder="Nome do novo sub-grupo">
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif">Adicionar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Associar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <h6 style="margin-bottom: 1.5rem;">Na barra de pesquisa, procure um dos seus utilizadores para adicionar a este grupo.</h6>
            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-2 col-form-label">NIM</label>
               <div class="col-sm-10">
                  <div class="input-group input-group-sm">
                     @csrf
                     <input type="number" class="form-control form-control-navbar" maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="search"
                        placeholder=" Procurar NIM" aria-label="Procurar NIM" id="searchBar" name="searchBar">
                     <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit" style="border: 1px solid #ced4da; border-left-width: 0;">
                        <i class="fas fa-search"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <table class="table results hide" id="resultsTableUsers" name="resultsTableUsers">
               <tbody>
                  <tr style="border-top: 0px;">
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="nameSearch" name="nameSearch">NOME</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="posto" name="posto">POSTO</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="unidade" name="unidade">UNIDADE</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="usrtype" name="usrtype">
                        <a href="#" id="addUserToMe">
                        <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" style="margin-top: -5px;">Adicionar</button>
                        </a>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="retirarChildrenUserModal" tabindex="-1" role="dialog" aria-labelledby="retirarChildrenUserModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Retirar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.retirarChildrenUser')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <p class="p_noMargin">Tem a certeza que pretende retirar este utilizador?</p>
               <p>O utilizador <b>não será removido</b>, apenas será retirado deste grupo e movido para o GERAL.</p>
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
   <div class=" modal-dialog modal-dialog-centered" role="document">
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
<div class="modal puff-in-center" id="disassiateUserModal" tabindex="-1" role="dialog" aria-labelledby="disassiateUserModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Desassociar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.desassociarUser')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <p class="p_noMargin">Tem a certeza que pretende desassociar este utilizador?</p>
               <p>Este utilizador <b>será retirado dos seus utilizadores</b>.</p>
               @csrf
               <input type="hidden" id="nimdesassocia" name="nimdesassocia" readonly>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-danger">Desassociar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="retireUsersRootModal" tabindex="-1" role="dialog" aria-labelledby="retireUsersRootModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Tem a certeza que pretende retirar utilizadores?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <h6 style="margin-bottom: 1.5rem;">Esta ação não irá remover utilizadores, apenas irá mover todos os utilizadores neste grupo (não associados a um sub-grupo) para o GERAL.</h6>
         </div>
         <div class="modal-footer">
            <a href="{{ route('grupo.top_level.retire', [$grupo['groupID'], '0'])}}">
            <button type="button" class="btn btn-warning">Retirar</button>
            </a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="retireUsersAllModal" tabindex="-1" role="dialog" aria-labelledby="retireUsersRootModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Tem a certeza que pretende retirar utilizadores?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <h6 style="margin-bottom: 1.5rem;">Esta ação não irá remover utilizadores, apenas irá mover todos os utilizadores neste grupo (<b>incluido sub-grupos</b>) para o GERAL.</h6>
         </div>
         <div class="modal-footer">
            <a href="{{ route('grupo.top_level.retire', [$grupo['groupID'], 'ALL'])}}">
            <button type="button" class="btn btn-danger">Retirar</button>
            </a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="deleteOneGrupo" tabindex="-1" role="dialog" aria-labelledby="de" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Tem a certeza que pretende retirar utilizadores?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <h6 style="margin-bottom: 1.5rem;">Esta ação não irá remover utilizadores, apenas irá mover todos os utilizadores neste sub-grupo para o GERAL deste grupo.</h6>
         </div>
         <div class="modal-footer">
            <a href="{{ route('grupo.top_level.retire', [$grupo['groupID'], 'ALL'])}}">
            <button type="button" class="btn btn-danger">Eliminar</button>
            </a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="deleteAllSubs" tabindex="-1" role="dialog" aria-labelledby="de" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Tem a certeza que pretende apagar os sub-grupos?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <h6 style="margin-bottom: 1.5rem;">Esta ação <b>não irá remover utilizadores</b>.<br>
               Todos os utilizadores serão retirods dos sub-grupos para o GERAL deste grupo. Caso pretenda utilizar sub-grupos novamente, terá que os criar.
            </h6>
         </div>
         <div class="modal-footer">
            <a href="{{ route('gerir.allsubgrupos.delete', $grupo['groupID'])}}">
            <button type="button" class="btn btn-warning">Apagar</button>
            </a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="deleteCurrentGroup" tabindex="-1" role="dialog" aria-labelledby="de" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Tem a certeza que pretende apagar o grupo?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <h6 style="margin-bottom: 1.5rem;">Esta ação <b>não irá remover utilizadores</b>.<br>
               Os utilizadores neste grupo (incluindo sub-grupos) serão retirados para o GERAL, e todos os sub-grupos serão eliminados.
            </h6>
         </div>
         <div class="modal-footer">
            <a href="{{ route('gerir.group.current.delete', $grupo['groupID'])}}">
            <button type="button" class="btn btn-danger">Apagar</button>
            </a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>
<div class="col-md-5">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Editar</h3>
            <div class="card-tools" style="margin-right: 0 !important; margin-top: 0.5rem !important;">
               <a href="{{ route('gestão.associatedUsersAdmin') }}" style="margin-right: 0 !important;">Ver todos os grupos</a>
               &nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </div>
         </div>
      </div>
      <div class="card-body" style="height: 14rem !important;">
         <form class="form-horizontal" method="POST" action="{{ route('gerir.grupo.edit') }}">
            @csrf
            <input type="hidden" id="groupID" name="groupID" value="{{ $grupo['groupID'] }}">
            <div class="form-group row">
               <label for="inputName2" class="col-sm-2 col-form-label">Nome</label>
               <div class="col-sm-10">
                  <input type="text" class="form-control" id="groupName" name="groupName" placeholder="Nome do grupo" value="{{ $grupo['groupName'] }}">
               </div>
            </div>
            <div class="form-group row">
               <label for="inputExperience" class="col-sm-2 col-form-label">Local preferencial</label>
               <div class="col-sm-10">
                  <select class="custom-select" id="inputLocalRefPref"  name="inputLocalRefPref">
                     @if($grupo['groupLocalPrefRef']==null)
                     <option disabled selected>Selecione um local preferencial</option>
                     @endif
                     <option @if($grupo['groupLocalPrefRef']=="QSP") selected @endif value="QSP">Quartel da Serra do Pilar</option>
                     <option @if($grupo['groupLocalPrefRef']=="QSO") selected @endif value="QSO">Quartel de Santo Ovídio</option>
                     <option @if($grupo['groupLocalPrefRef']=="MESSE ANTAS") selected @endif value="MESSE ANTAS">Messe das Antas</option>
                     <option @if($grupo['groupLocalPrefRef']=="MESSE BATALHA") selected @endif value="MESSE BATALHA">Messe da Batalha</option>
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
</div>
<div class="col-md-7">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Utilizadores sem sub-grupo</h3>
            <div class="card-tools">
               <a href="#" data-toggle="modal" data-target="#newUserModal">Associar utilizador</a>
               &nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </div>
         </div>
      </div>
      <div class="card-body" style="overflow-y: auto; max-height: 14rem !important;height: 14rem !important;padding-bottom: 1.5rem;">
         @if(isset($rootUsers) && $rootUsers!=null)
         <table class="table table-striped projects" style="padding-bottom: 1.5rem !important;">
            <tbody>
               @foreach($rootUsers as $user)
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
                     <div style="float: right;">
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
                     </div>
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
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Sub-grupos</h3>
            <div class="card-tools" style="margin-right: 0 !important; margin-top: 0.4rem !important;">
               <a href="#" data-toggle="modal" style="margin-right: 0 !important;" data-target="#newGroupModal">Adicionar sub-grupo</a>
               &nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </div>
         </div>
      </div>
      <div class="card-body" style="overflow-y: auto; max-height: 25rem;">
         <div class="row">
            @if(!empty($subgrupos))
            @foreach ($subgrupos as $sub)
            <div class="col-lg-3 col-6">
               <div class="small-box bg-info">
                  <div class="inner">
                     <h3 style="font-size: 1.8rem !important;">{{ $sub['subgroupName'] }}</h3>
                     <p class="p_noMargin">{{ $sub['totalUsers'] }} utilizadores</p>
                  </div>
                  <div class="icon">
                     <i class="fas fa-user-friends" style="font-size: 52px !important;"></i>
                  </div>
                  <a href="{{ route('gerir.subgrupo', [$grupo['groupID'], $sub['subgroupID']]) }}" class="small-box-footer" style="padding: 0.5rem; font-size: 1rem;">
                  <i class="fas fa-chevron-circle-right"></i> &nbsp;&nbsp; Gerir
                  </a>
                  <a href="{{ route('gerir.subgrupo.delete', [$grupo['groupID'], $sub['subgroupID']]) }}" class="small-box-footer" style="padding: 0.5rem; font-size: .9rem;">
                  <i class="fas fa-minus-circle"></i> &nbsp;&nbsp; Eliminar
                  </a>
               </div>
            </div>
            @endforeach
            @else
            <h6 style="margin-left: .75rem;">Nenhum sub-grupo criado.</h6>
            @endif
         </div>
      </div>
   </div>
   <div class="form-group row profile-settings-form-svbtn-spacer" style="margin-top: 3rem !important;">
      <div class="col-sm-12">
         <h4 style="margin-bottom: 1rem;font-size: 1.25rem;">Ferramentas</h4>
         @if(isset($rootUsers) && $rootUsers!=null)
         <button type="button" data-toggle="modal" data-target="#retireUsersRootModal" class="btn btn-warning" style="margin: .2rem">Retirar utilizadores sem sub-grupo</button>
         @endif
         @if(!empty($subgrupos))
         <button type="button" class="btn btn-danger"  data-toggle="modal" data-target="#retireUsersAllModal" style="margin: .2rem">Retirar utilizadores (incluindo sub-grupos)</button>
         <button type="submit" class="btn btn-warning"  data-toggle="modal" data-target="#deleteAllSubs"  style="margin: .2rem">Apagar todos os sub-grupos</button>
         @endif
         <button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#deleteCurrentGroup" style="margin: .2rem">Apagar grupo</button>
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
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
<script>
   $(document).on('click','.desassociar',function(){
        let id = $(this).attr('data-id');
        let name = $(this).attr('data-name');
        $('#nimdesassocia').val(id);
   });
</script>
<script>
   $('#searchBar').on("change paste keyup click",function(){
     $value=$(this).val();
     if (!$value) {
       $("#resultsTableUsers").addClass("hide");
       $("#nameSearch").html("");
       $("#posto").html("");
       $("#unidade").html("");
       return false;
     }
     $value=$(this).val();
       $.ajax({
           type : 'get',
           url : "{{route('super.userAddGroup.search')}}",
           data:{
             'search': $value
           },
         success:function(data){
           $('#resultsTableUsers').empty();
           $("#resultsTableUsers").removeClass("hide");
           var trHTML = '';
           $.each(data, function (i, item) {
             var addToGrupoHtml = '<a href="/gestao/user/addToGrupo/fromSearch/{{$grupo['groupID']}}/'
             + item.id + '/' + item.type + '"><button class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif puff-in-center" style="padding-top:.25rem;padding-bottom:.25rem;float: right;">Associar</button></a>'
             trHTML += '<tr>'
             + '<td><a href="http://10.102.21.45:81/alim/public/gestao/user/type/' + item.type + '/' + item.id  + '">' + item.id + '</a> </td>'
             + '<td>' + item.name + '</td>'
             + '<td>' + item.posto + '</td>'
             + '<td>' + addToGrupoHtml + '</td>'
             + '</tr>';
           });
           $('#resultsTableUsers').append(trHTML);
       }
     });
   }).delay(1000);
</script>
@endsection
