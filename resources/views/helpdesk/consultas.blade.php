@extends('layout.master')
@section('page-content')
<div class="modal puff-in-center" id="searchNIMModal" tabindex="-1" role="dialog" aria-labelledby="searchNIMModal" aria-hidden="true">
   <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="z-index: 400;">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Consulta</h5>
            <a href="{{ url()->previous() }}">
            <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </a>
         </div>
         <div class="modal-body">
            <div class="form-group row">
               <label for="reportLocalSelect" class="col-sm-2 col-form-label">NIM</label>
               <div class="col-sm-10">
                  <div class="input-group input-group-sm">
                     @csrf
                     <input type="number" class="form-control form-control-navbar" maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type="search"
                        placeholder=" Procurar NIM" aria-label="Procurar NIM" id="searchBar" name="searchBar">
                     <div class="input-group-append">
                        <button class="btn btn-dark swing-in-top-fwd" type="submit" style="border: 1px solid #ced4da; border-left-width: 0;">
                        <i class="fas fa-search"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <table class="table results hide" id="resultsTable" name="resultsTable">
              <thead>
              </thead>
               <tbody>
                  <tr style="border-top: 5px;">
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="name" name="name">NOME</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="posto" name="posto">POSTO</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="unidade" name="unidade">UNIDADE</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap; text-transform: uppercase;" id="usrtype" name="usrtype">USERTYPE</td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   $('#searchBar').on("change paste keyup click",function(){
     $value=$(this).val();
     if (!$value) {
       $("#resultsTable").addClass("hide");
       $("#name").html("");
       $("#posto").html("");
       $("#unidade").html("");
       $("#usrtype").val("");
       return false;
     }
     $value=$(this).val();
       $.ajax({
           type : 'get',
           url : "{{route('helpdesk.consultas.search')}}",
           data:{
             'search': $value
           },
         success:function(data){
           if (data.nome!=null) {
             $("#resultsTable").removeClass("hide");
             $("#name").html("<a href='http://10.102.21.45/alim/helpdesk/consultas/" + data.id + "'>" + data.nome + "</a>");
             $("#posto").html(data.posto);
             $("#unidade").html(data.unidade);
             $("#usrtype").html(data.user_type + ' / ' + data.user_perm);
           } else {
               $("#resultsTable").addClass("hide");
               $("#name").html("");
               $("#posto").html("");
               $("#unidade").html("");
               $("#usrtype").val("");
           }
       }
     });
   }).delay(1000);
</script>
<script>
   $(document).ready(function() {
     $('#searchNIMModal').modal({
       backdrop: 'static', keyboard: false
     });
     $( "#loading" ).remove();
   });
</script>
@endsection
