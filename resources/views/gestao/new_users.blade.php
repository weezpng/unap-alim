@extends('layout.master')
@section('title','Novos utilizadores')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Novos utilizadores</li>
@endsection
@section('page-content')
@include('layout.float-btn')
@if ($EXPRESS_TOKEN_GENERATION)
<div class="modal puff-in-center" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Criação de token</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestão.userCreateToken')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
               <p>
                  Insira um NIM para criar um token de activação express da conta.
               </p>
               @csrf
               <div class="form-group row">
                  <div class="col-sm-12">
                     <input type="number" maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control uppercase-only" id="inputNIM" name="inputNIM" placeholder="NIM">
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" style="width: 5rem;">Criar</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endif
@if ($ACCEPT_NEW_MEMBERS)
<div class="modal puff-in-center" id="confirmarAssocUsr" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Transferencia de utilizador</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p style="margin-bottom: 25px;">Este utilizador não têm uma conta, é necessário associar a uma conta de gestão.
               Procure na barra abaixo um NIM para o associar a um novo gestor.
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
                        <button class="btn btn-navbar" type="submit" style="border: 1px solid #ced4da; border-left-width: 0;">
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
@endif
<div class="col-md-12">
   @if($ACCEPT_NEW_MEMBERS==true)
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header">
         <h3 class="card-title">Confirmação de novas contas</h3>
         <div class="card-tools">
            @if ($EXPRESS_TOKEN_GENERATION)
            <a href="#" id="newTokenModalBtn" data-toggle="modal" data-target="#exampleModal">Criar token de activação express&nbsp; <i class="fas fa-user-astronaut"></i>  &nbsp;</a>
            @endif
            &nbsp;&nbsp;&nbsp;&nbsp;
            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </button>
         </div>
      </div>
      <div class="card-body" style="overflow-y: auto; max-height: 40vh;">
         @if(!empty($users))
         <table class="table table-striped projects">
            <thead>
               <tr>
                  <th style="width: 10%">
                     NIM
                  </th>
                  <th style="width: 15%">
                     NOME
                  </th>
                  <th style="width: 20%">
                     Email
                  </th>
                  <th style="width: 15%">
                     Tipo de conta
                  </th>
                  <th>
                     Unidade
                  </th>
                  <th style="width: 10%">
                  </th>
               </tr>
            </thead>
            <tbody>
               @foreach($users as $user)
               <tr>
                  <td>
                     <i class="fas fa-user-plus"></i>&nbsp&nbsp
                     @if ($user['type']!="Utilizador associado")
                     <a href="{{ route('user.profile',  $user['nim']) }}">{{ $user['nim'] }}</a>
                     @else
                     <a href="{{ route('gestão.viewUserChildren',  $user['nim']) }}">{{ $user['nim'] }}</a>
                     @endif
                  </td>
                  <td class="uppercase-only">
                     {{ $user['name'] }}
                  </td>
                  <td>
                     {{ $user['email'] }}
                  </td>
                  <td>
                     {{ $user['type'] }}
                  </td>
                  <td>
                     {{ $user['unidade'] }}
                  </td>
                  <td>
                     <form method="POST" action="{{route('gestão.newUsersConfirm')}}">
                        @csrf
                        <input type="hidden" id="nim" name="nim" value="{{$user['nim']}}"></input>
                        <button type="submit" class="btn btn-sm btn-primary marcar-ref-btn" style="float: right; ">Confirmar</button>
                     </form>
                     <form method="POST" action="{{route('gestão.newUsersReject')}}">
                        @csrf
                        <input type="hidden" id="nim" name="nim" value="{{$user['nim']}}"></input>
                        <button type="submit" class="btn btn-sm btn-danger marcar-ref-btn" style="float: right; margin-top: 5px;">Remover</button>
                     </form>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
         @else
         Nenhum utilizador novo para confirmar.
         @endif
      </div>
      <!-- /.card-body -->
   </div>
   @endif
   @if($CONFIRM_UNIT_CHANGE)
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header">
         <h3 class="card-title">Confirmação de transferência</h3>
         <div class="card-tools">
            &nbsp;
            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
            </button>
         </div>
      </div>
      <div class="card-body" style="overflow-y: auto; max-height: 40vh;">
         @if(!empty($transferedUsers))
         <table class="table table-striped projects">
            <thead>
               <tr>
                  <th style="width: 10%">
                     NIM
                  </th>
                  <th style="width: 15%">
                     NOME
                  </th>
                  <th style="width: 25%">
                     Email
                  </th>
                  <th style="width: 15%">
                     Tipo de conta
                  </th>
                  <th style="width: 20%">
                     Transferido de
                  </th>
                  <th style="width: 20%">
                    A tranferir para
                  </th>
                  <th>
                  </th>
               </tr>
            </thead>
            <tbody>
               @foreach($transferedUsers as $user)
               @if ($user['isAuthOK']===true)
               <tr>
                  <td>
                     <i class="fas fa-user-plus"></i>&nbsp&nbsp
                     @if ($user['type']!="Utilizador associado")
                     <a href="{{ route('user.profile',  $user['nim']) }}">{{ $user['nim'] }}</a>
                     @else
                     <a href="{{ route('gestão.viewUserChildren',  $user['nim']) }}">{{ $user['nim'] }}</a>
                     @endif
                  </td>
                  <td class="uppercase-only">
                     {{ $user['name'] }}
                  </td>
                  <td>
                     {{ $user['email'] }}
                  </td>
                  <td>
                     {{ $user['type'] }}
                  </td>
                  <td>
                     {{ $user['old_unidade'] }}
                  </td>
                  <td>
                     {{ $user['new_unidade'] }}
                  </td>
                  <td>
                     @if ($user['type']!="Utilizador associado")
                     <form method="POST" action="{{route('gestão.movedUsersConfirm')}}">
                        @csrf
                        <input type="hidden" id="nim" name="nim" value="{{$user['nim']}}"></input>
                        <button type="submit" class="btn btn-sm btn-primary marcar-ref-btn">Aceitar</button>
                     </form>
                     @else
                     <button  type="submit" class="btn btn-sm btn-primary marcar-ref-btn assocUsrBtn" data-id="{{ $user['nim'] }}" data-toggle="modal" data-target="#confirmarAssocUsr">Aceitar</button>
                     @endif
                     <form method="POST" action="{{route('gestão.movedUsersReject')}}">
                        @csrf
                        <input type="hidden" id="nim" name="nim" value="{{$user['nim']}}"></input>
                        <button type="submit" class="btn btn-sm btn-danger marcar-ref-btn" style="margin-top: 5px;">Negar</button>
                     </form>
                  </td>
               </tr>
               @endif
               @endforeach
            </tbody>
         </table>
         @else
         Nenhuma transferencia de unidade para confirmar.
         @endif
      </div>
      <!-- /.card-body -->
   </div>
   @endif
</div>
@endsection
@if ($ACCEPT_NEW_MEMBERS)
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
             console.log(item);
             var usrToAssc = $( "#nimToAssoc" ).val();
             var btnUrl = '<a href="/gestão/associar/' + usrToAssc + '/' + item.id   + '"><button class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" style="float: right;">Associar</button></a>'
             trHTML += '<tr>'
             + '<td><a href="/user/' + item.id  + '">' + item.id + '</a> </td>'
             + '<td>' + item.name + '</td>'
             + '<td>' + item.posto + '</td>'
             + '<td>' + btnUrl + '</td>'
             + '</tr>';
           });
           $('#resultadosProcura').append(trHTML);
       }
     });
   }).delay(1000);
</script>
@endsection
@endif
