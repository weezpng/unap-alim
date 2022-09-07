<tfoot>
   <tr>
      <td colspan="3" style="border-bottom: none !important;"></td>
      <td colspan="2" style="border-bottom: none !important;"></td>
      <td style="border-bottom: none !important;"></td>
   </tr>
   <tr>
      <td style="border-top: none !important;" colspan="2"></td>
      <td style="border-top: none !important;" colspan="2">TOTAL DE 1ºREFEIÇÃO</td>
      <td style="border-top: none !important;">{{ $total1Ref }}</td>
   </tr>
   <tr>
      <td style="border-top: none !important;" colspan="2"></td>
      <td style="border-top: none !important;" colspan="2">TOTAL DE 2ºREFEIÇÃO</td>
      <td style="border-top: none !important;">{{ $total2Ref }}</td>
   </tr>
   <tr>
      <td style="border-top: none !important;" colspan="2"></td>
      <td style="border-top: none !important;" colspan="2">TOTAL DE 3ºREFEIÇÃO</td>
      <td style="border-top: none !important;">{{ $total3Ref }}</td>
   </tr>
   <tr>
      <td style="border-top: none !important;" colspan="2"></td>
      <td style="border-top: none !important;" colspan="2">TOTAL</td>
      <td style="border-top: none !important;">{{ $totalRefs }}</td>
   </tr>
</tfoot>
</table>
<div id="notices">
   <div>Filtros</div>
   <div class="notice">Tipo de relatório: TEMPO<br>Local: {{ $localFilter }}<br>Período de tempo: {{ $timeStartFilter }} a {{ $timeEndFilter }}</div>
</div>
</main>
<footer>
   Relatório gerado por sistema informatico de Gestão de Alimentação, não dispensa confirmação.
</footer>
</body>
</html>
