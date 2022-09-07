@extends('layout.master')

@section('title','')

@section('page-content')
<div class="col-md-12">
        <div class="error-page" style="margin-top: 30vh; height: 5vh;" @if (Auth::check() && Auth::user()->lite_mode=='Y') style="opacity: 1 !important;" @endif>
          <h2 class="headline text-danger"></h2>
          <div class="error-content" style="margin-left: 20vh !important;">
            <h3 style="font-size: 36px;">
              <i class="fas fa-exclamation-triangle text-danger" style="margin-top: 25px;">&nbsp;&nbsp;</i>
              <strong>LOCKED</strong>
            </h3>
            <p class="p_noMargin">
              A sua conta encontra-se bloqueada.<br>Apenas está autorizado a <a href="{{ route('ementa.index') }}">ementa</a>.
            </p>
          </div>
        </div>
</div>
@endsection
