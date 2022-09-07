@extends('layout.master')
@section('title','Centro de Gestão')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Gestão</li>
@endsection

@section('page-content')

  <div class="gestao-box-parent">

    @if ($VIEW_ALL_MEMBERS )
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Utilizadores</h3>
          <p>Consultar e gerir os utilizadores existentes</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        <a href="{{ route('gestão.usersAdmin') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($ACCEPT_NEW_MEMBERS)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Utilizadores</h3>
          <p>Aceitar novos utilizadores e transferências</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-plus"></i>
        </div>
        <a href="{{ route('gestão.newUsersAdmin') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($ADD_EMENTA || $EDIT_EMENTA)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Ementa</h3>
          <p>Adicionar e editar ementas</p>
        </div>
        <div class="icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <a href="{{ route('gestao.ementa.index') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($SCHEDULE_USER_VACATIONS)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Ausências de utilizadores</h3>
          <p>Consultar e registar férias, diligências e convalescenças dos utilizadores</p>
        </div>
        <div class="icon">
          <i class="fas fa-calendar-week"></i>
        </div>
        <a href="{{ route('gestao.ferias.index') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if($TAG_USER_DIETAS)
    <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Utilizadores c/ dieta</h3>
          <p>Consultar e registar os utilizadores atualmente c/ menu de dieta atribuido</p>
        </div>
        <div class="icon">
          <i class="fas fa-list-alt"></i>
        </div>
        <a href="{{ route('gestao.dieta.index') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($VIEW_GENERAL_STATS)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Estatísticas</h3>
          <p>Consultar as estatísticas de marcações</p>
        </div>
        <div class="icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <a href="{{ route('gestão.statsAdmin') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($MEALS_TO_EXTERNAL)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Marcações quantitativas</h3>
          <p>Pedir refeições de forma não nominal</p>
        </div>
        <div class="icon">
          <i class="fas fa-calendar-plus"></i>
        </div>
        <a href="{{ route('marcacao.non_nominal') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($EDIT_DEADLINES_TAG || $EDIT_DEADLINES_UNTAG)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Definições</h3>
          <p>Alterar definições da plataforma</p>
        </div>
        <div class="icon">
          <i class="fas fa-cogs"></i>
        </div>
        <a href="{{ route('gestao.settings') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($VIEW_DATA_QUIOSQUE)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Entradas no quiosque</h3>
          <p>Ver as entradas no quiosque de entrada de refeição</p>
        </div>
        <div class="icon">
          <i class="fas fa-desktop"></i>
        </div>
        <a href="{{ route('gestão.quiosqueAdmin') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($GENERAL_WARNING_CREATION)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Avisos gerais</h3>
          <p>Gerir os avisos gerais mostrados aos utilizadores na plataforma</p>
        </div>
        <div class="icon">
          <i class="fas fa-envelope"></i>
        </div>
        <a href="{{ route('gestão.warnings.index') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($CHANGE_LOCAIS_REF)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Locais de refeição</h3>
          <p>Gerir os locais de refeição</p>
        </div>
        <div class="icon">
          <i class="fas fa-map-marked-alt"></i>
        </div>
        <a href="{{ route('gestão.locais.index') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if ($CHANGE_UNIDADES_MAN)
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Unidades</h3>
          <p>Gerir as Unidades na plataforma</p>
        </div>
        <div class="icon">
          <i class="fas fa-landmark"></i>
        </div>
        <a href="{{ route('gestão.unidades.index') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if (Auth::user()->user_type == "ADMIN" || Auth::user()->user_type == "POC")
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Utilizadores associados</h3>
          <p>Gerir e fazer marcações dos utilizadores associados a si.</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-friends"></i>
        </div>
        <a href="{{ route('gestão.associatedUsersAdmin') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Marcações em massa</h3>
          <p>Fazer marcações para os grupos de utilizadores associados</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-check"></i>
        </div>
        <a href="{{ route('marcacao.forgroup') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

    @if (Auth::user()->user_permission=="MESSES")
      <div class="small-box bg-info gestao-box">
        <div class="inner">
          <h3>Hóspedes</h3>
          <p>Gerir e efectuar marcações para hóspedes</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-clock"></i>
        </div>
        <a href="{{ route('gestao.hospedes') }}" class="small-box-footer">
          Abrir &nbsp;<i class="fas fa-arrow-right"></i>
        </a>
      </div>
    @endif

  </div>

@endsection
