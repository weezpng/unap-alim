@component('mail::message')
Exmo(a)&nbsp;<b>{{ $data['posto'] }} {{ $data['nome'] }}</b><br>
A ementa para o dia <b>{{ $data['data_alteracao'] }}</b>, para o <b>{{ $data['ref'] }}</b> foi alterada.
<br><br>Ementa anterior:
@component('mail::panel')
    <b>Sopa</b>&nbsp;{{ $data['sopa_old'] }}<br>
    <b>Prato</b>&nbsp;{{ $data['prato_old'] }}<br>
    <b>Sobremesa</b>&nbsp;{{ $data['sobremesa_old'] }}
@endcomponent
Ementa nova:
@component('mail::panel')
  <b>Sopa</b>&nbsp;{{ $data['sopa'] }}<br>
  <b>Prato</b>&nbsp;{{ $data['prato'] }}<br>
  <b>Sobremesa</b>&nbsp;{{ $data['sobremesa'] }}
@endcomponent
<br>
A alteração foi feita por <b>{{ $data['by'] }}</b> <br>
<br>
<small>Com os melhores cumprimentos,<br>
Unidade de Apoio do Comando do Pessoal - Exército Português.</small>
@endcomponent
