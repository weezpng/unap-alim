@extends('layout.master')

@section('title','')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Voltar</a></li>
@endsection

@section('page-content')
<div class="col-md-12">
  <div class="error-page" style="margin-top: 25vh; height: 5vh;" @if (Auth::check() && Auth::user()->lite_mode=='Y') style="opacity: 1 !important;" @endif>
    <h2 class="headline text-danger">403</h2>
    <div class="error-content">
      <h3 style="font-size: 35px;"><i class="fas fa-exclamation-triangle text-danger" style="margin-top: 25px;"></i>&nbsp&nbspFORBIDDEN</h3>
      <p class="p_noMargin">
        Você não têm os previlégios necessários para concluir o pedido feito ao servidor.
      </p>
    </div>
  </div>
</div>
@endsection
