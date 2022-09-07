@extends('layout.master')
@section('extra-links')
<link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
@endsection
@section('title','Gestão de utilizadores')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Utilizadores</li>
<li class="breadcrumb-item active">Verificação</li>
@endsection
@section('page-content')

<div class="modal fade" id="errorAddingModal" tabindex="-1" role="dialog" aria-labelledby="errorAddingModal" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="errorAddingTitle" name="errorAddingTitle"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p id="errorAddingText" name="errorAddingText"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>

<div class="modal puff-in-center" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="overlay fade-in-fwd" id="createUserModalOverlay">
           <i class="fas fa-2x fa-sync fa-spin"></i>
        </div>
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Criar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestao.users.loadusers.create')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <input type="hidden" id="tr_id" />
               <div class="form-group row">
                  <label for="nimIn" class="col-sm-3 col-form-label">Identificação</label>
                  <div class="col-sm-9">
                     <input required type="text" class="form-control" id="nimIn" name="nimIn" placeholder="NIM"
                        maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="nomeIn" class="col-sm-3 col-form-label">Nome</label>
                  <div class="col-sm-9">
                     <input required type="text" class="form-control" id="nomeIn" name="nomeIn" placeholder="Nome">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="postoIn" class="col-sm-3 col-form-label">Posto</label>
                  <div class="col-sm-9">
                     <select required class="custom-select" id="postoIn" name="postoIn">
                        <option value="ASS.OP.">ASSISTENTE OPERACIONAL</option>
                        <option value="ENC.OP.">ENCARREGADO OPERACIONAL</option>
                        <option value="ASS.TEC.">ASSISTENTE TÉCNICO</option>                           
                        <option value="TEC.SUP.">TÉCNICO SUPERIOR</option>
                        <option value="TIA">TÉCNICO INFORMÁTICA ADJUNTO</option>
                        <option value="TIG.1">TÉCNICO DE INFORMÁTICA GRAU 1</option>                           
                        <option value="TIE">TÉCNICO INFORMÁTICA ESPECIALISTA</option>  
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
                  <label for="nomeIn" class="col-sm-3 col-form-label">Secção</label>
                  <div class="col-sm-9">
                     <input required type="text" class="form-control" id="section" name="section" placeholder="Secção">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="unidadeIn" class="col-sm-3 col-form-label">Unidade</label>
                  <div class="col-sm-9">
                     <select required class="custom-select" id="unidadeIn" name="unidadeIn">
                       @foreach ($unidades as $key => $unidade)
                       <option value="{{ $unidade->slug }}"> {{ $unidade->name }}</option>
                       @endforeach
                     </select>
                  </div>
               </div>               
               <div required class="form-group row" id="customTimeInput" name="customTimeInput">
                  <label for="reportLocalSelect" class="col-sm-3 col-form-label">Permissões</label>
                  <div class="col-sm-9">
                  <select class="custom-select" name="inputUserType" id="inputUserType" style="margin-bottom: .5rem;">         
                     <option disabled value="USER">Utilizador</option>
                     <option value="POC">POC</option>
                     <option value="ADMIN">Administrador</option>
                  </select>                  
                  <select required class="custom-select" id="inputUserPerm"  name="inputUserPerm">         
                        <option selected value="GENERAL">Permissões base</option>
                        <option value="LOG">Permissões de Logistica</option>
                        <option value="GCSEL">Permissões de GabCSel</option>
                        <option value="PESS">Permissões de Pessoal</option>
                        <option value="MESSES">Permissões de Messe</option>
                        <option value="ALIM">Permissões de Alimentação</option>
                        <option value="CCS">Permissões de CCS</option>
                        <option value="TUDO">Permissões de acesso total</option>
                  </select>
                  </div>
               </div>

            </div>
            <div class="modal-footer">
               <button type="button" onclick="createUserPostCompletion()" style="width: 6rem;" class="btn btn-primary">Criar</button>
               <button type="button" id="closeModalBtn" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="col-md-12">

  @if (!@empty($__users_old))
  <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif" >
     <div class="card-header border-0">
        <div class="d-flex justify-content-between">
           <h3 class="card-title">Remoção de utilizadores</h3>
           <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Maximizar\Minimizar">
              <i class="fas fa-minus"></i></button>
              &nbsp;&nbsp;&nbsp;&nbsp;
              <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
              </button>
           </div>
        </div>
     </div>
     <div class="card-body" style="display: block !important; overflow-y: auto; max-height: 75vh;" id="removeUsersCard">
        <table class="table table-striped projects" id="removalOfUsers">
           <thead>
              <tr>
                 <th>
                    NIM
                 </th>
                 <th style="width: 15%;">
                    NOME
                 </th>
                 <th style="width: 15%;">
                    POSTO
                 </th>
                 <th >
                    UNIDADE
                 </th>
                 <th style="align: right; width: 10%;">

                 </th>
              </tr>
           </thead>
           <tbody>
            @foreach ($__users_old as $key => $__user)
              @php $update = false; $entryid = Str::random(32); @endphp
              <tr id="{{ $entryid }}">
                <td style="padding-top: 1rem !important;padding-bottom: 1rem !important;">
                  {{$__user['id']}}
                </td>
                <td>
                  {{$__user['name']}}
                </td>
                <td style="text-align-last: center;padding-right: 6rem !important;">
                  @if ($__user['posto'] != "ASS.TEC." && $__user['posto'] != "ASS.OP." && $__user['posto'] != "TEC.SUP." 
                  && $__user['POSTO'] != "ENC.OP." && $__user['POSTO'] != "TIA" && $__user['POSTO'] != "TIG.1" && $__user['POSTO'] != "TIE" && $__user['posto'] != "SOLDADO" && $__user['posto'] != "")

                    @if (Auth::check() && Auth::user()->dark_mode=='Y')
                      @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" .$__user['posto'] . ".png"; @endphp
                    @else
                       @php $filename2 = "assets/icons/postos/TRANSPARENT/" .$__user['posto'] . ".png"; @endphp
                    @endif
                    <img style="max-width: 3rem; object-fit: scale-down; margin-top: 1rem; margin-bottom: .25rem;" src="{{ asset($filename2) }}">
                    <br />
                    <h6 class="text-sm">{{ $__user['posto'] }}</h6>
                  @else
                    {{ $__user['posto'] }}<br />
                  @endif
                </td>
                <td>
                  {{$__user['unidade_name']}}
                </td>
                <td>
                  <form action="{{ route('gestao.users.loadusers.remove') }}" method="POST" id="form{{ $entryid }}">
                    <input type="hidden" name="user_id" id="id{{ $entryid }}" value=" {{$__user['id']}}" />
                    <button style="margin: 2px !important;" type="button" class="btn btn-sm btn-danger children-user-context-btn" onclick="removeUser('{{ $entryid }}')">Remover utilizador</a>
                  </form>
                </td>
              </tr>
            @endforeach
           </tbody>
        </table>
        <!-- /.card-body -->
     </div>
  </div>
@endif

   @if (!@empty($__users_exist))
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif" >
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Actualização de utilizadores</h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Maximizar\Minimizar">
               <i class="fas fa-minus"></i></button>
               &nbsp;&nbsp;&nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
               </button>
            </div>
         </div>
      </div>
      <div class="card-body" style="display: block !important; overflow-y: auto; max-height: 75vh;" id="existingUsersCard">
         <table class="table table-striped projects" id="existingUsers">
            <thead>
               <tr>
                  <th>
                     NIM
                  </th>
                  <th style="width: 15%;">
                     NOME
                  </th>
                  <th style="width: 15%;">
                     POSTO
                  </th>
                  <th >
                     UNIDADE
                  </th>
                  <th style="align: right; width: 10%;">

                  </th>
               </tr>
            </thead>
            <tbody>
               @foreach($__users_exist as $__usr)
               @php $update = false; $entryid = Str::random(32); @endphp
               @if (strtoupper($__usr['name'])!=strtoupper($__usr['db_data']['name']))
                 @php $update = true; @endphp
               @endif
               @if (strtoupper($__usr['posto'])!=strtoupper($__usr['db_data']['posto']))
                 @php $update = true; @endphp
               @endif
               @if (strtoupper($__usr['unidade'])!=strtoupper($__usr['db_data']['unidade']))
                 @php $update = true; @endphp
               @endif
               @if ($update)
                 <tr id="{{ $entryid }}">
                     <td>
                       {{$__usr['id']}}
                     </td>
                     <td>
                        @if (!$__usr['name'])
                          {{ $__usr['db_data']['name'] }}<br />
                          <span style="font-size: .75rem; color: #d14351 !important;">Nome não preenchido em actualização.</span>
                        @else
                          {{ $__usr['name'] }}<br />
                          @if (strtoupper($__usr['name'])!=strtoupper($__usr['db_data']['name']))
                            @php $update = true; @endphp
                            <span style="font-size: .75rem; color: #1b814c">Actualização.</span>
                          @endif
                        @endif
                     </td>
                     <td @if ($__usr['posto']) style="text-align-last: center;padding-right: 6rem !important;" @else style="padding-right: 6rem !important;text-align: center;"  @endif>
                        @if (!$__usr['posto'])
                          @if ($__usr['db_data']['posto'])
                            @if ($__usr['db_data']['posto'] != "ASS.TEC." && $__usr['db_data']['posto'] != "ASS.OP." && $__usr['db_data']['posto'] != "TEC.SUP."&& $__usr['db_data']['posto'] != "SOLDADO" && $__usr['db_data']['posto'] != "TIG.1" && $__usr['db_data']['posto'] != "TIA" && $__usr['db_data']['posto'] != ""  )
                              @if (Auth::check() && Auth::user()->dark_mode=='Y')
                                @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" . $__usr['db_data']['posto'] . ".png"; @endphp
                              @else
                                 @php $filename2 = "assets/icons/postos/TRANSPARENT/" . $__usr['db_data']['posto'] . ".png"; @endphp
                              @endif
                              <img style="max-width: 3rem; object-fit: scale-down; margin-top: 1rem; margin-bottom: .25rem;" src="{{ asset($filename2) }}">
                              <br />
                              <h6 class="text-sm">{{ $__usr['db_data']['posto'] }}</h6>
                            @else
                              {{ $__usr['db_data']['posto'] }}<br />
                            @endif
                          @endif                          
                        @else
                        @if (!$__usr['db_data']['posto'])
                        <span style="font-size: .75rem; color: #d14351 !important;">Sem POSTO em ficheiro e base de dados.</span>
                        @else
                          @if (($__usr['db_data']['posto'] != "ASS.TEC." && $__usr['db_data']['posto'] != "ASS.OP." && $__usr['db_data']['posto'] != "TEC.SUP." && $__usr['db_data']['posto'] != "TIG.1" && $__usr['db_data']['posto'] != "TIA"&& $__usr['db_data']['posto'] != "SOLDADO" && $__usr['db_data']['posto'] != "")
                          || ($__usr['posto'] != "ASS.TEC." && $__usr['posto'] != "ASS.OP." && $__usr['posto'] != "TEC.SUP." && $__usr['posto'] != "TIG.1" && $__usr['posto'] != "TIA" && $__usr['posto'] != "SOLDADO" && $__usr['posto'] != ""))
                           @if (Auth::check() && Auth::user()->dark_mode=='Y')
                              @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" . $__usr['db_data']['posto'] . ".png"; @endphp
                           @else
                              @php $filename2 = "assets/icons/postos/TRANSPARENT/" .  $__usr['db_data']['posto'] . ".png"; @endphp
                           @endif
                              <img style="max-width: 3rem; object-fit: scale-down; margin-top: 1rem; margin-bottom: .25rem;" src="{{ asset($filename2) }}">
                              <br />
                              <h6 class="text-sm">{{ $__usr['db_data']['posto'] }}</h6>
                          @else
                            {{ $__usr['db_data']['posto'] }}
                          @endif
                          @if (strtoupper($__usr['posto'])!=strtoupper($__usr['db_data']['posto']))
                              @if(strtoupper($__usr['posto'])==null || strtoupper($__usr['posto'])=="")
                              <span style="font-size: .75rem; color: #d14351 !important;">POSTO não preenchido em actualização.</span>
                              @else
                                 @php $update = true; @endphp
                                 <span style="font-size: .75rem; color: #1b814c;display: block;">Actualização.</span>
                              @endif
                           @else
                          @endif
                          @endif
                        @endif
                     </td>
                     <td>
                       @if (!$__usr['unidade'])
                         {{ $__usr['db_data']['unidade'] }}<br />
                         <span style="font-size: .75rem; color: #d14351 !important;">Unidade não preenchida em actualização.</span>
                       @else
                         @if ($__usr['unidade_name'])
                           {{ $__usr['unidade_name'] }}
                         @else
                            {{ $__usr['unidade'] }}
                         @endif
                         <br />
                         @if (strtoupper($__usr['unidade'])!=strtoupper($__usr['db_data']['unidade']))
                           @php $update = true; @endphp
                           <span style="font-size: .75rem; color: #1b814c">Actualização.</span>
                         @endif
                       @endif
                     </td>
                     <td>
                       @if ($update)
                         <form action="{{ route('gestao.users.loadusers.update') }}" method="POST" id="form{{ $entryid }}">
                           <input type="hidden" name="user_id" id="id{{ $entryid }}" value=" {{$__usr['id']}}" />
                           @if ($__usr['db_data']['name']!=$__usr['name'])
                             @if ($__usr['name'])
                               <input type="hidden" name="name" id="name{{ $entryid }}" value=" {{$__usr['name']}}" />
                             @else
                               <input type="hidden" name="name" id="name{{ $entryid }}" value=" {{$$__usr['db_data']['name']}}" />
                             @endif
                           @else
                             <input type="hidden" name="name" id="name{{ $entryid }}" value=" {{$__usr['name']}}" />
                           @endif
                           @if ($__usr['db_data']['posto']!=$__usr['posto'])
                             @if ($__usr['posto'])
                               <input type="hidden" name="posto" id="posto{{ $entryid }}" value=" {{$__usr['posto']}}" />
                             @else
                               <input type="hidden" name="posto" id="posto{{ $entryid }}" value=" {{$__usr['db_data']['posto']}}" />
                             @endif
                           @else
                             <input type="hidden" name="posto" id="posto{{ $entryid }}" value=" {{$__usr['posto']}}" />
                           @endif
                           @if ($__usr['db_data']['unidade']!=$__usr['unidade'])
                             @if ($__usr['unidade'])
                                <input type="hidden" name="unidade" id="unidade{{ $entryid }}" value=" {{$__usr['unidade']}}" />
                             @else
                                <input type="hidden" name="unidade" id="unidade{{ $entryid }}" value=" {{$__usr['db_data']['unidade']}}" />
                             @endif
                           @else
                             <input type="hidden" name="unidade" id="unidade{{ $entryid }}" value=" {{$__usr['unidade']}}" />
                           @endif
                           <button style="margin: 2px !important;" type="button" class="btn btn-sm btn-primary children-user-context-btn" onclick="updateUser('{{ $entryid }}')">Actualizar</a>
                         </form>
                       @else
                          <button style="margin: 2px !important;" type="button" class="btn btn-sm btn-secondary children-user-context-btn btn-secondary-disabled disabled" disabled>Nada a actualizar</button>
                       @endif
                     </td>
                 </tr>
                @endif
               @endforeach
            </tbody>
         </table>
         <!-- /.card-body -->
      </div>
   </div>
 @endif

 @if (!@empty($__users_new))
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif" >
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Criação de utilizadores</h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Maximizar\Minimizar">
               <i class="fas fa-minus"></i></button>
               &nbsp;&nbsp;&nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
               </button>
            </div>
         </div>
      </div>
      <div class="card-body" id="newUsersCard" style="display: block !important; overflow-y: auto; max-height: 75vh;">
         <table class="table table-striped projects" id="newUsers">
            <thead>
               <tr>
                  <th>
                     NIM
                  </th>
                  <th style="width: 15%;">
                     NOME
                  </th>
                  <th style="width: 15%;">
                     POSTO
                  </th>
                  <th >
                     UNIDADE
                  </th>
                  <th >
                     PERMISSÕES
                  </th>
                  <th style="align: right; width: 10%;">

                  </th>
               </tr>
            </thead>
            <tbody>
               @foreach($__users_new as $__usr)
                @php $update = false; $_new_user_entryid = '_new_user_'.Str::random(64); @endphp
               <tr id="{{ $_new_user_entryid }}">
                 @php $tudo_preenchido = true; @endphp
                   <td>
                     {{$__usr['id']}}
                   </td>
                   <td>
                      @if (!$__usr['name'])
                        <span style="font-size: .75rem; color: #d14351 !important;">Nome não preenchido em RGT.</span>
                        @php $tudo_preenchido = false; @endphp
                      @else
                        {{ $__usr['name'] }}<br />
                      @endif
                   </td>
                   <td style="text-align-last: center;padding-right: 6rem !important;">
                      @if (!$__usr['posto'])
                        <span style="font-size: .75rem; color: #d14351 !important;">Posto não preenchido em RGT.</span>
                        @php $tudo_preenchido = false; @endphp
                      @else

                      @if (($__usr['posto'] != "ASS.TEC." && $__usr['posto'] != "ASS.OP." && $__usr['posto'] != "TEC.SUP." && $__usr['posto'] != "TIG.1" && $__usr['posto'] != "SOLDADO"))

                          @if (Auth::check() && Auth::user()->dark_mode=='Y')
                            @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" . $__usr['posto'] . ".png"; @endphp
                          @else
                             @php $filename2 = "assets/icons/postos/TRANSPARENT/" .  $__usr['posto'] . ".png"; @endphp
                          @endif

                          <img style="max-width: 3rem; object-fit: scale-down; margin-top: 1rem; margin-bottom: 1rem;" src="{{ asset($filename2) }}">
                          <br />
                          <h6 class="text-sm">{{ $__usr['posto'] }}</h6>
                        @else
                          {{ $__usr['posto'] }}<br />
                        @endif
                      @endif
                   </td>
                   <td>
                      @if (!$__usr['unidade_name'])
                        <span style="font-size: .75rem; color: #d14351 !important;">Unidade não preenchida em RGT.</span>
                        @php $tudo_preenchido = false; @endphp
                      @else
                        {{ $__usr['unidade_name'] }}<br />
                      @endif
                   </td>
                   <td>
                     @if ($tudo_preenchido)
                     @if (($__usr['posto'] != "ASS.TEC." && $__usr['posto'] != "ASS.OP." && $__usr['posto'] != "TEC.SUP." && $__usr['posto'] != "TIG.1"))
                        <select required class="custom-select" name="inputUserType" id="inputUserType{{ $_new_user_entryid }}" style="margin: .25rem; width: 16rem; display: block;">        
                              <option selected value="USER">Utilizador</option>                           
                              <option value="POC">POC</option>
                              <option value="ADMIN">Administrador</option>                        
                        </select>                  
                        @else
                           <input readonly value="USER" type="text" class="form-control" name="inputUserType" id="inputUserType{{ $_new_user_entryid }}" style="margin: .25rem; width: 16rem; display: block;">
                        @endif
                        <select required class="custom-select" id="inputUserPerm{{ $_new_user_entryid }}"  name="inputUserPerm" style="margin: .25rem; width: 16rem; display: block;">         
                              <option selected value="GENERAL">Permissões base</option>
                              <option value="LOG">Permissões de Logistica</option>
                              <option value="GCSEL">Permissões de GabCSel</option>
                              <option value="PESS">Permissões de Pessoal</option>
                              <option value="MESSES">Permissões de Messe</option>
                              <option value="ALIM">Permissões de Alimentação</option>
                              <option value="CCS">Permissões de CCS</option>
                              @if (($__usr['posto'] != "ASS.TEC." && $__usr['posto'] != "ASS.OP." && $__usr['posto'] != "TEC.SUP." && $__usr['posto'] != "TIG.1"))
                                 <option value="TUDO">Permissões de acesso total</option>
                              @endif
                        </select>
                     @endif
                   </td>
                   <td>
                     <form action="{{ route('gestao.users.loadusers.create') }}" method="POST" id="form{{ $_new_user_entryid }}">
                        <input type="hidden" name="user_id" id="id{{ $_new_user_entryid }}" value=" {{$__usr['id']}}" />
                        <input type="hidden" name="name" id="name{{ $_new_user_entryid }}" value=" {{$__usr['name']}}" />
                        <input type="hidden" name="posto" id="posto{{ $_new_user_entryid }}" value=" {{$__usr['posto']}}" />
                        <input type="hidden" name="unidade" id="unidade{{ $_new_user_entryid }}" value=" {{$__usr['unidade']}}" />
                        <input type="hidden" name="data_nasc" id="data_nasc{{ $_new_user_entryid }}" value=" {{ date('d-m-Y', strtotime($__usr['data_nasc'])) }}" />
                      @if ($tudo_preenchido)
                        <button style="margin: 2px !important;" type="button" class="btn btn-sm btn-primary children-user-context-btn" onclick="createUser('{{ $_new_user_entryid }}')">Criar</a>
                      @else
                        <button style="margin: 2px !important;" type="button" class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif children-user-context-btn" onclick="completeUser('{{ $_new_user_entryid }}')">Completar</a>
                      @endif
                    </form>
                   </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>
@endif
</div>
@endsection
@section('extra-scripts')

  <script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
  <script type="text/javascript">
     $(window).on('load', function(){
       $('#createUserModalOverlay').removeClass( "overlay" );
       $('#createUserModalOverlay').hide()
     });
 </script>
 <script>
 function removeUser(entryID) {
    var trID = "#" + entryID;
    var formID = "#form" + entryID;
    var id = "id" + entryID;
     $.ajax({
         url: "{{route('gestao.users.loadusers.remove')}}",
         type: "POST",
         data: {
             "_token": "{{ csrf_token() }}",
             user_id: document.getElementById(id).value,
         },
         success: function(response) {
             console.log(response);
             if (response) {
                 if (response != 'success') {
                     document.getElementById("errorAddingTitle").innerHTML = "Erro";
                     document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro ao remover este utilizador.";
                     $("#errorAddingModal").modal()
                 } else {
                    var rowCount = $("#removalOfUsers tr").length;
                    rowCount = (rowCount - 1);
                    $(trID).addClass('puff-out-center');
                    $(trID).remove();
                    if (rowCount<=1) {
                      $('#removeUsersCard').append('<h6 style="margin-top: 1.25rem;">Todos os utilizadores que não constavam no ficheiro foram removidos.</h6>');
                    }
                 }
             }
         }
     });
 }

 </script>
  <script>
     function updateUser(entryID) {
        var trID = "#" + entryID;
         var formID = "#form" + entryID;
         var id = "id" + entryID;
         var name = "name" + entryID;
         var posto = "posto" + entryID;
         var unidade = "unidade" + entryID;
         $.ajax({
             url: "{{route('gestao.users.loadusers.update')}}",
             type: "POST",
             data: {
                 "_token": "{{ csrf_token() }}",
                 user_id: document.getElementById(id).value,
                 name: document.getElementById(name).value,
                 posto: document.getElementById(posto).value,
                 unidade:  document.getElementById(unidade).value,
             },
             success: function(response) {
                 console.log(response);
                 if (response) {
                     if (response != 'success') {
                         document.getElementById("errorAddingTitle").innerHTML = "Erro";
                         document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro ao actualizar este utilizador.";
                         $("#errorAddingModal").modal()
                     } else {
                        var rowCount = $("#existingUsers tr").length;
                        rowCount = (rowCount - 1);
                        $(trID).addClass('puff-out-center');
                        $(trID).remove();
                        if (rowCount<=1) {
                          $('#existingUsersCard').append('<h6 style="margin-top: 1.25rem;">Todos os utilizadores foram actualizados.</h6>');
                        }
                     }
                 }
             }
         });
     }
  </script>

  <script>
    function completeUser(entryID){
      var trID = "#" + entryID;
      var formID = "#form" + entryID;
      var id = "id" + entryID;
      var name = "name" + entryID;
      var posto = "posto" + entryID;
      var unidade = "unidade" + entryID;
      var data_nasc = "data_nasc" + entryID;
      $('#tr_id').val(entryID);
      $('#nimIn').val((document.getElementById(id).value).slice(1));
      $('#nomeIn').val((document.getElementById(name).value).slice(1));
      $("#postoIn").val((document.getElementById(posto).value).slice(1)).change();
      $("#unidadeIn").val((document.getElementById(unidade).value).slice(1)).change();
      $('#dateRangePicker').val((document.getElementById(data_nasc).value).slice(1));
      $("#createUserModal").modal()
    }
  </script>

  <script>
     function createUserPostCompletion() {
       $('#createUserModalOverlay').show()
       $('#createUserModalOverlay').addClass( "overlay" );
        var trID = "#" + $('#tr_id').val();
         $.ajax({
             url: "{{route('gestao.users.loadusers.create')}}",
             type: "POST",
             data: {
                 "_token": "{{ csrf_token() }}",
                 user_id: $('#nimIn').val(),
                 name: $('#nomeIn').val(),
                 posto: $('#postoIn').val(),
                 unidade: $('#unidadeIn').val(),
                 funcao: $('#funcaoIn').val(),
                 section: $('#section').val(),
                 user_type: $('#inputUserType').val(),
                 user_perm: $('#inputUserPerm').val(),               
             },
             success: function(response) {
               $('#createUserModalOverlay').removeClass( "overlay" );
               $('#createUserModalOverlay').hide()
                 if (response) {
                     if (response != 'success') {
                         document.getElementById("errorAddingTitle").innerHTML = "Erro";
                         document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro ao criar este utilizador.";
                         $("#errorAddingModal").modal()
                     } else {
                       $('#closeModalBtn').click();
                        var rowCount = $("#newUsers tr").length;
                        rowCount = (rowCount - 1);
                        $(trID).addClass('puff-out-center');
                        $(trID).remove();
                        if (rowCount<=1) {
                          $('#newUsersCard').append('<h6 style="margin-top: 1.25rem;">Todos os utilizadores foram criados.</h6>');
                        }
                     }
                 }
             }
         });
     }
  </script>

  <script>
     function createUser(entryID) {
        var trID = "#" + entryID;
        var formID = "#form" + entryID;
        var id = "id" + entryID;
        var name = "name" + entryID;
        var posto = "posto" + entryID;
        var unidade = "unidade" + entryID;
        var data_nasc = "data_nasc" + entryID;
        var type = "inputUserType" + entryID;
        var perm = "inputUserPerm" + entryID;
         $.ajax({
             url: "{{route('gestao.users.loadusers.create')}}",
             type: "POST",
             data: {
                 "_token": "{{ csrf_token() }}",
                 user_id: document.getElementById(id).value,
                 name: document.getElementById(name).value,
                 posto: document.getElementById(posto).value,
                 unidade:  document.getElementById(unidade).value,
                 section:  document.getElementById(section).value,
                 user_type: document.getElementById(type).value,
                 user_perm: document.getElementById(perm).value,
             },
             success: function(response) {
                 console.log(response);
                 if (response) {
                     if (response != 'success') {
                         document.getElementById("errorAddingTitle").innerHTML = "Erro";
                         document.getElementById("errorAddingText").innerHTML = "Ocorreu um erro ao criar este utilizador.";
                         $("#errorAddingModal").modal()
                     } else {
                        var rowCount = $("#newUsers tr").length;
                        rowCount = (rowCount - 1);
                        $(trID).addClass('puff-out-center');
                        $(trID).remove();
                        if (rowCount<=1) {
                          $('#newUsersCard').append('<h6 style="margin-top: 1.25rem;">Todos os utilizadores foram criados.</h6>');
                        }
                     }
                 }
             }
         });
     }
  </script>
@endsection
