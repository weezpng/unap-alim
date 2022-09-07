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
