@extends('layout.master')
@section('title','Consulta de perfil')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Helpdesk</li>
<li class="breadcrumb-item active">Consultas</li>
<li class="breadcrumb-item active">&commat;{{$user['id'] }}</li>
@endsection
@section('page-content')
@include('layout.float-btn')
<div class="modal puff-in-center" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 50vw !important">
        <div class="modal-content">
            <div class="modal-header">
                @php
                $totalEntradas = 0;
                @endphp
                @foreach ($historico as $entradaHistorico)
                  @php
                    $totalEntradas++;
                  @endphp
                @endforeach
                <h5 class="modal-title" id="exampleModalLabel">Histórico de NIM {{ $user['id'] }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="overflow-y: auto; max-height: 80vh;">
                <div style="margin-bottom: 15px;">
                    <span style="font-size: 16px;">Total de {{ $totalEntradas }} entradas</span>
                </div>
                <div class="timeline">
                    @foreach ($historico as $key => $entradaHistorico)
                    <div class="time-label">
                        <span class="bg-dark">
                            {{ date('d-m-Y', strtotime($entradaHistorico['data'])) }}
                        </span>
                    </div>
                    <div>
                        @if ($entradaHistorico['type']=='REF')
                        <i class="fas fa-book bg-teal"></i>
                        @elseif ($entradaHistorico['type']=='CREATE_NOTIFICATION')
                        @if ($entradaHistorico['PS']=="NORMAL")
                        <i class="fas fa-comment bg-purple"></i>
                        @else
                        <i class="fas fa-comment bg-orange"></i>
                        @endif
                        @else
                        @if ($entradaHistorico['PS']=="NORMAL")
                        <i class="far fa-bell bg-purple"></i>
                        @else
                        <i class="far fa-bell bg-orange"></i>
                        @endif
                        @endif
                        <div class="timeline-item">
                            <span class="time"><i class="far fa-clock "></i> {{ date('H:i:s', strtotime($entradaHistorico['data'])) }}</span>
                            <h3 class="timeline-header"><strong>{{ $entradaHistorico['title'] }}</strong></h3>
                            <div class="timeline-body" style="white-space: pre-line; margin-top: -15px;">
                                {{ $entradaHistorico['message'] }}
                            </div>
                            <div class="timeline-footer" style="padding-bottom: 20px !important;">
                                @if ($entradaHistorico['type']=='REF')
                                Local de refeição: <strong>{{ $entradaHistorico['PS'] }}</strong>
                                @else
                                Tipo de notificação: <strong> {{ $entradaHistorico['PS'] }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div>
                        <i class="fas fa-hourglass-end bg-navy"></i>
                        <span class="timeline-end-label">FIM</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between">
                <h3 class="card-title uppercase-only">Resultado da pesquisa</h3>
                <div class="card-tools">

                    <a href="{{ route('helpdesk.consultas.index') }}">Pesquisar novo &nbsp;&nbsp;<i class="fas fa-search"></i></a>
                </div>
            </div>
        </div>
        <div class="card-body" style="max-height: fit-content !important;">

            <div class="row" style="padding-bottom: 1rem;">
                <div class="btn-group btn-sm">
                    <button type="button" class="btn btn-sm disabled btn-dark">Conta</button>
                    <button type="button" class="btn btn-sm dropdown-toggle btn-dark dropdown-icon" data-toggle="dropdown">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.reset_perms', $user['id']) }}">Retirar permissões</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.reset_pendings', $user['id']) }}">Limpar pedidos pendentes</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.reset_pref', $user['id']) }}">Limpar preferencias da conta</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.reset_all', $user['id']) }}">Limpar todos os pedidos, permissões e preferencias</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.disable', $user['id']) }}">Desactivar conta</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.block', $user['id']) }}">Bloquear conta</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.delete', $user['id']) }}">Remover conta</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.loggoff', $user['id']) }}">Terminar sessão em todos os locais</a>
                    </div>
                </div>

                <div class="btn-group btn-sm">
                    <button type="button" class="btn btn-sm disabled btn-dark">Marcações</button>
                    <button type="button" class="btn btn-sm dropdown-toggle btn-dark dropdown-icon" data-toggle="dropdown">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_tags', [$user['id'], '1REF']) }}">Retirar marcações individuais 1ºrefeição</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_tags', [$user['id'], '2REF']) }}">Retirar marcações individuais 2ºrefeição</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_tags', [$user['id'], '3REF']) }}">Retirar marcações individuais 3ºrefeição</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_all_tags', $user['id']) }}">Retirar todas marcações individuais</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_all_quant', $user['id']) }}">Eliminar marcações quantitativas feitas por este utilizador</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_tag_oblig', $user['id']) }}">Remover registo de refeições a numerário</a>
                    </div>
                </div>

                <div class="btn-group btn-sm" style="margin-right: 2.5rem;">
                    <button type="button" class="btn btn-sm disabled btn-dark">Outros</button>
                    <button type="button" class="btn btn-sm dropdown-toggle btn-dark dropdown-icon" data-toggle="dropdown">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_entries', [$user['id'], '1REF']) }}">Limpar entradas de quiosque 1ºrefeição</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_entries', [$user['id'], '2REF']) }}">Limpar entradas de quiosque 2ºrefeição</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_entries', [$user['id'], '3REF']) }}">Limpar entradas de quiosque 3ºrefeição</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del_all_entries', $user['id']) }}">Limpar todas entradas de quiosque</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.assoc.from', $user['id']) }}">Limpar associações pendentes deste utilizador</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.assoc.to', $user['id']) }}">Limpar associações pendentes para este utilizador</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.nots.from', $user['id']) }}">Limpar notificações deste utilizador</a>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.nots.to', $user['id']) }}">Limpar notificações para este utilizador</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del.posts', $user['id']) }}">Limpar publicações deste utilizador</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item lite-effects" href="{{ route('helpdesk.user.del.warnings', $user['id']) }}">Limpar avisos de plataforma deste utilizador</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title uppercase-only">INFORMAÇÃO DE UTILIZADOR</h3>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: fit-content !important;">
                            @php $filename = "assets/profiles/". $user['id'].".jpg";
                            @endphp
                            @if (file_exists(public_path($filename)))
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;">
                            </div>
                            <h3 class="profile-username text-center uppercase-only">{{ $user['name'] }}</h3>
                            <p class="text-muted text-center" style="margin-bottom: 1.5rem;">{{ $user['posto'] }}</p>
                            <hr style="margin-bottom: 2rem;">
                            @else
                            @php
                            $NIM = $user['id'];
                            while ((strlen((string)$NIM))
                            < 8) { $NIM=0 . (string)$NIM; } $filename_jpg="https://cpes-wise2/Unidades/Fotos/" . $NIM . ".JPG" ; @endphp
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename_jpg) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;">
                            </div>
                            <h3 class="profile-username text-center uppercase-only">{{ $user['name'] }}</h3>
                            <p class="text-muted text-center" style="margin-bottom: 1.5rem;">{{ $user['posto'] }}</p>
                            <hr style="margin-bottom: 2rem;">
                            @endif
                            <strong><i class="fas fa-user-tag">&nbsp;</i>TIPO DE UTILIZADOR</strong>
                            <p class="text-muted">{{ $user['user_type'] }}</p>
                            <strong><i class="fas fa-stream">&nbsp;&nbsp;</i>NIVEL DE PERMISSÕES</strong>
                            <p class="text-muted">{{ $user['user_permission'] }}</p <strong><i class="fas fa-user">&nbsp;&nbsp;</i>NOME</strong>
                            <p class="text-muted uppercase-only">{{ $user['name'] }}</p>
                            <strong><i class="fas fa-id-card">&nbsp;&nbsp;</i>NIM</strong>
                            <p class="text-muted">{{ $user['id'] }}</p>
                            <strong><i class="fas fa-money-check">&nbsp;&nbsp;</i>POSTO</strong>
                            <p class="text-muted uppercase-only">{{ $user['posto'] }}</p>
                            <strong><i class="fas fa-map-marker-alt">&nbsp;&nbsp;</i>UNIDADE</strong>
                            <p class="text-muted">
                                @if ($user['unidade']) {{ $user['unidade'] }}
                                @else NÃO DEFINIDO @endif</p>
                                    <hr>
                                    <strong><i class="fas fa-phone-square-alt">&nbsp;&nbsp;</i>TELF / TELM</strong>
                                    <p class="text-muted">
                                        @if ($user['telf']) {{ $user['telf'] }}
                                        @else NÃO DEFINIDO @endif</p>
                                            <strong><i class="fa fa-envelope mr-1">&nbsp;</i>EMAIL</strong>
                                            @if ($user['email'])
                                            <a href="mailto:{{ $user['email'] }}">
                                                <p class="text-muted">{{ $user['email'] }}</p>
                                            </a>
                                            @else NÃO DEFINIDO
                                            @endif
                                            <hr>
                                            <strong><i class="fas fa-calendar-day">&nbsp;&nbsp;&nbsp;</i>DATA CRIAÇÃO</strong>
                                            <p class="text-muted">{{ $user['created_at'] }}</p>
                                            @if($user['account_verified']=='Y' || $user['account_verified']=='COMPLETE')
                                            <strong><i class="fas fa-user-check">&nbsp;&nbsp;</i>VERIFICADA</strong>
                                            <p class="text-muted">SIM</p>
                                            @if($token!=null)
                                            <strong><i class="fas fa-shield-alt"></i>&nbsp;&nbsp;METODO VERIFICAÇÃO</strong>
                                            <p class="text-muted">EXPRESS TOKEN</p>
                                            <strong><i class="fas fa-qrcode"></i>&nbsp;&nbsp;TOKEN ID</strong>
                                            <p class="text-muted">{{ $token['id'] }}</p>
                                            <strong><i class="fas fa-user-shield">&nbsp;</i>TOKEN CRIADO POR</strong>
                                            <p class="text-muted uppercase-only">{{ $token['created'] }}</p>
                                            @else
                                            <strong><i class="fas fa-shield-alt"></i>&nbsp;&nbsp;METODO VERIFICAÇÃO</strong>
                                            <p class="text-muted">MANUAL</p>
                                            <strong><i class="fas fa-user-shield">&nbsp;&nbsp;</i>VERIFICADO POR</strong>
                                            <p class="text-muted">{{ $user['verified_by'] }}</p>
                                            <strong><i class="fas fa-calendar-check">&nbsp;&nbsp;</i>DATA VERIFICAÇÃO</strong>
                                            <p class="text-muted">{{ $user['verified_at'] }}</p>
                                            @endif
                                            @else
                                            <strong><i class="fas fa-user-times">&nbsp;</i>VERIFICADA</strong>
                                            <p class="text-muted">NÃO</p>
                                            @endif
                                            <hr>
                                            <strong><i class="fa-solid fa-right-to-bracket"></i>&nbsp;&nbsp;</i>ULTÍMO LOGIN</strong>
                                            <p class="text-muted">{{ $user['last_login'] }}</p>
                                            <hr>
                                            <strong><i class="fas fa-database">&nbsp;&nbsp;</i>ULTIMA EDIÇÃO AO PERFIL</strong>
                                            <p class="text-muted">
                                                @if ($user['updated_at']) {{ $user['updated_at'] }}
                                                @else NÃO DEFINIDO @endif</p>
                                                    <strong><i class="fas fa-user-cog">&nbsp;</i>EDIÇÃO POR</strong>
                                                    <p class="text-muted">
                                                        @if ($user['updated_by']) {{ $user['updated_by'] }}
                                                        @else NÃO DEFINIDO @endif</p>
                                                            <hr>
                                                            <strong><i class="fas fa-utensils">&nbsp;&nbsp;</i>LOCAL PREFERENCIAL</strong>
                                                            <p class="text-muted">
                                                                @if ($user['localRefPref']) {{ $user['localRefPref'] }}
                                                                @else NÃO DEFINIDO @endif</p>

                                          <hr>
                                          <strong>
                                            <i class="fa-solid fa-feather-pointed">&nbsp;</i>
                                            ANIMAÇÕES ATIVADAS A ESTE UTILIZADOR
                                          </strong>
                                          <p class="text-muted">
                                            @if ($user['lite_mode']=='N')
                                              SIM
                                            @else
                                              NÃO
                                            @endif
                                        </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                @php $limit = 5; $index = 0;
                                @endphp
                                <h3 class="card-title uppercase-only">HISTÓRICO&nbsp;&nbsp;<span style="font-size: 14px">
                                        @if ($limit>=$totalEntradas)
                                        {{ $totalEntradas }} entradas
                                        @else
                                        ultimas {{ $limit }} entradas (total de {{ $totalEntradas }})
                                        @endif
                                    </span>
                                </h3>
                                <div class="card-tools">
                                    <a href="#" data-toggle="modal" data-target="#exampleModal">Ver tudo&nbsp;&nbsp;<i class="far fa-eye"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: fit-content !important;">
                            <div class="timeline">
                                @foreach ($historico as $key => $entradaHistorico)
                                @if ($index>$limit)
                                @break
                                @endif
                                @php
                                $index++;
                                @endphp
                                <div class="time-label">
                                    <span class="bg-dark">
                                        {{ date('d-m-Y', strtotime($entradaHistorico['data'])) }}
                                    </span>
                                </div>
                                <div>
                                    @if ($entradaHistorico['type']=='REF')
                                    <i class="fas fa-book bg-teal"></i>
                                    @elseif ($entradaHistorico['type']=='CREATE_NOTIFICATION')
                                    @if ($entradaHistorico['PS']=="NORMAL")
                                    <i class="fas fa-comment bg-purple"></i>
                                    @else
                                    <i class="fas fa-comment bg-orange"></i>
                                    @endif
                                    @else
                                    @if ($entradaHistorico['PS']=="NORMAL")
                                    <i class="far fa-bell bg-purple"></i>
                                    @else
                                    <i class="far fa-bell bg-orange"></i>
                                    @endif
                                    @endif
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock "></i> {{ date('H:i:s', strtotime($entradaHistorico['data'])) }}</span>
                                        <h3 class="timeline-header"><strong>{{ $entradaHistorico['title'] }}</strong></h3>
                                        <div class="timeline-body" style="white-space: pre-line; margin-top: -15px;">
                                            {{ $entradaHistorico['message'] }}
                                        </div>
                                        <div class="timeline-footer" style="padding-bottom: 20px !important;">
                                            @if ($entradaHistorico['type']=='REF')
                                            Local de refeição: <strong>{{ $entradaHistorico['PS'] }}</strong>
                                            @else
                                            Tipo de notificação: <strong> {{ $entradaHistorico['PS'] }}</strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                <div>
                                    <i class="fas fa-hourglass-end bg-navy"></i>
                                    <span class="timeline-end-label"> ... </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">Conteúdos associados a este utilizador</h3>
                <div class="card-tools" style="margin-right: 0 !important;">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h5 style="margin-bottom: 1rem;"><strong>Ementas publicadas e\ou editadas</strong></h5>
            @if (isset($ementas_publicadas) && $ementas_publicadas!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th style="width: 10%">
                            Data
                        </th>
                        <th style="width: 15%">
                            REF
                        </th>
                        <th>
                            EMENTA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ementas_publicadas as $refeiçao)
                    <tr>
                        <td rowspan="2">
                            @php
                            $mes = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                            $semana = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                            $mes_index = date('m', strtotime($refeiçao['data']));
                            $weekday_number = date('N', strtotime($refeiçao['data']));
                            @endphp
                            <strong>
                                {{ date('d', strtotime($refeiçao['data'])) }}
                                {{ $mes[($mes_index - 1)] }}<br>
                            </strong>
                            <span @if($weekday_number=="7" || $weekday_number=="6") style="font-size: .85rem;color: #92b1d1;"
                            @else style="font-size: .85rem;" @endif>{{ $semana[($weekday_number -1)] }}</span>
                        </td>
                        <td>
                            Almoço
                        </td>
                        <td>
                            Sopa: <strong>{{$refeiçao['sopa_almoço']}}</strong> <br>
                            Prato: <strong>{{$refeiçao['prato_almoço']}}</strong> <br>
                            Sobremesa: <strong>{{$refeiçao['sobremesa_almoço']}}</strong>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Jantar
                        </td>
                        <td>
                            Sopa: <strong>{{$refeiçao['sopa_jantar']}}</strong> <br>
                            Prato: <strong>{{$refeiçao['prato_jantar']}}</strong> <br>
                            Sobremesa: <strong>{{$refeiçao['sobremesa_jantar']}}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Sem publicações de ementas associadas.
            @endif

            <hr style="margin-top: 1.5rem; margin-bottom: 3rem; border: 0; border-top: 2px solid rgb(141 141 141 / 67%);">

            <h5 style="margin-bottom: 1rem;"><strong>Grupos de permissões</strong></h5>
            @if (isset($permissions) && $permissions!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            SLUG AND DESCRIPTOR
                        </th>
                        <th>
                            APPLY TO
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $entry)
                    <tr>
                        <td>
                            {{ $entry['id'] }}
                        </td>
                        <td>
                            {{ $entry['permission_slug'] }}<BR />
                            {{ $entry['permission_description'] }}

                        </td>
                        <td>
                            {{ $entry['permission_apply_to'] }}
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Sem alterações a grupos de permissões
            @endif


            <hr style="margin-top: 1.5rem; margin-bottom: 3rem; border: 0; border-top: 2px solid rgb(141 141 141 / 67%);">

            <h5 style="margin-bottom: 1rem;"><strong>Definições de plataforma</strong></h5>
            @if (isset($settings) && $settings!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            SLUG AND DESCRIPTOR
                        </th>
                        <th>
                            VALUE AND LABEL
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings as $entry)
                    <tr>
                        <td>
                            {{ $entry['id'] }}
                        </td>
                        <td>
                            {{ $entry['settingSlug'] }} <br>
                            {{ $entry['settingText'] }}
                        </td>
                        <td>
                            @if ($entry['settingToggleMode']=="INT")
                            {{ $entry['settingToggleInt'] }} {{ $entry['settingToggleIntLabel'] }}
                            @else
                            {{ $entry['settingToggleBoolean'] }} {{ $entry['settingToggleBoolLabel'] }}
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Sem alterações a definições da plataforma
            @endif

            <hr style="margin-top: 1.5rem; margin-bottom: 3rem; border: 0; border-top: 2px solid rgb(141 141 141 / 67%);">

            <h5 style="margin-bottom: 1rem;"><strong>Pedidos de refeições quantitativos</strong></h5>
            @if (isset($pedidos_quant) && $pedidos_quant!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>
                            DATA
                        </th>
                        <th>
                            REF
                        </th>
                        <th>
                            MOTIVO
                        </th>
                        <th>
                            QUANTIDADE
                        </th>
                        <th>
                            LOCAL
                        </th>
                        <th>
                            REFORÇOS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos_quant as $entry)
                    <tr>
                        <td>
                            {{ $entry['data_pedido'] }}
                        </td>
                        <td>
                            {{ $entry['meal'] }}
                        </td>
                        <td>
                            {{ $entry['motive'] }}
                        </td>
                        <td>
                            {{ $entry['quantidade'] }}
                        </td>
                        <td>
                            {{ $entry['local_ref'] }}
                        </td>
                        <td>
                            {{ $entry['qty_reforços'] }}
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Sem pedidos quantitativos
            @endif



            <hr style="margin-top: 1.5rem; margin-bottom: 3rem; border: 0; border-top: 2px solid rgb(141 141 141 / 67%);">

            <h5 style="margin-bottom: 1rem;"><strong>Avisos de plataforma</strong></h5>
            @if (isset($plat_warnings) && $plat_warnings!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            TITLE
                        </th>
                        <th style="width: 55%;">
                            MESSAGE
                        </th>
                        <th style="width: 6%;">
                            TO SHOW
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plat_warnings as $entry)
                    <tr>
                        <td>
                            {{ $entry['id'] }}
                        </td>
                        <td>
                            {{ $entry['title'] }}
                        </td>
                        <td style="padding-right: 1rem !important;">
                            {{ $entry['message'] }}
                        </td>
                        <td>
                            {{ $entry['to_show'] }}
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Nenhum aviso criado.
            @endif



            <hr style="margin-top: 1.5rem; margin-bottom: 3rem; border: 0; border-top: 2px solid rgb(141 141 141 / 67%);">

            <h5 style="margin-bottom: 1rem;"><strong>Posts de grupo</strong></h5>
            @if (isset($posts) && $posts!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            TITLE
                        </th>
                        <th style="padding-right: 1rem !important;">
                            MESSAGE
                        </th>
                        <th>
                            IN GROUP
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $entry)
                    <tr>
                        <td>
                            {{ $entry['id'] }}
                        </td>
                        <td>
                            {{ $entry['title'] }}
                        </td>
                        <td>
                            {{ $entry['message'] }}
                        </td>
                        <td>
                            {{ $entry['posted_group'] }}
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Sem posts criados
            @endif



            <hr style="margin-top: 1.5rem; margin-bottom: 3rem; border: 0; border-top: 2px solid rgb(141 141 141 / 67%);">

            <h5 style="margin-bottom: 1rem;"><strong>Edições a perfis de utilizadores</strong></h5>
            @if (isset($usrs) && $usrs!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>
                            NIM
                        </th>
                        <th>
                            POSTO E NOME
                        </th>
                        <th>
                            USER TYPE E PERMISSIONS
                        </th>
                        <th>
                            EDITED AT
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usrs as $entry)
                    <tr>
                        <td>
                            {{ $entry['id'] }}
                        </td>
                        <td>
                            {{ $entry['posto'] }} <br> {{ $entry['name'] }}
                        </td>
                        <td>
                            TYPE: <strong>{{ $entry['user_type'] }}</strong> <br> PERM: <strong>{{ $entry['user_permission'] }}</strong>
                        </td>
                        <td>
                            {{ $entry['updated_at'] }}
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Sem alterações a utilizadores
            @endif


            <hr style="margin-top: 1.5rem; margin-bottom: 3rem; border: 0; border-top: 2px solid rgb(141 141 141 / 67%);">

            <h5 style="margin-bottom: 1rem;"><strong>Registos de refeições a numerário</strong></h5>
            @if (isset($tag_oblig) && $tag_oblig!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            PARA NIM
                        </th>
                        <th>
                            DATA INICIO
                        </th>
                        <th>
                            DATA FIM
                        </th>
                        <th>
                            REGISTADO A
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tag_oblig as $entry)
                    <tr>
                        <td>
                            {{ $entry['id'] }}
                        </td>
                        <td>
                            {{ $entry['registered_to'] }}
                        </td>
                        <td>
                            {{ $entry['data_inicio'] }}
                        </td>
                        <td>
                            {{ $entry['data_fim'] }}
                        </td>
                        <td>
                            {{ $entry['created_at'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Sem registos efetuados
            @endif

            <hr style="margin-top: 1.5rem; margin-bottom: 3rem; border: 0; border-top: 2px solid rgb(141 141 141 / 67%);">

            <h5 style="margin-bottom: 1rem;"><strong>Registo de ausências de utilizadores</strong></h5>
            @if (isset($dilis) && $dilis!=null)

            <table class="table projects" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            PARA NIM
                        </th>
                        <th>
                            DATA INICIO
                        </th>
                        <th>
                            DATA FIM
                        </th>
                        <th>
                            REGISTADO A
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dilis as $entry)
                    <tr>
                        <td>
                            {{ $entry['id'] }}
                        </td>
                        <td>
                            {{ $entry['to_user'] }}
                        </td>
                        <td>
                            {{ $entry['data_inicio'] }}
                        </td>
                        <td>
                            {{ $entry['data_fim'] }}
                        </td>
                        <td>
                            {{ $entry['created_at'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            Sem registos efetuados
            @endif

        </div>
    </div>


    <div class="card">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Marcações do utilizador</h3>
            <div class="card-tools" style="margin-right: 0 !important;">
               <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
         </div>
      </div>
       <div class="card-body">
         @if (isset($marcadas) && !empty($marcadas))
         <table class="table table-striped projects">
            <thead>
               <tr>
                  <th style="width: 10%">
                    Data
                  </th>
                  <th style="width: 20%">
                     Refeição
                  </th>
                  <th style="width: 20%">
                     Dieta
                  </th>
                  <th>
                     Local
                  </th>
                  <th>
                    Criada por
                  </th>
                  <th>
                    Criada a
                  </th>
               </tr>
            </thead>
            <tbody>
               @php
                 $today = date("Y-m-d");
                 $skip=0;
               @endphp
               @foreach($marcadas as $key => $marcaçao)
               @php

                 $next_key_0 =$key+2;
                 $next_key_1 =$key+1;
                 $next_key_0_exits = array_key_exists($next_key_0, $marcadas);
                 $next_key_1_exits = array_key_exists($next_key_1, $marcadas);
                 $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                 $semana  = array("Segunda-Feira","Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                 $mes_index = date('m', strtotime($marcaçao['data_marcacao']));
                 $weekday_number = date('N',  strtotime($marcaçao['data_marcacao']));
               @endphp
               <tr>

                 @if ($skip==0)
                   <td class="project_progress"
                     @if ($next_key_0_exits && ($marcadas[$next_key_0]['data_marcacao']==$marcaçao['data_marcacao']))
                       rowspan="3" @php $skip = 3 @endphp
                     @elseif ($next_key_1_exits && ($marcadas[$next_key_1]['data_marcacao']==$marcaçao['data_marcacao']))
                       rowspan="2" @php $skip = 2 @endphp
                     @endif
                   style="width: 10%;">
                      <p class="p_noMargin">
                         <strong>
                         {{ date('d', strtotime($marcaçao['data_marcacao'])) }}
                         {{ $mes[($mes_index - 1)] }}  {{ date('Y', strtotime($marcaçao['data_marcacao'])) }}
                         </strong><br>
                         <span style="font-size: .85rem;">{{ $semana[($weekday_number -1)] }}</span>
                      </p>
                   </td>
                 @endif

                 @if ($skip!=0)
                   @php
                     $skip--;
                   @endphp
                 @endif

                  <td>
                     <p class="p_noMargin">
                        @if($marcaçao['meal']=='1REF' )
                          Pequeno-almoço
                        @elseif($marcaçao['meal']=='3REF' )
                          Jantar
                        @else
                          Almoço
                        @endif
                     </p>
                  </td>
                  <td>
                    @if ($marcaçao['dieta']=='N')
                      NÃO
                    @else
                      SIM
                    @endif
                  </td>
                  <td>
                    {{ $marcaçao['local_ref'] }}
                  </td>
                  <td>
                    @if (str_contains($marcaçao['created_by'], 'POC@'))
                      <B>POC</B> NIM
                      {{ str_replace('POC@', '', $marcaçao['created_by']) }}
                    @else
                      NIM {{ $marcaçao['created_by'] }}
                    @endif
                  </td>
                  <td>
                    {{ $marcaçao['created_at'] }}
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
         @else
            <h5>Este utilizador não tem nenhuma marcação.</h5>
         @endif

       </div>
    </div>


    <div class="card">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Entradas de quiosque do utilizador</h3>
            <div class="card-tools" style="margin-right: 0 !important;">
               <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
         </div>
      </div>
       <div class="card-body">

         @if (isset($quiosque_entries) && $quiosque_entries!=null)

           <table class="table table-striped projects">
              <thead>
                 <tr>
                    <th style="width: 10%">
                      Data
                    </th>
                    <th style="width: 20%">
                       Refeição
                    </th>
                    <th>
                       REFEIÇÃO MARCADA
                    </th>
                    <th>
                       Local
                    </th>
                    <th>
                      Criada a
                    </th>
                    <th>
                       REGISTADO POR QUIOSQUE IP
                    </th>
                 </tr>
              </thead>
              <tbody>
                 @php
                   $today = date("Y-m-d");
                   $skip=0;
                 @endphp
                 @foreach($quiosque_entries as $key => $marcaçao)
                 @php

                   $next_key_0 =$key+2;
                   $next_key_1 =$key+1;
                   $next_key_0_exits = array_key_exists($next_key_0, $marcadas);
                   $next_key_1_exits = array_key_exists($next_key_1, $marcadas);
                   $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                   $semana  = array("Segunda-Feira","Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                   $mes_index = date('m', strtotime($marcaçao['REGISTADO_DATE']));
                   $weekday_number = date('N',  strtotime($marcaçao['REGISTADO_DATE']));
                 @endphp
                 <tr>

                   @if ($skip==0)
                     <td class="project_progress"
                       @if ($next_key_0_exits && ($marcadas[$next_key_0]['REGISTADO_DATE']==$marcaçao['REGISTADO_DATE']))
                         rowspan="3" @php $skip = 3 @endphp
                       @elseif ($next_key_1_exits && ($marcadas[$next_key_1]['REGISTADO_DATE']==$marcaçao['REGISTADO_DATE']))
                         rowspan="2" @php $skip = 2 @endphp
                       @endif
                     style="width: 10%;">
                        <p class="p_noMargin">
                           <strong>
                           {{ date('d', strtotime($marcaçao['REGISTADO_DATE'])) }}
                           {{ $mes[($mes_index - 1)] }}
                           {{ date('Y', strtotime($marcaçao['REGISTADO_DATE'])) }}
                           </strong><br>
                           <span style="font-size: .85rem;">{{ $semana[($weekday_number -1)] }}</span>
                        </p>
                     </td>
                   @endif

                   @if ($skip!=0)
                     @php
                       $skip--;
                     @endphp
                   @endif

                    <td>
                       <p class="p_noMargin">
                          @if($marcaçao['REF']=='1REF' )
                            Pequeno-almoço
                          @elseif($marcaçao['REF']=='3REF' )
                            Jantar
                          @else
                            Almoço
                          @endif
                       </p>
                    </td>
                    <td>
                      @if ($marcaçao['MARCADA']=="true")
                        SIM
                      @else
                        NÃO
                      @endif
                    </td>
                    <td>
                      {{ $marcaçao['LOCAL'] }}
                    </td>
                    <td>
                      {{ $marcaçao['created_at'] }}
                    </td>
                    <td>
                      {{ $marcaçao['QUIOSQUE_IP'] }}
                    </td>
                 </tr>
                 @endforeach
              </tbody>
           </table>


        @else
            Sem alterações a grupos de permissões
        @endif

       </div>
    </div>


</div>
@endsection
