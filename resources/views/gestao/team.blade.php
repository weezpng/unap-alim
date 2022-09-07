@extends('layout.master')
@section('title','Membros de equipa')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Minha equipa</li>
<li class="breadcrumb-item active">Membros</li>
@endsection
@section('page-content')

  <div class="modal puff-in-center" id="errorAddingModal" tabindex="-1" role="dialog" aria-labelledby="errorAddingModal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="errorAddingTitle" name="errorAddingTitle"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body" style="padding: 1.25rem !important;">
              <p id="errorAddingText" name="errorAddingText"></p>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" data-dismiss="modal">Fechar</button>
           </div>
        </div>
     </div>
  </div>

  <div class="modal puff-in-center" id="SendPing" tabindex="-1" role="dialog" aria-labelledby="SendPingModal" aria-hidden="true">
     <div class=" modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-paper-plane"></i>&nbsp; Enviar <i>ping</i></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <form action="{{route('gestao.hospedes.new')}}" method="POST" enctype="multipart/form-data" id="SendPingForm">
              <div class="modal-body">
                 @csrf
                 <div class="form-group row">
                    <label for="inputName" class="col-sm-3 col-form-label">Titulo</label>
                    <div class="col-sm-9">
                       <input required type="text" class="form-control" id="inputTitle" name="inputTitle" placeholder="Titulo">
                    </div>
                 </div>

                 <div class="form-group row">
                    <label for="inputName" class="col-sm-3 col-form-label">Mensagem</label>
                    <div class="col-sm-9">
                       <textarea required type="text" class="form-control" id="inputMessagem" name="inputMessagem" rows="5" placeholder="Mensagem"></textarea>
                    </div>
                 </div>
              </div>
              <div class="modal-footer">
                 <button type="button" onclick="SendPing();" class="btn btn-primary" style="min-width: 6rem;">Enviar</button>
                 <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
              </div>
           </form>
        </div>
     </div>
  </div>

<div class="col-12">
  <div class="row">
      @if (isset($team_members) && $team_members!=null)
        @foreach ($team_members as $key => $member)
          <div class="col-md-4">

              <div class="card card-widget widget-user shadow">

                  <div class="widget-user-header bg-info" @if($member['id']==Auth::user()->id) style="background-color: #c9800de3 !important; "@endif>
                      <h3 class="widget-user-username uppercase-only"><strong>{{ $member['name'] }}</strong></h3>
                      <h5 class="widget-user-desc" style="font-weight: 300;">{{ $member['descriptor'] }}</h5>
                  </div>

                  <div class="widget-user-image">
                    @php
                        $NIM = $member['id'];
                        while ((strlen((string)$NIM)) < 8) {
                            $NIM = 0 . (string)$NIM;
                        }
                        $filename = "assets/profiles/".$NIM . ".JPG";
                        $filename_png = "assets/profiles/".$NIM . ".PNG";
                      @endphp

                      @if (file_exists(public_path($filename)))
                            <img src="{{ asset($filename) }}" class="img-circle elevation-2" alt="User Image" style="height: 90px;">
                      @elseif(file_exists(public_path($filename_png)))
                        <img src="{{ asset($filename_png) }}" class="img-circle elevation-2" alt="User Image" style="height: 90px;">
                      @else
                          @php
                          $NIM = Auth::user()->id;
                          while ((strlen((string)$NIM)) < 8) {
                              $NIM = 0 . (string)$NIM;
                          }
                            $filename2 = "https://cpes-wise2/Unidades/Fotos/". $NIM . ".JPG";
                          @endphp
                         <img src="{{ asset($filename2) }}" class="img-circle elevation-2" alt="User Image" style="height: 90px;">
                    @endif
                    <div class="image__overlay image__overlay--primary" @if($member['id']==Auth::user()->id) style="background-color: #c9800dbf !important; position: absolute; top: 4%; left: 4%; width: 93%; height: 93%;" @else style="background-color: #3498dbc2!important; position: absolute; top: 4%; left: 4%; width: 93%; height: 93%;" @endif>
                        <a @if($member['id']==Auth::user()->id) href="{{ route('profile.index') }}" @else href="{{ route('user.profile', $member['id']) }}" @endif style="color: white;">
                        <p class="image__description-link uppercase-only" style="margin: 0 auto !important; cursor: pointer;">
                            @if($member['id']==Auth::user()->id) Meu perfil @else Ver perfil @endif

                            <!-- @if ($member['posto'] != "ASS.OP." && $member['posto'] != "ENC.OP."
                            && $member['posto'] != "TIA" && $member['posto'] != "TIG.1"
                            && $member['posto'] != "TIE" && $member['posto'] != "ASS.TEC."
                            && $member['posto'] != "ASS.OP."
                            && $member['posto'] != "TEC.SUP." &&
                            $member['posto'] != ""
                            && $member['posto'] != "SOLDADO" )
                            @if (Auth::check() && Auth::user()->dark_mode=='Y')
                            @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" . $member['posto'] . ".png"; @endphp
                            @else
                            @php $filename2 = "assets/icons/postos/TRANSPARENT/" .  $member['posto'] . ".png"; @endphp
                            @endif
                            <img style="object-fit: scale-down; padding: 5px; height: 65px; width: 65px;" src="{{ asset($filename2) }}">
                            @else
                            {{ $member['posto'] }}
                            @endif -->
                        </p>
                        </a>
                    </div>
                  </div>

                  <div class="card-footer" @if($member['id']==Auth::user()->id) @endif>
                      <div class="row">
                          <div class="col-sm-4 border-right">
                              <div class="description-block">
                                  <span class="description-text">POSTO</span>
                                  <h5 class="description-header">{{ $member['posto'] }}</h5>
                              </div>
                          </div>
                          <div class="col-sm-4 border-right">
                              <div class="description-block">
                                <span class="description-text">PERMISSÕES</span>
                                <h5 class="description-header">
                                  @if ($member['user_type']=="ADMIN")
                                    ADMINISTRADOR
                                  @elseif ($member['user_type']=="POC")
                                    POC
                                  @elseif ($member['user_type']=="USER")
                                    UTILIZADOR
                                  @else
                                    {{ $member['user_type'] }}
                                  @endif
                                </h5>
                              </div>
                          </div>
                          <div class="col-sm-4">
                              <div class="description-block">
                                <span class="description-text">ULTIMO ACESSO</span>
                                <h5 class="description-header">
                                    @if ($member['last_login'])
                                      {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $member['last_login'])->format('d/m/Y') }}
                                      <span style="font-weight: 400;">({{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $member['last_login'])->format('H:i') }})</span>
                                    @else
                                      NUNCA
                                    @endif
                                </h5>
                              </div>
                          </div>
                          <div class="col-sm-12">
                            @if($member['id']!=Auth::user()->id)
                              <a id="Pinger" data-id="{{ $member['id'] }}" data-trg="{{ $member['posto'] }} {{ $member['name'] }}" data-target="#SendPing" data-toggle="modal">
                                <button type="button" class="btn btn-dark" style="margin-top: .75rem;width: 100%;border: 0;">Enviar <i>ping</i></button>
                              </a>
                              <a @if ($member['email']) href="mailto:{{ $member['email'] }}" @else href="mailto:{{ $member['id'] }}" @endif >
                                <button type="button" class="btn btn-dark" style="margin-top: .25rem;width: 100%;border: 0;">Contactar por email</button>
                              </a>
                            @else
                              <center><h6 style="margin: 15px auto;">  </h6></center>
                            @endif
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        @endforeach
      @endif
  </div>
</div>
@endsection

@section('extra-scripts')
  <script>
    var U_ID;
    var DESCRIPTOR;
     $(document).on('click','#Pinger',function(){
       U_ID = $(this).attr('data-id');
       DESCRIPTOR = $(this).attr('data-trg');
     });

     function SendPing(){
        var data = $("#SendPingForm").serializeArray();
        $.ajax({
            url: "{{route('gestão.equipa.ping-user')}}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "user_id": U_ID,
                data: data,
            },
            success: function(response) {
                 $("#SendPing").modal('hide');
                if (response) {
                    if (response != 'success') {
                        document.getElementById("errorAddingTitle").innerHTML = "Erro";
                        document.getElementById("errorAddingText").innerHTML = response;
                        $("#errorAddingModal").modal()
                    } else {
                      $(document).Toasts('create', {
                        title: "Enviado!",
                        subtitle: "",
                        body: "Foi enviado o ping para o " + DESCRIPTOR,
                        icon: "fas fa-paper-plane",
                        autohide: true,
                        autoremove: true,
                        delay: 3500,
                        class: "toast-not",
                      });
                    }
                }
            }
        });
      }
   </script>
@endsection
