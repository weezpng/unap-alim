@extends('layout.master')
@section('title','FAQ')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Helpdesk</li>
<li class="breadcrumb-item active">FAQ</li>
@endsection
@section('page-content')
<div class="col-md-12">
   <div class="card">
     <div class="card-header border-0">
        <div class="d-flex justify-content-between">
           <h3 class="card-title">Tutoriais</h3>
           <div class="card-tools" style="margin-right: 0 !important;">
              &nbsp;
              <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
           </div>
        </div>
     </div>
      <div class="card-body" style="max-height: 77vh;">
      <h6 style="margin-bottom: 1.5rem;">Aqui pode aprender a utilizar os recursos desta aplicação relevantes ao seu perfil.</h6>
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse0" class="collapsed" aria-expanded="false">
                      Marcação de refeições
                     </a>
                     </h4>
                  </div>
                  <div id="collapse0" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/FAZER_MARCAÇÃO.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                        <h5 style="margin-top: 2rem;"> Instruções:<h5>
                        <h6>
                           <ul>
                              <li>No painel lateral, aceda a <b>Marcações</b> e de seguida a <b>Marcar refeição</b></li>
                              <li>Clique em marcar, e de seguida</b></li>
                           </ul>
                        </h6>
                     </div>
                  </div>
               </div>
            </div>
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" class="collapsed" aria-expanded="false">
                      Remoção de marcações
                     </a>
                     </h4>
                  </div>
                  <div id="collapse1" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/REMOVER_MARCAÇÃO.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div> 
            @if(Auth::check() && $MEALS_TO_EXTERNAL)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" class="collapsed" aria-expanded="false">
                        Pedidos quantitativos de refeições
                     </a>
                     </h4>
                  </div>
                  <div id="collapse2" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/PEDIDO_QUANTITATIVO.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div> 
            @endif
            @if(Auth::check() && $EDIT_EMENTA)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse3" class="collapsed" aria-expanded="false">
                        Alterar ementa
                     </a>
                     </h4>
                  </div>
                  <div id="collapse3" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/ALTERAR_EMENTA.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif            
            @if(Auth::check() && $ADD_EMENTA)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse4" class="collapsed" aria-expanded="false">
                        Publicar ementa
                     </a>
                     </h4>
                  </div>
                  <div id="collapse4" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/PUBLICAR_EMENTA.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif
            @if(Auth::check() && $VIEW_GENERAL_STATS || Auth::check() && $GET_STATS_OTHER_UNITS || Auth::check() && $GET_STATS_NOMINAL || Auth::check() && $GET_CIVILIANS_REPORT)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse5" class="collapsed" aria-expanded="false">
                        Consultar e gerar relatórios
                     </a>
                     </h4>
                  </div>
                  <div id="collapse5" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/OBTER_RELATÓRIOS.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif
            @if(Auth::check() && ($VIEW_DATA_QUIOSQUE || $MASS_QR_GENERATE))
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse6" class="collapsed" aria-expanded="false">
                        Consultar informação de quiosque
                     </a>
                     </h4>
                  </div>
                  <div id="collapse6" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/VER_ENTRADAS_GERAR QR.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            @if(Auth::check())
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse06" class="collapsed" aria-expanded="false">
                        Consultar a sua informação de entradas em quiosque
                     </a>
                     </h4>
                  </div>
                  <div id="collapse06" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/VER_MINHAS_QUIOSQUE.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            @if(Auth::check() && $SCHEDULE_USER_VACATIONS)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse7" class="collapsed" aria-expanded="false">
                        Marcar ausências aos utilizadores
                     </a>
                     </h4>
                  </div>
                  <div id="collapse7" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/MARCAR_AUSENSCIA.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div> 
            @endif
            @if(Auth::check() && $TAG_USER_DIETAS)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse07" class="collapsed" aria-expanded="false">
                        Marcar utilizadores c/ dieta
                     </a>
                     </h4>
                  </div>
                  <div id="collapse07" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/MARCAR_DIETA.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div> 
            @endif
            @if(Auth::check() && $VIEW_ALL_MEMBERS || Auth::user()->user_type=="ADMIN")
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse8" class="collapsed" aria-expanded="false">
                        Aceder a um perfil de utilizador
                     </a>
                     </h4>
                  </div>
                  <div id="collapse8" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/ACEDER_PERFIL.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                        <h6 style="margin-bottom: 1.5rem;margin-top: 1.5rem;">ou</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/ACEDER_PERFIL_2.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif
            @if(Auth::user()->user_type=="ADMIN")
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse09" class="collapsed" aria-expanded="false">
                        Associar um utilizador ao seu grupo de gestão
                     </a>
                     </h4>
                  </div>
                  <div id="collapse09" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/ASS_UTILIZADOR.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif
            @if(Auth::user()->user_type=="ADMIN")            
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse9" class="collapsed" aria-expanded="false">
                        Associar um sub-gestor (parelha)
                     </a>
                     </h4>
                  </div>
                  <div id="collapse9" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/ASS_GESTOR.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif
            @if(Auth::check() && $EXPRESS_TOKEN_GENERATION || Auth::check() && $CONFIRM_UNIT_CHANGE || Auth::check() && $ACCEPT_NEW_MEMBERS)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse10" class="collapsed" aria-expanded="false">
                        Confirmar contas de utilizadores
                     </a>
                     </h4>
                  </div>
                  <div id="collapse10" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/CONF_UTILIZADORES.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>         
            @endif

            @if(Auth::check() && $EDIT_DEADLINES_TAG || Auth::check() && $EDIT_DEADLINES_UNTAG)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse11" class="collapsed" aria-expanded="false">
                       Alterar definições da plataforma
                     </a>
                     </h4>
                  </div>
                  <div id="collapse11" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/ALT_DEFINIÇOES.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            @if(Auth::check() && $GENERAL_WARNING_CREATION)
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse12" class="collapsed" aria-expanded="false">
                       Criar aviso geral de plataforma
                     </a>
                     </h4>
                  </div>
                  <div id="collapse12" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/CRIAR_AVISO.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif


            @if(Auth::check() && ($DELETE_MEMBERS || $BLOCK_MEMBERS || $USERS_NEED_FATUR))
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse13" class="collapsed" aria-expanded="false">
                       Remover ou bloquear uma conta, ou registar refeições a numerário
                     </a>
                     </h4>
                  </div>
                  <div id="collapse13" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                     <h6 style="margin-bottom: 1.5rem;">Apenas para utilizadores com as permissões necessárias.</h6>
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/DEL_BLOCK_TAGOBLIG.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse14" class="collapsed" aria-expanded="false">
                       Ver e alterar o seu perfil
                     </a>
                     </h4>
                  </div>
                  <div id="collapse14" class="panel-collapse in collapse" style="">
                     <div class="card-body">
                        <video width="960px" height="540px" controls muted>
                        <source src="{{asset('assets/tutorial/VER_MINHA_INFO.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                     </div>
                  </div>
               </div>
            </div>
            <div style="height: 1.5rem;"></div>
            <div id="accordion"><div class="card card-secondary">
                  <div class="card-header">
                     <h4 class="card-title">
                     <a data-toggle="collapse" data-parent="#accordion" href="#collapse15" class="collapsed" aria-expanded="false">
                       Instalar e utilizar aplicação Quiosque
                     </a>
                     </h4>
                  </div>
                  <div id="collapse15" class="panel-collapse in collapse" style="">
                     <div class="card-body" style="max-height: none !important;">
                     <h6 style="margin-bottom: 1.5rem;">Instalar:</h6>
                        <video width="960px" height="540px" controls muted>                        
                           <source src="{{asset('assets/tutorial/INSTALAR_APP_QUIOSQUE.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                        <h6 style="margin-bottom: 1.5rem;margin-top: 1.5rem;">Utilizar:</h6>
                        <video width="960px" height="540px" controls muted>
                           <source src="{{asset('assets/tutorial/USAR_APP_QUIOSQUE.mp4')}}" type="video/mp4">
                           O seu browser não suporta este tipo de conteúdo.
                        </video>
                        <h6 style="margin-top: 1.5rem;">Pode fazer download da aplicação <a href="http://10.102.21.45/client/" target="_blank">aqui</a>.</h6>
                        
                     </div>
                  </div>
               </div>
            </div>
            <h6 style="margin-bottom: 0.5rem; margin-top: 2.5rem;">Se mesmo depois de assistir a estes tutoriais, encontrar alguma dificuldade, por favor entre em contacto com o HELPDESK
               através de <a href="mailto:cpess.unap.informatica@exercito.pt?subject=Apoio Portal Alimentação&body=DEBUG INFO :: NIM: {{ Auth::user()->id}} || _TOKEN: {{ csrf_token() }} || UserPerm: {{ Auth::user()->user_type}}\{{ Auth::user()->user_permission }} || LOCK: {{ Auth::user()->lock }} || TagOblig: @if(Auth::user()->isTagOblig==null)NONE @else {{ Auth::user()->isTagOblig }} @endif">email</a>.
            </h6>
         </div>                  
      <!-- /.card-body -->
   </div>
   
</div>
@endsection
