@extends('layout.master')
@section('page-content')
<div class="modal fade" id="confirmarAssocUsr" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Transferencia de utilizador</h5>
            <a href="{{ url()->previous() }}">
            </a>
         </div>
         <div class="modal-body">
            <p style="margin-bottom: 25px;">
               Para concluir o pedido de troca de unidade, vocé deve indicar um utilizador para herdar as suas associações.<br>
               Esse processo só será concluido quando a sua troca de unidade for confirmada.
            </p>
            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-2 col-form-label">NIM</label>
               <div class="col-sm-10">
                  <div class="input-group input-group-sm">
                     @csrf
                     <input type="hidden"  id="nimToAssoc" name="nimToAssoc"/>
                     <input type="number" class="form-control form-control-navbar" maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="search"
                        placeholder=" Procurar NIM" aria-label="Procurar NIM" id="searchBar" name="searchBar">
                     <div class="input-group-append">
                        <button class="btn-navbar" type="submit" style="border: 1px solid #ced4da; border-left-width: 0;">
                        <i class="fas fa-search"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <table class="table results hide" id="resultadosProcura" name="Resultados">
               <tbody>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   $(document).on('click','.assocUsrBtn',function(){
        let id = $(this).attr('data-id');
        $('#nimToAssoc').val(id);
   });
</script>
<script>
   $('#searchBar').on("change paste keyup click",function(){
     $value=$(this).val();
     if (!$value) {
       $("#resultadosProcura").addClass("hide");
       return false;
     }
     $value=$(this).val();
       $.ajax({
           type : 'get',
           url : "{{route('search.User.Associate')}}",
           data:{
             'search': $value
           },
         success:function(data){
           $('#resultadosProcura').empty();
           $("#resultadosProcura").removeClass("hide");
           var trHTML = '';
           $.each(data, function (i, item) {
             if (item.id!="{{ $from }}") {
               var usrToAssc = $( "#nimToAssoc" ).val();
               var fromHtml = '<form method="POST" action=' + "{{ route("user.transfer.assoc") }}" + '> @csrf <input type="hidden" id="from_user" name="from_user" value="' + {{ $from }} + '"\><input type="hidden" id="to_user" name="to_user" value="' + item.id + '"\><button type="submit" style="opacity: 1 !important; float: right;" class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif slide-in-blurred-top" style="float: right;">Transferir</button></form>';
               trHTML += '<tr>'
               + '<td>' + item.id + '</td>'
               + '<td>' + item.name + '</td>'
               + '<td>' + item.posto + '</td>'
               + '<td>' + fromHtml + '</td>'
               + '</tr>';
             }
           });
           $('#resultadosProcura').append(trHTML);
       }
     });
   }).delay(1000);
</script>
<script>
   $(document).ready(function() {
     $( "#loading" ).fadeOut(500, function() {
       $( "#loading" ).remove();
       $('#confirmarAssocUsr').modal({backdrop: 'static', keyboard: false})
     });
   });
</script>
@endsection
