@extends('layout.master')

@section('page-content')
<div class="col-md-12">
  <div class="error-page center"  @if (Auth::check() && Auth::user()->lite_mode=='Y') style="opacity: 1 !important;" @endif>
    <h2 class="headline">
      <i class="fas fa-laptop-code"></i>
    </h2>
      <div class="error-content" style="color: #343a40 !important; margin-top: .35rem;" >
        <h3 style="font-size: 35px;">Em manutenção</h3>
        <p class="p_noMargin">
          A alicação está atualmente indisponível.
          <br>
          Tente novamente mais tarde.
        </p>
      </div>
    </div>
</div>
@endsection
