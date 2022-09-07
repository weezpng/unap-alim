<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>GESTÃO DE ALIMENTAÇÃO</title>

<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
</head>
<body class="hold-transition lockscreen">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
  <div class="lockscreen-logo">
    <a href="../../index2.html"><b>Admin</b>LTE</a>
  </div>
  <!-- User name -->
  <div class="lockscreen-name">{{ $NAME }}</div>

   <!-- Session Status -->
   <x-auth-session-status class="mb-4" :status="session('status')" style="margin-top: 1rem !important; width: 15rem; margin: 0 auto; text-align: center;"/>
   <!-- Validation Errors -->
   <x-auth-validation-errors class="mb-4" :errors="$errors" style="margin-top: 1rem !important; width: 15rem; margin: 0 auto; text-align: center;"/>

  <!-- START LOCK SCREEN ITEM -->
  <div class="lockscreen-item">
    <!-- lockscreen image -->
    <div class="lockscreen-image">
      <img src="https://cpes-wise2/Unidades/Fotos/{{ $NIM }}.JPG" alt="User Image" style="object-fit: revert;">
    </div>
    <!-- /.lockscreen-image -->

    <!-- lockscreen credentials (contains the form) -->
    <form class="lockscreen-credentials" method="POST" action="{{ route('login') }}">
      <div class="input-group">
      @csrf
      <input type="hidden"id="id" name="id" required >
        <input type="password" type="password" class="form-control" placeholder="Password" name="password" id="password" required autocomplete="current-password" class="form-control" placeholder="password" autofocus>

        <div class="input-group-append">
          <button type="button" class="btn"><i class="fas fa-arrow-right text-muted"></i></button>
        </div>
      </div>
    </form>
    <!-- /.lockscreen credentials -->

  </div>
  <!-- /.lockscreen-item -->
  <div class="help-block text-center">
    Insira a password para desbloquear a conta
  </div>
  <div class="text-center">
    <a href="{{ route('login') }}">Ou inicie sessão como outro utilizador</a>
  </div>
</div>
<!-- /.center -->
<!-- jQuery -->
<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

</body>
</html>
