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
         #logo {
            page-break-inside: avoid;
            float: left;
            margin-top: 8px;
         }
         #logo img {
            height: 70px;
         }
         #company {
            page-break-inside: avoid;
            margin-top: 8px;
            font-family: Arial, Helvetica, sans-serif;
            float: right;
            text-align: right;
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
         #invoice {
            font-family: Arial, Helvetica, sans-serif;
            float: right;
            text-align: right;
         }
         #invoice h1 {
            color: #28715a;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0  0 10px 0;
         }
         #invoice .date {
            font-size: 1.1em;
            color: #777777;
         }
         table {
            font-family: Arial, Helvetica, sans-serif;
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
         }
         table th,
         table td {
            padding: 10px;
            background: #EEEEEE;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
         }
         table th {
            white-space: nowrap;
            font-weight: normal;
         }
         table td {
            text-align: right;
         }
         table td h3{
            color: black;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
         }
         table .no {
            color: #FFFFFF;
            font-size: .8em;
            background: #3c3c3c;
         }
         table .desc {
            text-align: left;
         }
         table .unit {
            background: #DDDDDD;
            font-size: .8em;
         }
         table .total {
            background: #3c3c3c;
            color: #FFFFFF;
            font-size: .8em;
         }
         table td.unit,
         table td.qty,
         table td.total {
            font-size: .8em;
         }
         table tbody tr:last-child td {
            border: none;
         }
         #notices{
            margin-top: 15px;
            font-family: Arial, Helvetica, sans-serif;
            padding-left: 20px;
            border-left: 6px solid #28715a;
            page-break-inside: avoid;
         }
         #notices .notice {   
            font-size: 1.2em;
         }
         footer {
            font-family: Arial, Helvetica, sans-serif;
            color: #777777;
            width: 100%;
            height: 20px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
         }
      </style>
   </head>
   <body>
   <header>
         <div class="clearfix">
            <div id="logo">
               <img src="{{ public_path('assets/icons/cmdpesslogo.png') }}">
            </div>
            <div id="company">
               Relatório criado<br>
               <span style="text-transform: uppercase;">{{ $generated['nome'] }}</span><br>
               {{ $generated['id'] }}<br>
               <a href="mailto:{{ $generated['email'] }}"> {{ $generated['email'] }} </a>
            </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <div class="to">RELATÓRIO</div>
               <h2 class="name">Ordem de confeção</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
               <div class="address">Identificação única: {{ $generated['token'] }}</div>
            </div>
         </div>
         
         <table border="0" cellspacing="0" cellpadding="0">
            <thead>
               <tr>
                  <th class="no">DATA</th>
                  <th class="unit">MARCAÇÕES</th>
                  <th class="unit">PESSOAL SERVIÇO</th>
                  <th class="no">TOTAL<br><span style="font-size: 90%;">(A CONFECIONAR)</span></th>
               </tr>
            </thead>
            <tbody>
               @foreach ($refs as $key => $ref)
               <tr>
                  <td class="no" style="text-align: center;">
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($ref['COMPLETE']['DATA']));
                        $weekday_number = date('N',  strtotime($ref['COMPLETE']['DATA']));
                     @endphp
                     <b>
                        {{ date('d', strtotime($ref['COMPLETE']['DATA'])) }}
                        {{ $mes[($mes_index - 1)] }}<br>
                     </b>
                     <span style="font-size: 85%;">{{ $semana[($weekday_number -1)] }}</span>
                  </td>
                  <td class="unit" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['COMPLETE']['MARCA'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['COMPLETE']['MARCA'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['COMPLETE']['MARCA'][3] }}</b></td>
                  <td class="unit" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['COMPLETE']['SVÇ'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['COMPLETE']['SVÇ'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['COMPLETE']['SVÇ'][3] }}</b></td>
                  <td class="no" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['COMPLETE']['TOTAL'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['COMPLETE']['TOTAL'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['COMPLETE']['TOTAL'][3] }}</b></td>
               </tr>
               @endforeach
            </tbody>
         </table>
         <footer>
            Relatório gerado por sistema informatico de Gestão de Alimentação, não dispensa confirmação.
         </footer>

         <div style="page-break-after: always;"></div>
      <header>
         <div class="clearfix">
            <div id="logo">
               <img src="{{ public_path('assets/icons/cmdpesslogo.png') }}">
            </div>
            <div id="company">
               Relatório criado<br>
               <span style="text-transform: uppercase;">{{ $generated['nome'] }}</span><br>
               {{ $generated['id'] }}<br>
               <a href="mailto:{{ $generated['email'] }}"> {{ $generated['email'] }} </a>
            </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <div class="to">RELATÓRIO</div>
               <h2 class="name">Pedidos POC Unidade de Apoio</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
               <div class="address">Identificação única: {{ $generated['token'] }}</div>
            </div>
         </div>         
         <table border="0" cellspacing="0" cellpadding="0">
            <thead>
               <tr>
                  <th class="no">DATA</th>
                  <th class="unit">MARCAÇÕES</th>
                  <th class="unit">PEDIDOS QUANTITATIVOS</th>
                  <th class="total">TOTAL</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($refs as $key => $ref)
               <tr>
                  <td class="no" style="text-align: center;">
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($ref['UNAP']['DATA']));
                        $weekday_number = date('N',  strtotime($ref['UNAP']['DATA']));
                     @endphp
                     <b>
                        {{ date('d', strtotime($ref['UNAP']['DATA'])) }}
                        {{ $mes[($mes_index - 1)] }}<br>
                     </b>
                     <span style="font-size: 85%;">{{ $semana[($weekday_number -1)] }}</span>
                  </td>
                  <td class="unit" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['UNAP']['TAGS'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['UNAP']['TAGS'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['UNAP']['TAGS'][3] }}</b></td>
                  <td class="unit" style="text-align: left; padding-left: 4.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['UNAP']['PEDIDOS'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['UNAP']['PEDIDOS'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['UNAP']['PEDIDOS'][3] }}</b></td>                  
                  <td class="total" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['UNAP']['TOTAL'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['UNAP']['TOTAL'][2] }}</b><br> Jantar: {{ $ref['UNAP']['TOTAL'][3] }}</td>
               </tr>
               @endforeach
            </tbody>
         </table>
         <footer>
            Relatório gerado por sistema informatico de Gestão de Alimentação, não dispensa confirmação.
         </footer>           
         <div style="page-break-after: always;"></div>
         <header>
         <div class="clearfix">
            <div id="logo">
               <img src="{{ public_path('assets/icons/cmdpesslogo.png') }}">
            </div>
            <div id="company">
               Relatório criado<br>
               <span style="text-transform: uppercase;">{{ $generated['nome'] }}</span><br>
               {{ $generated['id'] }}<br>
               <a href="mailto:{{ $generated['email'] }}"> {{ $generated['email'] }} </a>
            </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <div class="to">RELATÓRIO</div>
               <h2 class="name">Marcações da Unidade de Apoio</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
               <div class="address">Identificação única: {{ $generated['token'] }}</div>
            </div>
         </div>
         
         <table border="0" cellspacing="0" cellpadding="0">
            <thead>
               <tr>
                  <th class="no">DATA</th>
                  <th class="unit">MARCAÇÕES</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($refs as $key => $ref)
               <tr>
                  <td class="no" style="text-align: center;">
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($ref['UNAP']['DATA']));
                        $weekday_number = date('N',  strtotime($ref['UNAP']['DATA']));
                     @endphp
                     <b>
                        {{ date('d', strtotime($ref['UNAP']['DATA'])) }}
                        {{ $mes[($mes_index - 1)] }}<br>
                     </b>
                     <span style="font-size: 85%;">{{ $semana[($weekday_number -1)] }}</span>
                  </td>
                  <td class="unit" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['UNAP']['MARCA'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['UNAP']['MARCA'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['UNAP']['MARCA'][3] }}</b></td>
               </tr>
               @endforeach
            </tbody>
         </table>
         <footer>
            Relatório gerado por sistema informatico de Gestão de Alimentação, não dispensa confirmação.
         </footer>

         <div style="page-break-after: always;"></div>

         <header>
         <div class="clearfix">
            <div id="logo">
               <img src="{{ public_path('assets/icons/cmdpesslogo.png') }}">
            </div>
            <div id="company">
               Relatório criado<br>
               <span style="text-transform: uppercase;">{{ $generated['nome'] }}</span><br>
               {{ $generated['id'] }}<br>
               <a href="mailto:{{ $generated['email'] }}"> {{ $generated['email'] }} </a>
            </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <div class="to">RELATÓRIO</div>
               <h2 class="name">Pedidos POC Messes Militares do Porto</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
               <div class="address">Identificação única: {{ $generated['token'] }}</div>
            </div>
         </div>
         
         <table border="0" cellspacing="0" cellpadding="0">
            <thead>
               <tr>
                  <th class="no">DATA</th>
                  <th class="unit">MARCAÇÕES</th>
                  <th class="unit">PEDIDOS QUANTITATIVOS</th>
                  <th class="total">TOTAL</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($refs as $key => $ref)
               <tr>
                  <td class="no" style="text-align: center;">
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($ref['MESSE']['DATA']));
                        $weekday_number = date('N',  strtotime($ref['MESSE']['DATA']));
                     @endphp
                     <b>
                        {{ date('d', strtotime($ref['MESSE']['DATA'])) }}
                        {{ $mes[($mes_index - 1)] }}<br>
                     </b>
                     <span style="font-size: 85%;">{{ $semana[($weekday_number -1)] }}</span>                  
                  </td>
                  <td class="unit" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['MESSE']['TAGS'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['MESSE']['TAGS'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['MESSE']['TAGS'][3] }}</b></td>
                  <td class="unit" style="text-align: left; padding-left: 4.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['MESSE']['PEDIDOS'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['MESSE']['PEDIDOS'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['MESSE']['PEDIDOS'][3] }}</b></td>                  
                  <td class="total" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['MESSE']['TOTAL'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['MESSE']['TOTAL'][2] }}</b><br> Jantar: {{ $ref['MESSE']['TOTAL'][3] }}</td>
               </tr>
               @endforeach
            </tbody>
         </table>
         <footer>
            Relatório gerado por sistema informatico de Gestão de Alimentação, não dispensa confirmação.
         </footer>        

         <div style="page-break-after: always;"></div>

         <header>
         <div class="clearfix">
            <div id="logo">
               <img src="{{ public_path('assets/icons/cmdpesslogo.png') }}">
            </div>
            <div id="company">
               Relatório criado<br>
               <span style="text-transform: uppercase;">{{ $generated['nome'] }}</span><br>
               {{ $generated['id'] }}<br>
               <a href="mailto:{{ $generated['email'] }}"> {{ $generated['email'] }} </a>
            </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <div class="to">RELATÓRIO</div>
               <h2 class="name">Marcações das Messes Militares do Porto</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
               <div class="address">Identificação única: {{ $generated['token'] }}</div>
            </div>
         </div>
         
         <table border="0" cellspacing="0" cellpadding="0">
            <thead>
               <tr>
                  <th class="no">DATA</th>
                  <th class="unit">MARCAÇÕES</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($refs as $key => $ref)
               <tr>
                  <td class="no" style="text-align: center;">
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($ref['MESSE']['DATA']));
                        $weekday_number = date('N',  strtotime($ref['MESSE']['DATA']));
                     @endphp
                     <b>
                        {{ date('d', strtotime($ref['MESSE']['DATA'])) }}
                        {{ $mes[($mes_index - 1)] }}<br>
                     </b>
                     <span style="font-size: 85%;">{{ $semana[($weekday_number -1)] }}</span>
                  </td>
                  <td class="unit" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['MESSE']['MARCA'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['MESSE']['MARCA'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['MESSE']['MARCA'][3] }}</b></td>
               </tr>
               @endforeach
            </tbody>
         </table>
         <footer>
            Relatório gerado por sistema informatico de Gestão de Alimentação, não dispensa confirmação.
         </footer>

         <div style="page-break-after: always;"></div>

         <header>
         <div class="clearfix">
            <div id="logo">
               <img src="{{ public_path('assets/icons/cmdpesslogo.png') }}">
            </div>
            <div id="company">
               Relatório criado<br>
               <span style="text-transform: uppercase;">{{ $generated['nome'] }}</span><br>
               {{ $generated['id'] }}<br>
               <a href="mailto:{{ $generated['email'] }}"> {{ $generated['email'] }} </a>
            </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <div class="to">RELATÓRIO</div>
               <h2 class="name">Pedidos POC Gabinete de Classificação e Seleção</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
               <div class="address">Identificação única: {{ $generated['token'] }}</div>
            </div>
         </div>
         
         <table border="0" cellspacing="0" cellpadding="0">
            <thead>
               <tr>
                  <th class="no">DATA</th>
                  <th class="unit">MARCAÇÕES</th>
                  <th class="unit">PEDIDOS QUANTITATIVOS</th>
                  <th class="total">TOTAL</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($refs as $key => $ref)
               <tr>
                  <td class="no" style="text-align: center;">
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($ref['GCS']['DATA']));
                        $weekday_number = date('N',  strtotime($ref['GCS']['DATA']));
                     @endphp
                     <b>
                        {{ date('d', strtotime($ref['GCS']['DATA'])) }}
                        {{ $mes[($mes_index - 1)] }}<br>
                     </b>
                     <span style="font-size: 85%;">{{ $semana[($weekday_number -1)] }}</span>                                                      
                  </td>
                  <td class="unit" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['GCS']['TAGS'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['GCS']['TAGS'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['GCS']['TAGS'][3] }}</b></td>
                  <td class="unit" style="text-align: left; padding-left: 4.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['GCS']['PEDIDOS'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['GCS']['PEDIDOS'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['GCS']['PEDIDOS'][3] }}</b></td>                  
                  <td class="total" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['GCS']['TOTAL'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['GCS']['TOTAL'][2] }}</b><br> Jantar: {{ $ref['GCS']['TOTAL'][3] }}</td>
               </tr>
               @endforeach
            </tbody>
         </table>
         <footer>
            Relatório gerado por sistema informatico de Gestão de Alimentação, não dispensa confirmação.
         </footer>                  
         
         <div style="page-break-after: always;"></div>

         <header>
         <div class="clearfix">
            <div id="logo">
               <img src="{{ public_path('assets/icons/cmdpesslogo.png') }}">
            </div>
            <div id="company">
               Relatório criado<br>
               <span style="text-transform: uppercase;">{{ $generated['nome'] }}</span><br>
               {{ $generated['id'] }}<br>
               <a href="mailto:{{ $generated['email'] }}"> {{ $generated['email'] }} </a>
            </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <div class="to">RELATÓRIO</div>
               <h2 class="name">Marcações do Gabinete de Classificação e Seleção</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
               <div class="address">Identificação única: {{ $generated['token'] }}</div>
            </div>
         </div>
         
         <table border="0" cellspacing="0" cellpadding="0">
            <thead>
               <tr>
                  <th class="no">DATA</th>
                  <th class="unit">MARCAÇÕES</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($refs as $key => $ref)
               <tr>
                  <td class="no" style="text-align: center;">
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($ref['GCS']['DATA']));
                        $weekday_number = date('N',  strtotime($ref['GCS']['DATA']));
                     @endphp
                     <b>
                        {{ date('d', strtotime($ref['GCS']['DATA'])) }}
                        {{ $mes[($mes_index - 1)] }}<br>
                     </b>
                     <span style="font-size: 85%;">{{ $semana[($weekday_number -1)] }}</span> 
                  </td>
                  <td class="unit" style="text-align: left; padding-left: 1.25rem;">Pequeno-almoço: &nbsp;<b>{{ $ref['GCS']['MARCA'][1] }}</b><br> Almoço: &nbsp;<b>{{ $ref['GCS']['MARCA'][2] }}</b><br> Jantar: &nbsp;<b>{{ $ref['GCS']['MARCA'][3] }}</b></td>
               </tr>
               @endforeach
            </tbody>
         </table>
         <footer>
            Relatório gerado por sistema informatico de Gestão de Alimentação, não dispensa confirmação.
         </footer>
   </body>  
</html>
