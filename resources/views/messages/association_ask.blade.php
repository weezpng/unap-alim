@extends('layout.master')
@section('title','')
@section('page-content')
<div class="col-md-12">
   <div class="error-page" style="margin-top: 25vh;text-align: center;">
      <h4 style="font-size: 35px;font-weight: 100 !important; "><i class="fas fa-question-circle" style="margin-top: 25px; margin-bottom: 10px !important;"></i>
         <br><span style="display: block;padding-bottom: 1rem;">ASSOCIAR CONTA</span>
      </h4>
      <h5 style="margin-bottom: 2.5rem; font-size: 1.5rem !important; font-weight: 300 !important;">
         <span style="font-weight: 400; font-size: 1.25rem;">{{ $nim }} {{ $posto }} {{ $nome }}</span><br> pediu para se associar à sua conta.
      </h5>
      <a style="margin-top: 20px;" href="{{ route('profile.association.confirm') }}">
      <button class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" style="width: 10rem; margin-right: 1rem">Confirmar</button>
      </a>
      <a style="margin-top: 20px;" href="{{ route('profile.association.decline') }}">
      <button class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" style="width: 9rem">Negar</button>
      </a>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   $( "nav" ).remove( ".main-header" );
   $( "aside" ).remove( ".main-sidebar" );
   $( "footer" ).remove( ".main-footer" );
   $( ".content-wrapper" ).attr('style', 'margin-left: 0 !important; padding-top: 56px !important;');
</script>
@endsection
