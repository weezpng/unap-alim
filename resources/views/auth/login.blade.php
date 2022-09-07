<html lang="en"><head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GESTÃO DE ALIMENTAÇÃO</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
  <style>
    .form-control{
      transition: 0.7s all ease;
      -webkit-transition: 0.7s all ease;
      -moz-transition: 0.7s all ease;
    }
    .usr-img{
      transition: all .3s ease-in-out;
      -webkit-transition: all .3s ease-in-out;
      -moz-transition: all .3s ease-in-out;
    }
  </style>
</head>
<body class="hold-transition lockscreen" style="min-height: 65%; max-height: 100vh;">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper" style="margin-top: 15% !important; margin-bottom: 30%;">
  <div class="lockscreen-logo" style="margin-bottom: 50px;">
    <a href="{{ route('index') }}">Gestão de <strong>Alimentação</strong></a><br>
  </div>
  <!-- User name -->
  <div class="lockscreen-name" id=>Iniciar sessão</div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" style="margin-top: 1rem !important; width: 15rem; margin: 0 auto; text-align: center;  "/>
    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" style="margin-top: 1rem !important; width: 15rem; margin: 0 auto; text-align: center; "/>

  <!-- START LOCK SCREEN ITEM -->
  <div class="lockscreen-item" style="width: 250px; border-radius: 10px; background-color: transparent;">
    <!-- lockscreen image -->
    <div class="lockscreen-image" style="margin-top: 7.5%; left: -60px; top: -35px; background-color: #e9ecef !important;">
      <img src="" alt="User Image" onerror="this.style.display='none'" id="userImg" class="usr-img" style="height: 95px;width: 90px;object-fit: revert; border: 2px solid white;">
    </div>
    <!-- /.lockscreen-image -->
    @if (isset($_SERVER['AUTH_USER']) && $_SERVER['AUTH_USER']!="")
      @php
        $temp_id = explode('\\', $_SERVER['AUTH_USER']);
        $ID = $temp_id[1];
      @endphp
    @endif
    <!-- lockscreen credentials (contains the form) -->
    <form class="lockscreen-credentials" style="margin-left: 0 !important;" method="POST" action="{{ route('login') }}">
      <div class="input-group">
        @csrf
        <input type="text" class="form-control" placeholder="NIM" style="width: 100%;" id="id" name="id" autocomplete="username"
               maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
               required utofocus>

        <input type="password" class="form-control" placeholder="Password" type="password" name="password" id="password"
               required autocomplete="current-password" style="display:none;">

        <div class="input-group-append">
          <button type="submit" class="btn" name="subm" id="subm" style="display:none;">
            <i class="fas fa-arrow-right text-muted"></i>
          </button>
        </div>
      </div>
      <h6 style="font-size: .8rem; padding: 0.5rem; padding-left: 1.5rem; color: #5e6976; display: none;" id="capswarn">Caps Lock activado</h6>
    </form>

  </div>
  <div class="help-block text-center">
  @if(isset($_SERVER['AUTH_USER']) && $_SERVER['AUTH_USER']!="")
  @php 
    $UID = str_replace("EXERCITO\\", "", $_SERVER['AUTH_USER']); 
    $USR = App\Models\User::where('id', $UID)->first();
  @endphp
    @if($USR)
    <a class="underline text-gray-600 hover:text-gray-900" href="{{ route('express.login') }}" data-toggle="tooltip" data-placement="top" title="Iniciar sessão com a conta atualmente aberta no computador" style="margin-right: .5rem;padding-bottom: 1rem;">
      Login como <span style="text-transform: capitalize;">{{$USR['posto']}} {{$USR['name']}} </span>
    </a>
    <br>
    @else
    <a class="underline text-sm text-gray-600 hover:text-gray-900" data-toggle="tooltip" data-placement="top" title="Desactivado" href="#" style="margin-right: .5rem;padding-bottom: 1rem; color: #5e5e5e;cursor: default;">
      {{ __('Login express') }}
    </a>
    <br>
    @endif
  @else
    <a class="underline text-sm text-gray-600 hover:text-gray-900" data-toggle="tooltip" data-placement="top" title="Desactivado" href="#" style="margin-right: .5rem;padding-bottom: 1rem; color: #5e5e5e;cursor: default;">
      {{ __('Login express') }}
    </a>
    <br>
  @endif
  <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}" style="margin-right: .5rem;">
        {{ __('Não tem conta?') }}
    </a>
  </div>
  <div class="lockscreen-footer text-center" style="margin-top: 10%;">
    @if($msgs!=null)
        @foreach($msgs as $msg)
        <div>
            <div class=" mt-6 bg-white" style="padding: 1rem;">
                <p class="text-sm" style="margin: 0 !important;">
                    <strong>{!! $msg['title'] !!}</strong><br>
                    {!! $msg['message'] !!}
                </p>
            </div>
        </div>
        @endforeach
    @endif
    </div>
</div>
<!-- /.center -->
<!-- jQuery -->
<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<script>

  $("#id").on('change keydown paste input', function(){
    var value = $("#id").val();
    if(value.length == 8){
      setPicture(value);
      showOtherFields();
    } else {
      hideOtherFields();
      $("#userImg").fadeOut( 500 );
    }
});

function showOtherFields(){
  $("#password").fadeIn( 700, function() {
    $("#subm").fadeIn(500);
  });
}

function hideOtherFields(){
  $("#subm").fadeOut( 500 );
  $("#password").fadeOut( 700 , function() {
    $("#password").css( "padding-left", "0.75rem" );
    $("#id").css( "padding-left", "0.75rem" );
  });
}


function setPicture(value){
  var LocationFileJPG = "{{ url('/assets/profiles/') }}" + "/" + value + ".JPG";
  var LocationFilePNG = "{{ url('/assets/profiles/') }}" + "/" + value + ".JPG";
  if(checkFileExist(LocationFileJPG)){
    var url = LocationFileJPG;
    $("#userImg").attr('src',url);
    $("#userImg").fadeIn(500);
  } else if(checkFileExist(LocationFilePNG)){
    var url = LocationFilePNG;
    $("#userImg").attr('src',url);
    $("#userImg").fadeIn(500);
  } else if(checkFileExist("https://cpes-wise2/Unidades/Fotos/" + value + ".JPG")) {
    var url = "https://cpes-wise2/Unidades/Fotos/" + value + ".JPG";
    $("#userImg").attr('src',url);
    $("#userImg").fadeIn(500);
  } else {
    $("#password").css( "padding-left", "50px" );
    $("#id").css( "padding-left", "50px" );
    $("#userImg").fadeOut(200);
  }
  $("#password").css( "padding-left", "50px" );
  $("#id").css( "padding-left", "50px" );

}

function checkFileExist(urlToFile) {
  try {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
    return (xhr.status == "404") ? false : true;
  } catch (e) {
    return false;
  }
}
</script>
<script>
var input = document.getElementById("password");
var text = document.getElementById("capswarn");
input.addEventListener("keyup", function(event) {

if (event.getModifierState("CapsLock")) {
    text.style.display = "block";
  } else {
    text.style.display = "none"
  }
});
</script>
</body>
</html>
