@extends('layout.master')
@section('extra-links')
   <link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
@endsection
@section('title','Perfil')
@section('breadcrumb')
   <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
   <li class="breadcrumb-item active">Gestão</li>
   <li class="breadcrumb-item active">Perfis</li>
   <li class="breadcrumb-item active">{{ $id }}</li>
@endsection
@section('page-content')
@if ($SCHEDULE_USER_VACATIONS)
  <div class="modal puff-in-center" id="deleteFeriasModal" tabindex="-1" role="dialog" aria-labelledby="deleteFeriasModalLabel" aria-hidden="true">
     <div class=" modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="deleteFeriasModalLabel">Remover férias</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <form action="{{route('gestão.destroyUser')}}" method="POST" enctype="multipart/form-data" id="removeFeriasForm">
              <div class="modal-body">
                 <p>Tem a certeza que pretende remover esta entrada de férias?<br>
                    Esta ação é <b>irreversível</b>.
                 </p>
                 @csrf
                 <input type="hidden" id="id_ferias" name="id_ferias" readonly>
              </div>
              <div class="modal-footer">
                 <button type="submit" class="btn btn-danger">Remover</button>
                 <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
              </div>
           </form>
        </div>
     </div>
  </div>

  <div class="modal puff-in-center" id="errorAddingModal" tabindex="-1" role="dialog" aria-labelledby="errorAddingModal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
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

  <div class="modal puff-in-center" id="addVacation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class=" modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Marcar férias</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <form action="{{route('gestao.ferias.add')}}" method="POST" enctype="multipart/form-data" id="addFerias">
              <div class="modal-body">
                <h6 style="margin-top: .5rem; margin-bottom: 1.25rem;">Apenas marque as férias do utilizador ao receber passaporte.</h6>
                 @csrf
                 <div class="form-group row" id="customTimeInput" name="customTimeInput">
                    <label for="reportLocalSelect" class="col-sm-3 col-form-label">Datas</label>
                    <div class="col-sm-9">
                       <div class="input-group">
                          <div class="input-group-prepend">
                             <span class="input-group-text">
                             <i class="far fa-calendar-alt"></i>
                             </span>
                          </div>
                          <input type="text" autocomplete="off" placeholder="Data de inicio a data de apresentação" required class="form-control float-right" name="dateRangePicker"
                            id="dateRangePicker1" data-toggle="dateRangePicker" data-target="#dateRangePicker">

                            <input type="hidden" value="" id="user_id_add" name="user_id">
                       </div>
                    </div>
                 </div>
              </div>
              <div class="modal-footer">
                 <button type="submit" class="btn btn-primary">Marcar</button>
                 <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
              </div>
           </form>
        </div>
     </div>
  </div>
@endif
@if ($USERS_NEED_FATUR)
<div class="modal puff-in-center" id="marcarParaConf" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Marcar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{ route('user.tagoblg') }}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <p>Marcar que este utilizador está a receber as refeições a dinheiro?
                  @if ($already_tagged)
                     <br><strong>Continuar irá sobrescrever a entrada actual.</strong>
                  @endif
               </p>
               @csrf
               <input type="hidden" name="user_ID" value="{{ $id }}">
               <div class="form-group row" id="customTimeInput" name="customTimeInput">
                  <label for="reportLocalSelect" class="col-sm-5 col-form-label">Periodo de tempo</label>
                  <div class="col-sm-7">
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text">
                           <i class="far fa-calendar-alt"></i>
                           </span>
                        </div>
                        <input type="text" autocomplete="off" placeholder="Periodo de tempo" required class="form-control float-right" name="dateRangePicker" id="dateRangePicker" data-toggle="dateRangePicker" data-target="#dateRangePicker">
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-warning">Concluir</button>
               <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endif

<div class="col-md-3">
   <div class="card @if ($lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif card-outline">
      <div class="card-body box-profile" style="padding-bottom: .5rem !important;">
         @php
            $NIM = $id;
            while ((strlen((string)$NIM)) < 8) {
               $NIM = 0 . (string)$NIM;
            }
            $filename = "assets/profiles/".$NIM.".PNG";
            $filename_jpg = "assets/profiles/".$NIM.".JPG";
         @endphp
         @if (file_exists(public_path($filename)))
           <div class="text-center">
              <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;">
           </div>
         @elseif (file_exists(public_path($filename_jpg)))
           <div class="text-center">
              <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename_jpg) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;">
           </div>
         @else
            @php
            $NIM = $id;
            while ((strlen((string)$NIM)) < 8) {
               $NIM = 0 . (string)$NIM;
            }
               $filename_jpg = "https://cpes-wise2/Unidades/Fotos/". $NIM . ".JPG";
            @endphp
            <div class="text-center">
              <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename_jpg) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;">
           </div>
         @endif
         <p class="text-muted text-center">
           @if ($posto != "ASS.OP." && $posto != "ENC.OP." && $posto != "TIA" && $posto != "TIG.1" && $posto != "TIE" && $posto != "ASS.TEC." && $posto != "ASS.OP." && $posto != "TEC.SUP."&&  $posto != "" &&  $posto != "SOLDADO" )
             @if (Auth::check() && Auth::user()->dark_mode=='Y')
               @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" . $posto . ".png"; @endphp
             @else
                @php $filename2 = "assets/icons/postos/TRANSPARENT/" .  $posto . ".png"; @endphp
             @endif
             <img style="max-width: 3rem; object-fit: scale-down; margin-top: .5rem;" src="{{ asset($filename2) }}">
           @else
             {{ $posto }}
           @endif
         </p>
         <h3 class="profile-username text-center uppercase-only">{{ $name }}</h3>
         @if ($descriptor)
           <p class="uppercase-only center-text">
                {{ $descriptor }}
           </p>
         @endif
      </div>
   </div>
   <div class="card @if ($lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      <div class="card-header">
         <h3 class="card-title">Acerca</h3>
      </div>
      <div class="card-body">
         <strong><i class="fas fa-id-badge mr-1"></i> NIM</strong>
         <p class="text-muted">
            {{ $NIM }}
         </p>
         <hr>
         <strong><i class="fas fa-map-marker-alt mr-1"></i> Unidade</strong>
         <p class="text-muted">@if(!empty($unidade)) {{ $unidade }} @else Não definido @endif</p>
         <hr>
         <strong><i class="fas fa-map-marker mr-1"></i> Secção</strong>
         <p class="text-muted">@if(!empty($seccao)) {{ $seccao }} @else Não definido @endif</p>
         <hr>
         <strong><i class="fa fa-envelope mr-1"></i> Email</strong>
         @if(!empty($email))
         <a href="mailto:{{ $email }}">
            <p class="text-muted"> {{ $email }}</p>
         </a>
         @else
         <p class="text-muted"> Não definido</p>
         @endif
         <hr>
         <strong><i class="fa fa-phone-square mr-1"></i> Extensão telefónica</strong>
         <p class="text-muted">@if(!empty($telf)) {{ $telf }} @else Não definido @endif</p>
         <hr>
         <strong><i class="far fa-user-circle mr-1"></i> Tipo de utilizador</strong>
         <p class="text-muted">{{ $user_type }}</p>
         @if ($already_tagged)
           <hr>
           <strong><i style="color: #d14351" class="fas fa-exclamation mr-1"></i>&nbsp; A receber as refeições a dinheiro.</strong>
         @endif
      </div>
   </div>
</div>
<div class="col-md-9">
   <div class="card card-outline @if ($lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      <div class="card-header p-2">
         @if ($lock=='N')
         <div class="d-flex justify-content-between">
            <ul class="nav nav-pills secondary">
               <li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab">Detalhes</a></li>
               @if($ZERO_PERIOD_TAGS)
                  <li class="nav-item"><a class="nav-link" href="#refes" data-toggle="tab">Marcações</a></li>
               @endif
               @if ($posto=="ASS.TEC." || $posto=="ASS.OP." || $posto=="TEC.SUP." ||$posto == "ENC.OP." || $posto == "TIA" ||$posto == "TIG.1" || $posto == "TIE" || $already_tagged)
                  <li class="nav-item"><a class="nav-link" href="#stats" data-toggle="tab">Confirmações</a></li>
               @endif
               @if($user_type=="POC" || $user_type=="ADMIN")
                 <li class="nav-item"><a class="nav-link" href="#childUsers" data-toggle="tab">Utilizadores</a></li>
               @endif
               @if ($SCHEDULE_USER_VACATIONS==true)
                 <li class="nav-item"><a class="nav-link" href="#ferias" data-toggle="tab">Ausências</a></li>
               @endif
               <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Definições</a></li>
            </ul>
            <form method="POST" action="{{ route('generate.general.user.Report') }}">
              @csrf
              <input type="hidden" id="id" name="id" value="{{ $id }}">
              <button type="submit" class="btn-d-flex">Gerar relatório&nbsp&nbsp&nbsp<i class="fas fa-file-invoice"></i></button>
           </form>
            @if(Auth::user()->user_type=="HELPDESK")
            <a href="{{ route('helpdesk.consultas.result', $id) }}" style="padding: .5rem 1rem;">
              Ver detalhes&nbsp;&nbsp;<i class="fas fa-plus-square"></i> </button>
            </a>
            @endif
         </div>
         @else
         <div class="d-flex justify-content-between">
            <ul class="nav nav-pills secondary">
               <li class="nav-item" disabled><a class="nav-link a_disabled">Detalhes</a></li>
               <li class="nav-item" disabled><a class="nav-link a_disabled">Marcações</a></li>
               @if($user_type=="POC" || $user_type=="ADMIN")
                  <li class="nav-item" disabled><a class="nav-link a_disabled">Utilizadores</a></li>
               @endif
               <li class="nav-item" disabled><a class="nav-link a_disabled">Definições</a></li>
            </ul>
            @if(Auth::user()->user_type=="HELPDESK")
               <a href="{{ route('helpdesk.consultas.result', $id) }}" style="padding: .5rem 1rem;">
                  Ver detalhes&nbsp;&nbsp;<i class="fas fa-plus-square"></i> </button>
               </a>
            @endif
         </div>
         @endif
      </div>
      <div class="card-body" style="max-height: 80vh; overflow-y: auto; padding-bottom: 10px;">
         @if ($lock=='N')
         <div class="tab-content" >
            <div class="tab-pane swing-in-left-fwd active swing-in-left-fwd" id="timeline">
               @if($isUserOutClassed==true)
               <h5>Você não tem permissões para ver detalhes deste perfil.</h5>
               @else
               <h6>
                  <strong>Conta verificada</strong>
                  <p>@if($account_verified=='Y') SIM @else NÃO @endif</p>
                  <strong>Conta criada</strong>
                  <p>{{ $created_at }} </p>
                  @if($last_modification_at!=null)
                  @if($created_at!=$last_modification_at)
                  <strong>Ultima modificação</strong>
                  <p>{{ $last_modification_at }}</p>
                  <strong>Modificado por</strong>
                  <p>NIM {{ $last_modification_by }}</p>
                  @endif
                  @endif
                  @if($verified_at!=NULL)
                  @if($verified_by!=NULL)
                  <strong>Autorizado</strong>
                  <p>{{ $verified_at }}</p>
                  <strong>Autorizado por</strong>
                  <p>NIM {{ $verified_by }}</p>
                  @else
                  @if($account_verified=='Y')
                  <strong>Autorizado</strong>
                  <p>Por Express Verification Token ({{ $verified_by }})</p>
                  @endif
                  @endif
                  @else
                  @if($account_verified=='Y')
                  <strong>Autorizado</strong>
                  <p>Por Express Verification Token ({{ $verified_by }})</p>
                  @endif
                  @endif
               </h6>
               @endif
            </div>
            @if($ZERO_PERIOD_TAGS)
            <div class="tab-pane swing-in-left-fwd" id="refes" style="overflow-y: auto;">
               <div class="card-body p-0">
                  @if(!$already_tagged)
                  @if (!empty($ementa))

                  @php
                     $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                     $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");                      
                     $skip = 0;
                  @endphp

                  <table class="table table-striped projects" id="marcainfo">
                  <thead>
                     <tr>
                        <th style="width: 10%">
                           Data
                        </th>
                        <th style="width: 10%">
                           Refeição
                        </th>
                        <th style="width: 35%">
                           Ementa
                        </th>
                        <th>

                        </th>
                        <th>

                        </th>
                     </tr>
                  </thead>
                  <tbody>

                     @foreach($ementa as $key => $refeiçao)
                        <tr>
                           @if($skip==0)
                           <td rowspan="3" style="padding: 1.2rem 0 1.2rem 10px !important;">
                              @php                                 
                                 $mes_index = date('m', strtotime($refeiçao['data']));
                                 $weekday_number = date('N',  strtotime($refeiçao['data']));     
         
                              @endphp

                              <strong>
                              {{ date('d', strtotime($refeiçao['data'])) }}
                              {{ $mes[($mes_index - 1)] }}<br>
                              </strong>
                              <span @if($weekday_number=="7" || $weekday_number=="6") style="font-size: .85rem;color: #92b1d1;" @else style="font-size: .85rem;" @endif>{{ $semana[($weekday_number -1)] }}</span>
                           </td>
                           @endif

                           <td>
                              Pequeno-almoço
                           </td>
                           <td>

                           </td>
                           <td>
                              @if($refeiçao['1REF']['marcada']=="1")
                                 Marcada em 
                                 <b>{{ \App\Models\locaisref::where('refName', $refeiçao['1REF']['local'])->first()->value('localName') }}</b>
                              @endif
                           </td>
                           <td>
                              @if($refeiçao['1REF']['marcada']=="0")
                              @php $formid = Str::random(32); @endphp
                                 <form method="POST" action="{{route('marcacao.store')}}" id="{{$formid}}" name="{{$formid}}">
                                    @csrf
                                    <input type="hidden" id="data{{$formid}}" name="data" value="{{$refeiçao['data']}}"></input>
                                    <input type="hidden" id="ref{{$formid}}" name="ref" value="1REF"></input>
                                    <input type="hidden" id="localDeRef{{$formid}}" name="localDeRef" value=""></input>
                                    <div class="btn-group marcar-ref-btn">
                                       <button type="button" style="width: 130px !important; height: 31px !important;" class="btn btn-sm btn-primary dropdown-toggle dropdown-icon slide-in-blurred-top" data-toggle="dropdown"
                                          aria-expanded="false">
                                          Marcar&nbsp&nbsp&nbsp
                                          <span class="sr-only" style="">Toggle Dropdown</span>
                                          <div class="dropdown-menu dropdown-menu-local" role="menu">
                                             @foreach (\App\Models\locaisref::get()->all() as $key => $local)
                                                <a class="dropdown-item @if($local['estado']=="NOK") disabled-drop @endif"
                                                   @if($local['estado']!="NOK" )onclick="changeLocalAndPost('{{$local['refName']}}', '{{ $formid }}')"@endif>{{$local['localName']}}
                                                </a>
                                             @endforeach
                                          </div>
                                       </button>
                                    </div>
                                 </form>
                              @else 
                                 
                                    <form method="POST" action="{{route('marcacao.destroy')}}">
                                       @csrf
                                       <input type="hidden" id="id" name="id" value="{{$refeiçao['1REF']['id']}}"></input>
                                       <button type="button" class="btn btn-sm btn-danger marcar-ref-btn slide-in-blurred-top" onclick="RemoveRef('{{$refeiçao['1REF']['id']}}')"><i class="fas fa-trash"></i>&nbspRemover</button>
                                    </form>

                              @endif
                           </td>                                                   
                        </tr>

                        <tr>
                           <td>
                                 Almoço
                              </td>
                              <td>  
                                 Sopa: <b>{{ $refeiçao['sopa_almoço'] }}</b><br>
                                 Prato: <b>{{ $refeiçao['prato_almoço'] }}</b><br>
                                 Sobremesa: <b>{{ $refeiçao['sobremesa_almoço'] }}</b>
                              </td>
                              <td>
                                 @if($refeiçao['2REF']['marcada']=="1")
                                    Marcada em 
                                    <b>{{ \App\Models\locaisref::where('refName', $refeiçao['2REF']['local'])->first()->value('localName') }}</b>
                                 @endif
                              </td>
                              <td>
                                 @if($refeiçao['2REF']['marcada']=="0")
                                    
                                 @php $formid = Str::random(32); @endphp
                                 <form method="POST" action="{{route('marcacao.store')}}" id="{{$formid}}" name="{{$formid}}">
                                    @csrf
                                    <input type="hidden" id="data{{$formid}}" name="data" value="{{$refeiçao['data']}}"></input>
                                    <input type="hidden" id="ref{{$formid}}" name="ref" value="2REF"></input>
                                    <input type="hidden" id="localDeRef{{$formid}}" name="localDeRef" value=""></input>
                                    <div class="btn-group marcar-ref-btn">
                                       <button type="button" style="width: 130px !important; height: 31px !important;" class="btn btn-sm btn-primary dropdown-toggle dropdown-icon slide-in-blurred-top" data-toggle="dropdown"
                                          aria-expanded="false">
                                          Marcar&nbsp&nbsp&nbsp
                                          <span class="sr-only" style="">Toggle Dropdown</span>
                                          <div class="dropdown-menu dropdown-menu-local" role="menu">
                                             @foreach (\App\Models\locaisref::get()->all() as $key => $local)
                                                <a class="dropdown-item @if($local['estado']=="NOK") disabled-drop @endif"
                                                   @if($local['estado']!="NOK" )onclick="changeLocalAndPost('{{$local['refName']}}', '{{ $formid }}')"@endif>{{$local['localName']}}
                                                </a>
                                             @endforeach
                                          </div>
                                       </button>
                                    </div>
                                 </form>


                                 @else 

                                 <form method="POST" action="{{route('marcacao.destroy')}}">
                                    @csrf
                                    <input type="hidden" id="id" name="id" value="{{$refeiçao['2REF']['id']}}"></input>
                                    <button type="button" class="btn btn-sm btn-danger marcar-ref-btn slide-in-blurred-top" onclick="RemoveRef('{{$refeiçao['2REF']['id']}}')"><i class="fas fa-trash"></i>&nbspRemover</button>
                                 </form>
                                                                     
                                 @endif
                              </td>                                                   
                           </tr>

                           <tr>
                              <td>
                                    Jantar
                                 </td>
                                 <td>  
                                    Sopa: <b>{{ $refeiçao['sopa_jantar'] }}</b><br>
                                    Prato: <b>{{ $refeiçao['prato_jantar'] }}</b><br>
                                    Sobremesa: <b>{{ $refeiçao['sobremesa_jantar'] }}</b>
                                 </td>
                                 <td>
                                    @if($refeiçao['3REF']['marcada']=="1")
                                       Marcada em 
                                       <b>{{ \App\Models\locaisref::where('refName', $refeiçao['3REF']['local'])->first()->value('localName') }}</b>
                                    @endif
                                 </td>
                                 <td>
                                    @if($refeiçao['3REF']['marcada']=="0")
                                       
                                    @php $formid = Str::random(32); @endphp
                                    <form method="POST" action="{{route('marcacao.store')}}" id="{{$formid}}" name="{{$formid}}">
                                       @csrf
                                       <input type="hidden" id="data{{$formid}}" name="data" value="{{$refeiçao['data']}}"></input>
                                       <input type="hidden" id="ref{{$formid}}" name="ref" value="3REF"></input>
                                       <input type="hidden" id="localDeRef{{$formid}}" name="localDeRef" value=""></input>
                                       <div class="btn-group marcar-ref-btn" id="subm{{$formid}}">
                                          <button type="button" style="width: 130px !important; height: 31px !important;" class="btn btn-sm btn-primary dropdown-toggle dropdown-icon slide-in-blurred-top" data-toggle="dropdown"
                                             aria-expanded="false">
                                             Marcar&nbsp&nbsp&nbsp
                                             <span class="sr-only" style="">Toggle Dropdown</span>
                                             <div class="dropdown-menu dropdown-menu-local" role="menu">
                                                @foreach (\App\Models\locaisref::get()->all() as $key => $local)
                                                   <a class="dropdown-item @if($local['estado']=="NOK") disabled-drop @endif"
                                                      @if($local['estado']!="NOK" )onclick="changeLocalAndPost('{{$local['refName']}}', '{{ $formid }}')"@endif>{{$local['localName']}}
                                                   </a>
                                                @endforeach
                                             </div>
                                          </button>
                                       </div>
                                    </form>

                                    @else 
                                       
                                    <form method="POST" action="{{route('marcacao.destroy')}}">
                                       @csrf
                                       <input type="hidden" id="id" name="id" value="{{$refeiçao['3REF']['id']}}"></input>
                                       <button type="button" class="btn btn-sm btn-danger marcar-ref-btn slide-in-blurred-top" id="removtag{{$refeiçao['3REF']['id']}}" onclick="RemoveRef('{{$refeiçao['3REF']['id']}}')"><i class="fas fa-trash"></i>&nbspRemover</button>
                                    </form>

                                    @endif
                                 </td>                                                   
                           </tr>
                     @endforeach
                     

                  </tbody>
               </table>


                  @else
                     <h5>Este utilizador não tem nenhuma marcação.</h5>
                  @endif
                  @else
                     <h6 style="padding: 5px;"><strong><i style="color: #d14351" class="fas fa-exclamation mr-1"></i>&nbsp; A receber as refeições a dinheiro.</strong></h6>
                  @endif
               </div>
            </div>
            @endif
            @if ($posto=="ASS.TEC." || $posto=="ASS.OP." || $posto=="TEC.SUP.")
            <div class="tab-pane swing-in-left-fwd" id="stats">
               @if(!empty($marcaçoes))
               <table class="table table-striped" style="margin-bottom: 0 !important;">
                  <thead>
                     <tr>
                        <th style="width: 1%">
                        </th>
                        <th>
                           Data
                        </th>
                        <th style="width: 20%">
                           Refeição
                        </th>
                        <th style="width: 40%">
                           Ementa
                        </th>
                        <th>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($marcaçoes as $marcaçao)
                     @if(isset($ementa[$marcaçao['id']]))
                     @php $refeiçaoEmMarcação = $ementa[$marcaçao['id']]; $isMarcada = $marcadasVerificadas[$marcaçao['id']]; @endphp
                     <tr>
                        <td>
                           @if(isset($isMarcada['data']))
                           @if($isMarcada['data']==$refeiçaoEmMarcação['data'] && $isMarcada['ref']==$marcaçao['meal'])
                           @if($isMarcada['check']=="Y")
                           @php $refMarcada = 1; @endphp
                           <i class="fas fa-check nav-icon"></i>
                           @else
                           @php $refMarcada = 0; @endphp
                           <i class="fas fa-times nav-icon"></i>
                           @endif
                           @else
                           @php $refMarcada = 0; @endphp
                           <i class="fas fa-times"></i>
                           @endif
                           @else
                           @php $refMarcada = 0; @endphp
                           <i class="fas fa-times"></i>
                           @endif
                        </td>
                        <td class="project_progress">
                           @php
                           $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                           $mes_index = date('m', strtotime($refeiçaoEmMarcação['data']));
                           @endphp
                           <p class="p_noMargin">
                              {{ date('d', strtotime($refeiçaoEmMarcação['data'])) }}
                              {{ $mes[($mes_index - 1)] }}
                           </p>
                        </td>
                        <td>
                           <p class="p_noMargin">
                              @if($marcaçao['meal']=='1REF')
                              1ºRefeição
                              @elseif($marcaçao['meal']=='3REF')
                              3ºRefeição
                              @else
                              2ºRefeição
                              @endif
                           </p>
                        </td>
                        <td>
                           @if($marcaçao['meal']=='1REF')
                           <p class="p_noMargin">
                              Pequeno almoço</b>
                           </p>
                           @else
                           <p class="p_noMargin">
                              Prato: <b>@if($marcaçao['meal']=='3REF'){{ $refeiçaoEmMarcação['prato_jantar'] }}@else {{ $refeiçaoEmMarcação['prato_almoço'] }}@endif</b>
                           </p>
                           <small>
                              <p class="p_noMargin">
                                 Sopa: <b>@if($marcaçao['meal']=='3REF'){{ $refeiçaoEmMarcação['sopa_jantar'] }}@else {{ $refeiçaoEmMarcação['sopa_almoço'] }}@endif</b>
                              </p>
                              <p class="p_noMargin">
                                 Sobremesa: <b>@if($marcaçao['meal']=='3REF'){{ $refeiçaoEmMarcação['sobremesa_jantar'] }}@else {{ $refeiçaoEmMarcação['sobremesa_almoço'] }}@endif</b>
                              </p>
                           </small>
                           @endif
                        </td>
                        <td class="project-actions text-right">
                           @if($refMarcada==1)
                           <form method="POST" action="{{route('gestão.untagMealChildrenUser')}}">
                              @csrf
                              <input type="hidden" id="meal" name="meal" value="{{$marcaçao['meal']}}"></input>
                              <input type="hidden" id="data" name="data" value="{{$refeiçaoEmMarcação['data']}}"></input>
                              <input type="hidden" id="user" name="user" value="{{$id}}"></input>
                              <button type="submit" class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn meal-confirmed @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif-untag "><i class="far fa-times-circle"></i>&nbsp&nbsp&nbspAnular confirmação</button>
                           </form>
                           @else
                           <form method="POST" action="{{route('gestão.tagMealChildrenUser')}}">
                              @csrf
                              <input type="hidden" id="meal" name="meal" value="{{$marcaçao['meal']}}"></input>
                              <input type="hidden" id="data" name="data" value="{{$refeiçaoEmMarcação['data']}}"></input>
                              <input type="hidden" id="user" name="user" value="{{$id}}"></input>
                              <button style="width: 200px !important;" type="submit" class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn"><i class="far fa-check-circle"></i>&nbsp&nbsp&nbspConfirmar</button>
                           </form>
                           @endif
                        </td>
                     </tr>
                     @endif
                     @endforeach
                  </tbody>
               </table>
               @else
               <h5>Este utilizador não tem refeições marcadas.</h5>
               @endif
            </div>
            @endif
            <div class="tab-pane swing-in-left-fwd" id="childUsers" style="overflow-y: auto;">
               <div class="tab-pane swing-in-left-fwd" id="childrenusers">
                  @if($childrenUsers!=null)
                  <table class="table table-striped projects">
                     <thead>
                        <tr>
                           <th style="width: 15%">
                              NIM
                           </th>
                           <th style="width: 30%">
                              NOME
                           </th>
                           <th style="width: 15%">
                              POSTO
                           </th>
                           <th>
                              GRUPO
                           </th>
                           <th style="width: 10%">
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($childrenUsers as $user)
                        <tr>
                           <td>

                              {{ $user['id'] }}
                           </td>
                           <td class="uppercase-only">
                              <b>{{ $user['name'] }}</b>
                           </td>
                           <td>
                              <b>{{ $user['posto'] }}</b>
                           </td>
                           <td>
                              @if($user['grupo_id']!=null)
                              {{ $user['grupo_name'] }}
                              @else GERAL
                              @endif
                              @if($user['subgrupo_id']!=null)
                              \  {{ $user['subgrupo_name'] }}
                              @else
                              @if($user['grupo_id']!=null)
                              \  GERAL
                              @endif
                              @endif
                           </td>
                           <td>
                              @if($user['type']=="UTILIZADOR")
                                 <a type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('user.profile', $user['id'])}}">Ver</a>
                              @else
                                 <a type="submit" class="btn btn-sm btn-primary children-user-context-btn" href="{{route('gestão.viewUserChildren', $user['id'])}}">Ver</a>
                              @endif
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
                  @else
                    @if(isset($childrenUsersError))
                      <h5> {{ $childrenUsersError }} </h5>
                    @else
                      <h5>Sem utilizadores associados.</h5>
                    @endif
                  @endif
               </div>
            </div>

            @if ($SCHEDULE_USER_VACATIONS==true)
              <div class="tab-pane swing-in-left-fwd" id="ferias" >
                <div style="float: right; margin-right: 1rem;">
                  <button type="submit" style="width: 10rem !important;"  data-toggle="modal" data-target="#addVacation" class="btn smallbtn
                  @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn addVac" data-id="{{$id}}">
                    <i class="far fa-calendar-plus">&nbsp&nbsp&nbsp</i>
                    Adicionar
                  </button>
                </div>

                @if (!empty($ferias))
                  @foreach ($ferias as $key => $ferias_entry)
                    <div class="ferias_entry_parent" style="margin-top: 1rem; width: 44rem !important; margin-bottom: 1rem !important;" id="feriasEntry{{ $ferias_entry['id'] }}">
                      <div class="ferias_entry_icon">
                        <i class="fas fa-calendar-alt"></i>
                      </div>
                      <div class="ferias_entry_details">
                        @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $mes_index_start = date('m', strtotime($ferias_entry['data_inicio']));
                        $mes_index_end = date('m', strtotime($ferias_entry['data_fim']));
                        @endphp
                        <h6 class="text-sm text-mute">Ausência marcada de</h6><h6 class="text-sm text-mute ferias_entry_spacer">até</h6><br>
                        <h5>{{ date('d', strtotime($ferias_entry['data_inicio'])) }}  {{ $mes[($mes_index_start - 1)] }}</h5> &nbsp;
                        <h5 class="ferias_entry_spacer">{{ date('d', strtotime($ferias_entry['data_fim'])) }} {{ $mes[($mes_index_end - 1)] }}</h5>
                      </div>
                      <div class="ferias_entry_button_parent">
                        <button type="button" data-id="{{ $ferias_entry['id'] }}"  data-toggle="modal" data-target="#deleteFeriasModal"
                        class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn ferias_entry_button">
                          <i class="far fa-calendar-times">&nbsp&nbsp&nbsp</i>
                          Desmarcar
                        </button>
                      </div>
                    </div>
                  @endforeach
                @else
                  <h5>Este utilizador atualmente não tem nenhum registo de ausência.</h5>
                @endif
              </div>
            @endif


            <div class="tab-pane swing-in-left-fwd" id="settings" >
               @if($isUserOutClassed==true)
               <h5>Você não tem permissões para editar este perfil.</h5>
               @else
               @if ($EDIT_MEMBERS==true)
               <form class="form-horizontal" method="POST" action="{{route('profile.admin.save')}}">
                  {{ csrf_field() }}
                  <div class="form-group row">
                     <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                     <div class="col-sm-10">
                        <input type="hidden" class="form-control uppercase-only" id="inputId" name="inputId" value="{{ $id }}">
                        <input type="text" class="form-control uppercase-only" id="inputName" name="inputName" placeholder="Nome" value="{{ $name }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputPosto" class="col-sm-2 col-form-label">Posto</label>
                     <div class="col-sm-10">
                        <select class="custom-select" id="inputPosto" name="inputPosto">
                           @if($posto==null)
                           <option disabled selected>Selecione um posto</option>
                           @endif
                           <option value="ASS.OP." @if($posto=="ASS.OP.") selected @endif>ASSISTENTE OPERACIONAL</option>
                           <option value="ENC.OP." @if($posto=="ENC.OP.") selected @endif>ENCARREGADO OPERACIONAL</option>
                           <option value="ASS.TEC." @if($posto=="ASS.TEC.") selected @endif>ASSISTENTE TÉCNICO</option>
                           <option value="TEC.SUP." @if($posto=="TEC.SUP.") selected @endif>TÉCNICO SUPERIOR</option>
                           <option value="TIA" @if($posto=="TIA") selected @endif>TÉCNICO INFORMÁTICA ADJUNTO</option>
                           <option value="TIG.1" @if($posto=="TIG.1") selected @endif>TÉCNICO DE INFORMÁTICA GRAU 1</option>
                           <option value="TIE" @if($posto=="TIE") selected @endif>TÉCNICO INFORMÁTICA ESPECIALISTA</option>

                           <option value="SOLDADO" @if($posto=="SOLDADO") selected @endif)>SOLDADO</option>
                           <option value="2ºCABO" @if($posto=="2ºCABO") selected @endif)>2º CABO</option>
                           <option value="1ºCABO" @if($posto=="1ºCABO") selected @endif>1º CABO</option>
                           <option value="CABO-ADJUNTO" @if($posto=="CABO-ADJUNTO") selected @endif>CABO-ADJUNTO</option>
                           <option value="2ºFURRIEL" @if($posto=="2ºFURRIEL") selected @endif>2º FURRIEL</option>
                           <option value="FURRIEL" @if($posto=="FURRIEL") selected @endif>FURRIEL</option>
                           <option value="2ºSARGENTO" @if($posto=="2ºSARGENTO") selected @endif>2º SARGENTO</option>
                           <option value="1ºSARGENTO" @if($posto=="1ºSARGENTO") selected @endif>1º SARGENTO</option>
                           <option value="SARGENTO-AJUDANTE" @if($posto=="SARGENTO-AJUDANTE") selected @endif>SARGENTO-AJUDANTE</option>
                           <option value="SARGENTO-CHEFE" @if($posto=="SARGENTO-CHEFE") selected @endif>SARGENTO-CHEFE</option>
                           <option value="SARGENTO-MOR" @if($posto=="SARGENTO-MOR") selected @endif>SARGENTO-MOR</option>
                           <option value="ASPIRANTE" @if($posto=="ASPIRANTE") selected @endif>ASPIRANTE</option>
                           <option value="ALFERES" @if($posto=="ALFERES") selected @endif>ALFERES</option>
                           <option value="TENENTE" @if($posto=="TENENTE") selected @endif>TENENTE</option>
                           <option value="CAPITAO" @if($posto=="CAPITAO") selected @endif>CAPITÃO</option>
                           <option value="MAJOR" @if($posto=="MAJOR") selected @endif>MAJOR</option>
                           <option value="TENENTE-CORONEL" @if($posto=="TENENTE-CORONEL") selected @endif>TENENTE-CORONEL</option>
                           <option value="CORONEL" @if($posto=="CORONEL") selected @endif>CORONEL</option>
                           <option value="BRIGADEIRO-GENERAL" @if($posto=="BRIGADEIRO-GENERAL") selected @endif>BRIGADEIRO-GENERAL</option>
                           <option value="MAJOR-GENERAL" @if($posto=="MAJOR-GENERAL") selected @endif>MAJOR-GENERAL</option>
                           <option value="TENENTE-GENERAL" @if($posto=="TENENTE-GENERAL") selected @endif>TENENTE-GENERAL</option>
                           <option value="GENERAL" @if($posto=="GENERAL") selected @endif>GENERAL</option>
                        </select>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                     <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email" value="{{ $email }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputTelf" class="col-sm-2 col-form-label">Extensão \<br>Telemóvel</label>
                     <div class="col-sm-10">
                        <input type="number" class="form-control" id="inputTelf" name="inputTelf" placeholder="Ext. telefónica \ Telemóvel" value="{{ $telf }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputTelf" class="col-sm-2 col-form-label">Secção</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputSecc" name="inputSecc" placeholder="Secção deste utilizador dentro da unidade" value="{{ $seccao }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputTelf" class="col-sm-2 col-form-label">Função</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputFunc" name="inputFunc" placeholder="Função deste utilizador" value="{{ $descriptor }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputUEO" class="col-sm-2 col-form-label">Unidade</label>
                     <div class="col-sm-10">
                        <select class="custom-select" id="inputUEO"  name="inputUEO" @if($pendenteTrocaUnidade==true) disabled @endif>
                        @if($unidade==null)
                        <option disabled selected>Selecione uma unidade</option>
                        @endif
                        @foreach ($unidades as $key => $unidadePossivel)
                          <option @if($unidade==$unidadePossivel->slug) selected @endif value="{{ $unidadePossivel->slug }}">{{ $unidadePossivel->name }}</option>
                        @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputExperience" class="col-sm-2 col-form-label">Local preferencial</label>
                     <div class="col-sm-10">
                        <select class="custom-select" id="inputLocalRefPref"  name="inputLocalRefPref">
                           @if($localPref==null)
                           <option disabled selected>Selecione um local preferencial</option>
                           @endif
                           <option @if($localPref=="QSP") selected @endif value="QSP">Quartel da Serra do Pilar</option>
                           <option @if($localPref=="QSO") selected @endif value="QSO">Quartel de Santo Ovídio</option>
                           <option @if($localPref=="MMANTAS") selected @endif value="MMANTAS">Messe das Antas</option>
                           <option @if($localPref=="MMBATALHA") selected @endif value="MMBATALHA">Messe da Batalha</option>
                        </select>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputExperience" class="col-sm-2 col-form-label">Tipo de conta</label>
                     <div class="col-sm-10">
                        @if ($user_type=="HELPDESK" && Auth::user()->user_type!="HELPDESK")
                        <select class="custom-select" name="inputUserType" id="inputUserType" disabled >
                           <option selected>HELPDESK</option>
                        </select>
                        @else

                          @php
                            $__canSelectPOC = (Auth::user()->user_type=="HELPDESK" || Auth::user()->user_type=="ADMIN") ;
                            $__canSelectADMIN = (Auth::user()->user_type=="HELPDESK" || Auth::user()->user_type=="ADMIN" || Auth::user()->user_type=="POC") ;
                            $__canSelectHELPDESK = (Auth::user()->user_type=="HELPDESK" || Auth::user()->user_type=="ADMIN") ;
                          @endphp

                        <select class="custom-select" name="inputUserType" id="inputUserType" @if($isUserOutClassed==true) disabled @endif >
                          <option @if($user_type=="USER") selected @endif value="USER">USER</option>
                          <option @if($user_type=="POC") selected @endif @if ($__canSelectPOC) value="POC" @else disabled @endif>POC</option>
                          <option @if($user_type=="ADMIN") selected @endif @if ($__canSelectADMIN) value="ADMIN" @else disabled @endif>ADMIN</option>
                          <option @if($user_type=="HELPDESK") selected @endif @if ($__canSelectHELPDESK) value="HELPDESK" @else disabled @endif>HELPDESK</option>
                        </select>
                        @endif
                     </div>
                  </div>
                  @if ($user_type!="HELPDESK")
                  <div class="form-group row">
                     <label for="inputExperience" class="col-sm-2 col-form-label">Previlégios</label>
                     <div class="col-sm-10">
                        <select class="custom-select" id="inputUserPerm"  name="inputUserPerm">
                           <option disabled>Selecione o tipo de permissões</option>
                           @if ($user_type=="ADMIN" || $user_type=="POC" || $user_type=="USER")
                             <option @if($permissionLevel=="GENERAL") selected @endif value="GENERAL">Permissões base</option>
                             <option @if($permissionLevel=="LOG") selected @endif value="LOG">Permissões de Logistica</option>
                             <option @if($permissionLevel=="GCSEL") selected @endif value="GCSEL">Permissões de GabCSel</option>
                             <option @if($permissionLevel=="PESS") selected @endif value="PESS">Permissões de Pessoal</option>
                             <option @if($permissionLevel=="MESSES") selected @endif value="MESSES">Permissões de Messe</option>
                             <option @if($permissionLevel=="ALIM") selected @endif value="ALIM">Permissões de Alimentação</option>
                               <option @if($permissionLevel=="CCS") selected @endif value="CCS">Permissões de CCS</option>
                           @endif
                           @if ($user_type=="ADMIN")
                             <option @if($permissionLevel=="TUDO") selected @endif value="TUDO">Permissões de acesso total</option>
                           @endif
                        </select>
                     </div>
                  </div>
                  @endif
                  <div class="form-group row profile-settings-form-svbtn-spacer">
                     <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif">Guardar</button>
                     </div>
                  </div>
               </form>
               @endif
               @if($MASS_QR_GENERATE || $BLOCK_MEMBERS || $DELETE_MEMBERS)
               <div class="form-group row profile-settings-form-svbtn-spacer" >
                  <div class="col-sm-10">
                     <h4 style="margin-bottom: 1rem;font-size: 1.25rem;">Ferramentas</h4>
                     @if ($USERS_NEED_FATUR)
                         @if($posto!="ASS.TEC." || $posto!="ASS.OP." || $posto!="TEC.SUP."|| $posto != "ENC.OP." || $posto != "TIA" ||$posto != "TIG.1" || $posto != "TIE" )
                         <a href="#" data-toggle="modal" data-target="#marcarParaConf">
                           <button type="submit" style="margin: .2rem; !important;" class="btn btn-warning">Registo de refeições em numerário</button>
                         </a>
                         @if($already_tagged)
                           <form style="display: inline-block;" method="post" action="{!! route('user.removeTagOblig') !!}">
                              @csrf
                              <input type="hidden" value="{{ $id }}" name="nim" id="nim" />
                              <button type="submit" style="margin: .2rem; !important;" class="btn btn-warning">Remover registo de refeições em numerário</button>
                           </form>
                         @endif
                        @endif
                     @endif

                     @if ($MASS_QR_GENERATE)
                        <a href="{!! route('gestao.qrs.user', $id) !!}">
                           <button type="submit" style="margin: .2rem; !important;" class="btn btn-warning">Gerar código QR</button>
                         </a>

                     @endif

                     @if ($BLOCK_MEMBERS)
                     <form style="display: inline-block;" method="post" action="{!! route('user.lock') !!}">
                        @csrf
                        <input type="hidden" value="{{ $id }}" name="nim" id="nim" />
                        <button type="submit" class="btn btn-danger" style="margin: .2rem; margin-right: 1.5rem !important;">Bloquear conta</button>
                     </form>
                     @endif

                     @if ($DELETE_MEMBERS)
                     <a href="{{route('helpdesk.remove.user', $id)}}">
                       <button type="submit" class="btn btn-danger" style="margin: .2rem">Apagar conta</button>
                     </a>
                     @endif
                  </div>
               </div>
               @elseif (Auth::user()->user_type=="HELPDESK")
               <div class="form-group row profile-settings-form-svbtn-spacer" style="margin-top: 3rem !important;">
                  <div class="offset-sm-2 col-sm-10">
                     <h4 style="margin-bottom: 1rem;font-size: 1.25rem;">Ferramentas</h4>
                     <a href="{{route('helpdesk.reset.user', $id)}}">
                       <button type="submit" class="btn btn-danger" style="margin: .2rem;">Reset de conta</button>
                     </a>
                     <a href="{{route('helpdesk.remove.user', $id)}}">
                       <button type="submit" class="btn btn-danger" style="margin: .2rem">Apagar conta</button>
                     </a>
                  </div>
               </div>
               @endif
               @endif
            </div>
         </div>
         @else
           <strong style="margin-left: 5px;">Aviso</strong>
           @if (!$BLOCK_MEMBERS)
             <p style="margin-left: 5px;" class="">Esta conta encontra-se bloqueada.</p>
           @else
             <p style="margin-left: 5px;" class="">Esta conta encontra-se bloqueada.<br />Utilize o botão abaixo para a desbloquear.</p>
             <form style="margin-top: 15px; margin-bottom: 10px;" method="post" action="{!! route('user.unlock') !!}">
                @csrf
                <input type="hidden" value="{{ $id }}" name="nim" id="nim" />
                <button type="submit" class="btn btn-danger" style="margin: .2rem; margin-right: 1.5rem !important;">Desbloquear conta</button>
             </form>
           @endif
         @endif
      </div>
   </div>
</div>
@endsection

@section('extra-scripts')
@if ($SCHEDULE_USER_VACATIONS)
  <script>
     $(document).on('click','.ferias_entry_button',function(){
          let id = $(this).attr('data-id');
          $('#id_ferias').val(id);
     });
  </script>

  <script>
     $(document).on('click','.addVac',function(){
          let id = $(this).attr('data-id');
          $('#user_id_add').val(id);
     });
  </script>

  <script>

  var startDate;
  var endDate;

     $(document).ready(function() {
       $('#dateRangePicker1').daterangepicker({
         format: 'DD/MM/YYYY',
         startDate: '{{ $minDayTag }}',
         separator: " até ",
         showDropdowns: true,
         timePicker: false,
         opens: 'center',
         singleDatePicker: false,
         showRangeInputsOnCustomRangeOnly: false,
         applyClass: 'rangePickerApplyBtn',
         cancelClass: 'rangePickerCancelBtn',
         minDate : '{{ $minDayTag }}',
         parentEl: $('#addVacation'),
         locale: {
           cancelLabel: 'Limpar',
           applyLabel: 'Aplicar',
           fromLabel: 'INICIO',
           toLabel: 'APRESENTAÇÃO',
           "daysOfWeek": [
               "D",
               "S",
               "T",
               "Q",
               "Q",
               "S",
               "S"
           ],
           monthNames: [
               "Janeiro",
               "Fevereiro",
               "Março",
               "Abril",
               "Maio",
               "Junho",
               "Julho",
               "Agosto",
               "Setembro",
               "Outubro",
               "Novembro",
               "Dezembro"
           ],
           firstDay: 1
         }
       },
         function(start, end) {
          startDate = start;
          endDate = end;

         })
      });

  $("#removeFeriasForm").submit(function(e) {
        e.preventDefault();
        var ferias_ID = document.getElementById("id_ferias").value;
         $.ajax({
             url: "{{route('gestao.ferias.remove')}}",
             type: "POST",
             data: {
                 "_token": "{{ csrf_token() }}",
                 id: ferias_ID
             },
             success: function(response) {
                 if (response) {
                     if (response != 'success') {
                      document.getElementById("errorAddingTitle").innerHTML = "Erro";
                      document.getElementById("errorAddingText").innerHTML = response;
                      $("#errorAddingModal").modal()
                      $('#deleteFeriasModal').modal('toggle');
                     } else {
                       $('#deleteFeriasModal').modal('toggle');
                       document.getElementById("feriasEntry" + ferias_ID).remove();
                     }
                 }
             }
         });
   });

    $("#addFerias").submit(function(e) {
          e.preventDefault();
          var timeframe = startDate.format('D/MM/YYYY') + ' até ' + endDate.format('D/MM/YYYY');
          var user = document.getElementById("user_id_add").value;
           $.ajax({
               url: "{{route('gestao.ferias.add')}}",
               type: "POST",
               data: {
                   "_token": "{{ csrf_token() }}",
                   dateRangePicker: timeframe,
                   user_id: user,
               },
               success: function(response) {
                   if (response) {
                       if (response != 'success') {
                        document.getElementById("errorAddingTitle").innerHTML = "Erro";
                        document.getElementById("errorAddingText").innerHTML = response;
                        $("#errorAddingModal").modal()
                        $('#addVacation').modal('toggle');
                       } else {
                         location.reload();
                       }
                   }
               }
           });
     });
  </script>
@endif
@if($ZERO_PERIOD_TAGS)
<script>
   function changeLocalAndPost(toWhere, formID) {
       var campoID = "localDeRef" + formID;
       var campoData = "data" + formID;
       var refData = "ref" + formID;
       document.getElementById(campoID).value = toWhere.toUpperCase();;
       var data = document.getElementById(campoData).value;
       var ref = document.getElementById(refData).value;
       var btn = "#subm" + formID;
       $.ajax({
           url: "{{route('user.profile.tag_meal', $id)}}",
           type: "POST",
           data: {
               "_token": "{{ csrf_token() }}",
               data: data,
               ref: ref,
               localDeRef: toWhere,
               uid: "{{$id}}",
               posto: "{{$posto}}"
           },
           success: function(response) {
               console.log(response);
               if (response) {
                   if (response != 'success') {
                       document.getElementById("errorAddingTitle").innerHTML = "Erro";
                       document.getElementById("errorAddingText").innerHTML = "Erro a fazer marcação.";
                       $("#errorAddingModal").modal()
                   } else {
                      $(document).Toasts('create', {
                        title: "Marcada",
                        subtitle: "",
                        body: "A <b>"+ ref + "</b> para o dia <b>" + data + "</b> foi marcada no <b>" + toWhere + "</b>.",
                        icon: "fas fa-book",
                        autohide: true,
                        autoremove: true,
                        delay: 3500,
                        class: "toast-not",
                     });
                     $(btn).remove();
                     var content = "#refes";
                      $(content).load(location.href + " " + content);
                   }
               }
           }
       });
   }
</script>
<script>
   function RemoveRef(tag_id) {
      var btn = "#removtag" + tag_id;
       $.ajax({
           url: "{{route('marcacao.destroy')}}",
           type: "POST",
           data: {
            "_token": "{{ csrf_token() }}",
            id: tag_id,
            uid: "{{$id}}",
         },
           success: function(response) {
               console.log(response);
               if (response) {
                   if (response != 'success') {
                       document.getElementById("errorAddingTitle").innerHTML = "Erro";
                       document.getElementById("errorAddingText").innerHTML = "Erro a fazer marcação.";
                       $("#errorAddingModal").modal()
                   } else {
                      $(document).Toasts('create', {
                        title: "Marcada",
                        subtitle: "",
                        body: "A marcação foi removida com sucesso.",
                        icon: "fas fa-calendar-times",
                        autohide: true,
                        autoremove: true,
                        delay: 3500,
                        class: "toast-not",
                     });
                     $(btn).remove();
                     var content = "#refes";
                     $(content).load(location.href + " " + content);
                   }
               }
           }
       });
   }
</script>
@endif

@php $minDay = date('d/m/Y'); @endphp
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/daterangepicker.js')}}"></script>
<script>
   $(document).ready(function() {
     $('#dateRangePicker').daterangepicker({
       format: 'DD/MM/YYYY',
       startDate: '{{ $minDay }}',
       separator: " até ",
       showDropdowns: false,
       timePicker: false,
       opens: 'center',
       singleDatePicker: false,
       showRangeInputsOnCustomRangeOnly: false,
       applyClass: 'rangePickerApplyBtn',
       cancelClass: 'rangePickerCancelBtn',
       minDate : '{{ $minDay }}',
       parentEl: $('#marcarParaConf'),
       locale: {
         cancelLabel: 'Limpar',
         applyLabel: 'Aplicar',
         fromLabel: 'DE',
         toLabel: 'ATÉ',
         "daysOfWeek": [
             "D",
             "S",
             "T",
             "Q",
             "Q",
             "S",
             "S"
         ],
         monthNames: [
             "Janeiro",
             "Fevereiro",
             "Março",
             "Abril",
             "Maio",
             "Junho",
             "Julho",
             "Agosto",
             "Setembro",
             "Outubro",
             "Novembro",
             "Dezembro"
         ],
         firstDay: 1
       }
     },
   );
   });
</script>
@endsection
