<tr>
   <td class="no" style="text-align: center;"> {{ $data }} </td>
   <td class="unit" style="text-align: center;">
      @if ($YesOrNo1REF==true)
      <b>MARCADA</b> <br>{{ $local1REF }}
      @else
      <B>NÃO </B>
      @endif
   </td>
   <td class="unit" style="text-align: center;">
      @if ($YesOrNo2REF==true)
      <b>MARCADA</b> <br>{{ $local2REF }}
      @else
      <B>NÃO </B>
      @endif
   </td>
   <td class="unit" style="text-align: center;">
      @if ($YesOrNo3REF==true)
      <b>MARCADA</b> <br>{{ $local3REF }}
      @else
      <B>NÃO </B>
      @endif
   </td>
</tr>
