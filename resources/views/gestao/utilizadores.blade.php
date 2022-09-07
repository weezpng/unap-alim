@extends('layout.master')
@section('title','Gestão de utilizadores')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Utilizadores</li>
<li class="breadcrumb-item active">Todos</li>
@endsection
@section('page-content')
<div class="modal puff-in-center" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Remover utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.destroyUser')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <p>Tem a certeza que pretende remover este utilizador?<br>
                  Esta ação é <b>irreversível</b>.
               </p>
               @csrf
               <input type="hidden" id="nim" name="nim" readonly>
               <input type="hidden" id="name" name="name" readonly>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-danger">Remover</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="filterUsers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filtrar</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{ route('gestao.filterUsers') }}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Tipo de utilizador</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="filter_account_type" name="filter_account_type">
                        @if (isset($filters['account_type_filter']))
                        <option @if ($filters['account_type_filter']=="0") selected @endif value="0">Não filtrar</option>
                        <option @if ($filters['account_type_filter']=="USER") selected @endif value="USER">USER</option>
                        <option @if ($filters['account_type_filter']=="POC") selected @endif value="POC">POC</option>
                        <option @if ($filters['account_type_filter']=="ADMIN") selected @endif value="ADMIN">ADMIN</option>
                        <option @if ($filters['account_type_filter']=="HELPDESK") selected @endif value="HELPDESK">HELPDESK</option>
                        @else
                        <option selected value="0">Não filtrar</option>
                        <option value="USER">USER</option>
                        <option value="POC">POC</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="HELPDESK">HELPDESK</option>
                        @endif
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Permissões</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="filter_account_permission" name="filter_account_permission">
                        @if (isset($filters['account_permission_filter']))
                        <option @if ($filters['account_permission_filter']=="0") selected @endif value="0">Não filtrar</option>
                        <option @if ($filters['account_permission_filter']=="GENERAL") selected @endif value="GENERAL">Permissões gerais</option>
                        <option @if ($filters['account_permission_filter']=="ALIM") selected @endif value="ALIM">Permissões de Alimentação</option>
                        <option @if ($filters['account_permission_filter']=="PESS") selected @endif value="PESS">Permissões de Pessoal</option>
                        <option @if ($filters['account_permission_filter']=="LOG") selected @endif value="LOG">Permissões de Logística</option>
                        <option @if ($filters['account_permission_filter']=="MESSES") selected @endif value="MESSES">Permissões de Messes</option>
                        <option @if ($filters['account_permission_filter']=="GCSEL") selected @endif value="GCSEL">Permissões de GCSel</option>
                        <option @if ($filters['account_permission_filter']=="CCS") selected @endif value="CCS">Permissões de CCS</option>
                        <option @if ($filters['account_permission_filter']=="TUDO") selected @endif value="TUDO">Permissões totais</option>
                        @else
                          <option value="0">Não filtrar</option>
                          <option value="GENERAL">Permissões gerais</option>
                          <option value="ALIM">Permissões de Alimentação</option>
                          <option value="PESS">Permissões de Pessoal</option>
                          <option value="LOG">Permissões de Logística</option>
                          <option value="MESSES">Permissões de Messes</option>
                          <option value="GCSEL">Permissões de GCSel</option>
                          <option value="CCS">Permissões de CCS</option>
                          <option value="TUDO">Permissões totais</option>
                        @endif
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="inputEmail" class="col-sm-5 col-form-label">Posto do utilizador</label>
                  <div class="col-sm-7">
                     <select class="custom-select" id="filter_account_posto" name="filter_account_posto">
                        @if (isset($filters['account_posto_filter']))
                        <option @if ($filters['account_posto_filter']=="0") selected @endif value="0">Não filtrar</option>
                        <option @if ($filters['account_posto_filter']=="ASS.OP.") selected @endif value="ASS.OP.">ASSISTENTE OPERACIONAL</option>
                        <option @if ($filters['account_posto_filter']=="ENC.OP.") selected @endif value="ENC.OP.">ENCARREGADO OPERACIONAL</option>
                        <option @if ($filters['account_posto_filter']=="ASS.TEC.") selected @endif value="ASS.TEC.">ASSISTENTE TÉCNICO</option>
                        <option @if ($filters['account_posto_filter']=="TEC.SUP.") selected @endif value="TEC.SUP.">TÉCNICO SUPERIOR</option>
                        <option @if ($filters['account_posto_filter']=="TIA") selected @endif  value="TIA" >TÉCNICO INFORMÁTICA ADJUNTO</option>
                        <option @if ($filters['account_posto_filter']=="TIG.1") selected @endif  value="TIG.1">TÉCNICO DE INFORMÁTICA GRAU 1</option>
                        <option @if ($filters['account_posto_filter']=="TIE") selected @endif  value="TIE">TÉCNICO INFORMÁTICA ESPECIALISTA</option>
                        <option @if ($filters['account_posto_filter']=="SOLDADO") selected @endif value="SOLDADO">SOLDADO</option>
                        <option @if ($filters['account_posto_filter']=="2ºCABO") selected @endif value="2ºCABO">2º CABO</option>
                        <option @if ($filters['account_posto_filter']=="1ºCABO") selected @endif value="1ºCABO">1º CABO</option>
                        <option @if ($filters['account_posto_filter']=="CABO-ADJUNTO") selected @endif value="CABO-ADJUNTO">CABO-ADJUNTO</option>
                        <option @if ($filters['account_posto_filter']=="2ºFURRIEL") selected @endif value="2ºFURRIEL">2º FURRIEL</option>
                        <option @if ($filters['account_posto_filter']=="FURRIEL") selected @endif value="FURRIEL">FURRIEL</option>
                        <option @if ($filters['account_posto_filter']=="2ºSARGENTO") selected @endif value="2ºSARGENTO">2º SARGENTO</option>
                        <option @if ($filters['account_posto_filter']=="1ºSARGENTO") selected @endif value="1ºSARGENTO">1º SARGENTO</option>
                        <option @if ($filters['account_posto_filter']=="SARGENTO-AJUDANTE") selected @endif value="SARGENTO-AJUDANTE">SARGENTO-AJUDANTE</option>
                        <option @if ($filters['account_posto_filter']=="SARGENTO-CHEFE") selected @endif value="SARGENTO-CHEFE">SARGENTO-CHEFE</option>
                        <option @if ($filters['account_posto_filter']=="SARGENTO-MOR") selected @endif value="SARGENTO-MOR">SARGENTO-MOR</option>
                        <option @if ($filters['account_posto_filter']=="ASPIRANTE") selected @endif value="ASPIRANTE">ASPIRANTE</option>
                        <option @if ($filters['account_posto_filter']=="ALFERES") selected @endif value="ALFERES">ALFERES</option>
                        <option @if ($filters['account_posto_filter']=="TENENTE") selected @endif value="TENENTE">TENENTE</option>
                        <option @if ($filters['account_posto_filter']=="CAPITAO") selected @endif value="CAPITAO">CAPITÃO</option>
                        <option @if ($filters['account_posto_filter']=="MAJOR") selected @endif value="MAJOR">MAJOR</option>
                        <option @if ($filters['account_posto_filter']=="TENENTE-CORONEL") selected @endif value="TENENTE-CORONEL">TENENTE-CORONEL</option>
                        <option @if ($filters['account_posto_filter']=="CORONEL") selected @endif value="CORONEL">CORONEL</option>
                        <option @if ($filters['account_posto_filter']=="BRIGADEIRO-GENERAL") selected @endif value="BRIGADEIRO-GENERAL">BRIGADEIRO-GENERAL</option>
                        <option @if ($filters['account_posto_filter']=="MAJOR-GENERAL") selected @endif value="MAJOR-GENERAL">MAJOR-GENERAL</option>
                        <option @if ($filters['account_posto_filter']=="TENENTE-GENERAL") selected @endif value="TENENTE-GENERAL">TENENTE-GENERAL</option>
                        <option @if ($filters['account_posto_filter']=="GENERAL") selected @endif value="GENERAL">GENERAL</option>
                        @else
                          <option selected value="0">Não filtrar</option>
                          <option value="ASS.OP.">ASSISTENTE OPERACIONAL</option>
                          <option value="ENC.OP.">ENCARREGADO OPERACIONAL</option>
                          <option value="ASS.TEC.">ASSISTENTE TÉCNICO</option>
                          <option value="TEC.SUP.">TÉCNICO SUPERIOR</option>
                          <option value="TIA" >TÉCNICO INFORMÁTICA ADJUNTO</option>
                          <option value="TIG.1">TÉCNICO DE INFORMÁTICA GRAU 1</option>
                          <option value="TIE">TÉCNICO INFORMÁTICA ESPECIALISTA</option>
                          <option value="TEC.SUP.">TEC.SUP.</option>
                          <option value="SOLDADO">SOLDADO</option>
                          <option value="2ºCABO">2º CABO</option>
                          <option value="1ºCABO">1º CABO</option>
                          <option value="CABO-ADJUNTO">CABO-ADJUNTO</option>
                          <option value="2ºFURRIEL">2º FURRIEL</option>
                          <option value="FURRIEL">FURRIEL</option>
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
                          @endif
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Associados a gestor</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="filter_account_assoc" name="filter_account_assoc">
                        @if (isset($filters['account_type_filter']))
                        <option @if ($filters['account_assoc_filter']=="0") selected @endif value="0">Não filtrar</option>
                        <option @if ($filters['account_assoc_filter']=="Y") selected @endif value="Y">SIM</option>
                        <option @if ($filters['account_assoc_filter']=="N") selected @endif value="N">NÃO</option>
                        @else
                        <option selected value="0">Não filtrar</option>
                        <option value="Y">SIM</option>
                        <option value="N">NÃO</option>
                        @endif
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Contas bloqueadas</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="filter_account_locked" name="filter_account_locked">
                        @if (isset($filters['account_lock_filter']))
                        <option @if ($filters['account_lock_filter']=="0") selected @endif value="0">Não filtrar</option>
                        <option @if ($filters['account_lock_filter']=="Y") selected @endif value="Y">SIM</option>
                        <option @if ($filters['account_lock_filter']=="N") selected @endif value="N">NÃO</option>
                        @else
                        <option selected value="0">Não filtrar</option>
                        <option value="Y">SIM</option>
                        <option value="N">NÃO</option>
                        @endif
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Local preferencial</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="filter_account_pref_type" name="filter_account_pref_type">
                        @if (isset($filters['account_local_pref_filter']))
                        <option @if ($filters['account_local_pref_filter']=="0") selected @endif value="0">Não filtrar</option>
                        <option @if ($filters['account_local_pref_filter']=="QSP") selected @endif value="QSP">Quartel da Serra do Pilar</option>
                        <option @if ($filters['account_local_pref_filter']=="QSO") selected @endif value="QSO">Quartel de Santo Ovídio</option>
                        <option @if ($filters['account_local_pref_filter']=="MMANTAS") selected @endif value="MMANTAS">Messe Militar das Antas</option>
                        <option @if ($filters['account_local_pref_filter']=="MMBATALHA") selected @endif value="MMBATALHA">Messe Militar da Batalha</option>
                        @else
                        <option selected value="0">Não filtrar</option>
                        <option value="QSP">Quartel da Serra do Pilar</option>
                        <option value="QSO">Quartel de Santo Ovídio</option>
                        <option value="MMANTAS">Messe Militar das Antas</option>
                        <option value="MMBATALHA">Messe Militar da Batalha</option>
                        @endif
                     </select>
                  </div>
               </div>
               @if ($MEALS_TO_EXTERNAL)
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Unidade do utilizador</label>
                  <div class="col-sm-7">
                     <select class="custom-select" id="filter_account_unit"  name="filter_account_unit">
                        @if (isset($filters['account_unit_filter']))
                        <option @if ($filters['account_unit_filter']=="0") selected @endif value="0">Não filtrar</option>
                        @foreach ($unidades as $key => $unidade)
                        <option @if ($filters['account_unit_filter']==$unidade->slug) selected @endif value="{{ $unidade->slug }}"> {{ $unidade->name }}</option>
                        @endforeach
                        @else
                        <option selected value="0">Não filtrar</option>
                        @foreach ($unidades as $key => $unidade)
                        <option value="{{ $unidade->slug }}"> {{ $unidade->name }}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
               </div>
               @endif
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Pediu troca de unidade</label>
                  <div class="col-sm-7">
                     <select required class="custom-select" id="filter_unit_change" name="filter_unit_change">
                        @if (isset($filters['account_unit_change']))
                        <option @if ($filters['account_unit_change']=="0") selected @endif value="0">Não filtrar</option>
                        <option @if ($filters['account_unit_change']=="Y") selected @endif value="Y">SIM</option>
                        <option @if ($filters['account_unit_change']=="N") selected @endif value="N">NÃO</option>
                        @else
                        <option selected value="0">Não filtrar</option>
                        <option value="Y">SIM</option>
                        <option value="N">NÃO</option>
                        @endif
                     </select>
                  </div>
               </div>
               @if ($USERS_NEED_FATUR)
                 <div class="form-group row">
                    <label for="reportLocalSelect" class="col-sm-5 col-form-label">Marcado para faturação</label>
                    <div class="col-sm-7">
                       <select required class="custom-select" id="filter_tagged_fat" name="filter_tagged_fat">
                          @if (isset($filters['tagged_to_fat']))
                          <option @if ($filters['tagged_to_fat']=="0") selected @endif value="0">Não filtrar</option>
                          <option @if ($filters['tagged_to_fat']=="Y") selected @endif value="Y">SIM</option>
                          <option @if ($filters['tagged_to_fat']=="N") selected @endif value="N">NÃO</option>
                          @else
                          <option selected value="0">Não filtrar</option>
                          <option value="Y">SIM</option>
                          <option value="N">NÃO</option>
                          @endif
                       </select>
                    </div>
                 </div>
               @endif
            </div>
            <div class="modal-footer">
               <button type="submit" style="width: 7rem;" class="btn btn-primary">Filtrar</button>
               @if (isset($filters) && $filters!=null)
               <a href="{!! route('gestão.usersAdmin') !!}">
               <button type="button"  class="btn btn-secondary">Limpar filtros</button>
               </a>
               @endif
               <button type="button" style="margin-left: 1rem;" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>


@if ($EXPRESS_MEMBERS_CHECK)
  <!--
  <div class="modal puff-in-center" id="loadUsersFiles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Verificar utilizadores</i></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="#" method="POST" enctype="multipart/form-data">
        gestao.users.loadusers
          <div class="modal-body">
            @csrf
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="customFile" name="customFile"  accept=".xls, .xlsm" />
              <label class="custom-file-label" for="customFile">Carregar listagem de utilizadores (formato EXCEL)</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif">Carregar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
-->
@endif

<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif" >
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Todos os utilizadores</h3>
            <div class="card-tools">
               @if ($EXPRESS_MEMBERS_CHECK)
                 <!--<a href="#" style="margin: 0 .5rem 0 .5rem;" id="filterUsersToggle" data-toggle="modal" data-target="#loadUsersFiles">Verificar utilizadores &nbsp; <i class="fas fa-people-arrows"></i></a>-->
               @endif
               @if ($MASS_QR_GENERATE)
                  <a href="{{ route('gestao.qrs.mass') }}" style="margin: 0 .5rem 0 .65rem;"> &nbsp; Gerar códigos QR &nbsp; <i class="fas fa-qrcode"></i></a>
               @endif
               <a href="#" style="margin: 0 .5rem 0 .5rem;" id="filterUsersToggle" data-toggle="modal" data-target="#filterUsers">Filtrar &nbsp; <i class="fas fa-filter"> &nbsp; &nbsp;</i></a>
               <button style="margin: 0 .5rem 0 0" type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
               </button>
            </div>
         </div>
      </div>
      <div class="card-body" style="display: block !important; overflow-y: auto; max-height: 73vh;">
         @if (!@empty($users))
         <table class="table table-striped projects">
            <thead>
               <tr>
                  <th style="width: 35%">
                     UTILIZADOR
                  </th>
                  <th  style="width: 10%">
                    POSTO
                  </th>
                  <th style="width: 10%">
                     COLOCAÇÃO
                  </th>
                  <th style="width: 20%">
                     CONTACTOS
                  </th>
                  <th>
                     TIPO
                  </th>
                  <th style="align: right; width: 15%">
                  </th>
               </tr>
            </thead>
            <tbody>
               @foreach($users as $user)
               <tr @if($user['lock']=="Y") style="color: #d14351 !important;" @endif>
               <td>
                 @php
                   $temp_id = strval($user['id']);
                   while ((strlen((string)$temp_id)) < 8) {
                     $temp_id = 0 . (string)$temp_id;
                   }

                   $filename = "assets/profiles/".$user['id'] . ".PNG";
                   $filename_jpg = "assets/profiles/".$user['id'] . ".JPG";
                 @endphp
                 @if (file_exists(public_path($filename)))
                    <a href="{{ route('user.profile',  $user['id']) }}">
                     <div style="display: inline-block; width: 5rem;">
                       <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename) }}" alt="User profile picture"
                       @if($user['lock']=="N")
                         style="border: 2px solid #6c757d !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;"
                       @else
                         style="border: 2px solid #d14351 !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;"
                       @endif>
                     </div>
                   </a>
                 @elseif (file_exists(public_path($filename_jpg)))
                   <a href="{{ route('user.profile',  $user['id']) }}">
                      <div style="display: inline-block; width: 5rem;">
                        <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename_jpg) }}" alt="User profile picture"
                        @if($user['lock']=="N")
                          style="border: 2px solid #6c757d !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;"
                        @else
                          style="border: 2px solid #d14351 !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;"
                        @endif>
                      </div>
                    </a>
                 @else

                  @if($user['posto']=="ASS.TEC." || $user['posto']=="ASS.OP." || $user['posto']=="TEC.SUP."||
                        $user['posto'] == "ENC.OP." || $user['posto'] == "TIA" ||$user['posto'] == "TIG.1" || $user['posto'] == "TIE")
                        @php
                           $filename = "assets/icons/CIVIL.PNG";
                        @endphp
                     @else
                        @if($user['user_type']=="ADMIN")
                           @php
                              $filename = "assets/icons/MILITAR_2.PNG";
                           @endphp
                        @else
                           @php
                              $filename = "assets/icons/MILITAR.PNG";
                           @endphp
                        @endif
                     @endif
                     <a href="{{ route('user.profile',  $user['id']) }}">
                     <div style="display: inline-block; width: 5rem;">
                       <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename) }}" alt="User profile picture"
                       @if($user['lock']=="N")
                         style="border: 2px solid #6c757d !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;"
                       @else
                         style="border: 2px solid #d14351 !important;padding: 2px !important; object-fit: cover; object-position: top; height: 5rem;"
                       @endif>
                     </div>
                   </a>
                 @endif



                    <span style="padding: 0.75rem; display: inline-block; vertical-align: top;">
                      <a @if($user['lock']=="Y") style="color: #d14351 !important; font-size: 1.15rem !important;" @else style="font-size: 1.15rem !important;" @endif href="{{ route('user.profile',  $user['id']) }}">
                        <h6 style="display: inline; margin-top: 1rem;">{{ $user['name'] }} (<span class="text-muted text-center text-sm">{{ $user['id'] }}</span>)</h6>
                      </a>
                      @if ($user['descriptor'])
                        <span class="text-muted text-center text-sm"><br />
                            {{\Illuminate\Support\Str::limit($user['descriptor'], 65, $end='...')}}
                        </span>
                      @endif

                    </span>

               </td>
               <td class="uppercase-only">
                 <span style="padding: 1rem; padding-left: .25rem; padding-right: 1.5rem;">
                   @if ($user['posto'] != "ASS.TEC." && $user['posto'] != "ASS.OP." && $user['posto'] != "TEC.SUP." && $user['posto'] != "SOLDADO" && $user['posto'] != "ENC.OP" && $user['posto'] != "TEC.SUP"&& $user['posto'] != "TIA" && $user['posto'] != "TIG.1"&& $user['posto'] != "TIE" && $user['posto'] != "")
                     @if (Auth::check() && Auth::user()->dark_mode=='Y')
                       @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" .$user['posto'] . ".png"; @endphp
                     @else
                        @php $filename2 = "assets/icons/postos/TRANSPARENT/" .$user['posto'] . ".png"; @endphp
                     @endif
                     <img style="max-width: 3.5rem; object-fit: scale-down;" src="{{ asset($filename2) }}">
                   @else
                    <span style="font-size: .75rem;"><strong>{{ $user['posto'] }}</strong></span>
                  @endif
                 </span>
               </td>
               <td>
                  {{ $user['unidade'] }}
                  @if ($user['seccao'])
                    <br />
                    <span class="text-muted text-center">{{ $user['seccao'] }}</span>
                  @endif
               </td>
               <td>
                 <a href="mailto:{{$user['email']}}">{{ $user['email'] }}</a><br />{{ $user['telf'] }}
               </td>
               <td>
                  {{ $user['user_type'] }}
                  @if($user['lock']=="Y") <strong style="float: right;">BLOQUEADA</strong> @endif
               </td>
               <td style="text-align: right">
                  @if ($EDIT_MEMBERS==true || $RESET_ACCOUNTS==true || $BLOCK_MEMBERS==true)
                  <a style="margin: 2px !important;" type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('user.profile', $user['id'])}}">Gerir</a>
                  @endif
                  @if ($DELETE_MEMBERS==true)
                  <button style="margin: 2px !important;" type="submit" data-id="{{ $user['id'] }}" data-name="- {{ $user['posto'] }} - {{ $user['name'] }}"
                     data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-danger children-user-context-btn delete">Remover</button>
                  @endif
               </td>
               </tr>
               @endforeach
            </tbody>
         </table>
         @else
         @if (isset($filters))
         <h6>Nenhum utilizador encontrado com os filtros definidos.</h6>
         @else
         @if ($MEALS_TO_EXTERNAL)
         <h6>Nenhum utilizador encontrado.</h6>
         @else
         <h6>Nenhum utilizador encontrado na sua unidade ({{ Auth::user()->unidade }}).</h6>
         @endif
         @endif
         @endif
         <!-- /.card-body -->
      </div>
   </div>
</div>

@endsection
@section('extra-scripts')
<script src="{{asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script type="text/javascript">
   $(document).ready(function() {
       bsCustomFileInput.init();
   });
</script>
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
   $('#searchBar').on("change paste keyup click",function(){
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
             $("#addUserToMe").attr("href", "/gestão/utilizadores/addUser/" + data.id);

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
