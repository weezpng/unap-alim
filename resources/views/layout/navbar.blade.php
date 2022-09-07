@php
  if(Auth::check() && Auth::user()->dark_mode=='Y'){
    $_dark_mode = true;
  } elseif (Session::has('dark_mode_inauth') && Session::get('dark_mode_inauth')=='on') {
    $_dark_mode = true;
  } else {
    $_dark_mode = false;
  }
@endphp

@if(Auth::check())


<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xs" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Código QR</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p class="text-center" style="margin-bottom: 20px;">
              <b>Código QR</b>
            </p>

            <div style="margin-bottom: 40px;">
              <center>

              @php
              $NIM = Auth::user()->id;
              while ((strlen((string)$NIM)) < 8) {
                  $NIM = 0 . (string)$NIM;
                }
              @endphp
                {!! QrCode::size(200)->format('svg')->margin(1)->generate($NIM); !!}
                <h6 style="margin-top: 10px;" class="text-sm text-muted">{{$NIM}}</h6>
              </center>
            </div>
            <p class="text-sm">
              Se pretender receber o código QR por email, por favor preencha abaixo.
              Alternativamente pode fazer download, ou criar um pedido para impressão.
            </p>
            <form method="POST" action="{{ route('qr.send_to_email') }}">
              @csrf
              <div class="form-group row">
                <div class="col-sm-8">
                  <input type="email" required="" class="form-control" id="mail" name="mail" placeholder="Endereço de email">
                </div>
                <div class="col-sm-4">
                    <button type="submit" style="width: 100% !important;" class="btn btn-primary slide-in-blurred-top">Enviar para email</button>
                </div>
              </div>
            </form>
        </div>
        <div class="modal-footer">
        <a href="{{route('qr.download')}}">
            <button style="margin-right: 1.5rem;" type="button" class="btn btn-primary slide-in-blurred-top">Download</button>
          </a>
          <a href="{{route('qr.request_pess')}}">
            <button type="button" class="btn btn-primary slide-in-blurred-top">Pedido de impressão</button>
          </a>
          <button type="button" class="btn btn-secondary slide-in-blurred-top" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

@endif

<nav class="main-header navbar navbar-expand @if ($_dark_mode==true) navbar-dark @endif @if (Auth::check() && Auth::user()->compact_mode=='Y') text-xs nav-compact @endif">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item" id="menutoggle">
      <a class="nav-link" style="max-height: 2.5rem;;" data-widget="pushmenu" href="#" role="button" @if ($_dark_mode==false) style="color: #000;" @endif><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a id="goToIndexBtn" href="{{ route('index') }}">
        <h5 class="nav-link" @if ($_dark_mode==false) style="color: #000;" @endif>GESTÃO DE ALIMENTAÇÃO</h5>
      </A>
    </li>

    @if (Auth::check())

    <li style="padding-left: .5rem;" class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('index') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
    </li>
    @if (Auth::user()->lock=='N')
    <li class="nav-item d-none d-sm-inline-block">
      @if (!$MARCACOES_A_DINHEIRO)
        <a href="{{ route('marcacao.index') }}" class="nav-link {{ request()->is('marcacao*') ? 'active' : '' }}">Marcações</a>
      @endif
    </li>

  @else
    <li style="padding-left: .5rem;" class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('ementa.index') }}" class="nav-link {{ request()->is('ementa*') ? 'active' : '' }}">Ementa</a>
    </li>
  @endif

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

    @if(($_is_admin) || ($_is_super) || ($_is_user && (!$__is_perm_general && !$__is_perm_GCSEL)))
      @if(Auth::user()->lock=='N')
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('gestao.index') }}" class="nav-link {{ request()->is('gestão*') ? 'active' : '' }}">Gestão</a>
        </li>
      @endif
    @endif

    @if (Auth::user()->user_type=="POC" && Auth::user()->lock=='N')
      <li class="nav-item d-none d-sm-inline-block">
          <a href="{{ route('poc.index') }}" class="nav-link {{ request()->is('poc-control-center*') ? 'active' : '' }}">Centro POC</a>
      </li>
    @endif

    @if ($VIEW_ALL_MEMBERS && Auth::user()->lock=='N')
      <li class="nav-item d-none d-sm-inline-block" style="padding: .4rem 1rem;">
        <form class="form-inline ml-3" method="POST" action="">
            <div class="input-group input-group-sm">
              @csrf
              <input id="navBarSearch" type="text" style="border-right: none !important;" name="navBarSearch" class="form-control form-control-navbar" type="search" placeholder=" Procurar..." aria-label="Procurar..." oninput="SearcNav(this);" onkeyup="QuickSearchAct();">
              <div class="input-group-append">
                <button disabled class="btn-navbar" @if (Auth::user()->dark_mode=='N') style="border: 1px solid #ced4da; border-left-width: 0;" @endif style="border-left: none !important; width: 4rem !important;">
                  <i class="fas fa-search" style="padding-right: 3px; margin-right: -3rem; "></i>
                </button>
              </div>
            </div>
          </form>
          <div class="searchFloat card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif swing-in-top-fwd hide" id="searchResults">
              <div class="card-header border-0" style="padding-right: 0.9rem;">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Resultados</h3>
                  <button type="button" id="closeSearchBtn" class="close" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              </div>
              <div class="card-body" style="padding: 0 !important; padding-bottom: 10px !important;">
                <table class="table" style="margin-top: 1vh; width: 99% !important; padding: 10px !important;" id="searchResults_table" name="searchResults_table">
                  <tbody>
                    <tr style="border-top: 0px;">
                    <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarResultName" name="searchBarResultName"></td>
                      <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarResultName" name="searchBarResultName">NOME</td>
                      <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarResultPosto" name="searchBarResultPosto">POSTO</td>
                      <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarUnidade" name="searchBarUnidade">UNIDADE</td>
                      <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarUsrtype" name="searchBarUsrtype">USERTYPE</td>
                    </tr>
                  </tbody>
                </table>
            </div>
          </div>
      </li>
    @elseif($__is_perm_MESSES && Auth::user()->lock=='N')
      <li class="nav-item d-none d-sm-inline-block" style="padding: .4rem 1rem;">
        <form class="form-inline ml-3" method="POST" action="">
            <div class="input-group input-group-sm">
              @csrf
              <input id="navBarSearch" type="text" style="border-right: none !important;" name="navBarSearch" class="form-control form-control-navbar" type="search" placeholder=" Procurar hóspede" aria-label="Procurar hóspede" oninput="SearcNav(this);" onkeyup="QuickSearchAct();">
              <div class="input-group-append">
                <button disabled class="btn-navbar" @if (Auth::user()->dark_mode=='N') style="border: 1px solid #ced4da; border-left-width: 0;" @endif style="border-left: none !important; width: 4rem !important;">
                  <i class="fas fa-search" style="padding-right: 3px; margin-right: -3rem; "></i>
                </button>
              </div>
            </div>
          </form>
          <div class="searchFloat card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif swing-in-top-fwd hide" id="searchResults" style="width: 50rem;">
              <div class="card-header border-0" style="padding-right: 0.9rem;">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title uppercase-only">RESULTADOS</h3>
                  <button type="button" id="closeSearchBtn" class="close" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              </div>
              <div class="card-body" style="padding: 0 !important; padding-bottom: 10px !important;">
                <table class="table" style="margin-top: 1vh; width: 99% !important;" id="searchResults_table" name="searchResults_table">
                  <tbody>
                    <tr style="border-top: 0px;">
                    <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarResultName" name="searchBarResultName"></td>
                      <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarResultName" name="searchBarResultName">NOME</td>
                      <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarResultPosto" name="searchBarResultPosto">POSTO</td>
                      <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarUnidade" name="searchBarUnidade">UNIDADE</td>
                      <td style="border-top: 0px; white-space: nowrap; text-transform: uppercase;" id="searchBarUsrtype" name="searchBarUsrtype">USERTYPE</td>
                    </tr>
                  </tbody>
                </table>
            </div>
          </div>
        </li>
    @endif
  @endif


  </ul>


  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    @if (Auth::check())
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" id="helpToggle">
          <i class="fas fa-question-circle"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right swing-in-top-fwd">
          <div class="media user_bar_padding ">
            <div class="media-body @if (Auth::check() && Auth::user()->compact_mode=='Y') text-xs @endif">
              <h3 class="dropdown-item-title uppercase-only text-center">
                <p class="text-sm text-muted user_type_label">AJUDA</p>
              </h3>

              <h3 class="dropdown-item-title" >
              <div class="navbar-user-infodisplay">
                <div style="font-size: 0.85rem !important; padding: 0.3rem;" >
                  Qualquer dificuldade na utilização da aplicação, consulte o FAQ. Se mesmo depois, encontrar alguma dificuldade, por favor entre em contacto com o HELPDESK.
                </div>
              </div>
            </h3>

              <div class="navbar-user-useroptions" style="margin-top: .5rem !important;">
                  @if(Auth::user()->lock=='N') <a href="{{route('help.faq')}}" class="dropdown-item dropdown-footer navbar-user-contextbtn">Ver FAQ</a> @endif
                  <a href="mailto:cpess.unap.informatica@exercito.pt?subject=Apoio Portal Alimentação&body=DEBUG INFO :: NIM: {{ Auth::user()->id}} || _TOKEN: {{ csrf_token() }} || UserPerm: {{ Auth::user()->user_type}}\{{ Auth::user()->user_permission }} || LOCK: {{ Auth::user()->lock }} || TagOblig: @if(Auth::user()->isTagOblig==null)NONE @else {{ Auth::user()->isTagOblig }} @endif" class="dropdown-item dropdown-footer navbar-user-contextbtn">Enviar email</a>
              </div>
            </div>
          </div>
        </div>
      </li>
      @if ($partner!=null)
        <!-- Partner Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#" id="partnertoggle">
            <i class="fas fa-handshake"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right swing-in-top-fwd">
            <div class="media user_bar_padding">
              <div class="media-body @if (Auth::check() && Auth::user()->compact_mode=='Y') text-xs @endif">
                <h3 class="dropdown-item-title uppercase-only text-center">
                  <p class="text-sm text-muted user_type_label">PARCEIRO</p>
                </h3>
                @php
                    $partner_id = $partner['id'];
                    while ((strlen((string)$partner_id)) < 8) {
                        $partner_id = 0 . (string)$partner_id;
                    }
                  $filename = "assets/profiles/".$partner_id.".PNG";
                  $filename2 = "assets/profiles/".$partner_id.".JPG";
                @endphp
                <a href="{{route('user.profile.parelha')}}">
                  <div class="image_navbar">
                      @if (file_exists(public_path($filename)))
                        <div class="text-center" style="padding-top: 1rem;">
                           <img class="profile-user-img img-fluid img-circle image__img" src="{{ asset($filename) }}" alt="User profile picture"
                           @if ($partner['lock']=='N') style="border: 2px solid #6c757d !important;" @else style="border: 2px solid #d14351 !important;" @endif>
                        </div>
                      @elseif (file_exists(public_path($filename2)))
                        <div class="text-center" style="padding-top: 1rem;">
                           <img class="profile-user-img img-fluid img-circle image__img" src="{{ asset($filename2) }}" alt="User profile picture"
                           @if ($partner['lock']=='N') style="border: 2px solid #6c757d !important;" @else style="border: 2px solid #d14351 !important;" @endif>
                        </div>
                      @else
                        @php $filename2 = "assets/icons/default.jpg"; @endphp
                        <div class="text-center" style="padding-top: 1rem;">
                            @php $filename2 = "https://cpes-wise2/Unidades/Fotos/". $partner_id . ".JPG"; @endphp
                           <img class="profile-user-img img-fluid img-circle image__img" src="{{ asset($filename2) }}" alt="Default profile picture"
                           @if ($partner['lock']=='N') style="border: 2px solid #6c757d !important;" @else style="border: 2px solid #d14351 !important;" @endif>
                        </div>
                      @endif
                      @if(Auth::user()->lock=='N')
                        <div class="image__overlay image__overlay--primary">
                          <p class="image__description-link uppercase-only">
                              Ver perfil
                          </p>
                      </div>
                    @endif
                  </div>
                </a>
                  <h3 class="dropdown-item-title uppercase-only">
                    <div class="navbar-user-infodisplay @if (Auth::check() && Auth::user()->compact_mode=='Y') text-sm @endif">
                      <div class="text-center">
                        <p class="text-xs">{{ $partner_id }}</p>
                        <p class="text-sm">{{ $partner['posto'] }}</p>
                        {{ $partner['name'] }}
                      </div>
                      @if ($partner['telf']!=null)
                        <div class="text-center" style="margin-top: 1rem;">
                              <span style="font-size: .85rem;">
                                Telf<br>{{ $partner['telf'] }}
                              </span>
                        </div>
                      @endif
                    </div>
                  </h3>
                  <div class="navbar-user-useroptions">
                    <a href="mailto:{{ $partner['email'] }}" class="dropdown-item dropdown-footer navbar-user-contextbtn">Novo email</a>
                  </div>
              </div>
            </div>
          </div>
        </li>
      @endif

      @if (Auth::user()->lock=='N')
        @include('layout.notificationsbar')
      @endif
    @endif

    @if(Auth::check())

    <li class="nav-item dropdown">
      <a class="nav-link" data-widget="control-sidebar" href="#" id="usertoggle">
        <i class="fas fa-user-circle"></i>
      </a>
    </li>
    @else

      <li class="nav-item">
        <a class="nav-link" role="button" onclick='toggle_dark_noauth();' >
          @if(Session::has('dark_mode_inauth') && Session::get('dark_mode_inauth')=="on")
            <i class="fa-solid fa-lightbulb"></i>
          @else
            <h6><i class="fa-solid fa-moon"></i></h6>
          @endif
        </a>
      </li>
    @endif
  </ul>
</nav>
