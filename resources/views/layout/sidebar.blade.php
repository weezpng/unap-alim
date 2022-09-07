@php
if(Auth::check() && Auth::user()->dark_mode=='Y'){
$_dark_mode = true;
} elseif (Session::has('dark_mode_inauth') && Session::get('dark_mode_inauth')=='on') {
$_dark_mode = true;
} else {
$_dark_mode = false;
}
$asideclass = "main-sidebar nav-child-indent elevation-4 ";
if ($_dark_mode == true) $asideclass = $asideclass."sidebar-dark-primary ";
else $asideclass = $asideclass."sidebar-light-primary ";
if (Auth::check() && Auth::user()->compact_mode=='Y') $asideclass."nav-compact text-xs";

$sideclass= "sidebar ";
if (Auth::check() && Auth::user()->flat_mode=='Y') $sideclass."nav-flat";
@endphp

<aside class="{{ $asideclass }}"id="sidebar" style="min-width: 5rem;">
<!-- Sidebar -->
<div class="{{ $sideclass }}" style="overflow: hidden;">
@if(Auth::check())
@php
$_is_admin = (auth()->user()->user_type=='ADMIN');
$_is_super =(auth()->user()->user_type=='POC');
$_is_user = (auth()->user()->user_type=='USER');
$__is_perm_general = (auth()->user()->user_permission=='GENERAL');
$__is_perm_ALIM = (auth()->user()->user_permission=='ALIM');
$__is_perm_PESS = (auth()->user()->user_permission=='PESS');
$__is_perm_LOG = (auth()->user()->user_permission=='LOG');
$__is_perm_MESSES = (auth()->user()->user_permission=='MESSES');
$__is_perm_GCSEL = (auth()->user()->user_permission=='GCSEL');
$__is_perm_TUDO = (auth()->user()->user_permission=='TUDO');
@endphp
<!-- Sidebar user panel (optional) -->

<div class="user-panel mt-3 pb-3 mb-3 d-flex" style="padding-bottom: 1rem !important;">
    <div class="image" style="padding-left: 0.5rem !important;">
        @php
        $NIM = Auth::user()->id;
        while ((strlen((string)$NIM)) < 8) { $NIM=0 . (string)$NIM; } $filename="assets/profiles/" .$NIM . ".JPG" ; $filename_png="assets/profiles/" .$NIM . ".PNG" ; @endphp

        @if (file_exists(public_path($filename)))
        <img src="{{ asset($filename) }}" class="img-circle elevation-2" alt="User Image" style="border: 2px solid #6c757d !important; padding: 3px; background: transparent;">
        @elseif(file_exists(public_path($filename_png)))
        <img src="{{ asset($filename_png) }}" class="img-circle elevation-2" alt="User Image" style="border: 2px solid #6c757d !important; padding: 3px; background: transparent;">
        @else
        @php
        $NIM = Auth::user()->id;
        while ((strlen((string)$NIM))
        < 8) { $NIM=0 . (string)$NIM; } $filename2="https://cpes-wise2/Unidades/Fotos/" . $NIM . ".JPG" ; @endphp
        <img src="{{ asset($filename2) }}" class="img-circle elevation-2" alt="User Image" style="border: 2px solid #6c757d !important; padding: 3px; background: transparent;">
        @endif
    </div>
    <div class="info" style="max-width: 6rem;padding-left: .5rem !important;margin-top: 0.5rem;white-space: normal;">
        <p href="#" class="d-block" style="text-align: left; font-size: 1.15rem; margin-top: -0.05rem;"><b>{{ Auth::user()->name }}</b>


        @if (Auth::user()->posto != "ASS.TEC." && Auth::user()->posto != "ASS.OP." && Auth::user()->posto != "TEC.SUP."
         && Auth::user()->posto != "ENC.OP." && Auth::user()->posto != "TIA" && Auth::user()->posto != "TIG.1" && Auth::user()->posto != "TIE" && Auth::user()->posto != "SOLDADO")
          @if (Auth::check() && Auth::user()->dark_mode=='Y')
            @php $filename2 = "assets/icons/POSTOs/TRANSPARENT_WHITE/" . Auth::user()->posto . ".png"; @endphp
          @else
             @php $filename2 = "assets/icons/POSTOs/TRANSPARENT/" . Auth::user()->posto . ".png"; @endphp
          @endif
          <img style="width: 2.5rem; height: 1.5rem; object-fit: scale-down; display: inline-block; background: transparent; margin-top: 0.5rem; margin-left: -0.15rem;" src="{{ asset($filename2) }}">
        @else
          <h6 style="margin-top: 0.5rem; margin-bottom: 0; font-size: 0.8rem; text-align: left; padding-left: .2rem !important;">{{ Auth::user()->posto }}</h6>
        @endif

        </p>
    </div>
</div>

@else
<div class="user-panel mt-3 pb-3 mb-3" style="margin-top: 1.5rem !important;">
    <div class="info">
        <h6 class="d-block" style="width: 100%; margin-top: .5rem; margin-left: 5%; padding-left: .8rem;">
            <a href="{{ route('login') }}">Iniciar sessão</a>
        </h6>
    </div>
</div>

@endif

<nav class="mt-2 ">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

      <li class="nav-item">
          <a href="{{ route('index') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
              <i class="nav-icon fa-solid fa-house"></i>
              <p>
                  Página inicial
              </p>
          </a>
      </li>

      <li class="nav-item">
          <a href="{{ route('ementa.index') }}" class="nav-link {{ request()->is('ementa') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>
                  Ementa
              </p>
          </a>
      </li>
        @if(Auth::check())
        @if (Auth::user()->lock=='N')
        <li class="nav-item has-treeview {{ request()->is('marcacao*') ? 'menu-is-opening menu-open' : '' }}">
            <a href="" class="nav-link {{ request()->is('marcacao*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-book"></i>
                <p>
                    Marcações
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @if (Auth::user()->isTagOblig==null)
                  <li class="nav-item">
                      <a href="{{ route('marcacao.index') }}" class="nav-link {{ request()->is('marcacao') ? 'active' : '' }}">
                          @if (Auth::user()->use_icons=='Y')
                          <i class="fas fa-calendar-plus nav-icon"></i>
                          @else
                          <i class="fas fa-angle-right nav-icon"></i>
                          @endif
                          <p>Marcar refeição</p>
                      </a>
                  </li>
                @else
                  <li class="nav-item">
                      <a href="#"class="nav-link disabled {{ request()->is('marcacao*') ? 'active' : '' }}">
                          @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-calendar-plus nav-icon"></i>
                          @else
                          <i class="fas fa-angle-right nav-icon"></i>
                          @endif
                          <p style="line-height: 1rem;">Marcar refeição</p>
                          <span class="badge badge-danger right">
                              <i class="fa-solid fa-lock" style="margin: 0; font-size: .7rem;"></i>
                          </span>
                      </a>
                  </li>
                @endif

                @if (Auth::user()->isTagOblig==null)
                <li class="nav-item">
                    <a href="{{ route('marcacao.minhas') }}" class="nav-link {{ request()->is('marcacao/minhas_marcacoes*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fas fa-calendar-check nav-icon"></i>
                        @else
                        <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p>Minhas marcaçoes</p>
                        @if(isset($howManyMarcacoes) && $howManyMarcacoes > 0)
                        <span class="badge badge-secondary right">
                            {{ $howManyMarcacoes }}
                        </span>
                        @endif
                    </a>
                </li>
                @else
                <li class="nav-item">
                    <a href="#" class="nav-link disabled {{ request()->is('marcacao/minhas_marcaçoes*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                          <i class="fas fa-calendar-check nav-icon"></i>
                        @else
                          <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p style="line-height: 1rem;">Minhas marcaçoes</p>
                        <span class="badge badge-danger right">
                            <i class="fa-solid fa-lock" style="margin: 0; font-size: .7rem;"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if (Auth::user()->posto=="ASS.TEC." || Auth::user()->posto=="ASS.OP." || Auth::user()->posto=="TEC.SUP."
                || auth()->user()->posto == "ENC.OP." || auth()->user()->posto == "TIA" || auth()->user()->posto == "TIG.1" || auth()->user()->posto == "TIE")
                <li class="nav-item">
                    <a href="{{ route('confirmacoes.index') }}" class="nav-link {{ request()->is('marcacao/confirmaçoes*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fas fa-angle-right nav-icon"></i>
                        @else
                        <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p>Confirmação de refeições</p>
                    </a>
                </li>
                @endif

                @if(auth()->user()->user_type=="POC" || auth()->user()->user_type=="ADMIN")
                    <li class="nav-item">
                        <a href="{{ route('marcacao.forgroup_select') }}" class="nav-link {{ request()->is('marcacao/group*') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-people-arrows nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Marcação de grupos</p>
                        </a>
                    </li>
                    @endif

                    @if ($MEALS_TO_EXTERNAL)
                    <li class="nav-item">
                        <a href="{{ route('marcacao.non_nominal') }}" class="nav-link {{ request()->is('marcacao/quantitativas*') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-calendar-alt nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Marcações quantitativas</p>
                        </a>
                    </li>
                    @endif

                    <hr style="margin-bottom: .1rem;border: 0;border-top: 2px solid rgb(190 197 209 / 0%);">
            </ul>
        </li>

        @if (Auth::user()->user_type=="POC")
        <li class="nav-item">
            <a href="{{ route('poc.index') }}" class="nav-link {{ request()->is('poc-control-center*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tags"></i>
                <p>
                    Centro POC
                </p>
                @if(isset($POC_Users))
                <span class="badge badge-secondary right">
                    {{ $POC_Users }}
                </span>
                @endif
            </a>
        </li>

        <li class="nav-item" style="display: none;">
            <a href="{{ route('poc.ferias.index') }}" class="nav-link {{ request()->is('ferias*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-people-arrows"></i>
                <p>
                    Férias de utilizadores
                </p>
            </a>
        </li>

        @endif

        @if(($_is_admin) || ($_is_super) || ($_is_user && (!$__is_perm_general && !$__is_perm_GCSEL)))
        <li class="nav-item has-treeview {{ request()->is('gestão*') ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('gestão*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-toolbox"></i>
                <p>
                    Gestão
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                @if ((Auth::user()->user_type!="USER") || ($_is_user && $__is_perm_PESS))
                <li class="nav-item has-treeview {{ request()->is('gestão/utilizadores*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('gestão/utilizadores*') ? 'active' : '' }} {{ request()->is('gestão/novos_utilizadores') ? 'active' : '' }} {{ request()->is('gestão/users/férias*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fas fa-user-friends nav-icon"></i>
                        @else
                        <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p> Utilizadores
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="display: {{ request()->is('gestão/utilizadores*') ? 'block' : 'none' }} !important;">

                        @if($_is_admin || $_is_super)
                        <li class="nav-item">
                            <a href="{{route('gestão.associatedUsersAdmin')}}" class="nav-link {{ request()->is('gestão/utilizadores/assoc*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-user-shield nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Utilizadores associados</p>
                                    @if(isset($assoc_user) && $assoc_user > 0)
                                    <span class="badge badge-secondary right">
                                        {{ $assoc_user }}
                                    </span>
                                    @endif
                            </a>
                        </li>
                        @endif

                        @if ($VIEW_ALL_MEMBERS==true)
                        <li class="nav-item">
                            <a href="{{route('gestão.usersAdmin')}}" class="nav-link {{ request()->is('gestão/utilizadores') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                    <i class="fas fa-users nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Todos utilizadores</p>
                                    @if(isset($howManyUsers))
                                    <span class="badge badge-secondary right">
                                        {{ $howManyUsers }}
                                    </span>
                                    @endif
                            </a>
                        </li>
                        @endif

                        @if ($ACCEPT_NEW_MEMBERS==true || $CONFIRM_UNIT_CHANGE==true)
                        <li class="nav-item">
                            <a href="{{route('gestão.newUsersAdmin')}}" class="nav-link {{ request()->is('gestão/novos_utilizadores') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-user-plus  nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Novos utilizadores</p>
                                    @if(isset($howManyNewUsers))
                                    <span class="badge badge-secondary right">
                                        {{ $howManyNewUsers }}
                                    </span>
                                    @endif
                            </a>
                        </li>
                        @endif

                        @if ($SCHEDULE_USER_VACATIONS==true)
                        <li class="nav-item">
                            <a href="{{route('gestao.ferias.index')}}" class="nav-link {{ request()->is('gestão/users/férias*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-user-minus  nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Ausências</p>
                                    @if(isset($users_ferias))
                                    <span class="badge badge-secondary right">
                                        {{ $users_ferias }}
                                    </span>
                                    @endif
                            </a>
                        </li>
                        @endif

                        @if ($TAG_USER_DIETAS==true)
                        <li class="nav-item">
                            <a href="{{route('gestao.dieta.index')}}" class="nav-link {{ request()->is('gestão/users/dietas*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-diagnoses nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Dietas</p>
                                    @if(isset($users_dieta))
                                    <span class="badge badge-secondary right">
                                        {{ $users_dieta }}
                                    </span>
                                    @endif
                            </a>
                        </li>
                        @endif
                        <hr style="margin-bottom: .5rem;border: 0;border-top: 2px solid rgb(190 197 209 / 0%);">
                    </ul>
                </li>
                @endif

                @if($CHANGE_LOCAIS_REF==true || $EDIT_DEADLINES_TAG==true || $EDIT_DEADLINES_UNTAG==true || $GENERAL_WARNING_CREATION==true)
                <li class="nav-item has-treeview
                      {{ request()->is('gestão/locais*') ? 'menu-is-opening menu-open' : '' }}
                      {{ request()->is('gestão/unidades*') ? 'menu-is-opening menu-open' : '' }}
                      {{ request()->is('gestão/definições*') ? 'menu-is-opening menu-open' : '' }}
                      {{ request()->is('gestão/avisos*') ? 'menu-is-opening menu-open' : '' }}
                ">
                    <a href="#" class="nav-link
                        {{ request()->is('gestão/locais*') ? 'active' : '' }}
                        {{ request()->is('gestão/unidades*') ? 'active' : '' }}
                        {{ request()->is('gestão/definições*') ? 'active' : '' }}
                        {{ request()->is('gestão/avisos*') ? 'active' : '' }}
                    ">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fas fa-server nav-icon"></i>
                        @else
                        <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p> Plataforma
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="display:
                        {{ request()->is('gestão/locais*') ? 'block' : 'none' }}
                        {{ request()->is('gestão/unidades*') ? 'block' : 'none' }}
                        {{ request()->is('gestão/definições*') ? 'block' : 'none' }}
                        {{ request()->is('gestão/avisos*') ? 'block' : 'none' }}
                    !important;">

                        @if($CHANGE_LOCAIS_REF==true)
                        <li class="nav-item">
                            <a href="{{route('gestão.locais.index')}}" class="nav-link {{ request()->is('gestão/locais*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-map-marked-alt nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Locais de refeição</p>
                                    @if((isset($locals_on_count) && $locals_on_count > 0) || (isset($locals_off_count) && $locals_off_count > 0))
                                    <span class="badge badge-info right" style="margin-right: 1rem;">
                                        {{ $locals_on_count }}
                                    </span>
                                    <span class="badge badge-danger right">
                                        {{ $locals_off_count }}
                                    </span>
                                    @endif
                            </a>
                        </li>
                        @endif

                        @if($CHANGE_UNIDADES_MAN==true)
                        <li class="nav-item">
                            <a href="{{route('gestão.unidades.index')}}" class="nav-link {{ request()->is('gestão/unidades*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-landmark nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Unidades</p>
                                    @if(isset($units_count) && $units_count > 0)
                                    <span class="badge badge-secondary right">
                                        {{ $units_count }}
                                    </span>
                                    @endif
                            </a>
                        </li>
                        @endif

                        @if($CHANGE_MEAL_TIMES==true)
                        <li class="nav-item">
                            <a href="#" data-toggle="modal" data-target="#mealTime" class="nav-link">
                                @if (Auth::user()->use_icons=='Y')
                                  <i class="fa-solid fa-clock nav-icon"></i>
                                @else
                                  <i class="fas fa-angle-double-right nav-icon"></i>
                                @endif
                                <p>Hórarios de refeição</p>
                            </a>
                        </li>
                        @endif

                        @if ($EDIT_DEADLINES_TAG==true || $EDIT_DEADLINES_UNTAG==true)
                        <li class="nav-item">
                            <a href="{{route('gestao.settings')}}" class="nav-link {{ request()->is('gestão/definições*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-cogs  nav-icon"></i>
                                @else
                                <i class="fas fa-angle-double-right nav-icon"></i>
                                @endif
                                <p>Definições</p>
                            </a>
                        </li>
                        @endif

                        @if($GENERAL_WARNING_CREATION==true)
                        <li class="nav-item">
                            <a href="{{ route('gestão.warnings.index') }}" class="nav-link {{ request()->is('gestão/avisos*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-envelope nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Avisos de plataforma</p>
                                    @if(isset($warnings_count) && $warnings_count > 0)
                                    <span class="badge badge-secondary right">
                                        {{ $warnings_count }}
                                    </span>
                                    @endif
                            </a>
                        </li>
                        @endif

                        <hr style="margin-bottom: .5rem;border: 0;border-top: 2px solid rgb(190 197 209 / 0%);">
                    </ul>
                </li>

                @endif

                @if (Auth::user()->user_permission=="MESSES")
                <li class="nav-item">
                    <a href="{{route('gestao.hospedes')}}" class="nav-link {{ request()->is('gestão/hóspedes*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fa-solid fa-id-card-clip nav-icon"></i>
                        @else
                        <i class="fas fa-angle-double-right nav-icon"></i>
                        @endif
                        <p>Hóspedes</p>
                    </a>
                </li>
                @endif

                @if ($VIEW_GENERAL_STATS==true)

                <li class="nav-item has-treeview {{ request()->is('gestão/estatisticas*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('gestão/estatisticas*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                          <i class="fa-solid fa-square-poll-vertical nav-icon"></i>
                        @else
                          <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p> Estatísticas
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="display: {{ request()->is('gestão/estatisticas*') ? 'block' : 'none' }} !important;">

                        <li class="nav-item">
                            <a href="{{route('gestão.statsAdmin')}}" class="nav-link {{ request()->is('gestão/estatisticas') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                    <i class="far fa-chart-bar nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    @endif
                                    <p>Dados de marcação</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('gestão.statsAdminDay')}}" class="nav-link {{ request()->is('gestão/estatisticas/day') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-chart-area nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    @endif
                                    <p>Dados por dia/refeição</p>
                            </a>
                        </li>

                        <li class="nav-item">
                          <a href="{{ route('gestão.estatisticas.allexports') }}" class="nav-link {{ request()->is('gestão/estatisticas/exportacoes*') ? 'active' : '' }}">
                              @if (Auth::user()->use_icons=='Y')
                                <i class="fa-solid  fa-diagram-next nav-icon"></i>
                              @else
                                <i class="fas fa-angle-right nav-icon"></i>
                              @endif
                              <p> Extrair relatórios
                              </p>
                          </a>
                        </li>

                        <hr style="margin-bottom: .5rem;border: 0;border-top: 2px solid rgb(190 197 209 / 0%);">

                      </ul>
                    </li>

                @endif


                @if ($ADD_EMENTA==true || $EDIT_EMENTA==true)
                <li class="nav-item">
                    <a href="{{ route('gestao.ementa.index') }}" class="nav-link {{ request()->is('gestão/ementa*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fas fa-calendar-alt nav-icon"></i>
                        @else
                        <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p>Ementa</p>
                    </a>
                </li>
                @endif


                @if($VIEW_DATA_QUIOSQUE==true)
                <li class="nav-item">
                    <a href="{{route('gestão.quiosqueAdmin')}}" class="nav-link {{ request()->is('gestão/quiosque*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fas fa-desktop nav-icon"></i>
                        @else
                        <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p>Entradas quiosque</p>
                    </a>
                </li>
                @endif

                @if (Auth::user()->user_permission!='GENERAL')

                  <li class="nav-item has-treeview {{ request()->is('gestão/equipa*') ? 'menu-is-opening menu-open' : '' }}">
                      <a href="#" class="nav-link {{ request()->is('gestão/equipa*') ? 'active' : '' }}">
                          @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-address-book nav-icon"></i>
                          @else
                            <i class="fas fa-angle-right nav-icon"></i>
                          @endif
                          <p> Minha equipa
                              <i class="right fas fa-angle-left"></i>
                          </p>
                      </a>

                      <ul class="nav nav-treeview" style="display: {{ request()->is('gestão/equipa*') ? 'block' : 'none' }} !important;">

                        <li class="nav-item">
                            <a href="{{route('gestão.equipa.index')}}" class="nav-link {{ request()->is('gestão/equipa') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                  <i class="fa-solid fa-user-tie nav-icon"></i>
                                @else
                                <i class="fas fa-angle-right nav-icon"></i>
                                @endif
                                <p>Membros</p>
                                @if(isset($team_members) && $team_members > 0)
                                <span class="badge badge-secondary right">
                                    {{ $team_members }}
                                </span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('gestão.equipa.posts')}}" class="nav-link {{ request()->is('gestão/equipa/posts') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                  <i class="fa-solid fa-folder-open nav-icon"></i>
                                @else
                                <i class="fas fa-angle-right nav-icon"></i>
                                @endif
                                <p>Publicações</p>
                                @if(isset($team_posts) && $team_posts > 0)
                                <span class="badge badge-secondary right">
                                    {{ $team_posts }}
                                </span>
                                @endif
                            </a>
                        </li>

                        <hr style="margin-bottom: .5rem;border: 0;border-top: 2px solid rgb(190 197 209 / 0%);">

                        </ul>
                      </li>

                      <hr style="margin-bottom: .1rem;border: 0;border-top: 2px solid rgb(190 197 209 / 0%);">


                @endif


            </ul>
        </li>
        </li>
        @endif
        @if(auth()->user()->user_type=="HELPDESK")
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link {{ request()->is('helpdesk*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-cogs"></i>
                    <p>
                        Helpdesk
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">

                    <li class="nav-item">
                        <a href="{{ route('helpdesk.permissões.index') }}" class="nav-link {{ request()->is('helpdesk/permissões*') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-desktop nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Permissões</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('helpdesk.consultas.index') }}" class="nav-link  {{ request()->is('helpdesk/consultas*') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-desktop nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Consultas</p>
                        </a>
                    </li>
                    <li class="nav-item" style="display: none;">
                        <a href="#" class="nav-link disabled disabled-navbar-item">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-desktop nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Tickets</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('helpdesk.settings.index') }}" class="nav-link {{ request()->is('helpdesk/definições*') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-desktop nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Definições</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('helpdesk.warnings.index') }}" class="nav-link {{ request()->is('helpdesk/avisos*') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-desktop nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Avisos de plataforma</p>
                        </a>
                    </li>

                </ul>
            </li>

            @else

            <li class="nav-item">
                <a href="{{route('help.faq')}}" class="nav-link {{ request()->is('FAQ*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-question-circle"></i>
                    <p>
                        FAQ
                    </p>
                </a>
            </li>


            @endif

            @endif

            @if (Auth::check() && Auth::user()->lock=='N')

            <li class="nav-item has-treeview {{ request()->is('perfil*') ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->is('perfil*') ? 'active' : '' }} ">
                    <i class="nav-icon fas fa-user-circle"></i>
                    <p>
                        Meu perfil
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{route('profile.index')}}" class="nav-link {{ request()->is('perfil') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-user-cog nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Perfil e definições</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{route('perfil.quiosque')}}" class="nav-link {{ request()->is('perfil/quiosque') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-chalkboard-teacher nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Minhas entradas</p>
                            @if(isset($myEntranceQu))
                            <span class="badge badge-secondary right">
                                {{ $myEntranceQu }}
                            </span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-sign-out-alt nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Terminar sessão</p>
                        </a>
                    </li>
                    <hr style="margin-bottom: .5rem; margin-top: 0 !important; border: 0; border-top: 1px solid rgb(1 1 1 / 0%);">
                </ul>
            </li>
            @endif
            <hr style="margin-bottom: .5rem; margin-top: 0 !important; border: 0; border-top: 1px solid rgb(1 1 1 / 0%);">
    </ul>
  @endif
</nav>
</div>
</aside>
