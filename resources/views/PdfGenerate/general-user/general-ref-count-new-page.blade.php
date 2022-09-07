</tbody>
</table>
</main>
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
      <h2 class="name">Geral de utilizador</h2>
      <div class="address">Criado | Data: {{ $generated['at_date'] }} | Hora: {{ $generated['at_hour'] }}</div>
      <div class="address">Identificação única: {{ $generated['token'] }} | Página: ÚNICA </div>
   </div>
</div>
<div id="details" class="clearfix">
   <div id="client">
      <div class="to">UTILIZADOR</div>
      <h2 class="name">{{ $user_info['posto'] }} {{ $user_info['id'] }} {{ $user_info['name'] }}</h2>
      <div class="address">Email:
         @if ( $user_info['email']=="NÃO PREENCHIDO")
         {{ $user_info['email'] }}
         @else
         <a href="mailto:{{ $user_info['email'] }}">{{ $user_info['email'] }}</a>
         @endif
         | Local preferencial: {{ $user_info['local_pref'] }}
      </div>
      <div class="address">Associação: {{ $user_info['association'] }} </div>
   </div>
</div>
<!-- MARCAÇÕES -->
<div id="details" class="clearfix" style="margin-bottom: 20px !important;">
   <div id="client">
      <h2 style="margin: 0;" class="name">Marcações deste utilizador</h2>
      <h4 style="margin: 0;" class="name">Máximo de 15 dias</h4>
   </div>
</div>
<table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
<thead>
   <tr>
      <th class="no">DATA</th>
      <th class="unit">1ºREFEIÇÃO</th>
      <th class="unit">2ºREFEIÇÃO</th>
      <th class="unit">3ºREFEIÇÃO</th>
   </tr>
</thead>
<tbody>
