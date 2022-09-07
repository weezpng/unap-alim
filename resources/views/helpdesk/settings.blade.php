@extends('layout.master')
@section('title','HELPDESK: Definições')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Helpdesk</li>
<li class="breadcrumb-item active">Definições</li>
@endsection
@section('page-content')
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
           <h3 class="card-title">&nbsp;</h3>
           &nbsp;
           <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
         </div>
      </div>
      <div class="card-body">
         <div style="margin: 1rem;"></div>
         @foreach ($settings as $key => $set)
         <div class="row">
            <div class="col-md-1">
               <i class="far fa-circle" style="margin-top: 10px !important;"></i>
            </div>
            <div class="col-md-8">
               <b>{{ $set['settingTitle'] }}</b>
               <p>
                  {{ $set['settingText'] }}
               </p>
            </div>
            <div class="col-md-3">
               @if ($set['settingToggleMode']=="INT")
               <label for="customRange3">{{ $set['settingToggleIntLabel'] }}: </label>
               <label id="{{$set['id']}}">{{ $set['settingToggleInt'] }}</label>
               <input type="range" class="custom-range" min="1" max="15" step="1.0" id="{{$set['id']}}" name="{{$set['id']}}" onchange="updateLabelAndPost(this.value, this.name)">
               @elseif ($set['settingToggleMode']=="BOOLEAN")
               <div class="custom-control custom-switch" style="padding-top: 1.5rem;">
                  <input type="checkbox" class="custom-control-input" data-perm-ID="{{$set['id']}}" id="{{$set['id']}}" @if($set['settingToggleBoolean']=="Y")checked @endif>
                  <label class="custom-control-label" for="{{$set['id']}}"> {{$set['settingToggleBoolLabel']}} </label>
               </div>
               @endif
            </div>
            <div style="margin: .5rem;"></div>
         </div>
         @endforeach
         <div style="margin: 1rem;"></div>
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   function updateLabelAndPost(ish, id){
     document.getElementById(id).innerHTML  = ish;
     $.ajax({
         type: "POST",
         url: "{{route('helpdesk.settings.change.int')}}",
         async: true,
         data: {
             "_token": "{{ csrf_token() }}",
             id: id,
             value: ish
         },
         success: function (msg) {
             if (msg != 'success') {
               alert(msg);
             }
         }
     });
   }
</script>
<script>
   $(".custom-control-input").change(function () {
     var setting_id = $(this).attr('data-perm-ID');
     var is_enabled = $(this).is(':checked');
     $.ajax({
         type: "POST",
         url: "{{route('helpdesk.settings.change.bools')}}",
         async: true,
         data: {
             "_token": "{{ csrf_token() }}",
             id: setting_id,
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
