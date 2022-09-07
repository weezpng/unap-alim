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
         page-break-inside: avoid;
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
         page-break-inside: avoid;
         }
         #details {
         font-family: Arial, Helvetica, sans-serif;
         margin-bottom: 50px;
         page-break-inside: avoid;
         }
         #client {
         font-family: Arial, Helvetica, sans-serif;
         padding-left: 10px;
         border-left: 6px solid #28715a;
         float: left;
         page-break-inside: avoid;
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
         page-break-inside: avoid;
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
         width: 100% !important;
         border-collapse: collapse;
         border-spacing: 0;
         margin-top: 20px;
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
         page-break-inside: avoid;
         }
         #notices{
         margin-top: 100px;
         font-family: Arial, Helvetica, sans-serif;
         padding-left: 10px;
         border-left: 6px solid #28715a;
         page-break-inside: avoid;
         }
         #notices2{
         font-family: Arial, Helvetica, sans-serif;
         padding-left: 10px;
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
         height: 30px;
         position: absolute;
         bottom: 0;
         border-top: 1px solid #AAAAAA;
         padding: 8px 0;
         text-align: center;
         }
         .checkbox{
         font-family: Arial, Helvetica, sans-serif;
         font-size: .8rem;
         overflow: hidden;
         width: 1rem;
         height: 1rem !important;
         background-color: white;
         margin: 2px auto;
         color: #3c3c3c;
         line-height: 1.2rem;
         vertical-align: middle;
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
      <div id="notices2">
         <div class="notice">Data: <b>{{ $date }}</b></div>
         <div class="notice">Refeição: <b>{{ $ref }}</b></div>
         <div class="notice">Local refeição: <b>{{ $local }}</b></div>
      </div>
      <table border="0" cellspacing="0" cellpadding="0">
      <thead>
         <tr>
            <th class="no">NIM</th>
            <th class="unit">POSTO</th>
            <th class="unit">NOME</th>
            <th class="unit">COLOCAÇÃO</th>
         </tr>
      </thead>
      <tbody>
