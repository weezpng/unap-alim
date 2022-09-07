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
  @if(Auth::user()->dark_mode=='Y')
  <style media="screen">
    body{
      background-color: #454d55 !important;
      color: white;
    }

    .lockscreen-image{
      background-color: #454d55 !important;
    }

    input, button{
      background-color: #454d55 !important;
      color: white !important;
      border: 1px solid #e9ecef !important;
    }
    h6{
      color: white !important;
    }
    input{
      border-right: none !important;
    }

    button{
      border-left: none !important;
    }
  </style>

  @else
    <style media="screen">

      .lockscreen-image{
        background-color: #e9ecef !important;
      }

      input, button{
        background-color: #e9ecef !important;

        border: 1px solid #454d55 !important;
      }

      input{
        border-right: none !important;
      }

      button{
        border-left: none !important;
      }

      .lockscreen-image>img{
        border-color: #454d55 !important;
      }

    </style>
  @endif
</head>
<body class="hold-transition lockscreen" style="min-height: 65%; max-height: 100vh;" onload="showUserPic()">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper" style="margin-top: 10% !important; margin-bottom: 20%;">
  <div class="lockscreen-logo">
    Gestão de <strong>Alimentação</strong>
  </div>
    <!-- User name -->
    <div class="lockscreen-name" style="margin-bottom: 3rem; font-weight: 500 !important;" id=>Confirme a password para continuar</div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" style="margin-top: 1rem !important; width: 15rem; margin: 0 auto; text-align: center;"/>
    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" style="margin-top: 1rem !important; width: 15rem; margin: 0 auto; text-align: center;"/>

    <!-- START LOCK SCREEN ITEM -->
  <div class="lockscreen-item" style="width: 250px; border-radius: 10px; background-color: transparent; margin-top: -.9rem;">
    <!-- lockscreen image -->
    <div class="lockscreen-image" style="margin-top: 7.5%; left: -60px; top: -35px;">
      <img src="" alt="User Image" onerror="this.style.display='none'" id="userImg" style="height: 95px;width: 90px;object-fit: revert; border: 2px solid white;">
    </div>
    <!-- /.lockscreen-image -->
    <form class="lockscreen-credentials" style="margin-left: 30px !important;" method="POST" action="{{ route('password.confirm') }}">
      <div class="input-group">
        @csrf
        <input type="password" style="top: 0.7rem; padding: 1.5rem; font-size: 1.1rem; font-weight: 200;" class="form-control" placeholder="Password" type="password" name="password" id="password" required autocomplete="current-password" autofocus>
        <div class="input-group-append" style="top: 0.73rem;">
          <button type="submit" class="btn" style="top: 0.71rem;">
            <i class="fas fa-arrow-right text-muted"></i>
          </button>
        </div>
      </div>
      <h6 style="font-size: .8rem; padding: 0.25rem; color: #5e6976; display: none; margin-top: 0.7rem; margin-left: -0.2rem;" id="capswarn">Caps Lock activado</h6>
    </form>

  </div>
</div>
<!-- /.center -->

<!-- jQuery -->
<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<script>
function showUserPic(){
    var LocationFileJPG = "{{ url('/assets/profiles/') }}" + "/{{ $id }}.JPG";
    var LocationFilePNG = "{{ url('/assets/profiles/') }}" + "/{{ $id }}.JPG";
    if(checkFileExist(LocationFileJPG)){
      var url = LocationFileJPG;
    } else if(checkFileExist(LocationFilePNG)){
      var url = LocationFilePNG;
    } else {
      var url = "https://cpes-wise2/Unidades/Fotos/{{$id}}.JPG";
    }
    $("#userImg").attr('src',url);
    $("#userImg").css( "display", "block" );
}

function checkFileExist(urlToFile) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
    return (xhr.status == "404") ? false : true;
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
