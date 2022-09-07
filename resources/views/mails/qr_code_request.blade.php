@component('mail::message')
O utilizador&nbsp;<b>{{ $data['posto'] }} {{ $data['nim'] }} {{ $data['nome'] }}</b>
fez um pedido de impressão de código QR de entrada em refeitório.<br><br>
Na plataforma, aceda a <b>Gestão</b> > <b>Utilizadores</b> > <b>Todos os utilizadores</b>, e imprima todos os pedidos clicando em <b>Gerar códigos QR.</b> .<br>
<br>
<br>
<small>Com os melhores cumprimentos,<br>
Unidade de Apoio do Comando do Pessoal - Exército Português.</small>
@endcomponent
