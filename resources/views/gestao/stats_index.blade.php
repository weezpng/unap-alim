@extends('layout.master')
@section('title','Centro de Estatísticas')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item">Gestão</li>
<li class="breadcrumb-item active">Estatísticas</li>
@endsection

@section('page-content')

<div class="gestao-box-parent">

  @if ($VIEW_GENERAL_STATS)

    <div class="small-box bg-primary gestao-box">
      <div class="inner" style="height: 25vh !important; max-height: 230px !important;">
        <h3>Dados de marcações</h3>
        <p>Veja de forma generalizada, os numeros de refeições, nos vários locais de refeição, separadas por marcações normais, marcações de dieta, e pedidos quantitativos.</p>
      </div>
      <div class="icon">
        <i class="far fa-chart-bar"></i>
      </div>
      <a href="{{ route('gestão.statsAdmin') }}" class="small-box-footer">
        Aceder &nbsp;<i class="fas fa-arrow-right"></i>
      </a>
    </div>

    <div class="small-box bg-primary gestao-box">
      <div class="inner" style="height: 25vh !important; max-height: 230px !important;">
        <h3>Dados por dia/marcação</h3>
        <p>Veja os numeros de refeições, de forma especifica por local de refeição, data e refeição, separadas por marcações normais, marcações de dieta, e pedidos quantitativos.</p>
      </div>
      <div class="icon">
        <i class="fa-solid fa-chart-column"></i>
      </div>
      <a href="{{ route('gestão.statsAdminDay') }}" class="small-box-footer">
        Aceder &nbsp;<i class="fas fa-arrow-right"></i>
      </a>
    </div>

    <div class="small-box bg-primary gestao-box">
      <div class="inner" style="height: 25vh !important; max-height: 230px !important;">
        <h3>Dados de consumo geral</h3>
        <p>Veja, por dia e por unidade, os nùmeros de consumo tendo em conta os nùmeros de marcações, dando-lhe uma estatística de consumo geral.</p>
      </div>
      <div class="icon">
        <i class="fa-solid fa-chart-line"></i>
      </div>
      <a href="{{ route('gestão.statsRemoved') }}" class="small-box-footer">
        Aceder &nbsp;<i class="fas fa-arrow-right"></i>
      </a>
    </div>

    <div class="small-box bg-primary gestao-box">
      <div class="inner" style="height: 25vh !important; max-height: 230px !important;">
        <h3>Dados díarios por unidade</h3>
        <p>Veja, por refeição numa unidade especifica, os nùmeros de consumo tendo em conta os nùmeros de marcações, dando-lhe uma estatística de consumo para esse dia.</p>
      </div>
      <div class="icon">
        <i class="fa-solid fa-square-poll-horizontal"></i>
      </div>
      <a href="{{ route('gestão.AdminUnit') }}" class="small-box-footer">
        Aceder &nbsp;<i class="fas fa-arrow-right"></i>
      </a>
    </div>

  @endif

</div>

@endsection
