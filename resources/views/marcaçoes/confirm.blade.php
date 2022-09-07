@extends('layout.master')
@section('title','Confirmações')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Confirmações</li>
<li class="breadcrumb-item active">Confirmar refeições</li>
@endsection
@section('page-content')
<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->lock=='N') @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif @else card-danger @endif">
      <div class="card-header border-0">
         <h3 class="card-title">Minhas confirmações</h3>
      </div>
      <div class="card-body"  style="overflow-y: scroll; max-height: 70vh;">
         @if (Auth::user()->lock=="N")
         @if (!empty($marcaçoes))
         <table class="table table-striped projects">
            <thead>
               <tr>
                  <th style="width: 1%">
                  </th>
                  <th style="width: 20%">
                     Data
                  </th>
                  <th style="width: 15%">
                     Refeição
                  </th>
                  <th style="width: 30%">
                     Ementa
                  </th>
                  <th>
                     Local
                  </th>
                  <th>
                  </th>
               </tr>
            </thead>
            <tbody>
               @php
               $today = date("Y-m-d");
               @endphp
               @foreach($marcaçoes as $marcaçao)
               <tr>
                  <td>
                     @if ($marcaçao['confirmada']=='Y')
                     <i class="fas fa-check nav-icon"></i>
                     @else
                     <i class="fas fa-times nav-icon"></i>
                     @endif
                  </td>
                  <td class="project_progress">
                     <p class="p_noMargin">
                        @php
                        $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
                        $mes_index = date('m', strtotime($marcaçao['data_marcacao']));
                        @endphp
                        {{ date('d', strtotime($marcaçao['data_marcacao'])) }}
                        {{ $mes[($mes_index - 1)] }}
                     </p>
                  </td>
                  <td>
                     <p class="p_noMargin">
                        @if($marcaçao['meal']=='1REF' )
                        Pequeno-almoço
                        @elseif($marcaçao['meal']=='3REF' )
                        Jantar
                        @else
                        Almoço
                        @endif
                     </p>
                  </td>
                  <td>
                     @if($marcaçao['meal']!='1REF' )
                     Sopa: <strong>{{ $marcaçao['sopa'] }}</strong><br>
                     Prato: <strong>{{ $marcaçao['prato'] }}</strong><br>
                     Sobremesa: <strong>{{ $marcaçao['sobremesa'] }}</strong>
                     @endif
                  </td>
                  <td>
                     {{$marcaçao['local_ref']}}
                  </td>
                  <td class="project-actions text-right">
                     @if ($marcaçao['confirmada']=='Y')
                     <button type="button" class="btn btn-sm remove-ref-btn meal-confirmed disabled" disabled><i class="fas fa-check-double"></i>&nbsp;&nbsp;Confirmada</button>
                     @else
                     <form method="POST" action="{{route('confirmacoes.post')}}">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{$marcaçao['id']}}"></input>
                        <button type="submit" class="btn btn-sm remove-ref-btn meal-confirm"><i class="fas fa-check"></i>&nbsp;&nbsp;Confirmar</button>
                     </form>
                     @endif
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
         @else
         <h6>Você não tem nenhuma marcação.<br />Carregue <a href="{{route('marcacao.index')}}">aqui</a> para fazer marcações.</h6>
         @endif
         @else
         <p>A sua conta encontra-se <strong>bloqueada</strong>.</p>
         @endif
      </div>
      <!-- /.card-body -->
   </div>
   <!-- /.card -->
</div>
@endsection
