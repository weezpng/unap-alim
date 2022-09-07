@extends('layout.master')
@section('title','Gestão de utilizadores associados')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Utilizadores</li>
<li class="breadcrumb-item active">Associados</li>
@endsection
@section('page-content')
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
<!-- <div class="modal puff-in-center" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Criar children</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.addChildrenUser')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <div class="form-group row">
                  <label for="inputName2" class="col-sm-3 col-form-label">Identificação</label>
                  <div class="col-sm-9">
                     <input required type="number" class="form-control" id="childID" name="childID" placeholder="NIM"
                        maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="inputName2" class="col-sm-3 col-form-label">Nome</label>
                  <div class="col-sm-9">
                     <input required type="text" class="form-control" id="childNome" name="childNome" placeholder="Nome">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="inputPosto" class="col-sm-3 col-form-label">Posto</label>
                  <div class="col-sm-9">
                     <select required class="custom-select" id="childPosto" name="childPosto">
                        <option value="ASS.OP.">ASSISTENTE OPERACIONAL</option>
                        <option value="ENC.OP.">ENCARREGADO OPERACIONAL</option>
                        <option value="ASS.TEC.">ASSISTENTE TÉCNICO</option>
                        <option value="TEC.SUP.">TÉCNICO SUPERIOR</option>
                        <option value="TIA">TÉCNICO INFORMÁTICA ADJUNTO</option>
                        <option value="TIG.1">TÉCNICO DE INFORMÁTICA GRAU 1</option>
                        <option value="TIE">TÉCNICO INFORMÁTICA ESPECIALISTA</option>
                        <option value="TEC.SUP.">TEC.SUP.</option>
                        <option value="SOLDADO">SOLDADO</option>
                        <option value="2ºCABO">2º CABO</option>
                        <option value="1ºCABO">1º CABO</option>
                        <option value="CABO-ADJUNTO">CABO-ADJUNTO</option>
                        <option value="2ºFURRIEL">2º FURRIEL</option>
                        <option value="FURRIEL" >FURRIEL</option>
                        <option value="2ºSARGENTO">2º SARGENTO</option>
                        <option value="1ºSARGENTO">1º SARGENTO</option>
                        <option value="SARGENTO-AJUDANTE">SARGENTO-AJUDANTE</option>
                        <option value="SARGENTO-CHEFE">SARGENTO-CHEFE</option>
                        <option value="SARGENTO-MOR">SARGENTO-MOR</option>
                        <option value="ASPIRANTE">ASPIRANTE</option>
                        <option value="ALFERES">ALFERES</option>
                        <option value="TENENTE">TENENTE</option>
                        <option value="CAPITAO">CAPITÃO</option>
                        <option value="MAJOR">MAJOR</option>
                        <option value="TENENTE-CORONEL">TENENTE-CORONEL</option>
                        <option value="CORONEL">CORONEL</option>
                        <option value="BRIGADEIRO-GENERAL">BRIGADEIRO-GENERAL</option>
                        <option value="MAJOR-GENERAL">MAJOR-GENERAL</option>
                        <option value="TENENTE-GENERAL">TENENTE-GENERAL</option>
                        <option value="GENERAL">GENERAL</option>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="inputUEO" class="col-sm-3 col-form-label">Grupo</label>
                  <div class="col-sm-9">
                     <select required class="custom-select" id="childGrupo"  name="childGrupo">
                        <option value="GERAL">GERAL</option>
                     </select>
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
</div> -->
<div class="modal puff-in-center" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Adicionar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
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
<div class="modal puff-in-center" id="searchUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Procurar</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-2 col-form-label">NIM</label>
               <div class="col-sm-10">
                  <div class="input-group input-group-sm">
                     @csrf
                     <input type="number" class="form-control form-control-navbar" maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="search"
                        placeholder=" Procurar NIM" aria-label="Procurar NIM" id="procurarInput" name="procurarInput">
                     <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit" style="border: 1px solid #ced4da; border-left-width: 0;">
                        <i class="fas fa-search"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <table class="table results hide" id="resultadosProcura" name="Resultados">
               <tbody>
                  <tr style="border-top: 0px;">
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="nameSearch" name="nameSearch">
                        <a href="#" id="idSearch">
                        NOME
                        </a>
                     </td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="postoSearch" name="postoSearch">POSTO</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="grupoSearch" name="grupoSearch">GRUPO</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="subgrupoSearch" name="subgrupoSearch">SUBGRUPO</td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="newGroupModal" tabindex="-1" role="dialog" aria-labelledby="newGroupModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Adicionar grupo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.addChildrenGroup')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <div class="form-group row">
                  <label for="inputName2" class="col-sm-4 col-form-label">Nome do grupo</label>
                  <div class="col-sm-8">
                     <input required type="text" maxlength="30" class="form-control" id="childGrupoName" name="childGrupoName" placeholder="Nome de grupo">
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
<div class="modal puff-in-center" id="addSubGestor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Adicionar sub-gestor</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-2 col-form-label">NIM</label>
               <div class="col-sm-10">
                  <div class="input-group input-group-sm">
                     @csrf
                     <input type="number" class="form-control form-control-navbar" maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="search"
                        placeholder=" Procurar NIM" aria-label="Procurar NIM" id="procurarInputSubgestor" name="procurarInputSubgestor">
                     <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit" style="border: 1px solid #ced4da; border-left-width: 0;">
                        <i class="fas fa-search"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <table class="table results hide" id="resultadosProcuraSubGestor" name="Resultados">
               <tbody>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<div @if($partner==null) class="col-md-12" @else class="col-md-10" @endif>
<div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
   <div class="card-header border-0">
      <div class="d-flex justify-content-between">
         <h3 class="card-title">Utilizadores por grupo</h3>
         <div class="card-tools" style="margin-right: 0 !important; margin-top: 0.4rem !important;">
            @if($partner==null)
            <a href="#" data-toggle="modal" data-target="#addSubGestor">Adicionar sub-gestor</a>
            @endif
            @if((!empty($allUsers)) || ($childrenUsers!=null))
            <a href="#" data-toggle="modal" data-target="#searchUserModal" style="margin-right: 20px;margin-left: 20px;">Procurar utilizador</a>
            <a href="#" data-toggle="modal" style="margin-right: 0 !important;" data-target="#newGroupModal">Adicionar grupo</a>
            &nbsp;&nbsp;
            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            @endif
         </div>
      </div>
   </div>
   <div class="card-body">
   @if($childrenUsers!=null)
   <div class="card-body" style="padding-top: .25rem !important;padding-right: 0 !important; padding-left: 0;">
      @foreach ($childrenUsers as $key1 => $grupo)
      <div class="card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card" style="box-shadow: none !important; margin-top: 1rem !important; margin-bottom: 1rem !important; ">
         <div class="card-header border-0">
            <div class="d-flex justify-content-between">
               <h3 class="card-title">{{ $grupo['groupName'] }}</h3>
               <div class="card-tools">
                  <a href="{{route('gerir.grupo', $grupo['groupID'])}}" >Gerir grupo&nbsp;&nbsp;&nbsp;&nbsp;</a>
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Maximizar\Minimizar">
                  <i class="fas fa-plus"></i></button>
                  &nbsp;
                  <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
                  </button>
               </div>
            </div>
         </div>
         <div class="card-body" style="padding-left: 1rem !important;padding-top: .25rem !important;padding-right: 0 !important;">
            @if(array_key_exists('SUBGRUPOS', $grupo))
            @foreach ($grupo['SUBGRUPOS'] as $key2 => $subgroup)
            <div class="card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif collapsed-card" style="box-shadow: 0 0 1px rgb(0 0 0 / 35%), 0 1px 3px rgb(0 0 0 / 35%) !important; margin-top: .25rem !important; margin-bottom: .25rem !important;">
               <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                     <h3 class="card-title">{{ $key2 }}</h3>
                     <div class="card-tools">
                        <a href="{{ route('gerir.subgrupo', [$grupo['groupID'], $subgroup['subgroupID']]) }}" >Gerir sub-grupo&nbsp;&nbsp;&nbsp;&nbsp;</a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Maximizar\Minimizar">
                        <i class="fas fa-plus"></i></button>
                        &nbsp;
                        <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
                     </div>
                  </div>
               </div>
               <div class="card-body" style="padding-left: 1rem !important;padding-right: 0 !important;">
                  @if(array_key_exists('USERS', $subgroup))
                  <table class="table table-striped projects">
                     <thead>
                        <tr>
                           <th style="width: 15%">
                              NÚMERO IDENTIFICADOR
                           </th>
                           <th style="width: 15%">
                              NOME
                           </th>
                           <th style="width: 15%">
                              POSTO
                           </th>
                           <th style="width: 45%">
                              TIPO DE CONTA
                           </th>
                           <th>
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($subgroup['USERS'] as $user)
                        <tr>
                           <td>
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
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
                  @else
                  <h6 style="padding-top: 1.5rem;">Este sub-grupo encontra-se vazio.</h6>
                  @endif
               </div>
            </div>
            @endforeach
            @else
            @if(!array_key_exists('USERS', $grupo))
            <h6 style="padding-top: 1.5rem;">Este grupo encontra-se vazio.</h6>
            @endif
            @endif
            @if(array_key_exists('USERS', $grupo))
            <div class="no-sub-users-table">
               <h6>Utilizadores neste grupo, não associados a um sub-grupo</h6>
            </div>
            <table class="table table-striped projects">
               <thead>
                  <tr>
                     <th style="width: 15%">
                        NÚMERO IDENTIFICADOR
                     </th>
                     <th style="width: 15%">
                        NOME
                     </th>
                     <th style="width: 15%">
                        POSTO
                     </th>
                     <th style="width: 45%">
                        TIPO DE CONTA
                     </th>
                     <th>
                     </th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($grupo['USERS'] as $user)
                  <tr>
                     <td>
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
                        @if($user['type']=="UTILIZADOR")
                        <a style="margin: 2px !important;" type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('user.profile', $user['id'])}}">Gerir</a>
                        @else
                        <a style="margin: 2px !important;" type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('gestão.viewUserChildren', $user['id'])}}">Gerir</a>
                        @endif
                     </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
            @endif
         </div>
      </div>
      @endforeach
   </div>
   @else
   <h6 style="padding-top: 1.5rem;">Você não tem grupos criados.</h6>
   @endif
</div>
</div>
</div>
@if($partner!=null)
<div class="col-md-2">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif" >
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Sub-gestor</h3>
         </div>
         <div class="card-body" style="padding-left: 0 !important; padding-bottom: 0 !important; padding-right: 0 !important; overflow-y: auto; max-height: 35rem;">
            <strong><i class="fas fa-id-badge mr-1"></i> Identificação</strong>
            <p class="text-muted">

              {{ $partner['posto'] }} <br />
              <a href="{{route('user.profile.parelha')}}">
                <strong class="uppercase-only">{{ $partner['name'] }}</strong>
              </a>
            </p>
            <strong><i class="fa fa-envelope mr-1"></i> Contacto</strong>
            <p class="text-muted">
               <strong>Email: </strong><a href="mailto:{{ $partner['email'] }}">{{ $partner['email'] }}</a><br><strong>Telf.:</strong> @if ($partner['telf']) {{ $partner['telf'] }} @else Não definido @endif
            </p>
            <a href="{{ route('subgest_desassoc') }}">
              <button type="button" class="btn btn-sm btn-danger puff-in-center" style="width: 100%">Desassociar</button>
            </a>
         </div>
      </div>
   </div>
</div>
@endif
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif" >
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Utilizadores sem grupo</h3>
            <div class="card-tools">
               <a href="#" data-toggle="modal" data-target="#newUserModal">Associar utilizador</a>
            </div>
         </div>
         <div class="card-body" style="display: block !important; padding-left: 0;padding-top: 2rem;padding-right: 0;padding-bottom: 0.5rem; overflow-y: auto; max-height: 76vh;">
            <div class="tab-pane" id="childrenusers">
               @if($allUsers!=null)
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
                        <th style="width: 15%">
                           UNIDADE
                        </th>
                        <th style="width: 35%">
                           TIPO DE CONTA
                        </th>
                        <th>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($allUsers as $user)
                     <tr>
                        <td>
                           <i class="fas fa-user"></i>&nbsp&nbsp
                           &nbsp;{{ $user['id'] }}
                        </td>
                        <td class="uppercase-only">
                           {{ $user['name'] }}
                        </td>
                        <td>
                           {{ $user['posto'] }}
                        </td>
                        <td>
                           {{ $user['unidade'] }}
                        </td>
                        <td>
                           {{ $user['type'] }}
                        </td>
                        <td>
                           <a style="margin: 2px !important;" type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('gestão.viewUserChildren', $user['id'])}}">Gerir</a>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @else
               <h6>Não existem utilizadores sem grupo.</h6>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   $('#procurarInput').on("change paste keyup",function(){
     $('#resultadosProcura').empty();
     $value=$(this).val();
     if (!$value) {
       $("#resultadosProcura").addClass("hide");
       $("#nameSearch").html("");
       $("#postoSearch").html("");
       $("#grupoSearch").html("");
       $("#subgrupoSearch").html("");
       $("#idSearch").attr("href", "#");
       return false;
     }
     $value=$(this).val();
       $.ajax({
           type : 'get',
           url : "{{route('super.userAdd.viewMine')}}",
           data:{
             'search': $value
           },
         success:function(data){
           $('#resultadosProcura').empty();
           $("#resultadosProcura").removeClass("hide");
           var trHTML = '';
           $.each(data, function (i, item) {
             if (item.grupo==null) {
               var groupHTML = 'NENHUM GRUPO';
             } else {
               var groupHTML = '<a href="/gestão/grupo/' + item.grupoID + '">' + item.grupo + '</a>';
             }
             if (item.subgrupo==null) {
               var subGroupHTML = 'NENHUM SUB-GRUPO';
             } else {
               var subGroupHTML = '<a href="/gestão/grupo/' + item.grupoID + '/sub/' + item.subgrupoID + '">' + item.subgrupo + '</a>';
             }
             trHTML += '<tr>'
             + '<td><a href="/gestao/user/type/' + item.type + '/' + item.id  + '">' + item.id + '</a> </td>'
             + '<td>' + item.name + '</td>'
             + '<td>' + item.posto + '</td>'
             + '<td>' + groupHTML  + '</td>'
             + '<td>' + subGroupHTML  + '</td>'
             + '</tr>';
           });
           $('#resultadosProcura').append(trHTML);
       }
     });
   }).delay(500);
</script>
<script>
   $('#procurarInputSubgestor').on("change paste keyup",function(){
     $('#resultadosProcuraSubGestor').empty();
     $value=$(this).val();
     if (!$value) {
       $("#resultadosProcuraSubGestor").addClass("hide");
       $("#nameSearch").html("");
       $("#postoSearch").html("");
       $("#grupoSearch").html("");
       $("#subgrupoSearch").html("");
       $("#idSearch").attr("href", "#");
       return false;
     }
     $value=$(this).val();
       $.ajax({
           type : 'get',
           url : "{{route('super.add_gestor')}}",
           data:{
             'search': $value
           },
         success:function(data){
           $('#resultadosProcuraSubGestor').empty();
           $("#resultadosProcuraSubGestor").removeClass("hide");
           var trHTML = '';
           $.each(data, function (i, item) {
             trHTML += '<tr>'
             + '<td style="width: 10%;vertical-align: middle;"><a href="/user/' + item.id  + '">' + item.id + '</a> </td>'
             + '<td style="vertical-align: middle;" class="uppercase-only">' + item.name + '</td>'
             + '<td style="vertical-align: middle;">' + item.posto + '</td>'
             + '<td style="vertical-align: middle;" class="project-actions text-right"> <a href="/gestao/subgestor/add/' + item.id  + '"><button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif slide-in-blurred-top" id="addSub'+ item.id + '">Adicionar</button> </a> </td>'
             + '</tr>';
           });
           $('#resultadosProcuraSubGestor').append(trHTML);
       }
     });
   }).delay(500);
</script>
<script>
   $('#searchBar').on("change paste keyup",function(){
     $value=$(this).val();
     if (!$value) {
       $("#resultsTableUsers").addClass("hide");
       $("#nameSearch").html("");
       $("#posto").html("");
       $("#unidade").html("");
       $("#addUserToMe").attr("href", "#");
       return false;
     }
     $value=$(this).val();
       $.ajax({
           type : 'get',
           url : "{{route('super.userAdd.search')}}",
           data:{
             'search': $value
           },
         success:function(data){
           if (data.nome!=null) {
             $("#resultsTableUsers").removeClass("hide");
             $("#nameSearch").html(data.nome);
             $("#posto").html(data.posto);
             $("#unidade").html(data.unidade);
             $("#addUserToMe").attr("href", "http://10.102.21.45:81/alim/public/gestão/utilizadores/addUser/" + data.id);

           } else {
               $("#resultsTableUsers").addClass("hide");
               $("#nameSearch").html("");
               $("#posto").html("");
               $("#unidade").html("");
               $("#addUserToMe").attr("href", "#");
               return false;
           }
       }
     });
   }).delay(1000);
</script>
<script>
   $(document).ready(function() {
   $("#resultsTable").css("display", "none");
   $value=$(this).val();
     $.ajax({
         type : 'get',
         url : "{{route('gestão.getUserGroups')}}",
         success:function(data){
           $.each(data, function(i, item) {
             $.each(item, function( key, value ) {
               $("#childGrupo").append(new Option(value.nome,value.ref));
             });
           });
         }
     });
   });
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
