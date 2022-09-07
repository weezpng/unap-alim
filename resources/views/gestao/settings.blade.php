@extends('layout.master')
@section('title','Definições de plataforma')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Definições</li>
@endsection
@section('page-content')
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
           <h3 class="card-title">&nbsp;</h3>
           <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
           </button>
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
                 <label id="lb{{$set['id']}}">{{ $set['settingToggleInt'] }}</label>
                 <label for="customRange3" style="font-weight:500 !important;">{{ $set['settingToggleIntLabel'] }}</label>
                 @php
                  $max = ($set['settingToggleInt'] + $set['settingToggleInt']) * 1.25;
                 @endphp
                 <input type="range" class="custom-range" min="0" max="{{ $max }}" step="1.0" id="{{$set['id']}}" name="{{$set['id']}}" oninput="updateLabel(this.value, this.name)" onchange="PostVal(this.value, this.name)">
               @elseif ($set['settingToggleMode']=="BOOLEAN")
                 <div class="custom-control custom-switch" style="padding-top: 1.5rem;">
                    <input type="checkbox" class="custom-control-input" data-perm-ID="{{$set['id']}}" id="{{$set['id']}}" @if($set['settingToggleBoolean']=="Y")checked @endif>
                    <label class="custom-control-label" for="{{$set['id']}}"> {{$set['settingToggleBoolLabel']}} </label>
                 </div>
               @endif
            </div>
            <div style="margin: .5rem;"></div>
         </div>
         @if(!$loop->last)
            <hr style="margin-bottom: 2rem; border: 0; border-top: 1px solid #454d5594;">
         @endif
         @endforeach
         <div style="margin: 1rem;"></div>
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')

<script>
   function updateLabel(ish, id){
     var temp_lb = "lb"+id;
     document.getElementById(temp_lb).innerHTML  = ish;
   }
</script>

<script>
  function PostVal(ish, id){
    $.ajax({
        type: "POST",
        url: "{{route('gestao.settings.change.int')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            id: id,
            value: ish
        },
        success: function (msg) {
            if (msg != 'success') {
              $(document).Toasts('create', {
                 title: "Erro",
                 subtitle: "",
                 body: msg,
                 icon: "fas fa-exclamation-triangle",
                 autohide: true,
                 autoremove: true,
                 delay: 3500,
                 class: "toast-not",
              });
            } else {
              $(document).Toasts('create', {
                 title: "Alterado",
                 subtitle: "",
                 body: "A definição foi alterada com sucesso!",
                 icon: "fas fa-check",
                 autohide: true,
                 autoremove: true,
                 delay: 3500,
                 class: "toast-not",
              });
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
         url: "{{route('gestao.settings.change.bools')}}",
         async: true,
         data: {
             "_token": "{{ csrf_token() }}",
             id: setting_id,
             enable: is_enabled
         },
         success: function (msg) {
             if (msg != 'success') {
               $(document).Toasts('create', {
                  title: "Erro",
                  subtitle: "",
                  body: msg,
                  icon: "fas fa-exclamation-triangle",
                  autohide: true,
                  autoremove: true,
                  delay: 3500,
                  class: "toast-not",
               });
             } else {
               $(document).Toasts('create', {
                  title: "Alterado",
                  subtitle: "",
                  body: "A definição foi alterada com sucesso!",
                  icon: "fas fa-check",
                  autohide: true,
                  autoremove: true,
                  delay: 3500,
                  class: "toast-not",
               });
             }
         }
     });
   });
</script>
@endsection
