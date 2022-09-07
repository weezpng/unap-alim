@extends('layout.master')

@section('title','')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url()->previous() }}">Voltar atrás</a></li>
@endsection

@section('page-content')
<div class="col-md-12">
    <div class="error-page" style="margin-top: 25vh; height: 5vh;" @if (Auth::check() && Auth::user()->lite_mode=='Y') style="opacity: 1 !important;" @endif>
          <h2 class="headline text-danger">401</h2>
          <div class="error-content">
            <h3 style="font-size: 35px;"><i class="fas fa-exclamation-triangle text-danger" style="margin-top: 25px;"></i>&nbsp&nbspUNAUTHORIZED</h3>
            <p class="p_noMargin">
              A autentificação necessária para concluir o pedido falhou.
            </p>
          </div>
        </div>
</div>
@endsection
