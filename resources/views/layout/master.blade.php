<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="author" content="1Cb P.Rocha">

  <title>GESTÃO DE ALIMENTAÇÃO</title>

  @if (isset($_SESSION) && $_SESSION["intranet"]==true)
    @include('layout.intranet_message')
  @endif

  @php
    if(Auth::check() && Auth::user()->dark_mode=='Y'){
      $_dark_mode = true;
    } elseif (Session::has('dark_mode_inauth') && Session::get('dark_mode_inauth')=='on') {
      $_dark_mode = true;
    } else {
      $_dark_mode = false;
    }
  @endphp

  <link rel="stylesheet" href="{{asset('assets/custom/ionicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
  <link href="{{asset('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.js')}}">
  <link rel="stylesheet" href="{{asset('assets/custom/SourceSansPro.css')}}">
  <link rel="stylesheet" href="{{asset('assets/custom/custom.exp.css')}}">

  @if ($_dark_mode==false)
    <link rel="stylesheet" href="{{asset('assets/custom/css/light.css')}}">
    <link rel="stylesheet" href="{{asset('assets/custom/toasts_light.css')}}">
  @elseif ($_dark_mode==true)
    <link rel="stylesheet" href="{{asset('assets/custom/css/dark.css')}}">
    <link rel="stylesheet" href="{{asset('assets/custom/toasts_dark.css')}}">
  @endif

  @if (isset($_SESSION) && $_SESSION["intranet"]==true)
    @include('layout.float-btn')
    <link rel="stylesheet" href="{{asset('assets/custom/css/intranet.css')}}">
  @else
    @if(Auth::check() && Auth::user()->resize_box=='Y')
      <link rel="stylesheet" href="{{asset('assets/custom/css/resizer.css')}}">
    @else
      <link rel="stylesheet" href="{{asset('assets/custom/css/no_resizer.css')}}">
    @endif
  @endif

  @if(Auth::check())

    @if($GET_STATS_NOMINAL)
      <link rel="stylesheet" type="text/css" href="{{asset('adminlte/plugins/datarange-picker/daterangepicker-bs3.css')}}">
    @endif

    @if (Auth::user()->flat_mode=='Y')
      <link rel="stylesheet" href="{{asset('assets/custom/css/flatbar.css')}}">
    @else
      <link rel="stylesheet" href="{{asset('assets/custom/css/legacybar.css')}}">
    @endif

    @if (Auth::user()->sticky_top=='Y')
      <link rel="stylesheet" href="{{asset('assets/custom/css/sticky_top.css')}}">
    @endif

    @if (Auth::user()->compact_mode=='Y')
      <link rel="stylesheet" href="{{asset('assets/custom/css/compact_mode.css')}}">
    @endif

    @if (Auth::user()->lite_mode=='Y')
      <link rel="stylesheet" href="{{asset('assets/custom/css/lite_mode.css')}}">
    @endif

  @endif

  @yield('extra-links')

</head>

@php
  $body_class = "sidebar-mini ";
  if (Auth::check() && Auth::user()->auto_collapse=='Y')                                                    $body_class = $body_class."sidebar-collapse ";
  if (Auth::check() && Auth::user()->compact_mode=='Y')                                                     $body_class = $body_class."text-sm ";
  if (Auth::check() && Auth::user()->sticky_top=='Y' || (isset($_SESSION) && $_SESSION['intranet']==true))  $body_class = $body_class."layout-fixed layout-navbar-fixed ";
  if ($_dark_mode==true)                                                                                    $body_class = $body_class."dark-mode";
@endphp
<body class="{{ $body_class }}">
  <div class="wrapper">
    @include('layout.navbar')
    @include('layout.sidebar')
    @if(Auth::check())
      @include('layout.controlbar')
    @endif
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid container-tweak">
          <div class="row mb-2" style="margin-top: .7rem;">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark">
                <i style="margin-right: 0.25rem;" class="@yield('icon')"></i>
                @yield('title')
              </h1>
              <h5 class="m-0 text-dark subtitle">@yield('subtitle')</h5>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                @yield('breadcrumb')
              </ol>
            </div>
          </div>
        </div>
      </div>
      <div class="content">
        <div class="container-fluid container-tweak">
          <div class="row" id="tarbody">
            <div id="loading">
              <div id="water" class="water scale-in-center"></div>
            </div>
              <button id="scroll_to_top" class="btn btn-dark backer-top tooltip elevation-4" onclick="ScrollToTopSmooth();" role="button" aria-label="Scroll to top">
                <i class="fa-solid fa-turn-up" style="margin: 0 auto;"></i>
                <span class="tooltiptext">Voltar ao topo</span>
             </button>
              @yield('page-content')
              @include('layout.modals.notificationmodal')
              @include('layout.modals.changeRefModal')
              @include('layout.modals.statstotal_modal')
              @include('layout.modals.statsmonthly_modal')
              @include('layout.modals.statsquant_modal')
              @include('layout.modals.statstotal_general')
          </div>
        </div>
      </div>
    </div>
  @include('layout.footer')
</div>
<!-- REQUIRED SCRIPTS -->
<noscript>Este browser não suporta JavaScript!</noscript>
<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('adminlte/js/adminlte.js')}}"></script>
<script src="{{asset('js/FontAwesome/fawesome.js')}}"></script>
@if(Auth::check() && $GET_STATS_NOMINAL)
  <script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/moment.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('adminlte/plugins/datarange-picker/daterangepicker.js')}}"></script>
@endif
@include('layout.scripts')
<!-- Extra, page-specific script imports -->
@yield('extra-scripts')
</body>
</html>
