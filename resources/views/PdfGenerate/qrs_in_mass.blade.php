<html lang="en">
   <head>
      <style>
         .clearfix:after {
            content: "";
            display: table;
            clear: both;
         }
         html{
            font-family: Arial, Helvetica, sans-serif;
         }
         a {
            color: #28715a;
            text-decoration: none;
         }
         body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0 auto;
            padding: 0;
            color: #555555;
            background: #FFFFFF;
            font-size: 12px;
         }
         header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
            height: 2.5cm;
         }
         #details {
            page-break-inside: avoid;
            font-family: Arial, Helvetica, sans-serif;
            margin-bottom: 50px;
         }
         #client {
            font-family: Arial, Helvetica, sans-serif;
            padding-left: 10px;
            border-left: 6px solid #28715a;
            float: left;
         }
         #client .to {
            color: #777777;
         }
         h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
         }

        .flex-item{
            text-align: center;
            display: inline-block;
            width: 160px;
            height: 170px;
            border: 1px dashed black;
            border-radius: 5px;
            margin: 5px;
            page-break-inside: avoid;
        }

        .qr{
            width: 80px;
            height: 80px;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .qr_parent{
            height: 105px;
            page-break-inside: avoid;
        }

        .text {

            page-break-inside: avoid;
        }

        .text > h5{
            margin: 5px;
            page-break-inside: avoid;
        }

      </style>
   </head>
   <body>
    <div id="details" class="clearfix">
    <div id="client">
        <div class="to">EXPORTAÇÃO</div>
        <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
        <div class="address">Identificação única: {{ $generated['token'] }}</div>
    </div>
    </div>
    <br>
    <div>
      @php $i = 0; @endphp
      @php $i_sec = 0; @endphp
      @foreach($users_qr as $usr)
        @if (strlen($usr['UNIDADE'])<35)
          <div class="flex-item" style="margin-top: -5px !important;">
              <div class="qr_parent">
                  <img src="{{ $usr['QR'] }}" class="qr">
              </div>
              <div class="text ">
                  <h5 style="line-height: 0.8rem !important; OVERFLOW-WRAP: break-word; max-height: 47px !important; min-height: 42px !important;">
                  {{ $usr['NOME'] }} <br>
                  {{ $usr['NIM'] }} <br>
                  <span style="font-size: 0.5rem; line-height: 1.5rem;"> {{ $usr['UNIDADE'] }} </span><br />
                  </h5>
              </div>
          </div>
        @else
          <div class="flex-item">
              <div class="qr_parent">
                  <img src="{{ $usr['QR'] }}" class="qr">
              </div>
              <div class="text ">
                  <h5 style="line-height: 0.8rem !important; OVERFLOW-WRAP: break-word; max-height: 47px !important; min-height: 42px !important;">
                  {{ $usr['NOME'] }} <br>
                  {{ $usr['NIM'] }} <br>
                  <span style="font-size: 0.5rem; line-height: .5rem;"> {{ $usr['UNIDADE'] }} </span>
                  </h5>
              </div>
          </div>
        @endif
          @php $i++; @endphp
          @php $i_sec++; @endphp
          @if($i==4) <br>  @php $i = 0; @endphp @endif

          @if($i_sec==20) <div style="page-break-before: always"></div>  @php $i_sec = 0; @endphp @endif

      @endforeach
    </div>
   </body>
</html>
