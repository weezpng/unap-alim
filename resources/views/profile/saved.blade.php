@extends('layout.master')
@section('title','')
@section('page-content')
<div class="col-md-12">
   <div class="error-page" style="margin-top: 25vh;text-align: center;">
      <h3 style="font-size: 35px;font-weight: 100 !important; "><i class="fas fa-check text-success" style="margin-top: 25px; margin-bottom: 5px !important;"></i>
         <br><span style="display: block;padding-bottom: 1.5rem; margin-top: 1rem;">GUARDADO</span>
         @if(isset($changedUnidade) && $changedUnidade==true)
         <span style="display: block;padding-bottom: 1.5rem; font-size: 1.25rem;">A troca de unidade para <b>{{ $newUnidade }}</b> vai ter que ser confirmada por um administrador. Até lá, o seu perfil vai continuar na <b>{{$oldUnidade}}</b>.</span>
         @endif
      </h3>
      <a style="margin-top: 20px;" href="{{ $url }}">
      <button class="btn btn-dark" style="width: 10rem">Voltar atrás</button>
      </a>
   </div>
</div>
@endsection
