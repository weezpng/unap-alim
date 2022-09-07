@extends('layout.master')
@section('title','Publicações de equipa')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('index') }}">Gestão</a></li>
<li class="breadcrumb-item active">Minha equipa</li>
<li class="breadcrumb-item active">Publicações</li>
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
            <div class="modal-body" style="padding: 1.25rem !important;">
                <p id="errorAddingText" name="errorAddingText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal puff-in-center" id="CreatePost" tabindex="-1" role="dialog" aria-labelledby="SendPingModal" aria-hidden="true">
   <div class=" modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-folder"></i>&nbsp; Criar publicação</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{route('gestao.hospedes.new')}}" method="POST" enctype="multipart/form-data" id="CreatePostForm">
            <div class="modal-body">
               @csrf
               <div class="form-group row">
                  <label for="inputName" class="col-sm-3 col-form-label">Titulo</label>
                  <div class="col-sm-9">
                     <input required type="text" class="form-control" id="inputTitle" name="inputTitle" placeholder="Titulo">
                  </div>
               </div>

               <div class="form-group row">
                  <label for="inputName" class="col-sm-3 col-form-label">Mensagem</label>
                  <div class="col-sm-9">
                     <textarea required type="text" class="form-control" id="inputMessagem" name="inputMessagem" rows="5" placeholder="Mensagem"></textarea>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" onclick="CreatePostJS();" class="btn btn-primary" style="min-width: 6rem;">Criar</button>
               <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
            </div>
         </form>
      </div>
   </div>
</div>

<a id="back-to-top" href="#" data-target="#CreatePost" data-toggle="modal" class="btn btn-primary back-to-top" role="button" aria-label="Save to file">
    <i class="fa-solid fa-folder-plus"></i>
 </a>

@if ($posts!=null)

  @foreach ($posts as $key => $post)
    <div class="col-md-12" id="postId{{ $post['id'] }}">

      <div class="card card-widget">
          <div class="card-header">
              <div class="user-block">

                  @php
                     $filename = "assets/profiles/".$post['by_ID'].".PNG";
                     $filename_jpg = "assets/profiles/".$post['by_ID'].".JPG";
                  @endphp
                  @if (file_exists(public_path($filename)))
                    @php $usr_image = $filename; @endphp
                      <img class="img-circle" src="{{ asset($usr_image) }}" alt="User">
                  @elseif (file_exists(public_path($filename_jpg)))
                      @php $usr_image = $filename_jpg; @endphp
                      <img class="img-circle" src="{{ asset($usr_image) }}" alt="User">
                  @else
                     @php $usr_image = "https://cpes-wise2/Unidades/Fotos/". $post['by_ID'] . ".JPG"; @endphp
                     <img class="img-circle" src="{{ $usr_image }}" alt="User">
                  @endif

                  <span class="username"><a href="{{ route('user.profile', $post['by_ID']) }}">{{ $post['by_POST'] }} {{ $post['by_NAME'] }} </a></span>
                  <span class="description">Publicado a <b>{{ $post['created_at_date'] }}</b> às <b>{{ $post['created_at_time'] }}</b></span>
              </div>

              <div class="card-tools">
                @if ($post['by_ID']==Auth::user()->id)
                  <button type="button" class="btn btn-tool" onclick="RemovePost('{{ $post['id'] }}')">
                    <i class="fas fa-trash-can"></i>
                  </button>
                @endif

                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                  </button>
              </div>

          </div>

          <div class="card-body" style="display: block;">
              <h6><b>{{ $post['title'] }}</b></h6>
              <p>{{ $post['message'] }}</p>
          </div>
      </div>
    </div>
  @endforeach

@else
  <div class="col-md-12" style="padding-left: 1rem;">
    <h6>Nenhuma publicação. <br>
      Pode você começar publicando para a sua equipa, utilizando o botão no canto inferior direito!
    </h6>

  </div>
@endif


@endsection

@section('extra-scripts')
<script>

  function CreatePostJS(){
     var data = $("#CreatePostForm").serializeArray();
     $.ajax({
         url: "{{route('gestão.equipa.create-post')}}",
         type: "POST",
         data: {
             "_token": "{{ csrf_token() }}",
             data: data,
         },
         success: function(response) {
              $("#CreatePost").modal('hide');
             if (response) {
                 if (response != 'success') {
                     document.getElementById("errorAddingTitle").innerHTML = "Erro";
                     document.getElementById("errorAddingText").innerHTML = response;
                     $("#errorAddingModal").modal()
                 } else {
                   $(document).Toasts('create', {
                     title: "Criada!",
                     subtitle: "",
                     body: "A sua mensagem foi publicada com sucesso",
                     icon: "fas fa-solid fa-folder",
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

    function RemovePost(id) {
        $.ajax({
            url: "{{route('gestão.equipa.del-post')}}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "post_id": id,
            },
            success: function(response) {
                if (response) {
                    if (response != 'success') {
                        document.getElementById("errorAddingTitle").innerHTML = "Erro";
                        document.getElementById("errorAddingText").innerHTML = response;
                        $("#errorAddingModal").modal()
                    } else {
                        var post_card_id = "#postId"+id;
                        $( post_card_id ).remove();
                        $(document).Toasts('create', {
                            title: "Removida!",
                            subtitle: "",
                            body: "Foi removida a sua publicação",
                            icon: "fas fa-trash-can",
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
</script>
@endsection
