@extends('layout.master')
@section('title','HELPDESK: Permissões')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Helpdesk</li>
<li class="breadcrumb-item active">Permissões</li>
@endsection
@section('page-content')
<div class="col-md-12">
   <div class="card @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif card-outline">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
            <h3 class="card-title">Nivel de permissões</h3>
            <div class="card-tools" style="margin-right: 0 !important;">
               <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Maximizar\Minimizar">
               <i class="fas fa-minus"></i></button>
            </div>
         </div>
      </div>
      <div class="card-body">
         <table class="table table-hover table-striped">
            <thead>
               <tr>
                  <th style="width: 1%">
                  </th>
                  <th style="width: 29%">
                     PERMISSÃO
                  </th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
               </tr>
            </thead>
            <tbody>
               <tr style="border-top: none; border-bottom: none;">
                  @foreach($permissionsAccAtrib as $permission)
                  @php
                  $var = explode(',', $permission['permission_apply_to'], 5);
                  @endphp
                  <td style="border-bottom: none; padding-top: 1.25rem !important; border-top: none;">
                     <i class="fas fa-user-shield"></i>
                  </td>
                  <td style="border-top: none; border-bottom: none; padding-top: 1rem !important; padding-bottom: 1rem;">
                     {{$permission['permission_title']}}<br>
                     {{$permission['permission_description']}}
                  </td>
                  <td style="border-top: none; border-bottom: none;">
                     <div class="form-group" style="display: inline-flex; margin-top: 2rem;">
                        <div class="custom-control custom-switch">
                           <input type="checkbox" class="custom-control-input checkbox-acc-ass-type" data-perm-ID="{{$permission['id']}}" id="alim{{$permission['id']}}" name="ALIM" @if(in_array("ALIM", $var)) checked @endif>
                           <label class="custom-control-label" for="alim{{$permission['id']}}"></label>
                        </div>
                        <p style="padding-left: .25rem !important; padding-right: 1rem !important; margin-top: -1rem !important; margin-bottom: 0 !important;">
                           <span style="font-size: .75rem; font-weight: 500;">Pessoal associado à<br></span>
                           Alimentação
                        </p>
                     </div>
                  </td>
                  <td style="border-top: none; border-bottom: none;">
                     <div class="form-group" style="display: inline-flex; margin-top: 2rem;">
                        <div class="custom-control custom-switch">
                           <input type="checkbox" class="custom-control-input checkbox-acc-ass-type" data-perm-ID="{{$permission['id']}}" id="pess{{$permission['id']}}" name="PESS" @if(in_array("PESS", $var)) checked @endif>
                           <label class="custom-control-label" for="pess{{$permission['id']}}"></label>
                        </div>
                        <p style="padding-left: .25rem !important; padding-right: 1rem !important;  margin-top: -1rem !important; margin-bottom: 0 !important;">
                           <span style="font-size: .75rem; font-weight: 500;">Pessoal associado à<br></span>
                           Pessoal
                        </p>
                     </div>
                  </td>
                  <td style="border-top: none; border-bottom: none;">
                     <div class="form-group" style="display: inline-flex; margin-top: 2rem;">
                        <div class="custom-control custom-switch">
                           <input type="checkbox" class="custom-control-input checkbox-acc-ass-type" data-perm-ID="{{$permission['id']}}" id="log{{$permission['id']}}" name="LOG" @if(in_array("LOG", $var)) checked @endif>
                           <label class="custom-control-label" for="log{{$permission['id']}}"></label>
                        </div>
                        <p style="padding-left: .25rem !important; padding-right: 1rem !important; margin-top: -1rem !important; margin-bottom: 0 !important;">
                           <span style="font-size: .75rem; font-weight: 500;">Pessoal associado à<br></span>
                           Logística
                        </p>
                     </div>
                  </td>
                  <td style="border-top: none; border-bottom: none;">
                     <div class="form-group" style="display: inline-flex; margin-top: 2rem;">
                        <div class="custom-control custom-switch">
                           <input type="checkbox" class="custom-control-input checkbox-acc-ass-type" data-perm-ID="{{$permission['id']}}" id="messes{{$permission['id']}}" name="MESSES" @if(in_array("MESSES", $var)) checked @endif>
                           <label class="custom-control-label" for="messes{{$permission['id']}}"></label>
                        </div>
                        <p style="padding-left: .25rem !important; padding-right: 1rem !important; margin-top: -1rem !important; margin-bottom: 0 !important;">
                           <span style="font-size: .75rem; font-weight: 500;">Pessoal associado às<br></span>
                           Messes
                        </p>
                     </div>
                     </div>
                  </td>

                  <td style="border-top: none; border-bottom: none;">
                     <div class="form-group" style="display: inline-flex; margin-top: 2rem;">
                        <div class="custom-control custom-switch">
                           <input type="checkbox" class="custom-control-input checkbox-acc-ass-type" data-perm-ID="{{$permission['id']}}" id="messes{{$permission['id']}}" name="MESSES" @if(in_array("CCS", $var)) checked @endif>
                           <label class="custom-control-label" for="messes{{$permission['id']}}"></label>
                        </div>
                        <p style="padding-left: .25rem !important; padding-right: 1rem !important; margin-top: -1rem !important; margin-bottom: 0 !important;">
                           <span style="font-size: .75rem; font-weight: 500;">Pessoal associado à<br></span>
                           CCS
                        </p>
                     </div>
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
   $(".checkbox-acc-lvl").change(function () {
     var permission_id = $(this).attr('data-perm-ID');
     var user_permi_toggle = $(this).attr('name');
     var is_enabled = $(this).is(':checked');
     $.ajax({
         type: "POST",
         url: "{{route('helpdesk.permissões.change')}}",
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
<script>
   $(".checkbox-acc-ass-type").change(function () {
     var permission_id = $(this).attr('data-perm-ID');
     var user_permi_toggle = $(this).attr('name');
     var is_enabled = $(this).is(':checked');
     $.ajax({
         type: "POST",
         url: "{{route('helpdesk.permissões.specific.change')}}",
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
