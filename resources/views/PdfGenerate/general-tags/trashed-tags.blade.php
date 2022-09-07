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
            margin-bottom: 25px;
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
            margin-bottom: 10px;
         }

         #notices_remov{
            margin-top: 10px;
            padding-top: 50px;
            font-family: Arial, Helvetica, sans-serif;
            padding-left: 20px;
            border-left: 5px solid #713128;
            page-break-inside: avoid;
            padding-bottom: 5px;
         }
         #notices_remov .notice_remov {   
            font-size: 1.2em;
            margin-right: 5rem;
            display: inline-block;
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

         .user-img {
            border: 3px solid #adb5bd;
            margin: 0 auto;
            padding: 3px;
            width: 100px;
            border-radius: 50px;
        }

        .parent{
           padding: 20px;
        }

        .entry_main{
            width: 100%; 
            height: 270px;            
            border: 2px solid #4a4a4a;
            margin: 0 auto;
        }

         .bar_entry{
            padding-top: .5rem; 
            padding-left: .5rem; 
            background-color: #4a4a4a; 
            width: 100%;
            height: 25px;
            color: white;
            font-size: .8rem;
         }

         .child{
            width: 95%;
            height: 35%;
            padding-left: .6rem;
            padding-right: 1rem;
            margin-top: 0.5rem;
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
               <h2 class="name">Marcações removidas</h2>
               <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>               
            </div>
         </div>

         <div id="notices">
            <div>Período extraido</div>
            <div class="notice">
               Inicio: <b>{{ $time_start }}</b><br>
               Fim: <b>{{ $time_end }}</b><br>
            </div>
         </div>

         <div class="parent">
            @foreach($trashed as $key => $trash)
            <div style="page-break-inside: avoid; margin-bottom: 40px;">
               <div style="font-size: 14px;"><b>Remoção de marcação</b></div>
                  <div id="notices_remov">
                     <div class="notice_remov">
                     <div>Detalhes da marcação</div><br>
                        Data: <b>{{ $trash['tag_date'] }}</b><br>
                        Refeição: <b>{{ $trash['tag_meal'] }}</b><br>
                        Local: <b>{{ $trash['tag_local'] }}</b><br>
                        Tipo de refeição: <b>{{ $trash['tag_type'] }}</b><br>
                        Marcada a: <b>{{ $trash['tagged_at'] }}</b><br>
                        Removida a: <b>{{ $trash['trashed_at'] }}</b>       
                     </div>
                  <div class="notice_remov">
                     <div>Marcação para utilizador</div><br>
                     NIM: <b>{{ $trash['user']['id'] }}</b><br>
                     Posto: <b>{{ $trash['user']['posto'] }}</b><br>
                     Nome: <b>{{ $trash['user']['name'] }}</b><br>
                     Colocação: <b>{{ $trash['user']['colocacao'] }}</b><br>                             
                  </div>
                  <br>
                  <div class="notice_remov">
                     <div>Marcada por</div><br>
                     @if(is_array($trash['tag_by']))
                        NIM: <b>{{ $trash['tag_by']['id'] }}</b><br>
                        Posto: <b>{{ $trash['tag_by']['posto'] }}</b><br>
                        Nome: <b>{{ $trash['tag_by']['name'] }}</b><br><br>                     
                     @else
                        <b>O próprio</b>
                     @endif                        
                  </div>
               </div>
            </div>

            @endforeach

            
            
             
   </body>
</html>
