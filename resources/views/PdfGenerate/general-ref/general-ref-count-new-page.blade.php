</tbody>
</table>
</main>
<header>
   <div class="clearfix" style="page-break-before: always;">
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
      <h2 class="name">Geral de numeros de alimentação</h2>
      <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
      <div class="address">Identificação unica: {{ $generated['token'] }} | Página: {{ $page }}</div>
   </div>
</div>
<table border="0" cellspacing="0" cellpadding="0" @if ($key!=18) style="width: 110% !important;" @endif>
<thead>
   <tr>
      <th class="no">DATA</th>
      <th class="unit">1ºREFEIÇÃO</th>
      <th class="unit">2ºREFEIÇÃO</th>
      <th class="unit">3ºREFEIÇÃO</th>
      <th class="total">TOTAL</th>
   </tr>
</thead>
<tbody>
