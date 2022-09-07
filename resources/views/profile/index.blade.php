@extends('layout.master')
@section('title','Perfil')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Perfil</li>
<li class="breadcrumb-item active">Meu perfil</li>
@endsection
@section('page-content')
<div class="col-md-3">
   <!-- Profile Image -->
   <div class="card @if (Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif card-outline">
      <div class="card-body box-profile" style="padding-bottom: 10px !important; padding-left: 10px !important; padding-right: 10px !important;">
         @php
           $NIM = Auth::user()->id;
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
         <h3 class="profile-username text-center uppercase-only">{{ auth()->user()->name }}</h3>

         <p class="text-muted text-center">
         @if (Auth::user()->posto != "ASS.TEC." && Auth::user()->posto != "ASS.OP." &&
                Auth::user()->posto != "TEC.SUP." && auth()->user()->posto != "ENC.OP." && auth()->user()->posto != "TIA" && auth()->user()->posto != "TIG.1" && auth()->user()->posto != "TIE" && auth()->user()->posto != null)
             @if (Auth::check() && Auth::user()->dark_mode=='Y')
               @php $filename2 = "assets/icons/postos/TRANSPARENT_WHITE/" . Auth::user()->posto . ".png"; @endphp
             @else
                @php $filename2 = "assets/icons/postos/TRANSPARENT/" .  Auth::user()->posto . ".png"; @endphp
             @endif

             <img style="max-width: 3rem; object-fit: scale-down; margin-top: .5rem;" src="{{ asset($filename2) }}">
             <br /><span style="font-size: .8rem; font-family: sans-serif;">{{ auth()->user()->posto }}</span>
           @else
             <span style="font-size: .8rem; font-family: sans-serif;">POSTO NÃO DEFINIDO</span>
           @endif

         @if (auth()->user()->descriptor)
              <br><span class="uppercase-only">{{ auth()->user()->descriptor }}</span>
         @endif
         </p>
         @if (Auth::user()->lock=='N')
         <form id="profilePictureForm" name="profilePictureForm" method="post" action="{{ route('profile.picture.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="custom-file" style="visibility: hidden;">
               <input type="file" class="custom-file-input" id="profilePicUpload" name="profilePicUpload" accept="image/*">
               <label class="custom-file-label" for="customFile">Carregar imagem</label>
            </div>
            <input id="fakeUpload" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif btn-block" style="margin-top: -2rem !important;font-size: .75rem !important; width: 100%;"
            @if (file_exists(public_path($filename)))
            value="Alterar foto de perfil"
            @else
            value="Carregar foto de perfil"
            @endif
            ></input>
         </form>
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
            @php
            $_user_id = auth()->user()->id;
            while ((strlen((string)$_user_id)) < 8) {
               $_user_id = 0 . (string)$_user_id;
            }
            @endphp
            {{ $_user_id }}
         </p>
         <hr>
         <strong><i class="fas fa-map-marker-alt mr-1"></i> Unidade</strong>
         <p class="text-muted">{{ auth()->user()->unidade }}</p>
         <hr>
         <strong><i class="fas fa-map-marker mr-1"></i> Secção</strong>
         <p class="text-muted">{{ auth()->user()->seccao }}</p>
         <hr>
         <strong><i class="fa fa-envelope mr-1"></i> Email</strong>
         <a href="mailto:{{ auth()->user()->email }}">
            <p class="text-muted">{{ auth()->user()->email }}</p>
         </a>
         <hr>
         <strong><i class="fa fa-phone-square mr-1"></i> Extensão telefónica</strong>
         <p class="text-muted">{{ auth()->user()->telf }}</p>
         <hr>
         <strong><i class="far fa-user-circle mr-1"></i> Tipo de utilizador</strong>
         <p class="text-muted">
            {{ auth()->user()->user_type }}
         </p>
      </div>
      <!-- /.card-body -->
   </div>
   <!-- /.card -->
</div>
<!-- /.col -->
<div class="col-md-9">
   <div class="card card-outline @if (Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      <div class="card-header p-2">
         <ul class="nav nav-pills" >
            <li class="nav-item"><a class="nav-link active" @if (Auth::user()->lock=='Y') style="background-color: #d14351 !important;" @endif href="#settings" data-toggle="tab">Definições</a></li>
         </ul>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
         <div class="tab-content">
            <div class="tab-pane swing-in-left-fwd active" id="settings">
               <form class="form-horizontal" method="POST" action="{{route('profile.settings.save')}}">
                  {{ csrf_field() }}
                  <div class="form-group row">
                     <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                     <div class="col-sm-10">
                        <input required type="text" class="form-control uppercase-only" id="inputName" name="inputName" placeholder="Nome" value="{{ auth()->user()->name }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputEmail" class="col-sm-2 col-form-label">Posto</label>
                     <div class="col-sm-10">
                        <select required class="custom-select" id="inputPosto" name="inputPosto">
                           @if(auth()->user()->posto==null) <option disabled @if(auth()->user()->posto==null) selected @endif>Selecione o seu posto</option> @endif
                           <!-- CIVIS -->
                           <option value="ASS.OP." @if(auth()->user()->posto=="ASS.OP.") selected @endif>ASSISTENTE OPERACIONAL</option>
                           <option value="ENC.OP." @if(auth()->user()->posto=="ENC.OP.") selected @endif>ENCARREGADO OPERACIONAL</option>
                           <option value="ASS.TEC." @if(auth()->user()->posto=="ASS.TEC.") selected @endif>ASSISTENTE TÉCNICO</option>
                           <option value="TEC.SUP." @if(auth()->user()->posto=="TEC.SUP.") selected @endif>TÉCNICO SUPERIOR</option>
                           <option value="TIA" @if(auth()->user()->posto=="TIA") selected @endif>TÉCNICO INFORMÁTICA ADJUNTO</option>
                           <option value="TIG.1" @if(auth()->user()->posto=="TIG.1") selected @endif>TÉCNICO DE INFORMÁTICA GRAU 1</option>
                           <option value="TIE" @if(auth()->user()->posto=="TIE") selected @endif>TÉCNICO INFORMÁTICA ESPECIALISTA</option>
                           <!-- MILITARES -->
                           <option value="SOLDADO" @if(auth()->user()->posto=="SOLDADO") selected @endif)>SOLDADO</option>
                           <option value="2ºCABO" @if(auth()->user()->posto=="2ºCABO") selected @endif)>2º CABO</option>
                           <option value="1ºCABO" @if(auth()->user()->posto=="1ºCABO") selected @endif>1º CABO</option>
                           <option value="CABO-ADJUNTO" @if(auth()->user()->posto=="CABO-ADJUNTO") selected @endif>CABO-ADJUNTO</option>
                           <option value="2ºFURRIEL" @if(auth()->user()->posto=="2ºFURRIEL") selected @endif>2º FURRIEL</option>
                           <option value="FURRIEL" @if(auth()->user()->posto=="FURRIEL") selected @endif>FURRIEL</option>
                           <option value="2ºSARGENTO" @if(auth()->user()->posto=="1ºSARGENTO") selected @endif>2º SARGENTO</option>
                           <option value="1ºSARGENTO" @if(auth()->user()->posto=="1ºSARGENTO") selected @endif>1º SARGENTO</option>
                           <option value="SARGENTO-AJUDANTE" @if(auth()->user()->posto=="SARGENTO-AJUDANTE") selected @endif>SARGENTO-AJUDANTE</option>
                           <option value="SARGENTO-CHEFE" @if(auth()->user()->posto=="SARGENTO-CHEFE") selected @endif>SARGENTO-CHEFE</option>
                           <option value="SARGENTO-MOR" @if(auth()->user()->posto=="SARGENTO-MOR") selected @endif>SARGENTO-MOR</option>
                           <option value="ASPIRANTE" @if(auth()->user()->posto=="ASPIRANTE") selected @endif>ASPIRANTE</option>
                           <option value="ALFERES" @if(auth()->user()->posto=="ALFERES") selected @endif>ALFERES</option>
                           <option value="TENENTE" @if(auth()->user()->posto=="TENENTE") selected @endif>TENENTE</option>
                           <option value="CAPITAO" @if(auth()->user()->posto=="CAPITAO") selected @endif>CAPITÃO</option>
                           <option value="MAJOR" @if(auth()->user()->posto=="MAJOR") selected @endif>MAJOR</option>
                           <option value="TENENTE-CORONEL" @if(auth()->user()->posto=="TENENTE-CORONEL") selected @endif>TENENTE-CORONEL</option>
                           <option value="CORONEL" @if(auth()->user()->posto=="CORONEL") selected @endif>CORONEL</option>
                           <option value="BRIGADEIRO-GENERAL" @if(auth()->user()->posto=="BRIGADEIRO-GENERAL") selected @endif>BRIGADEIRO-GENERAL</option>
                           <option value="MAJOR-GENERAL" @if(auth()->user()->posto=="MAJOR-GENERAL") selected @endif>MAJOR-GENERAL</option>
                           <option value="TENENTE-GENERAL" @if(auth()->user()->posto=="TENENTE-GENERAL") selected @endif>TENENTE-GENERAL</option>
                           <option value="GENERAL" @if(auth()->user()->posto=="GENERAL") selected @endif>GENERAL</option>
                        </select>
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputName2" class="col-sm-2 col-form-label">Email</label>
                     <div class="col-sm-10">
                        <input required type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email" value="{{ auth()->user()->email }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputName2" class="col-sm-2 col-form-label">Extensão \<br>Telemóvel</label>
                     <div class="col-sm-10">
                        <input required type="number" class="form-control" id="inputTelf" name="inputTelf" placeholder="Ext. telefónica \ Telemóvel" value="{{ auth()->user()->telf }}">
                     </div>
                  </div>
                  <div class="form-group row">
                     <label for="inputExperience" class="col-sm-2 col-form-label">Unidade</label>
                     <div class="col-sm-10">
                        <select required class="custom-select" id="inputUEO"  name="inputUEO" @if($pendenteTrocaUnidade==true) disabled @endif>
                           @if(auth()->user()->unidade==null)
                              <option disabled selected>Selecione a sua unidade</option>
                           @endif
                           @foreach ($unidades as $key => $unidade)
                              <option @if(auth()->user()->unidade==$unidade->slug) selected @endif value="{{ $unidade->slug }}"> {{ $unidade->name }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="inputName2" class="col-sm-2 col-form-label">Secção</label>
                     <div class="col-sm-10">
                        <input type="text" required class="form-control" id="inputSecçao" name="inputSecçao" placeholder="Secção" value="{{ auth()->user()->seccao }}">
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="inputName2" class="col-sm-2 col-form-label">Descrição</label>
                     <div class="col-sm-10">
                        <input type="text" required class="form-control" id="inputDescriptor" name="inputDescriptor" placeholder="Descrição da função do utilizador" value="{{ auth()->user()->descriptor }}">
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="inputExperience" class="col-sm-2 col-form-label">Local preferencial</label>
                     <div class="col-sm-10">
                        <select required class="custom-select" id="inputLocalRefPref"  name="inputLocalRefPref">
                           @if(auth()->user()->localRefPref==null) <option disabled @if(auth()->user()->localRefPref==null) selected @endif>Selecione um local preferencial</option> @endif
                           <option @if(auth()->user()->localRefPref=="QSP") selected @endif value="QSP">Quartel da Serra do Pilar</option>
                           <option @if(auth()->user()->localRefPref=="QSO") selected @endif value="QSO">Quartel de Santo Ovídio</option>
                           <option @if(auth()->user()->localRefPref=="MMANTAS") selected @endif value="MMANTAS">Messe das Antas</option>
                           <option @if(auth()->user()->localRefPref=="MMBATALHA") selected @endif value="MMBATALHA">Messe da Batalha</option>
                        </select>
                     </div>
                  </div>
                  <div class="form-group row profile-settings-form-svbtn-spacer">
                     <div class="offset-sm-2 col-sm-10">
                        <button type="submit" @if (Auth::user()->lock=='Y') disabled  @endif class="btn @if (Auth::user()->lock=="Y") btn-danger btn_blocked_disable @else @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif  @endif">Guardar</button>
                     </div>
                  </div>
               </form>
            </div>
            <!-- /.tab-pane swing-in-left-fwd -->
         </div>
         <!-- /.tab-content -->
      </div>
      <!-- /.card-body -->
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
