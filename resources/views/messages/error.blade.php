@extends('layout.master')
@section('title','')
@section('page-content')
<div class="col-md-12">
   <div class="error-page" style="margin-top: 25vh;text-align: center;">
      <h4 style="font-size: 35px;font-weight: 100 !important; "><i class="fas fa-times text-error" style="margin-top: 25px; margin-bottom: 5px !important;"></i>
         <br><span style="display: block;padding-bottom: 1.5rem;padding-top: 1rem !important; font-size: 1.5rem !important;">{{$message}}</span>
      </h4>
      <a style="margin-top: 20px;" href="{{ url()->previous() }}">
      <button class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" style="width: 10rem">Voltar atrás</button>
      </a>
   </div>
</div>
@endsection
