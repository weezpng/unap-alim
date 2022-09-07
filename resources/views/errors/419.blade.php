@extends('layout.master')

@section('title','')

@section('page-content')
<div class="col-md-12">
  <div class="error-page" style="margin-top: 25vh; height: 5vh;" @if (Auth::check() && Auth::user()->lite_mode=='Y') style="opacity: 1 !important;" @endif>
          <h2 class="headline text-warning">419</h2>
          <div class="error-content">
            <h3 style="font-size: 35px;"><i class="fas fa-exclamation-triangle text-warning" style="margin-top: 20px;"></i>&nbsp;&nbsp;SESSION EXPIRED</h3>
            <p class="p_noMargin">
              É necessária reautenticação no servidor.<br> Clique <a style="font-weight: 600;" href="{{ route('index') }}">aqui</a> para tentar novamente.
            </p>
          </div>
        </div>
</div>
@endsection
