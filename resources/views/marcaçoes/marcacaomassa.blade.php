@extends('layout.master')
@section('title','Marcações em massa')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Marcações</li>
<li class="breadcrumb-item active">@if (isset($groupName)) {{ $groupName }} @else Grupos @endif</li>
@endsection
@section('page-content')
@if(isset($groupName))
<div class="modal fade" id="marcarForGroup" tabindex="-1" role="dialog" aria-labelledby="searchGroupModal" aria-hidden="true">
   <div class="modal-dialog" role="document" style="margin-top: 30vh; padding: 0rem;">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="marcarForGroupTitle">Marcação de refeição</h5>
            <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form class="form-horizontal" method="POST" action="{{ route('marcacao.ref.forgrupo') }}" id="marcarRefGrupo">
            <div class="modal-body">
               <p id="marcarForGroupText">
                  Confirme os utilizadores para marcar a refeição para o dia <span id="dateOfMarcar">X</span>
               </p>
               <div class="form-group row">
                  @csrf
                  <input type="hidden" id="mealForGroup" name="mealForGroup" value=""/>
                  <input type="hidden" id="dateForGroup" name="dateForGroup" value=""/>
               </div>
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-3 col-form-label">Local</label>
                  <div class="col-sm-9">
                     <div class="input-group input-group-sm">
                        <select class="custom-select" id="localDeRef" name="localDeRef" required>
                           <option disabled="" selected="">Selecione o local de refeição</option>
                           @foreach ($locais as $key => $local)
                           <option value="{{ $local['refName'] }}" @if($local['status']=="NOK") disabled @endif> {{ $local['localName'] }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
               </div>
               <div style="max-height: 20vh; overflow-y: auto; margin-bottom: 2rem;">
                  @foreach ($users as $key => $usr)
                  <div class="input-group">
                     <div class="input-group-prepend">
                        <span class="input-group-text">
                        <input type="checkbox" checked name="IDs[]" value="{{ $usr['id'] }}">
                        </span>
                     </div>
                     <input type="text" style="font-size: 1.1rem;" class="form-control" readonly value="{{ $usr['posto'] }} &nbsp;{{ $usr['id'] }} &nbsp;{{ $usr['name'] }}">
                  </div>
                  @endforeach
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" id="submitBtn" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" style="width: 7rem;">Marcar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endif
<div class="modal fade" id="searchGroupModal" tabindex="-1" role="dialog" aria-labelledby="searchGroupModal" aria-hidden="true">
   <div class="modal-dialog" role="document" style="margin-top: 30vh; padding: 0rem;">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">@if (!empty($groups)) Selecionar grupo @else Erro @endif </h5>
            @if (isset($groupRef))
            <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            @endif
         </div>
         <form class="form-horizontal" method="POST" action="{{ route('marcacao.forgroup') }}">
            <div class="modal-body">
               @if (!empty($groups))
               <div class="form-group row">
                  @csrf
                  <label for="reportLocalSelect" class="col-sm-3 col-form-label">GRUPO</label>
                  <div class="col-sm-9">
                     <div class="input-group input-group-sm">
                        <select class="custom-select" id="inputGroup" name="inputGroup">
                           <option disabled selected >Selecione um grupo</option>
                           @foreach ($groups as $group)
                           <option value="{{ $group['groupID'] }}">{{ $group['groupName'] }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="reportLocalSelect" class="col-sm-3 col-form-label">SUB-GRUPO</label>
                  <div class="col-sm-9">
                     <div class="input-group input-group-sm">
                        <select class="custom-select" id="inputSubGroup"  name="inputSubGroup" disabled>
                           <option disabled selected >Selecione o sub-grupo</option>
                           <option value="GERAL">GERAL</option>
                        </select>
                     </div>
                  </div>
               </div>
               @else
               <h6>Você não tem nenhum grupo de utilizadores criado.</h6>
               @endif
            </div>
            <div class="modal-footer">
               @if (!empty($groups))
               <button type="submit" disabled id="submitBtn" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" >Selecionar</button>
               @if (isset($groupRef))
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
               @endif
               @else
               <a href="{{ url()->previous() }}">
               <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif slide-in-blurred-top" >Voltar</button>
               </a>
               @endif
            </div>
         </form>
      </div>
   </div>
</div>
@if(isset($groupName))
<div class="col-md-12">
   <div class="card card-outline card-dark">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">{{ $groupName }}</h3>
            <div class="card-tools">
               @if (isset($groupRef))
               <a href="{{ route('gerir.subgrupo', [$groupRef, $subRef]) }}">Editar grupo &nbsp; <i class="fas fa-edit">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i></a>
               @endif
               <a href="#" id="newEmentaModalBtn" data-toggle="modal" data-target="#searchGroupModal">Selecionar outro grupo &nbsp; <i class="fas fa-users"></i></a>
               &nbsp;&nbsp;&nbsp;
               <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </div>
         </div>
      </div>
      @php
      $today = date("Y-m-d");
      @endphp
      <div class="card-body">
         <div class="card-body" style="padding-top: .25rem !important;padding-right: 0 !important;">
            @foreach ($marcacoes as $key1 => $refs)
            <div class="card card-dark collapsed-card" style="box-shadow: none !important; margin-top: 1rem !important; margin-bottom: 1rem !important;">
               <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                     <h3 class="card-title">
                        @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $mes_index = date('m', strtotime($refs['data']));
                        @endphp
                        {{ date('d', strtotime($refs['data'])) }} {{ $mes[($mes_index - 1)] }}
                     </h3>
                     <div class="card-tools">
                        <span class="badge badge-info right" style="margin-right: 1rem !important;">{{ $refs['count'] }} marcações</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Maximizar\Minimizar">
                        <i class="fas fa-plus"></i></button>
                     </div>
                  </div>
               </div>
               <div class="card-body" style="padding-left: 0 !important;padding-top: .5rem !important;padding-right: 2rem !important;  padding-bottom: 1.5rem !important;">
                  @foreach ($refs['refs'] as $key2 => $users)
                  @if ($key2!="2REF")
                  <div class="card card-dark collapsed-card" style="box-shadow: 0 0 1px rgb(0 0 0 / 35%), 0 1px 3px rgb(0 0 0 / 35%) !important; margin-top: .25rem !important; margin-bottom: .25rem !important;">
                     <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                           <h3 class="card-title">
                              @if($key2=="1REF")
                              Pequeno-almoço

                              @else
                              Jantar
                              @endif
                           </h3>
                           <div class="card-tools">
                              <span class="badge badge-info right" style="margin-right: 1rem !important;">{{ $users['count'] }} marcações</span>
                              <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Maximizar\Minimizar">
                              <i class="fas fa-plus"></i></button>
                           </div>
                        </div>
                     </div>
                     <div class="card-body" style="padding-left: 1rem !important;padding-top: .25rem !important;padding-right: 0 !important;">
                        <div class="d-flex justify-content-between">
                           <table class="table projects" style="margin: 1rem;width: 35vw;">
                              @if($key2!="1REF")
                              <tbody>
                                 <tr>
                                    <td style="border-top: none; padding: .25rem; width: 15%">
                                       <b>Sopa</b>
                                    </td>
                                    <td style="border-top: none; padding: .25rem;">
                                       {{ $users['sopa'] }}
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="border-top: none; padding: .25rem;">
                                       <b>Prato</b>
                                    </td>
                                    <td style="border-top: none; padding: .25rem;">
                                       {{ $users['prato'] }}
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="border-top: none; padding: .25rem;">
                                       <b>Sobremesa</b>
                                    </td>
                                    <td style="border-top: none; padding: .25rem;">
                                       {{ $users['sobremesa'] }}
                                    </td>
                                 </tr>
                              </tbody>
                              @else
                              <td style="border-top: none; padding: .25rem; width: 15%">
                                 <b>Pequeno Almoço</b>
                              </td>
                              @endif
                           </table>
                           <button type="button" style="max-width: 10rem; max-height: 2rem; margin: 1.5rem;" data-ref="{{ $key2 }}" data-date="{{ $refs['data'] }}" data-datepresent="{{ date('d/m/Y', strtotime($refs['data'])) }}"
                              class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn generalMarcar">
                           <i class="far fa-calendar-check">&nbsp;&nbsp;</i>
                           Marcar todos&nbsp;&nbsp;&nbsp;&nbsp;</button>
                        </div>
                        <table class="table table-striped projects" style="margin-bottom: 15vh;">
                           <thead>
                              <tr>
                                 <th style="width: 15%; border-bottom-width: 0px;">
                                 </th>
                                 <th style="width: 15%; border-bottom-width: 0px;">
                                 </th>
                                 <th style="width: 15%; border-bottom-width: 0px;">
                                 </th>
                                 <th style="width: 45%; border-bottom-width: 0px;">
                                 </th>
                                 <th style="border-bottom-width: 0px;">
                                 </th>
                              </tr>
                           </thead>
                           <tbody>
                              @foreach ($users["USERS"] as $user)
                              <tr>
                                 <td>
                                    <a href="{{ route('gestão.viewUserChildren', $user['id']) }}">
                                    {{ $user['id'] }}
                                    </a>
                                 </td>
                                 <td class="uppercase-only">
                                    {{ $user['name'] }}
                                 </td>
                                 <td>
                                    {{ $user['posto'] }}
                                 </td>
                                 <td>
                                    {{ $user['type'] }}
                                 </td>
                                 @php $formid = Str::random(32); @endphp
                                 <td id="formParent{{ $formid }}">
                                    @php
                                    $today = date("Y-m-d");
                                    $maxdate = date("Y-m-d", strtotime("-".$maxDays." days"));
                                    $maxDateAdd = date("Y-m-d", strtotime("+".$marcarRefMax." days"));
                                    @endphp
                                    @if ($user['marcado']=="1")
                                    @php $maxdate = date("Y-m-d", strtotime("-".$maxDays." days", strtotime($refs['data']))); @endphp
                                    @if($today<=$maxdate)
                                    <form method="POST" action="{{route('marcacao.destroy')}}">
                                       @csrf
                                       <input type="hidden" id="id" name="id" value="{{$refs['id']}}"></input>
                                       <button type="submit" class="btn btn-sm btn-danger remove-ref-btn"><i class="fas fa-trash"></i>&nbspRemover</button>
                                    </form>
                                    @else
                                    <a class="btn btn-danger btn-sm disabled remove-ref-btn" href="#"><i class="fas fa-ban">&nbsp</i>Bloqueada</a>
                                    @endif
                                    @elseif ($user['marcado']=="0")
                                    @php $maxdateToAdd = date("Y-m-d", strtotime("-".$maxDateAdd." days", strtotime($refs['data']))); @endphp
                                    @if($today>=$maxdateToAdd)  <!--  CHANGE TO <= -->
                                    <form method="POST" action="{{route('marcacao.store.children')}}" id="{{$formid}}" name="{{$formid}}">
                                       @csrf
                                       <input type="hidden" id="data{{$formid}}" name="data" value="{{$refs['data']}}"></input>
                                       <input type="hidden" id="ref{{$formid}}" name="ref" value="{{$key2}}"></input>
                                       <input type="hidden" id="id{{$formid}}" name="id" value="{{$user['id']}}"></input>
                                       <input type="hidden" id="localDeRef{{$formid}}" name="localDeRef" value=""></input>
                                       <div class="btn-group">
                                          <button type="button" style="width: 130px !important; height: 31px !important;" class="btn btn-sm btn-primary dropdown-toggle dropdown-icon" data-toggle="dropdown"
                                             aria-expanded="false">
                                             Marcar&nbsp&nbsp&nbsp
                                             <span class="sr-only" style="">Toggle Dropdown</span>
                                             <div class="dropdown-menu dropdown-menu-local" role="menu">
                                                @foreach ($locais as $key => $local)
                                                @if ($local['refName']!=$localPref)
                                                <a class="dropdown-item @if($local['status']=="NOK") disabled-drop @endif"
                                                @if($local['status']!="NOK" )onclick="changeLocalAndPost('{{$local['refName']}}', '{{ $formid }}')"@endif>{{$local['localName']}}
                                                </a>
                                                @else
                                                <a class="dropdown-item @if($local['status']=="NOK") disabled-drop @endif"
                                                @if($local['status']!="NOK" )onclick="changeLocalAndPost('{{$local['refName']}}', '{{ $formid }}')"@endif><b>Preferencial&nbsp&nbsp</b>{{$local['localName']}}
                                                </a>
                                                @endif
                                                @endforeach
                                             </div>
                                          </button>
                                       </div>
                                    </form>
                                    @else
                                    <button type="button" class="btn btn-sm btn-warning disabled marcar-ref-btn">Data ultrapassada</button>
                                    @endif
                                    @endif
                                 </td>
                              </tr>
                              @endforeach
                           </tbody>
                        </table>
                     </div>
                  </div>
                  @endif
                  @endforeach
               </div>
            </div>
            @endforeach
         </div>
      </div>
      <!-- /.card-body -->
   </div>
</div>
@endif
@endsection
@section('extra-scripts')
@if (!isset($groupRef))
<script type="text/javascript">
   $(document).ready(function () {
       $('#searchGroupModal').modal({backdrop: 'static', keyboard: false});
     });
</script>
@endif
<script>
   $("#marcarRefGrupo").on("submit", function(e){
     e.preventDefault();

     $.ajax({
     url: '{{ route('marcacao.ref.forgrupo') }}',
     type: 'post',
     data: $('#marcarRefGrupo').serialize(),
     success: function(msg) {
       if (msg=="success") {
         $('#marcarForGroup').modal('hide');
         location.reload();
       } else {
         $("#marcarForGroupText").text("Ocorreu um erro.");
         $("#dateOfMarcar").text('');
       }
      }
    });
   })

</script>
<script>
   function changeLocalAndPost(toWhere, formID){
     var campoID="localDeRef" + formID;
     var campoData="data" + formID;
     var refData="ref" + formID;
     var idData="id"+ formID;

     document.getElementById(campoID).value = toWhere;
     var data = document.getElementById(campoData).value;
     var ref = document.getElementById(refData).value;
     var id = document.getElementById(idData).value;

     $.ajax({
       url: "{{route('marcacao.store.children')}}",
       type: "POST",
       data:{
         "_token": "{{ csrf_token() }}",
           id:id,
           data:data,
           ref:ref,
           localDeRef:toWhere,
       },
       success:function(response){
         if (response) {
           if (response != 'success') {
             document.getElementById("errorAddingTitle").innerHTML = "Erro";
             document.getElementById("errorAddingText").innerHTML = "Erro a fazer marcação.";
             $("#errorAddingModal").modal()
           } else {
             var form = "#" + formID;
             var formParent = "#formParent" + formID;
             $(form).remove();
             $(formParent).append("<button type='submit' disabled class='btn btn-sm btn-success remove-ref-btn'><i class='fas fa-check'></i></i>&nbsp;&nbsp;Marcada</button>");
           }
         }
       }
      });
   }
</script>
<script>
   $('.generalMarcar').click(function(e){
     var data = $(this).attr( "data-date" )
     var data_present = $(this).attr( "data-datepresent" )
     var ref = $(this).attr( "data-ref" )
     $("#dateOfMarcar").text(data_present);
     $("#mealForGroup").attr("value",ref);
     $("#dateForGroup").attr("value",data);
     $("#marcarForGroup").modal()
   })
</script>
<script>
   $('#inputSubGroup').change(function(){
       $("#submitBtn").prop('disabled', false);
   });

   $('#inputGroup').change(function(){
      var group = $(this).val();
      $.ajax({
         url : '{{ route( 'marcacao.getgroups' ) }}',
         data: {
           "_token": "{{ csrf_token() }}",
           "group": group,
           },
         type: 'post',
         dataType: 'json',
         success: function( result )
         {
             $('#inputSubGroup').empty();
             $('#inputSubGroup').append($('<option selected>', {disabled:'disabled',text:'Selecione o sub-grupo'}));
             $('#inputSubGroup').append($('<option>', {value:'GERAL', text:'GERAL'}));
              $.each( result, function(k, v) {
                   $('#inputSubGroup').append($('<option>', {value:k, text:v}));
              });
              $("#inputSubGroup").prop('disabled', false);
         },
         error: function()
        {
            alert('error...');
        }
      });
   });
</script>
@endsection
