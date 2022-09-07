@extends('layout.master')
@section('title','Perfil')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Perfil</li>
<li class="breadcrumb-item active uppercase-only">{{ $partner['name'] }}</li>
@endsection
@section('page-content')
<div class="col-md-3">
   <!-- Profile Image -->
   <div class="card @if (Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif card-outline">
      <div class="card-body box-profile" style="padding-bottom: 10px !important; padding-left: 10px !important; padding-right: 10px !important;">
        @php
          $NIM = $partner['id'];
          while ((strlen((string)$NIM)) < 8) {
              $NIM = 0 . (string)$NIM;
          }
          $filename = "assets/profiles/".$NIM . ".PNG";
          $filename_jpg = "assets/profiles/".$NIM . ".JPG";
        @endphp
        @if (file_exists(public_path($filename)))
          <div class="text-center">
             <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;">
          </div>
        @elseif (file_exists(public_path($filename_jpg)))
          <div class="text-center">
             <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename_jpg) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;">
          </div>
        @else
          @php $filename2 = "https://cpes-wise2/Unidades/Fotos/". $NIM . ".JPG"; @endphp
          <div class="text-center">
             <img class="profile-user-img img-fluid img-circle" src="{{ asset($filename2) }}" alt="User profile picture" style="border: 2px solid #6c757d !important;">
          </div>
        @endif
         <h3 class="profile-username text-center uppercase-only">{{ $partner['name'] }}</h3>
         <p class="text-muted text-center">{{ $partner['posto'] }}
         @if ($partner['descriptor'])
              <br><span class="uppercase-only">{{ $partner['descriptor'] }}</span>
         @endif
      </div>
      <!-- /.card-body -->
   </div>
   <!-- /.card -->
   <!-- About Me Box -->
   <div class="card @if (Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      <div class="card-header">
         <h3 class="card-title">Acerca</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
         <strong><i class="fas fa-id-badge mr-1"></i> NIM</strong>
         <p class="text-muted">
            {{ $partner['id'] }}
         </p>
         <hr>
         <strong><i class="fas fa-map-marker-alt mr-1"></i> Unidade</strong>
         <p class="text-muted">{{ $partner['unidade'] }}</p>
         <hr>
         <strong><i class="fas fa-map-marker mr-1"></i> Secção</strong>
         <p class="text-muted">{{ $partner['seccao'] }}</p>
         <hr>
         <strong><i class="fa fa-envelope mr-1"></i> Email</strong>
         <a href="mailto:{{ $partner['email'] }}">
            <p class="text-muted">{{ $partner['email'] }}</p>
         </a>
         <hr>
         <strong><i class="fa fa-phone-square mr-1"></i> Extensão telefónica</strong>
         <p class="text-muted">{{ $partner['telf'] }}</p>
         <hr>
         <strong><i class="far fa-user-circle mr-1"></i> Tipo de utilizador</strong>
         <p class="text-muted">
            {{ $partner['user_type'] }}
         </p>
      </div>
      <!-- /.card-body -->
   </div>
   <!-- /.card -->
</div>
<!-- /.col -->
<div class="col-md-9">
   <div class="card card-outline @if ($partner['lock']=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      @if ($partner['lock']=='N')
      <div class="card-header p-2">
         <ul class="nav nav-pills" >
            <li class="nav-item"><a class="nav-link active" @if ($partner['lock']=='Y') style="background-color: #d14351 !important;" @endif href="#marca" data-toggle="tab">Marcações</a></li>
            <li class="nav-item"><a class="nav-link" @if ($partner['lock']=='Y') style="background-color: #d14351 !important;" @endif href="#ferramentas" data-toggle="tab">Ferramentas</a></li>
         </ul>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <div class="tab-content">
           <div class="tab-pane swing-in-left-fwd active" id="marca">
             <div class="card-body p-0">
                @if (!empty($ementa))
                <table class="table table-striped projects">
                   <thead>
                      <tr>
                         <th style="width: 1%">
                         </th>
                         <th style="width: 20%">
                            Refeição
                         </th>
                         <th style="width: 40%">
                            Ementa
                         </th>
                         <th>
                            Local
                         </th>
                         <th>
                            Data
                         </th>
                      </tr>
                   </thead>
                   <tbody>
                      @php
                      $today = date("Y-m-d");
                      @endphp
                      @foreach($marcaçoes as $marcaçao)
                      @php $refeiçaoEmMarcação = $ementa[$marcaçao['id']];
                      @endphp
                      @if($marcaçao['data_marcacao']>=$today)
                      <tr>
                         <td>
                            <i class="fas fa-check nav-icon"></i>
                         </td>
                         <td>
                            <p class="p_noMargin">
                               @if($marcaçao['meal']=='1REF' )
                               Pequeno-almoço
                               @elseif($marcaçao['meal']=='3REF' )
                               Jantar
                               @else
                               Almoço
                               @endif
                            </p>
                         </td>
                         <td>
                            @if($marcaçao['meal']!='1REF' )
                            <p class="p_noMargin">
                               Prato: <b>
                               @if($marcaçao['meal']=='3REF' ){{ $refeiçaoEmMarcação['prato_jantar'] }}@else {{ $refeiçaoEmMarcação['prato_almoço'] }}
                               @endif</b>
                            </p>
                            <small>
                               <p class="p_noMargin">
                                  Sopa: <b>
                                  @if($marcaçao['meal']=='3REF' ){{ $refeiçaoEmMarcação['sopa_jantar'] }}@else {{ $refeiçaoEmMarcação['sopa_almoço'] }}
                                  @endif</b>
                               </p>
                               <p class="p_noMargin">
                                  Sobremesa: <b>
                                  @if($marcaçao['meal']=='3REF' ){{ $refeiçaoEmMarcação['sobremesa_jantar'] }}@else {{ $refeiçaoEmMarcação['sobremesa_almoço'] }}
                                  @endif</b>
                               </p>
                            </small>
                            @endif
                         </td>
                         <td>
                            @foreach ($locais as $key => $local)
                            @if ($local['refName']==$marcaçao['local_ref'])
                            {{ $local['localName'] }}
                            @endif
                            @endforeach
                         </td>
                         <td class="project_progress">
                            <p class="p_noMargin">
                               @php
                               $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                               $semana  = array("Segunda-Feira","Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                               $mes_index = date('m', strtotime($marcaçao['data_marcacao']));
                               $weekday_number = date('N',  strtotime($marcaçao['data_marcacao']));
                               @endphp
                               <strong>
                               {{ date('d', strtotime($marcaçao['data_marcacao'])) }}
                               {{ $mes[($mes_index - 1)] }}
                               </strong>
                               <br>
                               <span style="font-size: .85rem;">{{ $semana[($weekday_number -1)] }}</span>
                            </p>

                         </td>
                      </tr>
                      @endif
                      @endforeach
                   </tbody>
                </table>
                @else
                <h5>Este utilizador não tem nenhuma marcação.</h5>
                @endif
             </div>
           </div>
           <!-- /.tab-pane swing-in-left-fwd -->
           <div class="tab-pane swing-in-left-fwd" id="ferramentas" >
              <div class="form-group row profile-settings-form-svbtn-spacer">
                 <div class="col-sm-12">
                    <h4 style="margin-bottom: 1rem;font-size: 1.2rem;">Ferramentas</h4>
                    @if ($partner['email'])
                      <a href="mailto:{{ $partner['email'] }}" style="margin-right: .5rem;">
                        <button type="submit" class="btn btn-primary puff-in-center">Enviar email</button>
                      </a>
                    @endif
                    <a href="{{ route('subgest_desassoc') }}">
                      <button type="button" class="btn btn-danger puff-in-center">Desassociar</button>
                    </a>
                 </div>
              </div>
           </div>
        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.card-body -->
    @else
      @if ($BLOCK_MEMBERS)
        <strong style="margin-left: 5px;">Aviso</strong>
        <p style="margin-left: 5px;" class="">Esta conta encontra-se bloqueada.</p>
      @else
        <strong style="margin-left: 5px;">Aviso</strong>
        <p style="margin-left: 5px;" class="">Esta conta encontra-se bloqueada.<br />Utilize o botão abaixo para a desbloquear.</p>
        <form style="margin-top: 15px; margin-bottom: 10px;" method="post" action="{!! route('user.unlock') !!}">
           @csrf
           <input type="hidden" value="{{ $partner['id'] }}" name="nim" id="nim" />
           <button type="submit" class="btn btn-danger" style="margin: .2rem; margin-right: 1.5rem !important;">Desbloquear conta</button>
        </form>
      @endif
    @endif

   </div>
   <!-- /.nav-tabs-custom -->
</div>
@endsection
@section('extra-scripts')
<script>
   $('#fakeUpload').click(function(e) {
       e.preventDefault();
       $('#profilePicUpload').trigger('click');
   });
</script>
<script>
   $("#profilePicUpload").change(function(){
     setTimeout(postForm, 500);

    });

    function postForm(){
      $("#profilePictureForm").submit();
    }

</script>
@endsection
