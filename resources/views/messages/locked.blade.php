@extends('layout.master')
@section('page-content')
<div class="col-md-12">
   <div class="error-page" style="margin-top: 26.5vh ;text-align: center;">
      <h4 style="font-size: 35px;font-weight: 100 !important; "><i class="fas fa-ban" style="color: #d14351; font-size: 64px;margin: 25px;"></i>
         <br><span style="display: block;padding-bottom: 1.5rem;padding-top: 1rem !important; font-size: 1.5rem !important;">
         A sua conta encontra-se <strong>bloqueada</strong>.<br>
         Apenas pode consultar a ementa.
         </span>
      </h4>
      <a style="margin-top: 5vh;" href="{{ url()->previous() }}">
      <button class="btn btn-danger" style="width: 10rem">Voltar atrás</button>
      </a>
   </div>
</div>
@endsection
