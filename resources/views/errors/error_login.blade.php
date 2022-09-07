@extends('layout.master')

@section('title','')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Voltar</a></li>
@endsection

@section('page-content')
<div class="col-md-12">
  <div class="error-page" style="margin-top:25vh; height: 5vh;">
        <h2 class="headline text-danger">405</h2>
        <div class="error-content">
            <h3 style="font-size: 35px;"><i class="fas fa-exclamation-triangle text-danger" style="margin-top: 25px;"></i>&nbsp&nbspMETHOD NOT ALLOWED</h3>
            <p class="p_noMargin">
                <b>ERRO CRÍTICO.&nbsp</b>
            </p>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
  <script>
    $( "nav" ).attr('style', 'margin-left: 0 !important;');
    $( "aside" ).remove( ".main-sidebar" );
    $( "footer" ).remove( ".main-footer" );
    $( ".fa-bars" ).attr('style', 'display: none !important;');
    $( ".form-control-navbar" ).attr('style', 'display: none !important;');
    $( ".input-group-append" ).attr('style', 'display: none !important;');
    $( ".content-wrapper" ).attr('style', 'margin-left: 0 !important; padding-top: 56px !important;');
    $( ".dropdown" ).attr('style', 'display: none !important;');
    $( "#goToIndexBtn" ).attr('href', '#');
    $( "#loading" ).fadeOut(1000, function() { $( "#loading" ).remove(); });
    jQuery('.card').each(function() {
        $(this).addClass('slide-in-blurred-top');
    });
    jQuery('.btn').each(function() {
      $(this).addClass('slide-in-blurred-top');
    });
    jQuery('.info-box').each(function() {
        $(this).addClass('slide-in-blurred-top');
    });
    jQuery('.error-page').each(function() {
        $(this).addClass('slide-in-blurred-top');
    });
  </script>
@endsection
