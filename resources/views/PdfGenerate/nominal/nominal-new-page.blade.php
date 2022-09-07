</tbody>
</table>
</main>
<header style="page-break-before: always;">
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
