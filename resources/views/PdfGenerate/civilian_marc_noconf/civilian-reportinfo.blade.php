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
