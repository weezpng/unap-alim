@extends('layout.master')
@section('icon','fas fa-user-check')
@section('title','Marcações hóspedes')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('gestao.hospedes')}}">Hospedes</a></li>
<li class="breadcrumb-item active">Marcações</li>
@endsection
@section('page-content')
<div class="modal puff-in-center" id="errorAddingModal" tabindex="-1" role="dialog" aria-labelledby="errorAddingModal" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="errorAddingTitle" name="errorAddingTitle"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p id="errorAddingText" name="errorAddingText"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>
@php
  $today = date("Y-m-d");
  $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
  $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
@endphp
<div class="col-md-12">
  @if (!empty($entries))
    @foreach ($entries as $_entry)
        @php
          $weekday_number = date('N',  strtotime($_entry['data']));
          $mes_index = date('m', strtotime($_entry['data']));
        @endphp
        <div class="card card-dark card-outline" id="contentcard">
          <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapse{{$_entry['id']}}" aria-expanded="false" id="accordion">
              <div class="card-header">
                  <h4 class="card-title w-100" @if(Auth::user()->dark_mode=="Y") style="color: white !important;" @else style="color: #343a40 !important;" @endif>
                    {{ date('d', strtotime($_entry['data'])) }}
                    {{ $mes[($mes_index - 1)] }}
                    </strong>
                    <span style="font-size: .85rem;"> ({{ $semana[($weekday_number -1)] }})</span>
                  </h4>
              </div>
          </a>
          <div id="collapse{{$_entry['id']}}" class="collapse" data-parent="#accordion">
              <div class="card-body" id="content{{$_entry['id']}}" style="max-height: none !important;">
                <div style="margin-bottom: 2rem;">
                  <h5>Ementa </h5>
                  Sopa: <strong>{{$_entry['sopa_almoço']}}</strong> <br>
                  Prato: <strong>{{$_entry['prato_almoço']}}</strong> <br>
                  Sobremesa: <strong>{{$_entry['sobremesa_almoço']}}</strong>
                </div>
               @if (array_key_exists('users_available', $_entry))
                    @if($_entry['data'] >= date("Y-m-d", strtotime("+1 days")))
                      <div id="marc{{$_entry['id']}}">
                          <h5>Hóspedes para marcação</h5>
                          <div class="poc_users_list" style="margin-left: .5rem;">
                            <div class="fade-in-fwd" id="overlay{{$_entry['id']}}" style="display: none;">
                              <i class="fas fa-2x fa-sync fa-spin"></i>
                          </div>
                          @php $e_id = $_entry['id']; @endphp
                          <button type="button" id="toggleVistos{{$_entry['id']}}" class="btn btn-dark text-xs" style="width: 12rem; margin-left: 12px;margin-bottom: 24px; opacity: 1 !important;"
                            onclick="ToggleVistos('{{$e_id}}');">Desselecionar todos</button>
                          <form action="{{route('gestao.htagcenter.marcar')}}" method="POST" enctype="multipart/form-data" id="addTags{{$_entry['id']}}">
                            <input type="hidden" value="{{$_entry['data']}}" name="date" />
                              @foreach ($_entry['users_available'] as $key => $usr)

                                <div class="card card-dark card-outline" id="contentcard">
                                  <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapse{{$_entry['id']}}{{ $key }})" aria-expanded="false" id="accordion">
                                      <div class="card-header">
                                          <h4 class="card-title w-100" @if(Auth::user()->dark_mode=="Y") style="color: white !important;" @else style="color: #343a40 !important;" @endif>
                                              @if($key=="1REF") Pequeno-almoço
                                              @elseif($key=="2REF") Almoço
                                              @else Jantar
                                              @endif
                                          </h4>
                                      </div>
                                  </a>
                                    <div class="card-body">
                                      @foreach($usr as $ref)

                                        <div class="input-group"  style="background: transparent !important;">
                                          <div class="input-group-prepend"  style="background: transparent !important;">
                                              <span class="input-group-text" style="border: none; background: transparent !important;">
                                                <input type="checkbox" id="sel{{$e_id}}" checked class="mr-1"
                                                @if($key=="1REF") name="IDs1[]"
                                                @elseif($key=="2REF") name="IDs2[]"
                                                @else name="IDs3[]"
                                                @endif


                                                value="{{ $ref['id'] }}" style="background: transparent !important;">
                                              </span>
                                          </div>
                                          <input type="text" style="font-size: 1.2rem;padding-left: 0; border: none; margin-left: 1rem; height: 3.5rem; background: transparent !important;" class="form-control" readonly
                                          value="&nbsp;({{ $ref['type'] }} {{ $ref['id'] }}) &nbsp;{{ $ref['name'] }}">
                                        </div>

                                      @endforeach
                                    </div>
                                  </div>
                                <br>
                              @endforeach
                              <br>
                                <h6>Confirme todos os hóspedes, ao pressionar em marcar irá marcar para todos os hóspedes com o visto selecionado.</h6>
                              <br>
                              <button type="button" style="opacity: 1 !important;" onclick="marcar({{$_entry['id']}});" id="submitBtn" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif poc_users_list_btn">Marcar</button>
                          </div>
                        </form>
                      </div>
                    @else
                      <div class="callout callout-danger" style="box-shadow: none !important; z-index: 999;">
                        <h5>Data ultrapassada</h5>
                        <p>Já não é possivel marcar refeiçoes para este dia.</p>
                      </div>
                    @endif
                @else
                  <div class="callout callout-danger" style="box-shadow: none !important; z-index: 999;">
                    <h5>Sem hóspedes</h5>
                    <p>Não existem mais hóspedes disponiveis para marcar refeição para este dia.</p>
                  </div>
                @endif
                @if (array_key_exists('users_tagged', $_entry))
                <div id="desmarc{{$_entry['id']}}" style="margin-top: 4rem;">
                  <h5>Hóspedes já com marcação</h5>
                  <div class="poc_users_list" style="margin-left: .5rem;">
                    <div class="row">
                      @foreach ($_entry['users_tagged'] as $key => $ref)
                        <h6><b>
                        @if($key=="1REF")
                              Pequeno-almoço
                          @elseif($key=="2REF")
                              Almoço
                          @else
                              Jantar
                          @endif
                          </b></h6>
                          <div class="col-md-12">
                            <div class="row">
                              @foreach($ref as $u_key => $user)
                                <div class="col-md-4">
                                  <div class="info-box shadow-none" style="opacity: 1;" >
                                    <div class="fade-in-fwd" id="entry_tagged_{{$_entry['id']}}{{$u_key}}{{$user['id']}}" style="display: none;">
                                        <i class="fas fa-2x fa-sync fa-spin"></i>
                                    </div>
                                      <span class="info-box-icon bg-primary" style="max-height: 5rem; height: 5rem; margin-bottom: 1.5rem; margin-top: .5rem; @if(Auth::user()->dark_mode=='Y') border-left: 2px solid #fff; @else border-left: 2px solid #6c757d; @endif border-top-left-radius: 7px 7px; border-bottom-left-radius: 7px 7px;">
                                        <i class="fas fa-user-tag"></i>
                                      </span>
                                      <div class="info-box-content" style="display: inline-block; max-width: 17rem;">
                                        <span class="info-box-text text-sm">{{ $user['id'] }}</span>
                                        <span class="info-box-text" style="line-height: 1.3;"><span class="text-sm">{{ $user['type'] }}</span><br><strong>{{ $user['name'] }}</strong><br><span style="font-size: .9rem;">Marcado para <b id="taggedAt{{$user['id']}}">{{$user['localRefActual']}}</b></span></span>
                                      </div>
                                        <div class="info-box-content" style="display: inline-block; padding: 1.5rem; padding-right: .5rem; max-width: 10rem; text-align-last: center;">
                                          @php $maxdate_remove = date("Y-m-d", strtotime($_entry['data'])); @endphp
                                          @if($today<=$maxdate_remove)
                                            <button type="button" data-date="{{$_entry['data']}}" data-id="{{$user['id']}}" data-entry="{{$_entry['id']}}" data-ref={{$key}} onclick="removeMarcacao(this);"
                                            class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn ferias_entry_button" style="opacity: 1 !important;">
                                              <i class="far fa-calendar-times">&nbsp&nbsp&nbsp</i>
                                              Desmarcar
                                            </button>
                                            @else
                                            :(
                                          @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                          </div>
                          @if(!$loop->last)
                            <div class="col-md-12">
                              <hr style="border-top: 1px solid rgba(0,0,0,0.2); margin-bottom: 3.5rem;">
                            </div>
                          @endif
                        @endforeach
                      </div>
                    </div>
                  </div>
                @endif
            </div>
          </div>
      </div>
   @endforeach
 @else
   <div class="card card-outline card-dark slide-in-blurred-top">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
           <h3 class="card-title">Erro</h3>
         </div>
      </div>
      <div class="card-body">
        <h6>Não foi possivel obter datas para a marcação refeições para os hóspedes da sua dependência!</h6>
      </div>
   </div>
 @endif
</div>
@endsection
@if (!empty($entries))
@section('extra-scripts')
  <script>
   var startDate;
   var toggled = true;
    function marcar(formID) {
      var form = "#addTags" + formID;
      var data = $(form).serializeArray();
      var overlayname = "#overlay" + formID;
      $(overlayname).show();
      $(overlayname).addClass( "overlay" );
      $.ajax({
          url: "{{route('gestao.htagcenter.marcar')}}",
          type: "POST",
          data: {
              "_token": "{{ csrf_token() }}",
              data: data,
          },
          success: function(response) {
              if (response) {
                  if (response != 'success') {

                      document.getElementById("errorAddingTitle").innerHTML = "Erro";
                      document.getElementById("errorAddingText").innerHTML = "Erro a fazer marcação.";
                      $("#errorAddingModal").modal()
                  } else {
                    var marc = "#marc" + formID;
                    var content = "#content" + formID;
                    $(content).load(location.href + " " + content);
                    $( ".info-box" ).each(function() {
                      $( this ).css('opacity', '1');
                    });
                    $(document).Toasts('create', {
                      title: "Alterado",
                      subtitle: "",
                      body: "As refeições foram marcadas para os hóspedes selecionados.",
                      icon: "fas fa-book",
                      autohide: true,
                      autoremove: true,
                      delay: 3500,
                      class: "toast-not",
                    });
                  }
                  $(overlayname).hide()
                  $(overlayname).removeClass( "overlay" );
              }
          }
      });
    };
    function ToggleVistos(id){
      var togglebtn = "#toggleVistos" + id;
      $('.mr-1').each(function(){
        let btn_parent = $(this);
        if((typeof btn_parent.attr("id") !== 'undefined') && (btn_parent.attr("id").includes(id))) {
          $(this).attr('checked', (!toggled));
        }
      });
      if (toggled==true) {
        $(togglebtn).text("Selecionar todos");
      } else {
        $(togglebtn).text("Desselecionar todos");
      }
      toggled = !toggled;
    }
    function removeMarcacao(btn) {
      var id = $(btn).data("id");
      var date = $(btn).data("date");
      var entry_id = $(btn).data("entry");
      var ref = $(btn).data("ref");
      var overlayname = "#entry_tagged_" + entry_id + ref + id;
      $(overlayname).show();
      $(overlayname).addClass( "overlay" );
        $.ajax({
            url: "{{route('gestao.htagcenter.desmarcar')}}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                user: id,
                data: date,
                ref: ref,
            },
            success: function(response) {
                if (response) {
                    if (response != 'success') {
                    $(overlayname).hide()
                    $(overlayname).removeClass( "overlay" );
                        document.getElementById("errorAddingTitle").innerHTML = "Erro";
                        document.getElementById("errorAddingText").innerHTML = "Erro ao retirar a marcação. " + response;
                        $("#errorAddingModal").modal()
                    } else {
                      $(document).Toasts('create', {
                      title: "Alterado",
                      subtitle: "",
                      body: "A refeição foi removida!",
                      icon: "fas fa-calendar-times",
                      autohide: true,
                      autoremove: true,
                      delay: 3500,
                      class: "toast-not",
                    });
                      var content = "#desmarc" + entry_id;
                      $(content).load(location.href + " " + content);
                    }
                    $(overlayname).hide()
                    $(overlayname).removeClass( "overlay" );
                }
            }
        });
    }
</script>
@endsection
@endif
