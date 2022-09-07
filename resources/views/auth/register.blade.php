<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>GESTÃO DE ALIMENTAÇÃO</title>
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Font-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/custom/auth/register/css/opensans-font.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/custom/auth/register/fonts/material-design-iconic-font/css/material-design-iconic-font.min.css')}}">
    <!-- Main Style Css -->
    <link rel="stylesheet" href="{{asset('assets/custom/auth/register/css/style.css')}}" />
</head>

<body>
    <div class="page-content" style="background: #e9ecef;">
        @if($msgs!=null)
        @foreach($msgs as $msg)
        <div class="warn-parent">
            <div class="warning">
                <p class="text-sm">
                    {!! $msg['title'] !!}<br>
                    {!! $msg['message'] !!}
                </p>
            </div>
        </div>
        @endforeach
        <br>
        @endif

        <div class="form-v1-content">
            <div class="wizard-form">
                <form class="form-register" method="POST" action="{{ route('register') }}" id="registerform">
                  @csrf
                    <div id="form-total">
                        <!-- SECTION 1 -->
                        <h2>
                            <p class="step-icon"><span>01</span></p>
                            <span class="step-text">Nome</span>
                        </h2>
                        <section>
                            <div class="inner">
                                <div class="wizard-header">
                                    <h3 class="heading">Nome</h3>
                                    <p>Insira o seu nome. Este nome será visivel para todos os utilizadores.
                                      <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                    </p>

                                </div>
                                <div class="form-row">
                                    <div class="form-holder" style="width: 100% !important;">
                                        <fieldset style="margin: 4px auto;">
                                            <input autocomplete="off" type="text" class="form-control" id="name" name="name" placeholder="Nome" style="text-transform: capitalize;" required autofocus>
                                        </fieldset>
                                    </div>
                                </div>


                            </div>
                        </section>
                        <!-- SECTION 2 -->
                        <h2>
                            <p class="step-icon"><span>02</span></p>
                            <span class="step-text">Dados militares</span>
                        </h2>
                        <section>
                            <div class="inner" style="padding: 10px 45px !important;">
                                <div class="wizard-header">
                                    <h3 class="heading">Dados militares</h3>
                                    <p>Insira o seu NIM e a sua unidade de colocação.</p><br>
                                </div>
                                <div class="form-row">
                                    <div class="form-holder" style="width: 30% !important;">
                                        <fieldset>
                                            <input autocomplete="off" type="text" class="form-control" id="nim" name="nim" placeholder="NIM" required :value="old('nim')" maxlength="8">
                                        </fieldset>
                                    </div>
                                    <div class="form-holder" style="width: 70% !important;">
                                        <select required class="form-control" id="unidade" type="text" name="unidade" required="required" style="padding: 13.5px;">
                                            <option selected disabled>U/E/O de colocação</option>
                                            @foreach ($unidades as $key => $unidade)
                                              <option value="{{ $unidade->slug }}">{{ $unidade->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-holder" style="width: 100% !important;">
                                        <fieldset>
                                            <input autocomplete="off" type="text" class="form-control" id="section" name="section" placeholder="Divisão\repartição\secção do utilizador" required :value="old('section')" maxlength="8">
                                        </fieldset>
                                    </div>
                                </div>


                            </div>
                        </section>
                        <!-- SECTION 3 -->
                        <h2>
                            <p class="step-icon"><span>03</span></p>
                            <span class="step-text">Contactos</span>
                        </h2>
                        <section>
                            <div class="inner">
                                <div class="wizard-header">
                                    <h3 class="heading">Contactos</h3>
                                    <p>Insira os seus contactos. O preenchimento de email é obrigatório.</p>
                                </div>
                                <div class="form-row">
                                    <div class="form-holder">
                                        <fieldset>
                                            <input autocomplete="off" type="email" class="form-control" id="email" name="email" placeholder="Email" required :value="old('email')">
                                        </fieldset>
                                    </div>
                                    <div class="form-holder">
                                        <fieldset>
                                            <input autocomplete="off" type="text" class="form-control" id="telf" name="telf" placeholder="Telefone" :value="old('telf')">
                                        </fieldset>
                                    </div>
                                </div>

                            </div>
                        </section>
                    </div>
                </form>
            </div>
        </div>
        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
              {{ __('Já tem conta?') }}
          </a>
    </div>
    <script src="{{asset('assets/custom/auth/register/js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('assets/custom/auth/register/js/jquery.steps.js')}}"></script>
    <script src="{{asset('assets/custom/auth/register/js/main.js')}}"></script>

    <script>
    $(document).ready(function(){
      $('a[href="\#finish"]').click(function(){
        $( "#registerform" ).submit();
      });
    });
    </script>

</body>

</html>
