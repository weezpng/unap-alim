@extends('layout.master')
@section('page-content')
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Apagar conta de gestão</h5>
            <a href="{{ url()->previous() }}">
            <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </a>
         </div>
         <div class="modal-body">
           @if (!empty($possible_replaces))
            <p style="margin-bottom: 20px;">
               Para continuar, é necessário indicar um utilizador para herdar as associações deste utilizador.<br />
               Selecione abaixo um utilizador.
            </p>
            <table class="table table-striped projects">
               <tbody>
                  @foreach($possible_replaces as $user)
                  @if ($user['id']!=$old_user_id)
                  <tr>
                     <td>
                        <i class="fas fa-user-plus"></i>&nbsp&nbsp
                        {{ $user['id'] }}
                     </td>
                     <td>
                        {{ $user['posto'] }}
                     </td>
                     <td class="uppercase-only">
                        {{ $user['name'] }}
                        @if ($user['descriptor'])
                          <br />
                          <span class="text-muted text-center">
                              {{\Illuminate\Support\Str::limit($user['descriptor'], 35, $end='...')}}
                          </span>
                        @endif
                        @if ($user['seccao'])
                          <br />
                          <span class="text-muted text-center"><strong>{{ $user['seccao'] }}</strong></span>
                        @endif
                     </td>
                     <td>
                        <form method="POST" action="{{route('user.transfer.andDelete')}}">
                           @csrf
                           <input type="hidden" id="old_user" name="old_user" value="{{ $old_user_id }}"></input>
                           <input type="hidden" id="nim" name="nim" value="{{$user['id']}}"></input>
                           <button type="submit" class="btn btn-sm @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif marcar-ref-btn scale-in-center" style="float: right;">Associar</button>
                        </form>
                     </td>
                  </tr>
                  @endif
                  @endforeach
               </tbody>
            </table>
            @else
              <p>
                Não foi encontrado nenhum utilizador que seja possivel assumir as responsabilidades deste utilizador. <br />É necessário criar um utilizador.
              </p>
           @endif
         </div>
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')
<script>
   $(document).ready(function() {
     $( "#loading" ).fadeOut(500, function() {
       $( "#loading" ).remove();
       $('#exampleModal').modal({backdrop: 'static', keyboard: false})
     });
   });
</script>
@endsection
