<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>RELATÓRIO</title>
    <style>
        body {
            background-color: white;
            color: #8c8c8c;
            font-family: Arial, sans-serif !important;
            font-size: 18px;
            font-weight: 400;
            padding: 0;
            margin: 0 auto;
        }

        .page-break {
            page-break-after: always;
        }

        .dont-break{
          page-break-inside: avoid;
        }

        .tm-container {
            max-width: 640px;
            margin-left: auto;
            margin-right: auto;
        }

        .tm-main-content {
            background-color: #ffffff;
        }

        .tm-text-white {
            color: black;
        }

        .tm-page-header-container {
            text-align: center;
            width: 100%;
        }

        .tm-page-header {
            display: block;
            vertical-align: baseline;
            margin-top: 20px;
            margin-bottom: 35px;
            font-size: 2.1rem;
            font-weight: 400;
        }

        .tm-page-icon {
            display: block;
            vertical-align: baseline;
            padding: 15px;
        }

        .tm-section-header {
            color: #996633;
            text-align: center;
            font-weight: 400;
            font-size: 1.5rem;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        td {
            text-align: center;
            padding: 10px 15px;
        }

        .tm-text-left {
            text-align: left;
        }

        .tm-text-right {
            text-align: right;
        }

        th {
            color: #333333;
            font-weight: 400;
            font-size: 1.2rem;
            padding-left: 15px;
            padding-right: 15px;
        }

        tr:nth-child(odd) {
            background-color: #e5e8ed;
        }

        tr.tm-tr-header {
            background-color: #c4cdd6;
            height: 50px;
        }

        .tm-section {
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 20px;
            padding-bottom: 30px;
        }

        .tm-section-small {
            max-width: 490px;
            margin-left: auto;
            margin-right: auto;
            padding-top: 0;
        }

        p {
            font-size: 1rem;
            line-height: 1.7;
        }

        .tm-mb-0 {
            margin-bottom: 0;
        }

        figure {
            margin: 0;
        }

        figcaption {
            text-align: center;
        }

        figcaption span {
            display: block;
            color: #333333;
            font-size: 18px;
        }

        .tm-item-name {
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .tm-special-items {
            display: flex;
            margin-left: -15px;
            margin-right: -15px;
        }

        .tm-special-item {
            padding-left: 15px;
            padding-right: 15px;
        }

        hr {
            width: 60%;
            border: 0.5px solid #ccc;
        }

        .tm-social-icons {
            text-align: center;
            margin-top: 30px;
        }

        .tm-social-icons i {
            font-size: 1.2rem;
        }

        .tm-social-link-container {
            display: block;
        }

        .tm-social-link {
            color: white;
            background-color: #c5ced8;
            border-radius: 2px;
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 3px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .tm-social-link:hover {
            background-color: #808e9e;
        }

        .tm-contact-link {
            color: #333333;
            font-weight: 600;
            text-decoration: none;
        }

        a {
            transition: all 0.3s ease;
        }

        .tm-contact-link:hover {
            color: #808e9e;
        }

        .tm-footer-text {
            font-size: 0.9rem;
            margin-top: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .tm-footer-link {
            color: #fff;
            text-decoration: none;
        }

        .tm-footer-link:hover {
            color: #c5ced8;
        }

        @media(max-width: 550px) {
            .tm-special-item {
                padding-left: 10px;
                padding-right: 10px;
            }

            .tm-special-items {
                margin-left: -10px;
                margin-right: -10px;
            }
        }

        @media(max-width: 480px) {
            .tm-special-item {
                padding-left: 5px;
                padding-right: 5px;
            }

            .tm-special-items {
                margin-left: -5px;
                margin-right: -5px;
            }
        }

        @media(max-width: 430px) {
            .tm-responsive-table {
                overflow-x: auto;
            }

            table {
                width: auto;
            }

            .tm-special-items {
                flex-direction: column;
            }

            .tm-special-item {
                margin-bottom: 40px;
            }

            figcaption p {
                margin-bottom: 0;
                line-height: 1;
            }
        }
    </style>
</head>

<body>
    <div class="tm-container">
        <div class="tm-text-white tm-page-header-container">
            <i class="fas fa-mug-hot fa-2x tm-page-icon"></i>
            <h1 class="tm-page-header" >{{ $user['childNome'] }}</h1>
        </div>
        <div class="tm-main-content page-break dont-break">
            <section class="tm-section">
                <h2 class="tm-section-header">MARCAÇÕES</h2>
                <div class="tm-responsive-table">
                    <table>
                        @foreach ($marcacoes as $key => $marcacao)
                        <tr>
                            <td class="tm-text-left" style="font-family: Verdana, sans-serif !important;">{{ $marcacao['data_marcacao'] }}</td>
                            <td class="tm-text-left" style="font-family: Verdana, sans-serif !important;">
                                @if ($marcacao['meal']=="1REF")
                                  PEQUENO-ALMOÇO
                                @elseif ($marcacao['meal']=="2REF")
                                  ALMOÇO
                                @else
                                  JANTAR
                                @endif
                            </td>
                            <td class="tm-text-right" style="font-family: Verdana, sans-serif !important;">{{ $marcacao['local_ref'] }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </section>
            <section class="tm-section">
                <h2 class="tm-section-header">CONFIRMAÇÕES</h2>
                <div class="tm-responsive-table">
                    <table>
                        @foreach ($tagMarcacoes as $key => $marcada)
                        <tr>
                            <td class="tm-text-left" style="font-family: Verdana, sans-serif !important;">{{ $marcada['data'] }}</td>
                            <td class="tm-text-right" style="font-family: Verdana, sans-serif !important;">
                                @if ($marcacao['meal']=="1REF")
                                  PEQUENO-ALMOÇO
                                @elseif ($marcacao['meal']=="2REF")
                                  ALMOÇO
                                @else
                                  JANTAR
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </section>
        </div>
        <div class="tm-main-content dont-break" style="margin-top: 100px !important;">
            <section class="tm-section-small">
                <h2 class="tm-section-header">DETALHES DO UTILIZADOR</h2>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">NÚMERO IDENTIFICADOR</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $user['childID'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">NOME</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $user['childNome'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">POSTO</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $user['childPosto'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">UNIDADE</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $user['childUnidade'] }}</h5>
                </p>
            </section>
        </div>
        <div class="tm-main-content dont-break" style="margin-top: 100px !important;">
            <section class="tm-section-small">
                <h2 class="tm-section-header">PERTENCENTE</h2>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">NIM</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $parent['id'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">NOME</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $parent['name'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">POSTO</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $parent['posto'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">UNIDADE</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $parent['unidade'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">CONTACTO</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">EMAIL:<br>{{ $parent['email'] }} <br> <br> TELF:<br>{{ $parent['telf'] }}</h5>
                </p>
            </section>
          </div>
          <div class="tm-main-content dont-break" style="margin-top: 100px !important;">
            <section class="tm-section-small">
                <h2 class="tm-section-header">DETALHES DE GRUPO</h2>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">ID DO GRUPO</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $grupo['groupID'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">NOME DO GRUPO</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $grupo['groupName'] }}</h5>
                </p>
                <p>
                <h4 style="text-align: center; font-weight: 400; margin: 0; font-family: Verdana, sans-serif !important;">UNIDADE DO GRUPO</h4>
                <h5 style="text-align: center; font-weight: 900; margin-top: 10px; font-family: Verdana, sans-serif !important;">{{ $grupo['groupUnidade'] }}</h5>
                </p>
            </section>
            <br /><br /><br />
        <footer>
            <p class="tm-text-white tm-footer-text">
                EXÉRCITO PORTUGUÊS | UNIDADE DE APOIO DO COMANDO DO PESSOAL
            </p>
        </footer>
    </div>
</body>

</html>
