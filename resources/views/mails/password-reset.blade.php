@component('mail::message')
Exmo(a)&nbsp;<b>{{ $data['posto'] }} {{ $data['nome'] }}</b><br>
A sua password para a aplicação de gestão de refeições foi redefinida.
<br><br>A password temporária é a seguinte:
@component('mail::panel')
    <b>{{ $data['pw'] }}</b>
@endcomponent
Após iniciar sessão, é obrigatório definir uma nova password. <br>
<br>
<small>Com os melhores cumprimentos,<br>
Unidade de Apoio do Comando do Pessoal - Exército Português.</small>
@endcomponent
