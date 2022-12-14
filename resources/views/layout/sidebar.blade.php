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
            <a href="{{ route('login') }}">Iniciar sess??o</a>
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
                  P??gina inicial
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
                    Marca????es
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
                          <p>Marcar refei????o</p>
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
                          <p style="line-height: 1rem;">Marcar refei????o</p>
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
                        <p>Minhas marca??oes</p>
                        @if(isset($howManyMarcacoes) && $howManyMarcacoes > 0)
                        <span class="badge badge-secondary right">
                            {{ $howManyMarcacoes }}
                        </span>
                        @endif
                    </a>
                </li>
                @else
                <li class="nav-item">
                    <a href="#" class="nav-link disabled {{ request()->is('marcacao/minhas_marca??oes*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                          <i class="fas fa-calendar-check nav-icon"></i>
                        @else
                          <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p style="line-height: 1rem;">Minhas marca??oes</p>
                        <span class="badge badge-danger right">
                            <i class="fa-solid fa-lock" style="margin: 0; font-size: .7rem;"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if (Auth::user()->posto=="ASS.TEC." || Auth::user()->posto=="ASS.OP." || Auth::user()->posto=="TEC.SUP."
                || auth()->user()->posto == "ENC.OP." || auth()->user()->posto == "TIA" || auth()->user()->posto == "TIG.1" || auth()->user()->posto == "TIE")
                <li class="nav-item">
                    <a href="{{ route('confirmacoes.index') }}" class="nav-link {{ request()->is('marcacao/confirma??oes*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fas fa-angle-right nav-icon"></i>
                        @else
                        <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p>Confirma????o de refei????es</p>
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
                            <p>Marca????o de grupos</p>
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
                            <p>Marca????es quantitativas</p>
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
                    F??rias de utilizadores
                </p>
            </a>
        </li>

        @endif

        @if(($_is_admin) || ($_is_super) || ($_is_user && (!$__is_perm_general && !$__is_perm_GCSEL)))
        <li class="nav-item has-treeview {{ request()->is('gest??o*') ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('gest??o*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-toolbox"></i>
                <p>
                    Gest??o
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                @if ((Auth::user()->user_type!="USER") || ($_is_user && $__is_perm_PESS))
                <li class="nav-item has-treeview {{ request()->is('gest??o/utilizadores*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('gest??o/utilizadores*') ? 'active' : '' }} {{ request()->is('gest??o/novos_utilizadores') ? 'active' : '' }} {{ request()->is('gest??o/users/f??rias*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fas fa-user-friends nav-icon"></i>
                        @else
                        <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p> Utilizadores
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="display: {{ request()->is('gest??o/utilizadores*') ? 'block' : 'none' }} !important;">

                        @if($_is_admin || $_is_super)
                        <li class="nav-item">
                            <a href="{{route('gest??o.associatedUsersAdmin')}}" class="nav-link {{ request()->is('gest??o/utilizadores/assoc*') ? 'active' : '' }}">
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
                            <a href="{{route('gest??o.usersAdmin')}}" class="nav-link {{ request()->is('gest??o/utilizadores') ? 'active' : '' }}">
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
                            <a href="{{route('gest??o.newUsersAdmin')}}" class="nav-link {{ request()->is('gest??o/novos_utilizadores') ? 'active' : '' }}">
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
                            <a href="{{route('gestao.ferias.index')}}" class="nav-link {{ request()->is('gest??o/users/f??rias*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-user-minus  nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Aus??ncias</p>
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
                            <a href="{{route('gestao.dieta.index')}}" class="nav-link {{ request()->is('gest??o/users/dietas*') ? 'active' : '' }}">
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
                      {{ request()->is('gest??o/locais*') ? 'menu-is-opening menu-open' : '' }}
                      {{ request()->is('gest??o/unidades*') ? 'menu-is-opening menu-open' : '' }}
                      {{ request()->is('gest??o/defini????es*') ? 'menu-is-opening menu-open' : '' }}
                      {{ request()->is('gest??o/avisos*') ? 'menu-is-opening menu-open' : '' }}
                ">
                    <a href="#" class="nav-link
                        {{ request()->is('gest??o/locais*') ? 'active' : '' }}
                        {{ request()->is('gest??o/unidades*') ? 'active' : '' }}
                        {{ request()->is('gest??o/defini????es*') ? 'active' : '' }}
                        {{ request()->is('gest??o/avisos*') ? 'active' : '' }}
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
                        {{ request()->is('gest??o/locais*') ? 'block' : 'none' }}
                        {{ request()->is('gest??o/unidades*') ? 'block' : 'none' }}
                        {{ request()->is('gest??o/defini????es*') ? 'block' : 'none' }}
                        {{ request()->is('gest??o/avisos*') ? 'block' : 'none' }}
                    !important;">

                        @if($CHANGE_LOCAIS_REF==true)
                        <li class="nav-item">
                            <a href="{{route('gest??o.locais.index')}}" class="nav-link {{ request()->is('gest??o/locais*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-map-marked-alt nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-double-right nav-icon"></i>
                                    @endif
                                    <p>Locais de refei????o</p>
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
                            <a href="{{route('gest??o.unidades.index')}}" class="nav-link {{ request()->is('gest??o/unidades*') ? 'active' : '' }}">
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
                                <p>H??rarios de refei????o</p>
                            </a>
                        </li>
                        @endif

                        @if ($EDIT_DEADLINES_TAG==true || $EDIT_DEADLINES_UNTAG==true)
                        <li class="nav-item">
                            <a href="{{route('gestao.settings')}}" class="nav-link {{ request()->is('gest??o/defini????es*') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-cogs  nav-icon"></i>
                                @else
                                <i class="fas fa-angle-double-right nav-icon"></i>
                                @endif
                                <p>Defini????es</p>
                            </a>
                        </li>
                        @endif

                        @if($GENERAL_WARNING_CREATION==true)
                        <li class="nav-item">
                            <a href="{{ route('gest??o.warnings.index') }}" class="nav-link {{ request()->is('gest??o/avisos*') ? 'active' : '' }}">
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
                    <a href="{{route('gestao.hospedes')}}" class="nav-link {{ request()->is('gest??o/h??spedes*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                        <i class="fa-solid fa-id-card-clip nav-icon"></i>
                        @else
                        <i class="fas fa-angle-double-right nav-icon"></i>
                        @endif
                        <p>H??spedes</p>
                    </a>
                </li>
                @endif

                @if ($VIEW_GENERAL_STATS==true)

                <li class="nav-item has-treeview {{ request()->is('gest??o/estatisticas*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('gest??o/estatisticas*') ? 'active' : '' }}">
                        @if (Auth::user()->use_icons=='Y')
                          <i class="fa-solid fa-square-poll-vertical nav-icon"></i>
                        @else
                          <i class="fas fa-angle-right nav-icon"></i>
                        @endif
                        <p> Estat??sticas
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="display: {{ request()->is('gest??o/estatisticas*') ? 'block' : 'none' }} !important;">

                        <li class="nav-item">
                            <a href="{{route('gest??o.statsAdmin')}}" class="nav-link {{ request()->is('gest??o/estatisticas') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                    <i class="far fa-chart-bar nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    @endif
                                    <p>Dados de marca????o</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('gest??o.statsAdminDay')}}" class="nav-link {{ request()->is('gest??o/estatisticas/day') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                <i class="fas fa-chart-area nav-icon"></i>
                                    @else
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    @endif
                                    <p>Dados por dia/refei????o</p>
                            </a>
                        </li>

                        <li class="nav-item">
                          <a href="{{ route('gest??o.estatisticas.allexports') }}" class="nav-link {{ request()->is('gest??o/estatisticas/exportacoes*') ? 'active' : '' }}">
                              @if (Auth::user()->use_icons=='Y')
                                <i class="fa-solid  fa-diagram-next nav-icon"></i>
                              @else
                                <i class="fas fa-angle-right nav-icon"></i>
                              @endif
                              <p> Extrair relat??rios
                              </p>
                          </a>
                        </li>

                        <hr style="margin-bottom: .5rem;border: 0;border-top: 2px solid rgb(190 197 209 / 0%);">

                      </ul>
                    </li>

                @endif


                @if ($ADD_EMENTA==true || $EDIT_EMENTA==true)
                <li class="nav-item">
                    <a href="{{ route('gestao.ementa.index') }}" class="nav-link {{ request()->is('gest??o/ementa*') ? 'active' : '' }}">
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
                    <a href="{{route('gest??o.quiosqueAdmin')}}" class="nav-link {{ request()->is('gest??o/quiosque*') ? 'active' : '' }}">
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

                  <li class="nav-item has-treeview {{ request()->is('gest??o/equipa*') ? 'menu-is-opening menu-open' : '' }}">
                      <a href="#" class="nav-link {{ request()->is('gest??o/equipa*') ? 'active' : '' }}">
                          @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-address-book nav-icon"></i>
                          @else
                            <i class="fas fa-angle-right nav-icon"></i>
                          @endif
                          <p> Minha equipa
                              <i class="right fas fa-angle-left"></i>
                          </p>
                      </a>

                      <ul class="nav nav-treeview" style="display: {{ request()->is('gest??o/equipa*') ? 'block' : 'none' }} !important;">

                        <li class="nav-item">
                            <a href="{{route('gest??o.equipa.index')}}" class="nav-link {{ request()->is('gest??o/equipa') ? 'active' : '' }}">
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
                            <a href="{{route('gest??o.equipa.posts')}}" class="nav-link {{ request()->is('gest??o/equipa/posts') ? 'active' : '' }}">
                                @if (Auth::user()->use_icons=='Y')
                                  <i class="fa-solid fa-folder-open nav-icon"></i>
                                @else
                                <i class="fas fa-angle-right nav-icon"></i>
                                @endif
                                <p>Publica????es</p>
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
                        <a href="{{ route('helpdesk.permiss??es.index') }}" class="nav-link {{ request()->is('helpdesk/permiss??es*') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-desktop nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Permiss??es</p>
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
                        <a href="{{ route('helpdesk.settings.index') }}" class="nav-link {{ request()->is('helpdesk/defini????es*') ? 'active' : '' }}">
                            @if (Auth::user()->use_icons=='Y')
                            <i class="fas fa-desktop nav-icon"></i>
                            @else
                            <i class="fas fa-angle-right nav-icon"></i>
                            @endif
                            <p>Defini????es</p>
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
                            <p>Perfil e defini????es</p>
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
                            <p>Terminar sess??o</p>
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
