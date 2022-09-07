</tbody>
</table>
</main>
<header style="page-break-before: always;">
   <div class="clearfix">
      <div id="logo">
         <img src="{{ public_path('assets/icons/cmdpesslogo.png') }}">
      </div>
      <div id="company">
         Relatório criado CLOSE TABLE<br>
         <span style="text-transform: uppercase;">{{ $generated['nome'] }}</span><br>
         {{ $generated['id'] }}<br>
         <a href="mailto:{{ $generated['email'] }}"> {{ $generated['email'] }} </a>
      </div>
   </div>
</header>
<main>
