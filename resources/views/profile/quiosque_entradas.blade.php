@extends('layout.master')
@section('title','Entradas quiosque')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Perfil</a></li>
<li class="breadcrumb-item active">Minhas entradas</li>
@endsection
@section('page-content')
<div class="col-md-12">
   <div class="card">
     <div class="card-header border-0">
        <div class="d-flex justify-content-between">
           <h3 class="card-title">Entradas por dia</h3>
           <div class="card-tools" style="margin-right: 0 !important;">
              &nbsp;
              <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>              
           </div>
        </div>
     </div>
      <div class="card-body" style="max-height: 77vh;">   
         @if(!empty($info))
            <table class="table table-striped projects">
               <thead>
                  <tr>
                     <th>
                        Data
                     </th>
                     <th style="width: 15%">
                        Refeição
                     </th>
                     <th style="width: 15%">
                        Refeição marcada
                     </th>
                     <th style="width: 15%">
                        Local
                     </th>                                 
                     <th style="width: 15%">
                        Entrada no quiosque
                     </th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($info as $key => $info_entry)
                     @if (is_array($info_entry))
                     <tr>
                        <td>
                           {{ $info_entry['DATE'] }}
                        </td>
                        <td>
                           {{ $info_entry['REF'] }}
                        </td>
                        <td>
                           <div class="custom-control custom-checkbox">
                              <input class="custom-control-input" type="checkbox" id="customCheckbox3" disabled="" @if($info_entry['MARCADA']=="1") checked @endif>
                              <label for="customCheckbox3" class="custom-control-label"></label>
                              @if($info_entry['MARCADA']=="1") Sim @else Não @endif
                           </div>
                        </td>
                        <td>
                           {{ $info_entry['LOCAL'] }}
                        </td>
                        <td>
                           <b>{{ $info_entry['REGISTADO_TIME'] }}</b> 
                        </td>
                     </tr>
                     @endif
                  @endforeach
               </tbody>
            </table>
         @else
            <h6>Nenhuma entrada de quiosque.</h6>
         @endif   
      </div>     
   </div>
</div>
@endsection
