@extends('layout.master')
@section('title','HELPDESK: Gestão de permissões')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Gestão</li>
<li class="breadcrumb-item active">Permissões</li>
@endsection
@section('page-content')
<div class="col-md-12">
   <div class="card">
      <div class="card-body">
         <table class="table table-hover text-nowrap table-striped">
            <thead>
               <tr>
                  <th style="width: 40%">
                     PERMISSION
                  </th>
                  <th></th>
                  <th></th>
                  <th></th>
               </tr>
            </thead>
            <tbody>
               <tr style="border-top: none; border-bottom: none;">
                  @foreach($permissions as $perm)
                  <td style="border-top: none; border-bottom: none; height: 2vh;">
                     <i class="fas fa-user-shield"></i>&nbsp&nbsp
                     {{$perm['permission']}}
                  </td>
                  <td style="border-top: none; border-bottom: none; height: 2vh;">
                     <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" data-perm-ID="{{$perm['id']}}" id="adminPerm{{$perm['id']}}" name="ADMIN" @if($perm['usergroupAdmin']=="Y")checked @endif>
                        <label class="custom-control-label" for="adminPerm{{$perm['id']}}">ADMIN</label>
                     </div>
                  </td>
                  <td style="border-top: none; border-bottom: none; height: 5vh !important;">
                     <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" data-perm-ID="{{$perm['id']}}" id="superPerm{{$perm['id']}}" name="SUPER" @if($perm['usergroupSuper']=="Y")checked @endif>
                        <label class="custom-control-label" for="superPerm{{$perm['id']}}">POC</label>
                     </div>
                  </td>
                  <td style="border-top: none; border-bottom: none; height: 5vh !important;">
                     <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" data-perm-ID="{{$perm['id']}}" id="userPerm{{$perm['id']}}" name="USER" @if($perm['usergroupUser']=="Y")checked @endif>
                        <label class="custom-control-label" for="userPerm{{$perm['id']}}">USER</label>
                     </div>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
      <!-- /.card-body -->
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   $(".custom-control-input").change(function () {
     var permission_id = $(this).attr('data-perm-ID');
     var user_permi_toggle = $(this).attr('name');
     var is_enabled = $(this).is(':checked');
     $.ajax({
         type: "POST",
         url: "{{route('gestao.permissões.change')}}",
         async: true,
         data: {
             "_token": "{{ csrf_token() }}",
             id: permission_id,
             user_perm: user_permi_toggle,
             enable: is_enabled
         },
         success: function (msg) {
             if (msg != 'success') {
               alert(msg);
             }
         }
     });
   });
</script>
@endsection
