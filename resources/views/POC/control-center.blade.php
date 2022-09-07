@extends('layout.master')
@section('title','Centro POC')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">POC</li>
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

<div class="modal fade" id="changeLc" tabindex="-1" role="dialog" aria-labelledby="changeLc" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xs" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Alterar local de refeição</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
          <div class="modal-body">
            <h6 id="textToChangeLc">Pretende alterar </h6>
            <form id="ChangeLcFrm" name="ChangeLcFrm">
              @csrf
              <input type="hidden" name="ChangeLc_data" id="ChangeLc_data" value="">
              <input type="hidden" name="ChangeLc_uid" id="ChangeLc_uid" value="">
              <select class="custom-select" name="sltNewLcl" id="sltNewLcl" style="margin-top: 1rem; margin-bottom: 0.5rem;">
                  @foreach ($locais as $key => $local)
                      @if($local['estado']!="NOK")
                        <option value="{{$local['ref']}}">
                          {{$local['nome']}}
                        </option>
                    @endif
                  @endforeach
              </select>
            </form>
          </div>
          <div class="modal-footer">
            <a href="#">
              <button type="button" class="btn btn-primary slide-in-blurred-top" onclick="changeLcUsr();" data-id="" id="SubmitChLc">Alterar</button>
            </a>
            <button type="button" class="btn btn-secondary slide-in-blurred-top" data-dismiss="modal">Fechar</button>
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
              <div class="card-body" id="content{{$_entry['id']}}">
                <div style="margin-bottom: 2rem;">
                  <h5>Ementa </h5>
                  Sopa: <strong>{{$_entry['sopa_almoço']}}</strong> <br>
                  Prato: <strong>{{$_entry['prato_almoço']}}</strong> <br>
                  Sobremesa: <strong>{{$_entry['sobremesa_almoço']}}</strong>
                </div>
               @if (array_key_exists('users_available', $_entry))
                    @if($_entry['data'] >= date("Y-m-d", strtotime("+".$ADD_MAX." days")))
                      <div id="marc{{$_entry['id']}}">
                          <h5>Utilizadores para marcação</h5>
                          <h6>{{ $_entry['users_available_count'] }} de {{ $_entry['users_total_count'] }} utilizadores</h6>

                          <div class="poc_users_list" style="margin-left: .5rem;">
                            <div class="fade-in-fwd" id="overlay{{$_entry['id']}}" style="display: none;">
                              <i class="fas fa-2x fa-sync fa-spin"></i>
                          </div>
                          @php $e_id = $_entry['id']; @endphp
                          <button type="button" id="toggleVistos{{$_entry['id']}}" class="btn btn-dark text-xs" style="width: 12rem; margin-left: 12px;margin-bottom: 24px;"
                            onclick="ToggleVistos('{{$e_id}}');">Desselecionar todos</button>
                          <form action="{{route('poc.marcar')}}" method="POST" enctype="multipart/form-data" id="addTags{{$_entry['id']}}">
                            <input type="hidden" value="{{$_entry['data']}}" name="date" />
                              @foreach ($_entry['users_available'] as $key => $usr)
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                      <span class="input-group-text" style="border: none;">
                                        <input type="checkbox" id="sel{{$e_id}}" checked class="mr-1" name="IDs[]" value="{{ $usr['id'] }}">
                                      </span>
                                      @php
                                        $NIM = $usr['id'];
                                        while ((strlen((string)$NIM)) < 8) { $NIM = 0 . (string)$NIM; }
                                        $filename = "/assets/profiles/". $NIM . ".JPG";
                                        $filename_png = "/assets/profiles/". $NIM . ".PNG";
                                     @endphp
                                      @if (file_exists(public_path($filename)))
                                        <img src="{{ asset($filename) }}" style="margin-top: 0.5rem;border-radius: 5px;height: 4rem;object-fit: cover;width: 4rem;object-position: top;">
                                      @elseif(file_exists(public_path($filename_png)))
                                        <img src="{{ asset($filename_png) }}" style="margin-top: 0.5rem;border-radius: 5px;height: 4rem;object-fit: cover;width: 4rem;object-position: top;">
                                      @else
                                        <img src="http://cpes-wise2/Unidades/Fotos/{{$NIM}}.jpg" style="margin-top: 0.5rem;border-radius: 5px;height: 4rem;object-fit: cover;width: 4rem;object-position: top;">
                                      @endif
                                  </div>
                                  <input type="text" style="font-size: 1.2rem;padding: 1.5rem; padding-left: 0; border: none; margin-left: 1.5rem; height: 5rem;" class="form-control" readonly
                                  value="{{ $usr['posto'] }} &nbsp;{{ $usr['id'] }} &nbsp;{{ $usr['name'] }}">
                                </div>
                              @endforeach
                              <br>
                                <h6>Confirme todos os utilizadores, ao pressionar em marcar irá marcar para todos os utilizadores com o visto selecionado.</h6>
                              <br>
                              <button type="button" style="opacity: 1 !important;" onclick="marcar('{{$_entry['id']}}');" id="submitBtn" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif poc_users_list_btn">Marcar</button>
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
                <h5>Sem utilizadores</h5>
                <p>Não existem mais utilizadores disponiveis para marcar refeição para este dia.</p>
              </div>
            @endif
            @if (array_key_exists('users_tagged', $_entry))
             <div id="desmarc{{$_entry['id']}}" style="margin-top: 4rem;">
               <h5>Utilizadores já com marcação</h5>
               <h6>{{ $_entry['users_marcados_count'] }} de {{ $_entry['users_total_count'] }} utilizadores</h6>
               <div class="poc_users_list" style="margin-left: .5rem;">
                 <div class="row">
                   @foreach ($_entry['users_tagged'] as $key => $usr)
                     <div class="col-md-4">
                       <div class="info-box shadow-none" style="opacity: 1;" >
                       <div class="fade-in-fwd" id="entry_tagged_{{$_entry['id']}}" style="display: none;">
                          <i class="fas fa-2x fa-sync fa-spin"></i>
                      </div>
                         <span class="info-box-icon bg-primary" style="max-height: 5rem; height: 5rem; margin-bottom: 1.5rem; margin-top: .5rem; @if(Auth::user()->dark_mode=='Y') border-left: 2px solid #fff; @else border-left: 2px solid #6c757d; @endif border-top-left-radius: 7px 7px; border-bottom-left-radius: 7px 7px;">
                            @php
                             $NIM = $usr['id'];
                             while ((strlen((string)$NIM)) < 8) { $NIM = 0 . (string)$NIM; }
                             $filename = "/assets/profiles/". $NIM . ".JPG";
                             $filename_png = "/assets/profiles/". $NIM . ".PNG";
                            @endphp
                            @if (file_exists(public_path($filename)))
                             <img src="{{ asset($filename) }}" style="border-radius: 5px;height: 100%;object-fit: cover;width: 100%;object-position: top;" />
                            @elseif(file_exists(public_path($filename_png)))
                             <img src="{{ asset($filename_png) }}" style="border-radius: 5px;height: 100%;object-fit: cover;width: 100%;object-position: top;" />
                            @else
                             <img src="http://cpes-wise2/Unidades/Fotos/{{$NIM}}.jpg" style="border-radius: 5px;height: 100%;object-fit: cover;width: 100%;object-position: top;">
                            @endif
                         </span>
                         <div class="info-box-content" style="display: inline-block; max-width: 17rem;">
                           <a href="{{ route('user.profile', $usr['id']) }}"><span class="info-box-text text-sm">{{ $usr['id'] }}</span></a>
                           <span class="info-box-text" style="line-height: 1.3;"><span class="text-sm">{{ $usr['posto'] }}</span><br><strong>{{ $usr['name'] }}</strong><br><span style="font-size: .9rem;">Marcado para <b id="taggedAt{{$usr['id']}}">{{$usr['localRefActual']}}</b></span></span>
                           @if ($usr['seccao']!=null)
                             <span class="info-box-number">{{ $usr['seccao'] }}</span>
                           @endif
                         </div>
                          <div class="info-box-content" style="display: inline-block; padding: 1.5rem; padding-right: .5rem; max-width: 10rem; text-align-last: center;">
                            @php $maxdate_remove = date("Y-m-d", strtotime("-".$REMOVE_MAX." days", strtotime($_entry['data']))); @endphp
                            @if($today<=$maxdate_remove)
                              <button type="button" data-date="{{$_entry['data']}}" data-id="{{$usr['id']}}" data-entry="{{$_entry['id']}}" onclick="removeMarcacao(this);"
                              class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif remove-ref-btn ferias_entry_button" style="opacity: 1 !important;">
                                <i class="far fa-calendar-times">&nbsp&nbsp&nbsp</i>
                                Desmarcar
                              </button>
                            @endif
                            @php $maxdate_change = date("Y-m-d", strtotime("-1 days", strtotime($_entry['data']))); @endphp
                            @if($today<=$maxdate_change)
                              <button type="button" data-toggle="modal" data-target="#changeLc"class="btn smallbtn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-primary @endif remove-ref-btn ferias_entry_button btn-primary" style="opacity: 1 !important; margin-top: 3px;"
                              data-date="{{$_entry['data']}}" data-entry="{{$_entry['id']}}" data-id="{{$usr['id']}}" data-posto="{{ $usr['posto'] }}" data-name="{{ $usr['name'] }}" data-local="{{$usr['localRefActual']}}" onclick="populateModal(this);">
                              <i class="fas fa-map-marker-alt">&nbsp&nbsp&nbsp</i>
                              Alterar local
                            </button>
                            @endif
                          </div>
                       </div>
                     </div>
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
        <h6>Não foi possivel obter datas para a marcação de 2º refeição para os utilizadores da sua dependência!</h6>
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
          url: "{{route('poc.marcar')}}",
          type: "POST",
          data: {
              "_token": "{{ csrf_token() }}",
              data: data,
          },
          success: function(response) {

              if (response) {
                  if (response != 'success') {
                      $(overlayname).hide()
                      $(overlayname).removeClass( "overlay" );
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
                      body: "As refeições foram marcadas para os militares selecionados.",
                      icon: "fas fa-book",
                      autohide: true,
                      autoremove: true,
                      delay: 3500,
                      class: "toast-not",
                    });
                  }
              }
          }
      });
    };

    function populateModal(btn){
      var id = $(btn).data("id");
      var name = $(btn).data("name");
      var posto = $(btn).data("posto");
      var date = $(btn).data("date");
      var entry_id = $(btn).data("entry");
      var local_actual = $(btn).data("local");
      $("#ChangeLc_uid").val(id);
      $("#ChangeLc_data").val(date);
      $('#SubmitChLc').data('id',id);
      $("#textToChangeLc").html("O militar <b>" + posto + " " + id + " " + name + "</b> tem atualmente marcação para o <b>" + local_actual + "</b>.<br/><br/> Se pretender alterar, selecione abaixo na lista e carregue em <b>Alterar</b>.");
    }

    function changeLcUsr(){
      var uid = $('#SubmitChLc').data("id");
      var form= $("#ChangeLcFrm");
      $.ajax({
          type: 'POST',
          url: "{{route('poc.change_loc')}}",
          data: form.serialize(),
          success: function(response) {
            if (response) {
              $('#changeLc').modal('hide')
                if (!response.includes('success')) {
                    document.getElementById("errorAddingTitle").innerHTML = "Erro";
                    document.getElementById("errorAddingText").innerHTML = "Erro ao alterar o local de refeição. " + response;
                    $("#errorAddingModal").modal()
                } else {
                    // taggedAt
                    var Span = "#taggedAt" + uid;
                    const responselc = response.split(",");
                    $(Span).html(responselc[1]);
                    $(document).Toasts('create', {
                      title: "Alterado",
                      subtitle: "",
                      body: "O local da refeição foi alterado para " + responselc[1] + ".",
                      icon: "fas fa-book",
                      autohide: true,
                      autoremove: true,
                      delay: 3500,
                      class: "toast-not",
                    });
                }
            }
          }
        });
      }

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
      var overlayname = "#entry_tagged_" + entry_id;
      $(overlayname).show();
      $(overlayname).addClass( "overlay" );
        $.ajax({
            url: "{{route('poc.remove')}}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                user: id,
                data: date
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
                      var content = "#content" + entry_id;
                      $(content).load(location.href + " " + content);
                    }
                }
            }
        });
    }
</script>
@endsection
@endif
