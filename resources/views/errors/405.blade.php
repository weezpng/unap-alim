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
                <b>ERRO CRÍTICO.&nbsp</b>Por favor contacte o HELPDESK com a informação do que o que estava a tentar fazer.
            </p>
        </div>
    </div>
</div>
@endsection
