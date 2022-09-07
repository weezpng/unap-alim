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
         float: left;
         margin-top: 8px;
         }
         #logo img {
         height: 70px;
         }
         #company {
         margin-top: 8px;
         font-family: Arial, Helvetica, sans-serif;
         float: right;
         text-align: right;
         }
         #details {
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
         width: 110%;
         border-collapse: collapse;
         border-spacing: 0;
         margin-bottom: 20px;
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
         table tfoot td {
         padding: 10px 20px;
         background: #FFFFFF;
         border-bottom: none;
         font-size: .95em;
         white-space: nowrap;
         border-top: 1px solid #AAAAAA;
         }
         table tfoot tr:first-child td {
         border-top: none;
         }
         table tfoot tr:last-child td {
         color: #3c3c3c;
         font-size: 1em;
         border-top: 1px solid #3c3c3c;
         }
         table tfoot tr td:first-child {
         border: none;
         }
         #thanks{
         font-family: Arial, Helvetica, sans-serif;
         font-size: 2em;
         margin-bottom: 50px;
         }
         #notices{
         margin-top: 100px;
         font-family: Arial, Helvetica, sans-serif;
         padding-left: 10px;
         border-left: 6px solid #28715a;
         }
         #notices .notice {
         font-size: 1.2em;
         }
         footer {
         font-family: Arial, Helvetica, sans-serif;
         color: #777777;
         width: 100%;
         height: 30px;
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
         <div id="details" class="clearfix" style="margin-bottom: 10px !important;">
            <div id="client">
               <div class="to">RELATÓRIO</div>
               <h2 class="name">Utilizador sem confirmações</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
               <div class="address">Identificação única: {{ $generated['token'] }}</div>
            </div>
         </div>
         <div id="details" class="clearfix">
            <div id="client">
               <div class="to">UTILIZADOR</div>
               <h2 class="name">{{ $user_posto }} {{ $user_id }} {{ $user_name }}</h2>
               <div class="address">Email:
                  @if ($user_email=="")
                  NÃO DEFINIDO
                  @else
                  <a href="mailto:{{ $user_email }}">{{ $user_email }}</a>
                  @endif
                  | Local preferencial: {{ $user_localRefPref }}
               </div>
               <div class="address">Associação: {{ $user_parent_id }} </div>
            </div>
         </div>
         @if ($total_tags==0 && $total_marcacoes>0)
         <div id="details" class="clearfix">
            <div id="client">
               <div class="address">
                  <h3 style="font-weight: 400; margin-top: 4px; margin-bottom: 4px;">Este utilizador têm as seguintes marcações, mas <strong>nenhuma confirmação</strong> para faturação da mesma.</h3>
               </div>
            </div>
         </div>
         @endif
         <!-- MARCAÇÕES -->
         <div id="details" class="clearfix" style="margin-bottom: 20px !important;">
            <div id="client">
               <h2 style="margin: 0;" class="name">Marcações deste utilizador</h2>
               <h4 style="margin: 0;" class="name">{{ $timeperiod }}</h4>
            </div>
         </div>
         <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
            <thead>
               <tr>
                  <th class="no">DATA</th>
                  <th class="unit">REFEIÇÃO</th>
                  <th class="unit">CONFIRMAÇÃO</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($marcaçoes as $key => $oneMarcacao)
               @if (is_array($oneMarcacao))
               <tr>
                  <td class="no" style="text-align: center;"> {{ date("d/m/Y",strtotime($oneMarcacao['data'])) }} </td>
                  <td class="unit" style="text-align: center;">
                     @if ($oneMarcacao['meal']=="1REF")
                     PEQUENO-ALMOÇO
                     @elseif ($oneMarcacao['meal']=="2REF")
                     ALMOÇO
                     @else
                     JANTAR
                     @endif
                  </td>
                  <td class="unit" style="text-align: center;">
                     @if ($oneMarcacao['confirmation']==1)
                     <B>CONFIRMADA</B>
                     @else
                     <B>NÃO</B>
                     @endif
                  </td>
               </tr>
               @endif
               @endforeach
            </tbody>
         </table>
         <!-- CONFIRMAÇÕES -->
         @if ($total_tags!=0)
         <div id="details" class="clearfix" style="margin-top: 50px !important;margin-bottom: 10px !important;">
            <div id="client">
               <h2 style="margin: 0;" class="name">Confirmações para faturação deste utilizador</h2>
               <h4 style="margin: 0; margin-top: 1rem;" class="name"><span style="font-weight: 100 !important;">Total de refeições: </span><b>{{ $total_marcacoes }}</b></h4>
               <h4 style="margin: 0;" class="name"><span style="font-weight: 100 !important;">Total de confirmações: </span><b>{{ $total_tags }}</b></h4>
            </div>
         </div>
         @endif
      </main>
      <footer>
         Relatório gerado por sistema informático de Gestão de Alimentação.
      </footer>
   </body>
</html>
