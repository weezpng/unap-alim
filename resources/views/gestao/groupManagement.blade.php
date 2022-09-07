@extends('layout.master')
@section('title','Gestão de grupo')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Grupos</li>
<li class="breadcrumb-item active">{{ $groupName }}</li>
@endsection
@section('page-content')
@include('layout.float-btn')
<div class="modal puff-in-center" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Remover utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.destroyFromGroup')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <p>Tem a certeza que pretende remover este utilizador do grupo {{ $groupUnidade }} \ {{ $groupName }} ?</p>
               {{ csrf_field() }}
               <input style="width: 4.5em !important;"class="form-control uppercase-only delete-usr-box" id="nim" name="nim" readonly>
               <input style="width: 22em !important;"class="form-control uppercase-only delete-usr-box" id="name" name="name" readonly>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-danger">Remover</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div class="modal puff-in-center" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Adicionar utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="input-group input-group-sm">
               <input type="hidden" name="_token" value="Yx57kbtRcqrBgNDQzzviplKzjrgWi7uGuT8PzpOu">
               <input class="form-control form-control-navbar" maxlength="8"
                  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                  type="search" placeholder=" Procurar NIM \ Identificação" aria-label="Procurar NIM \ Identificação" id="searchBar" name="searchBar">
               <div class="input-group-append">
                  <button class="btn btn-navbar" type="submit" style="border: 1px solid #ced4da; border-left-width: 0;">
                  <i class="fas fa-search"></i>
                  </button>
               </div>
            </div>
            <table class="table" style="margin-top: 1vh; display: none;" id="resultsTable" name="resultsTable">
               <tbody>
                  <tr style="border-top: 0px;">
                     <a href="">
                        <td style="border-top: 0px; width: 30%; white-space: nowrap;" id="searchednome" name="searchednome">NOME</td>
                     </a>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap;" id="searchedposto" name="searchedposto">POSTO</td>
                     <td style="border-top: 0px; width: 30%; white-space: nowrap;" id="searchedunidade" name="searchedunidade">UNIDADE</td>
                     <td style="border-top: 0px; float: right;">
                        <form method="POST" action="{{route('gestão.addToGrupo')}}">
                           {{ csrf_field() }}
                           <input type="hidden" name="childrenAddToGroup" id="" value="childrenAddToGroup">
                           <input type="hidden" name="groupName" id="" value="{{ $groupRef }}">
                           <button type="submit" style="color: white" class="btn btn-sm btn-primary" href="" id="childrenAddToGroup" name="childrenAddToGroup">ADICIONAR</button>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>
<div class="col-md-12">
<div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
<div class="card-header border-0">
<div class="d-flex justify-content-between">
<h3 class="card-title">{{ $groupUnidade }} \ {{ $groupName }}</h3>
<div class="card-tools">
<a href="{{ url()->previous() }}">Voltar atrás</a>
</div>
</div>
</div>
<div class="card-body">
@if($groupRef!="GERAL")
<div class="card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
<div class="card-header border-0">
<div class="d-flex justify-content-between">
<h3 class="card-title">Definições do grupo</h3>
<div class="card-tools">
<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Minimizar">
<i class="fas fa-minus"></i></button>
&nbsp;&nbsp;
<button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
</div>
</div>
</div>
<div class="card-body">
<form class="form-horizontal" method="POST" action="{{ route('gestão.editGroupName') }}">
{{ csrf_field() }}
<div class="form-group row">
<label for="inputRefer" class="col-sm-2 col-form-label">Referência</label>
<div class="col-sm-10">
<input readonly type="text" class="form-control uppercase-only" id="inputRefer" placeholder="Referencia do grupo" name="inputRefer" value="{{ $groupRef }}">
</div>
</div>
<div class="form-group row">
<label for="inputName" class="col-sm-2 col-form-label">Nome do grupo</label>
<div class="col-sm-10">
<input required type="text" class="form-control uppercase-only" id="inputName" name="inputName" placeholder="Nome do grupo" value="{{ $groupName }}">
</div>
</div>
<div class="form-group row">
<label for="inputLocalRefPref" class="col-sm-2 col-form-label">Local preferencial</label>
<div class="col-sm-10">
<select required class="custom-select" id="inputLocalRefPref"  name="inputLocalRefPref">
@if($localPref==null) <option disabled @if($localPref==null) @endif>Selecione um local preferencial</option> @endif
<option @if($localPref=="QSP") selected @endif value="QSP">Quartel da Serra do Pilar</option>
<option @if($localPref=="QSO") selected @endif value="QSO">Quartel de Santo Ovídio</option>
<option @if($localPref=="MESSE ANTAS") selected @endif value="MESSE ANTAS">Messe das Antas</option>
<option @if($localPref=="MESSE BATALHA") selected @endif value="MESSE BATALHA">Messe da Batalha</option>
</select>
</div>
</div>
<div class="form-group row profile-settings-form-svbtn-spacer">
<div class="offset-sm-2 col-sm-10">
<button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif">Guardar</button>
</div>
</div>
</form>
</div>
<!-- /.card-body -->
</div>
@endif
<div class="d-flex justify-content-between">
<div><h5>Total de utilizadores: &nbsp{{ $howManyUsers }}</h5></div>
<form method="post" action="{{route('marcacao.forgroup')}}" id="marcarEmMassa" name="marcarEmMassa">
@csrf
<input type="hidden" name="groupRef" id="groupRef" value="{{ $groupRef }}">
<button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif groupManagementMarkMealsBtn">Marcar refeições</button>
</form>
</div>
<div class="card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
<div class="card-header border-0">
<div class="d-flex justify-content-between">
<h3 class="card-title">Utilizadores deste grupo</h3>
<div class="card-tools">
<a href="#" data-toggle="modal" data-target="#newUserModal">Adicionar utilizador</a>
&nbsp;&nbsp;
<button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
</div>
</div>
</div>
<div class="card-body">
<table class="table table-hover dataTable dtr-inline">
<thead>
<tr>
<th style="width: 15%">
NÚMERO IDENTIFICADOR
</th>
<th style="width: 15%">
NOME
</th>
<th style="width: 15%">
POSTO
</th>
<th style="width: 45%">
UNIDADE / GRUPO
</th>
<th>
</th>
</tr>
</thead>
<tbody>
@foreach($childUsers as $user)
<tr>
<td>
@if($user['childPosto']=="CIVIL")
<i class="far fa-user"></i>&nbsp&nbsp
CC
@else
<i class="fas fa-user"></i>&nbsp&nbsp
NIM
@endif
&nbsp{{ $user['childID'] }}
</td>
<td class="uppercase-only">
{{ $user['childNome'] }}
</td>
<td>
{{ $user['childPosto'] }}
</td>
<td>
{{$groupUnidade}} / {{$groupName}} ({{ $groupRef }})
</td>
<td>
<a style="color: white" href="{{route('gestão.viewUserChildren', $user['childID'])}}" class="btn btn-sm btn-primary children-ingroup-context-btn" href="">Gerir</a>
@if($groupRef!="GERAL")
<button type="submit" data-id="{{ $user['childID'] }}" data-name="- {{ $user['childPosto'] }} - {{ $user['childNome'] }}"
   data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-danger delete children-ingroup-context-btn">Remover do grupo</button>
@endif
</td>
</tr>
@endforeach
</tbody>
</div>
</table>
</div>
<!-- /.card-body -->
</div>
<!-- /.card-body -->
</div>
</div>
</div>
@endsection
@section('extra-scripts')
<script>
   $(document).on('click','.delete',function(){
        let id = $(this).attr('data-id');
        let name = $(this).attr('data-name');
        $('#nim').val(id);
        $('#name').val(name);
   });
</script>
<script>
   $('#searchBar').on('keyup',function(){
     $("#resultsTable").css("display", "none");
     $value=$(this).val();
       $.ajax({
           type : 'get',
           url : "{{route('gestão.grupo.searchUserToAdd')}}",
           data:{
             'unidade': '{{$groupUnidade}}',
             'search': $value
           },
         success:function(data){
           if (data.nome!="") {
             $("#resultsTable").css("display", "block");
             $("#searchednome").html(data.nome);
             $("#searchedposto").html(data.posto);
             $("#searchedunidade").html(data.unidade);
           } else {
               $("#resultsTable").css("display", "none");
               $("#searchednome").html("");
               $("#searchedposto").html("");
               $("#searchedunidade").html("");
               $("#searchedUsrtype").val("");
           }
       }
     });
   })
</script>
<script type="text/javascript">
   $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>
@endsection
