<html lang="en">
   <head>
      <style>
         @page {
            size: a4 portrait;
         }
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
            max-height: 95%;
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
               <h2 class="name">Pedidos POC</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>               
            </div>
         </div>
         @foreach ($P_POC as $key => $ref)

            <div id="notices">
               <div class="notice">{{ $ref['name'] }}</div>
               <div>Local de refeição: {{ $ref['local'] }}</div>
            </div>
            <br>
            @if(array_key_exists('TAGS', $ref))
            <table border="0" cellspacing="0" cellpadding="0">
               <thead>
                  <tr>
                     <th class="no">DATA</th>
                     <th class="unit" style="width; 5;"></th>
                     <th class="unit">MARCAÇÕES</th>
                     <th class="unit">PEDIDOS</th>
                     <th class="no">TOTAL<br><span style="font-size: 80%;">PEDIDOS</span></th>
                  </tr>
               </thead>
               <tbody>                  
                     @php
                        $TAGS = $ref['TAGS'];
                        $ITR = 0;
                     @endphp                     
                     @foreach($TAGS as $key => $TAG)
                     @php
                        $ITR++;
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($key));
                        $weekday_number = date('N',  strtotime($key));
                     @endphp
                  
                        <tr style="page-break-inside: auto; border-top: 5px solid white;">
                           <td class="no" style="text-align: center; border-bottom: 1px solid #3c3c3c;">                              
                            
                           </td>
                           <td class="unit" style="text-align: left !important;">
                              <b>1º</b> Refeição:
                           </td>
                           <td class="unit" style="text-align: center;">
                               {{ $TAG['MARCA']['1'] }}
                           </td>
                           <td class="unit" style="text-align: center;">
                               {{ $TAG['PEDIDO']['1'] }}
                           </td>
                           <td class="total">
                              {{ $TAG['TOTAL']['1'] }}
                           </td>
                        </tr>
                        <tr>
                        <td class="no" style="text-align: center; border-bottom: 5px solid #3c3c3c;">                              
                              <b>
                                 <span style="font-size: 105%;">  {{ date('d', strtotime($key)) }} 
                                 {{ $mes[($mes_index - 1)] }}</span>
                                 <br>
                                 <span style="font-size: 90%;">{{ $semana[($weekday_number -1)] }}</span>
                              </b>                              
                           </td>
                           <td class="unit" style="text-align: left !important;">
                              <b>2º</b> Refeição:
                           </td>
                           <td class="unit" style="text-align: center;">
                             {{ $TAG['MARCA']['2'] }}
                           </td>
                           <td class="unit" class="unit"style="text-align: center;">
                              {{ $TAG['PEDIDO']['2'] }}
                           </td>
                           <td class="total">
                              {{ $TAG['TOTAL']['2'] }}
                           </td>
                        </tr>
                        <tr>
                           <td class="no" style="text-align: center;">                                                       
                           </td>
                           <td class="unit" style="text-align: left !important;">
                              <b>3º</b> Refeição:
                           </td>
                           <td class="unit" style="text-align: center;">
                              {{ $TAG['MARCA']['3'] }}
                           </td>
                           <td class="unit" class="unit"style="text-align: center;">
                               {{ $TAG['PEDIDO']['3'] }}
                           </td>
                           <td class="total">
                              {{ $TAG['TOTAL']['3'] }}
                           </td>
                        </tr>
                        @if($ITR >= 7)            
                           @php
                              $ITR = 0;
                           @endphp
                        @endif
                     @endforeach               
               </tbody>
            </table>
            @else            
               <h3>Nenhum POC associado a esta U/E/O.</h3>
            @endif
            @if(!$loop->last)
               <div style="page-break-after: always;"></div>
            @endif

         @endforeach
   </body>  

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
               <h2 class="name">Pedidos de reforços</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>               
            </div>
         </div>
         <table border="0" cellspacing="0" cellpadding="0">
               <thead>
                  <tr>
                     <th class="no">DATA</th>
                     <th class="unit">PEDIDOS</th>
                  </tr>
               </thead>
               <tbody>

                  @foreach($P_REFRC as $key => $REFR)

                  @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($key));
                        $weekday_number = date('N',  strtotime($key));
                     @endphp

                     <tr style="page-break-inside: auto">
                        <td class="no" style="text-align: left;">                              
                           <b>
                              <span style="font-size: 105%;">  {{ date('d', strtotime($key)) }} 
                              {{ $mes[($mes_index - 1)] }}</span>
                              <br>
                              <span style="font-size: 90%;">{{ $semana[($weekday_number -1)] }}</span>
                           </b>                              
                        </td>
                        <td class="unit" style="text-align: center !important;">
                              <b>{{ $REFR }}</b> pedidos
                        </td>

                     </tr>
                  @endforeach

               </tbody>
            </table>
      </main>
   </body>


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
               <h2 class="name">Números totais</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>               
            </div>
         </div>

         @foreach ($P_TOTAL as $key => $ENTRY)

            <div id="notices">
               <div>@if($key!="TOTAL") Local: {{ $key }} @endif</div>
               <div class="notice">
                  @if($key=="QSP")
                     Quartel da Serra do Pilar
                  @elseif($key=="QSO")
                     Quartel de Santo Ovídeo
                  @elseif($key=="MMANTAS")
                     Messe Militar: Polo das Antas
                  @elseif($key=="MMBATALHA")
                     Messe Militar: Polo da Batalha
                  @else
                     Contagem total<br>
                  @endif
               </div>
            </div>
            <br>
            <table border="0" cellspacing="0" cellpadding="0">
               <thead>
                  <tr>
                     <th class="no">DATA</th>
                     <th class="unit" style="width; 5;"></th>
                     <th class="unit">MARCAÇÕES @if($key!="TOTAL")<br><span style="font-size: 90%;">MILITARES</span> @endif</th>
                     @if($key!="TOTAL")<th class="unit">MARCAÇÕES<br><span style="font-size: 90%;">CIVIS</span></th> @endif
                     <th class="unit">PEDIDOS</th>
                     <th class="no">TOTAL<br><span style="font-size: 80%;">PEDIDOS</span></th>
                  </tr>
               </thead>
               <tbody>                  
                     @foreach($ENTRY as $key_date => $ref)
                     @php
                        $TAGS = $ref['MARCA'];
                        if($key!="TOTAL"){ $CIVIL = $ref['CV']; }
                        $REQUEST = $ref['PEDIDOS'];                        
                        $TOTAL = $ref['TOTAL'];  
                     @endphp                     
                     @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
                        $mes_index = date('m', strtotime($key_date));
                        $weekday_number = date('N',  strtotime($key_date));
                     @endphp
                  
                        <tr style="page-break-inside: auto">
                           <td class="no" style="text-align: center; border-bottom: 1px solid #3c3c3c;">                              
                            
                           </td>
                           <td class="unit" style="text-align: left !important;">
                              <b>1º</b> Refeição:
                           </td>
                           <td class="unit" style="text-align: center;">
                               {{ $TAGS['1'] }}
                           </td>
                           @if($key!="TOTAL")
                           <td class="unit" style="text-align: center;">
                               {{ $CIVIL['1'] }}
                           </td>
                           @endif
                           <td class="unit" style="text-align: center;">
                               {{ $REQUEST['1'] }}
                           </td>
                           <td class="total">
                              {{ $TOTAL['1'] }}
                           </td>
                        </tr>
                        
                        <tr>
                        <td class="no" style="text-align: center; border-bottom: 1px solid #3c3c3c;">                              
                              <b>
                                 <span style="font-size: 105%;">  {{ date('d', strtotime($key_date)) }} 
                                 {{ $mes[($mes_index - 1)] }}</span>
                                 <br>
                                 <span style="font-size: 90%;">{{ $semana[($weekday_number -1)] }}</span>
                              </b>                              
                           </td>
                           <td class="unit" style="text-align: left !important;">                              
                              @if($key!="TOTAL")
                                 <b>2º</b> Refeição (<b>Normal</b>): <br>
                                 <b>2º</b> Refeição (<b>Dieta</b>):
                              @else
                                 <b>2º</b> Refeição:
                              @endif
                           </td>
                           <td class="unit" style="text-align: center;">
                              @if($key!="TOTAL")
                                 {{ $TAGS['2']['NORMAL'] }}<br>
                                 {{ $TAGS['2']['DIETA'] }}
                              @else
                                 {{ $TAGS['2'] }}
                              @endif
                           </td>

                           @if($key!="TOTAL")
                           <td class="unit" style="text-align: center;">
                               {{ $CIVIL['2']['NORMAL'] }}
                               <br>{{ $CIVIL['2']['DIETA'] }}
                           </td>
                           @endif

                           <td class="unit" style="text-align: center;">
                               {{ $REQUEST['2'] }}<br>&nbsp;
                           </td>
                           <td class="total">
                              @if($key!="TOTAL")
                                 {{ $TOTAL['2']['NORMAL'] }}<br>
                                 {{ $TOTAL['2']['DIETA'] }}
                              @else
                                 {{ $TOTAL['2'] }}
                              @endif
                           </td>
                        </tr>
                        <tr>
                           <td class="no" style="text-align: center;">                                                       
                           </td>
                           <td class="unit" style="text-align: left !important;">
                              @if($key!="TOTAL")
                                 <b>3º</b> Refeição (<b>Normal</b>): <br>
                                 <b>3º</b> Refeição (<b>Dieta</b>):
                              @else
                                 <b>3º</b> Refeição:
                              @endif
                           </td>
                           <td class="unit" style="text-align: center;">
                              @if($key!="TOTAL")
                                 {{ $TAGS['3']['NORMAL'] }}<br>
                                 {{ $TAGS['3']['DIETA'] }}
                              @else
                                 {{ $TAGS['3'] }}
                              @endif
                           </td>
                           @if($key!="TOTAL")
                           <td class="unit" style="text-align: center;">
                               {{ $CIVIL['3']['NORMAL'] }}
                               <br>{{ $CIVIL['3']['DIETA'] }}
                           </td>
                           @endif
                           
                           <td class="unit" style="text-align: center;">
                               {{ $REQUEST['3'] }}
                           </td>
                           <td class="total">
                              @if($key!="TOTAL")
                                 {{ $TOTAL['3']['NORMAL'] }}<br>
                                 {{ $TOTAL['3']['DIETA'] }}
                              @else
                                 {{ $TOTAL['3'] }}
                              @endif
                           </td>
                        </tr>
                     @endforeach               
               </tbody>
            </table>
            @if(!$loop->last)
               <div style="page-break-after: always;"></div>
            @endif

         @endforeach
      
      </main>      
   </body>
</html>
