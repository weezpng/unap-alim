@extends('layout.master')
@section('extra-links')
<script src="{{asset('adminlte/plugins/fullcalendar/main.js')}}"></script>
<link rel="stylesheet" href="{{asset('adminlte/plugins/fullcalendar/main.css')}}">
@endsection
@section('title','Página inicial')
@section('breadcrumb')
<li class="breadcrumb-item active">Página inicial</li>
@endsection
@section('page-content')
@if (Auth::check())
@if($needsParent)
  @if (Auth::check() && Auth::user()->lock=='N')
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Associar a gestor</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p style="margin-bottom: 20px;">
              Selecione da lista abaixo um gestor disponivel para si:
            </p>
            <table class="table table-striped projects">
                <tbody>
                    @foreach($users as $user)
                    <tr>
                          <td>
                            <i class="fas fa-user-plus"></i>&nbsp&nbsp
                            {{ $user['id'] }}
                          </td>
                          <td>
                            {{ $user['posto'] }}
                            @if ($user['seccao'])
                              <br />
                              <span class="text-muted text-center">{{ $user['seccao'] }}</span>
                            @endif
                          </td>
                          <td class="uppercase-only">
                            {{ $user['name'] }}
                            @if ($user['descriptor'])
                              <br />
                              <span class="text-muted text-center">
                                  {{\Illuminate\Support\Str::limit($user['descriptor'], 35, $end='...')}}
                              </span>
                            @endif
                          </td>
                          <td>
                            <form method="POST" action="{{route('profile.association.request')}}">
                              @csrf
                              <input type="hidden" id="nim" name="nim" value="{{$user['id']}}"></input>
                              <button type="submit" class="btn btn-sm btn-dark marcar-ref-btn" style="float: right;">Associar</button>
                            </form>
                          </td>
                      </tr>
                    @endforeach
                </tbody>
              </table>
        </div>
      </div>
    </div>
  </div>
@endif
@endif


@if (Auth::check() && Auth::user()->lock=='N')
<div class="col-md-5">
  <div class="col-md-12">
     <div class="card @if (Auth::check() && Auth::user()->lock=='N')  @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif card-outline"
       @if($needsParent)
         style="overflow-y: auto;"
       @else
         @if (auth()->user()->isAccountChildren=='WAITING')
            style="height: 20vh; overflow-y: auto;"
         @else
            style="height: 40vh; overflow-y: auto;"
         @endif

       @endif
         >
        <div class="card-header border-0">
           <h3 class="card-title">
              <b>Bem-vindo<div style="display: inline; text-transform: capitalize !important;">, {{ auth()->user()->name }}</div></b>
           </h3>
        </div>
        <div class="card-body">
           Para marcar refeições, clique <a href="{{ route('marcacao.index') }}">aqui</a>.
           <br>Para consultar as refeições já marcadas, clique <a href="{{ route('marcacao.minhas') }}"> aqui</a>.
           <br>Para consultar as entradas de refeições tomadas, clique <a href="{{ route('perfil.quiosque') }}"> aqui</a>.
           <br>Se tem duvidas de como utilizar esta aplicação, pode assistir os vídeos de tutorial <a href="{{ route('help.faq') }}"> aqui</a>.<br><br>
           Para obter o código QR de entrada no refeitório, clique <a data-toggle="modal" data-target="#qrModal" href="#">aqui</a>.
           <br><br>Para navegar no site, utiliza a barra de navegação à esquerda.
        </div>
     </div>
  </div>
  @if($needsParent)
    <div class="col-md-12">
       <div class="card @if (Auth::check() && Auth::user()->lock=='N')  @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif card-outline" style="height: auto;">
          <div class="card-header border-0">
             <h3 class="card-title">Problema de conta</h3>
          </div>
          <div class="card-body">
            <div style="height: 60px;">
              A sua conta não se encontra associada a nenhum gestor.<br>
              Clique abaixo para associar.
            </div>

               <button type="submit" class="btn btn-sm btn-dark marcar-ref-btn"  data-toggle="modal" data-target="#exampleModal" style="float: right; width: 10rem !important;">Associar a um gestor</button>
          </div>
       </div>
    </div>
  @endif

  @if($msgs!=null)
  <h4 style="padding-left: 7.5px;" class="slide-in-blurred-top">Avisos</h4>
    @foreach($msgs as $msg)
    <div class="col-md-12">
       <div class="card @if (Auth::check() && Auth::user()->lock=='N')  @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif card-outline" style="height: auto;">
          <div class="card-header border-0">

              @php
                $message_title = $msg['title'];
                $message_title =  str_replace('href', '', $message_title);
                $message_title =  str_replace('class', '', $message_title);
                $message_title =  str_replace('style', '', $message_title);
                $message_title =  str_replace('name', '', $message_title);
                $message_title =  str_replace('id', '', $message_title);
                $message_title =  str_replace('script', '', $message_title);
                $message_title =  str_replace('link', '', $message_title);
                $message_title =  str_replace('iframe', '', $message_title);
                $message_title =  str_replace('body', '', $message_title);
                $message_title =  str_replace('head', '', $message_title);
                $message_title =  str_replace('html', '', $message_title);
                $message_title =  str_replace('header', '', $message_title);
                $message_title =  str_replace('input', '', $message_title);
                $message_title =  str_replace('button', '', $message_title);
                $message_title =  str_replace('meta', '', $message_title);
              @endphp
             <h3 class="card-title"><b>{!! $message_title !!}</b></h3>
          </div>
          <div class="card-body">
            <div>
              @php
                $message = $msg['message'];
                $user_name = Auth::user()->name;
                $user_nim = Auth::user()->id;
                $user_email = Auth::user()->email;
                $user_unidade = Auth::user()->unidade;
                $user_posto = Auth::user()->posto;
                $user_type = Auth::user()->user_type;
                $user_local_pref = Auth::user()->localRefPref;
                $user_local_unidade = \App\Models\unap_unidades::where('slug', Auth::user()->unidade)->value('local');
                $user_local_unidade = \App\Models\locaisref::where('refName', $user_local_unidade)->value('localName');
                $user_count_marcacoes =  \App\Models\marcacaotable::where('NIM', Auth::user()->id)->where('data_marcacao', '>=', date('Y-m-d'))->count();

                if(Auth::user()->user_permission=="ALIM") $user_perm = "Permissões de SecAlimentação";
                elseif(Auth::user()->user_permission=="PESS") $user_perm = "Permissões de SecPessoal";
                elseif(Auth::user()->user_permission=="LOG") $user_perm = "Permissões de SecLogística";
                elseif(Auth::user()->user_permission=="MESSES") $user_perm = "Permissões de Messes";
                elseif(Auth::user()->user_permission=="GCSEL") $user_perm = "Permissões de GabClSel";
                elseif(Auth::user()->user_permission=="CCS") $user_perm = "Permissões de CCS";
                elseif(Auth::user()->user_permission=="TUDO") $user_perm = "Permissões totais";
                else $user_perm = "Sem permissões";

                $message =  str_replace('%user_name%', $user_name, $message);
                $message =  str_replace('%user_nim%', $user_nim, $message);
                $message =  str_replace('%user_unidade%', $user_unidade, $message);
                $message =  str_replace('%user_posto%', $user_posto, $message);
                $message =  str_replace('%user_type%', $user_type, $message);
                $message =  str_replace('%user_local_pref%', $user_local_pref, $message);
                $message =  str_replace('%user_unidade_local%', $user_local_unidade, $message);
                $message =  str_replace('%user_count_marcacoes%', $user_count_marcacoes, $message);
                $message =  str_replace('%user_perm%', $user_perm, $message);
                $message =  str_replace('%user_email%', $user_email, $message);
                $message =  str_replace('href', '', $message);
                $message =  str_replace('class', '', $message);
                $message =  str_replace('style', '', $message);
                $message =  str_replace('name', '', $message);
                $message =  str_replace('id', '', $message);
                $message =  str_replace('script', '', $message);
                $message =  str_replace('link', '', $message);
                $message =  str_replace('iframe', '', $message);
                $message =  str_replace('body', '', $message);
                $message =  str_replace('head', '', $message);
                $message =  str_replace('html', '', $message);
                $message =  str_replace('header', '', $message);
                $message =  str_replace('input', '', $message);
                $message =  str_replace('button', '', $message);
                $message =  str_replace('meta', '', $message);
              @endphp
              {!! $message !!}
            </div>
            @if( $msg['link']!= null)
              <a href="{{ route($msg['link']) }}">
                <button class="btn btn-sm btn-dark marcar-ref-btn" style="float: right; width: 10rem !important; margin-top: 1rem;">Abrir ligação</button>
               </a>
            @endif
          </div>
       </div>
    </div>
    @endforeach
  @endif

  @if(auth()->user()->isAccountChildren=='WAITING')
    <div class="col-md-12">
       <div class="card @if (Auth::check() && Auth::user()->lock=='N')  @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif card-outline" style="height: 18.25vh;">
          <div class="card-header border-0">
             <h3 class="card-title"><b>Associação a gestor</b></h3>
          </div>
          <div class="card-body">
            <div style="height: 60px;">
              O seu pedido de associação ao gestor NIM <b>{{ auth()->user()->accountChildrenOf }}</b> ainda está pendente.<br />
              Por favor aguarde resposta.
            </div>
            <a href="{{ route('profile.association.cancel') }}">
              <button type="submit" class="btn btn-sm btn-dark marcar-ref-btn" style="float: right;">Cancelar pedido</button>
            </a>
          </div>
       </div>
    </div>
  @endif
</div>
<div class="col-md-7">
  <div class="col-md-12">
     <div class="card @if (Auth::check() && Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif" style="height: max-content; overflow-y: auto;">
        <div class="card-header border-0">
           <h3 class="card-title"><b>Minha conta</b></h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4" style="border-right: 1px solid rgba(0,0,0,.1);">
                 <strong><i class="fas fa-map-marker-alt mr-1"></i> Unidade</strong>
                 <p class="text-muted">
                   {{ $unidade['name'] }}
                 </p>
                  <strong><i class="fas fa-map-marker-alt mr-1"></i> Local de Refeição (Serviço)</strong>
                 <p class="text-muted">
                   {{ $local_ref['localName'] }}
                 </p>

               @if (auth()->user()->trocarUnidade)
                 <strong><i class="fas fa-paper-plane mr-1"></i> Troca de unidade pendente</strong>
                 <p class="text-muted">{{ auth()->user()->trocarUnidade }}</p>
               @endif

               @if (auth()->user()->seccao)
                 <strong><i class="fa fa-map-marker mr-1"></i> Secção</strong>
                 <p class="text-muted">{{ auth()->user()->seccao }}</p>
               @endif

               @if (auth()->user()->descriptor)
                 <strong><i class="fa fa-comment mr-1"></i> Função</strong>
                 <p class="text-muted">{{ auth()->user()->descriptor }}</p>
               @endif

               @if (auth()->user()->isTagOblig)
                 <strong><i class="fas fa-user-lock mr-1"></i> &nbsp;A receber refeições a dinheiro.</strong>
               @endif


            </div>
            <div class="col-md-4" style="padding-left: 35px; border-right: 1px solid rgba(0,0,0,.1);">
              @if (auth()->user()->email)
                <strong><i class="fa fa-envelope mr-1"></i> Email</strong>
                <a href="mailto:{{ auth()->user()->email }}">
                  <p class="text-muted">{{ auth()->user()->email }}</p>
                </a>
              @endif

             @if (auth()->user()->telf)
               <strong><i class="fa fa-phone-square mr-1"></i> Extensão telefónica</strong>
               <p class="text-muted">{{ auth()->user()->telf }}</p>
             @endif
           </div>
           <div class="col-md-4" style="padding-left: 35px;">
             <strong><i class="fas fa-book mr-1"></i> Marcações actuais</strong>
             <p class="text-muted">{{ $howManyMarcacoes }}
               @if ($howManyMarcacoes==1)
                 marcação
               @else
                 marcações
               @endif
           </p>
           @if (Auth::user()->posto=="ASS.TEC." || Auth::user()->posto=="ASS.OP." || Auth::user()->posto=="TEC.SUP."
           || auth()->user()->posto == "ENC.OP." || auth()->user()->posto == "TIA" || auth()->user()->posto == "TIG.1" || auth()->user()->posto == "TIE")
            <strong><i class="fas fa-check-double mr-1"></i> Confirmações actuais</strong>
           <p class="text-muted">{{ $howManyConf }}
             @if ($howManyConf==1)
               confirmação
             @else
               confirmações
             @endif
           </p>
           @endif
           @if ($partner)
              <span style="font-size: 1.25rem; margin-top: 1rem; margin-bottom: .75rem; display: inline-block; width: 100%;"><i class="fa fa-handshake "></i> &nbsp;<strong style="font-size: 1.15rem;">Parceiro</strong></span>
              <strong>Posto</strong>
              <p class="text-muted" style="margin-bottom: .5rem;">{{ $partner['posto'] }}</p>
              <strong>Nome</strong>
              <p class="text-muted" style="margin-bottom: .5rem;">{{ $partner['name'] }}</p>
              <strong>Email</strong>
              <a href="mailto:{{ $partner['email'] }}">
                <p class="text-muted" style="margin-bottom: .5rem;">{{ $partner['email'] }}</p>
              </a>
           @endif
           </div>
          </div>
          @if((isset($REFS) && $REFS!=null) && (isset($REFS_LAST) && $REFS_LAST!=null))
            <h6><br><b>Minhas estatísticas</b><br><span class="text-sm">Esta semana</span></h6>
            <canvas id="stackedBarChart" style="min-width: 100%; max-height: 20vh;"></canvas><br>
            <span class="text-sm">Semana passada</span>
            <canvas id="stackedBarChart2" style="min-width: 100%; max-height: 20vh;"></canvas>
          @endif
        </div>
     </div>
  </div>
</div>
@if(auth()->user()->posto===NULL || auth()->user()->localRefPref===NULL || auth()->user()->email===NULL )
<div class="col-md-6">
  <div class="col-md-10">
     <div class="card @if (Auth::check() && Auth::user()->lock=='N') card-dark @else card-danger @endif card-outline">
        <div class="card-header border-0">
           <h3 class="card-title"><b>Problema de conta</b></h3>
        </div>
        <div class="card-body">
           @if(auth()->user()->posto===NULL && auth()->user()->localRefPref===NULL && auth()->user()->email===NULL )
               Para completar o registo, falta preencher o seu <strong>posto</strong>, o seu <strong>EMAIL</strong> e o local preferencial de marcação de refeição.<br>
               Pode faze-lo <a href="{{ route('profile.index') }}">aqui.</a>
           @else
             @if(auth()->user()->posto===NULL)
               Para completar o registo, falta preencher o seu <strong>posto</strong>.<br>
               Pode faze-lo <a href="{{ route('profile.index') }}">aqui.</a>
             @endif
             @if(auth()->user()->email===NULL)
               Para completar o registo, falta preencher o seu <strong>email</strong>.<br>
               Pode faze-lo <a href="{{ route('profile.index') }}">aqui.</a>
             @endif
             @if(auth()->user()->localRefPref===NULL)
               Para completar o registo, falta preencher o local preferencial de refeição.<br>
               Pode faze-lo <a href="{{ route('profile.index') }}">aqui.</a>
             @endif
           @endif
        </div>
     </div>
  </div>
</div>
@endif
@else
  @if (Auth::check())
    <div class="col-md-12">
       <div class="card @if (Auth::user()->lock=='N') card-dark @else card-danger @endif" style="height: 40vh; overflow-y: auto;">
          <div class="card-header border-0">
             <h3 class="card-title"><b>Minha conta</b></h3>
          </div>
          <div class="card-body">
             <div class="col-md-2">
                @if (auth()->user()->lock=='Y')
                  <strong><i class="fas fa-ban mr-1"></i> Conta bloqueada</strong>
                  <hr>
                @endif
                  <strong><i class="fas fa-map-marker-alt mr-1"></i> Unidade</strong>
                  <p class="text-muted">{{ auth()->user()->unidade }}</p>
                @if (auth()->user()->trocarUnidade)
                  <hr>
                  <strong><i class="fas fa-map-marker-alt mr-1"></i> Troca de unidade pendente</strong>
                  <p class="text-muted">{{ auth()->user()->trocarUnidade }}</p>
                @endif
                <hr>
                <strong><i class="fa fa-envelope mr-1"></i> Email</strong>
                <a href="mailto:{{ auth()->user()->email }}">
                <p class="text-muted">{{ auth()->user()->email }}</p>
                </a>
                <hr>
                <strong><i class="fa fa-phone-square mr-1"></i> Extensão telefónica</strong>
                <p class="text-muted">{{ auth()->user()->telf }}</p>
             </div>
          </div>
       </div>
    </div>
    @endif
@endif


@else
  <div class="col-md-12">
     <div class="card card-dark card-outline">
        <div class="card-header border-0">
           <h3 class="card-title">
              <b>Bem-vindo</b>
           </h3>
        </div>
        <div class="card-body">
           Para poder utilizar esta aplicação, tem de <a href="{{ route('login') }}">iniciar sessão</a>.
           <br>Se não tem conta, pode criar <a href="{{ route('register') }}">aqui</a>.
           <br><br>Clique <a href="{{ route('ementa.index') }}">aqui</a> para consultar a ementa.
        </div>
     </div>
  </div>

  @if($msgs_no_auth!=null)
    <h4 style="padding-left: 7.5px;" class="slide-in-blurred-top">Avisos</h4>
    @foreach($msgs_no_auth as $msg)
    <div class="col-md-12">
    <div class="card card-dark card-outline">
          <div class="card-header border-0">

          @php
            $message_title = $msg['title'];
            $message_title =  str_replace('href', '', $message_title);
            $message_title =  str_replace('class', '', $message_title);
            $message_title =  str_replace('style', '', $message_title);
            $message_title =  str_replace('name', '', $message_title);
            $message_title =  str_replace('id', '', $message_title);
            $message_title =  str_replace('script', '', $message_title);
            $message_title =  str_replace('link', '', $message_title);
            $message_title =  str_replace('iframe', '', $message_title);
            $message_title =  str_replace('body', '', $message_title);
            $message_title =  str_replace('head', '', $message_title);
            $message_title =  str_replace('html', '', $message_title);
            $message_title =  str_replace('header', '', $message_title);
            $message_title =  str_replace('input', '', $message_title);
            $message_title =  str_replace('button', '', $message_title);
            $message_title =  str_replace('meta', '', $message_title);
          @endphp
             <h3 class="card-title">{!! $message_title !!}</h3>
          </div>
          <div class="card-body">
            <div style="height: 70px;">

            @php
                $message = $msg['message'];
                $message =  str_replace('href', '', $message);
                $message =  str_replace('class', '', $message);
                $message =  str_replace('style', '', $message);
                $message =  str_replace('name', '', $message);
                $message =  str_replace('id', '', $message);
                $message =  str_replace('script', '', $message);
                $message =  str_replace('link', '', $message);
                $message =  str_replace('iframe', '', $message);
                $message =  str_replace('body', '', $message);
                $message =  str_replace('head', '', $message);
                $message =  str_replace('html', '', $message);
                $message =  str_replace('header', '', $message);
                $message =  str_replace('input', '', $message);
                $message =  str_replace('button', '', $message);
                $message =  str_replace('meta', '', $message);
              @endphp
              {!! $message !!}
            </div>
            @if( $msg['link']!= null)
              <a href="{{ route($msg['link']) }}">
                <button class="btn btn-sm btn-dark marcar-ref-btn" style="float: right; width: 10rem !important;">Abrir ligação</button>
               </a>
            @endif
          </div>
       </div>
    </div>
    @endforeach
  @endif
@endif
@endsection

@section('extra-scripts')
  @if (Auth::check())
  <script src="{{asset('adminlte/plugins/chart.js/Chart.min.js')}}"></script>
  <script>
  @if(isset($REFS) && $REFS!=null)
  var ctx = document.getElementById('stackedBarChart');
  var MeSeData = {
         labels: [
             @foreach($REFS as $data)
             @php
                $curr_data= $data['DATA'];
                $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                $mes_index = date('m', strtotime($curr_data));
             @endphp
               "{{ date('d', strtotime($curr_data)) }} {{ $mes[($mes_index - 1)] }}",

             @endforeach
         ],
         datasets: [
           {
               label: "Marcações",
               data: [
                @foreach($REFS as $curr)
                  "{{ $curr['TAGS'] }}",
                 @endforeach
               ],
               backgroundColor: [@foreach($REFS as $NULLABLE)"#629dbf",@endforeach],
               hoverBackgroundColor: [@foreach($REFS as $NULLABLE)"#4f819e",@endforeach]
           },
           {
               label: "Consumidas",
               data: [
                 @foreach($REFS as $curr)
                  "{{ $curr['CONF'] }}",
                 @endforeach
               ],
               backgroundColor: [@foreach($REFS as $NULLABLE) "#68b073", @endforeach],
               hoverBackgroundColor: [@foreach($REFS as $NULLABLE)"#598f61", @endforeach]
           },
           ]
     };

  var MeSeChart = new Chart(ctx, {
     type: 'bar',
     data: MeSeData,
     options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
        yAxes: [{
          ticks: {
          	min: 0,
            max: 3,
            stepSize: 1
          },
        }],
      },
  }
  });
  @endif

  @if(isset($REFS_LAST) && $REFS_LAST!=null)
  var ctx = document.getElementById('stackedBarChart2');
  var MeSeData = {
         labels: [
             @foreach($REFS_LAST as $data)
             @php
                $curr_data= $data['DATA'];
                $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                $mes_index = date('m', strtotime($curr_data));
             @endphp
               "{{ date('d', strtotime($curr_data)) }} {{ $mes[($mes_index - 1)] }}",

             @endforeach
         ],
         datasets: [
           {
               label: "Marcações",
               data: [
                @foreach($REFS_LAST as $curr)
                  "{{ $curr['TAGS'] }}",
                 @endforeach
               ],
               backgroundColor: [@foreach($REFS_LAST as $NULLABLE)"#629dbf",@endforeach],
               hoverBackgroundColor: [@foreach($REFS_LAST as $NULLABLE)"#4f819e",@endforeach]
           },
           {
               label: "Consumidas",
               data: [
                 @foreach($REFS_LAST as $curr)
                  "{{ $curr['CONF'] }}",
                 @endforeach
               ],
               backgroundColor: [@foreach($REFS_LAST as $NULLABLE) "#68b073", @endforeach],
               hoverBackgroundColor: [@foreach($REFS_LAST as $NULLABLE)"#598f61", @endforeach]
           },
           ]
     };

  var MeSeChart = new Chart(ctx, {
     type: 'bar',
     data: MeSeData,
     options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
        yAxes: [{
          ticks: {
          	min: 0,
            max: 3,
            stepSize: 1
          },
        }],
      },
  }
  });
  @endif
  </script>
@endif
@endsection
