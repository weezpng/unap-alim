@component('mail::message')
Exmo(a)s<br>
Ocorreu um erro <b>{{ $data['title'] }}</b>
<br><br>Debug info:
@component('mail::panel')
    <b>{{ $data['message'] }}</b>
@endcomponent
<br>
<small>Com os melhores cumprimentos,<br>
Unidade de Apoio do Comando do Pessoal - Exército Português.</small>
@endcomponent
