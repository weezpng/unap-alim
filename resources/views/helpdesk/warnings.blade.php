@extends('layout.master')

@if(Auth::user()->user_type == "HELPDESK")
   @section('title','HELPDESK: Avisos de Plataforma')
@else
   @section('title','Avisos de Plataforma')
@endif
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>

@if(Auth::user()->user_type == "HELPDESK")
   <li class="breadcrumb-item active">Helpdesk</li>
@else
   <li class="breadcrumb-item"><a href="{{ route('gestao.index') }}">Gestão</a></li>
@endif

<li class="breadcrumb-item active">Avisos de plataforma</li>
@endsection
@section('page-content')

<div class="modal puff-in-center fade" id="addWarning" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class=" modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Criar aviso</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <form @if(Auth::user()->user_type == "HELPDESK") action="{{route('helpdesk.warnings.new')}}" @else action="{{route('gestão.warnings.new')}}" @endif method="POST" enctype="multipart/form-data">
              <div class="modal-body">                
                 @csrf
                 <div class="form-group row">
                     <label for="inputName" class="col-sm-3 col-form-label">Titulo</label>
                     <div class="col-sm-9">
                        <input type="text" required class="form-control" id="title" name="title" placeholder="Titulo do aviso">
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="inputName" class="col-sm-3 col-form-label">Mensagem</label>
                     <div class="col-sm-9">
                        <textarea type="text" required class="form-control" id="message" name="message" placeholder="Mensagem para o aviso"> </textarea>
                     </div>
                  </div>
                  @if(Auth::user()->user_type == "HELPDESK")
                  <div class="form-group row">
                     <label for="inputName" class="col-sm-3 col-form-label">Link</label>
                     <div class="col-sm-9">
                        <select class="form-control" name="link" id="link">
                        <option selected disabled>Selecione se há algum link nesta mensagem</option>
                           @foreach($routes as $route)  
                              <option value="{{ $route }}">{{ $route }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  @endif
                  <div class="form-group row">
                     <label for="inputName" class="col-sm-3 col-form-label">Página</label>
                     <div class="col-sm-9">
                        <select required class="form-control" name="show" id="show">
                           <option selected disabled>Selecione onde deve ser mostrada</option>
                           <option value="LOGIN">LOGIN</option>
                           <option value="REGISTER">REGISTER</option>
                           <option value="INDEX">INDEX (com sessão iniciada)</option>
                           <option value="INDEX2">INDEX (sem sessão iniciada)</option>
                        </select>
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12">
                     <label class="col-form-label" style="font-weight: 400; margin-left: 5px;">
                        É possivel preencher a mensagem com informação de utilizador, se publicado na página <b>INDEX (com sessão iniciada)</b>
                        Apenas insira o seguinte texto na <b>mensagem</b> para substituir com a informação:<br>
                        <ul style="margin-top: 1rem;">
                           <li><b>%user_name%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;Nome de utilizador</li>
                           <li><b>%user_nim%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;NIM do utilizador</li>
                           <li><b>%user_posto%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;Posto do utilizador</li>
                           <li><b>%user_email%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;Email do utilizador</li>
                           <li><b>%user_unidade%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;U/E/O do utilizador</li>
                           <li><b>%user_type%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;Tipo de utilizador</li>
                           <li><b>%user_perm%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;Permissões do utilizador</li>
                           <li><b>%user_local_pref% &nbsp;&nbsp;&nbsp;</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;Local preferencial do utilizador</li>
                           <li><b>%user_unidade_local%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;Local da unidade do utilizador</li>
                           <li><b>%user_count_marcacoes%</b>&nbsp; <i class="fas fa-arrow-right"></i> &nbsp;Contagem de marcações do utilizador</li>
                        </ul>  
                        Também é possivel a formatação com tags HTML.
                     </label>

                     </div>
                  </div>

              </div>
              <div class="modal-footer">
                 <button type="submit" class="btn btn-primary">Guardar</button>
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
              </div>
           </form>
        </div>
     </div>
  </div>

<div class="col-md-12">
   <div class="card card-outline @if (Auth::user()->dark_mode=='Y') card-dark @else card-secondary @endif">
      <div class="card-header border-0">
         <div class="d-flex justify-content-between">
           <h3 class="card-title">Avisos ativos</h3>
           <div class="card-tools">                        
            <a href="#" data-toggle="modal" data-target="#addWarning">&nbsp; <i class="fas fa-plus"></i>&nbsp; Novo aviso  &nbsp;  &nbsp; </a>

            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
           </div>
         </div>
      </div>
      <div class="card-body">
         @if(!empty($warnings))
            @foreach ($warnings as $key => $msg)
            <div class="row">
               <div class="col-md-7">
                  <b>{{ $msg['title'] }}</b>
                  <p>
                     {{ $msg['message'] }}
                  </p>
               </div>
               <div class="col-md-2">
                  @if($msg['link'])
                     <h6 style="margin: 0;">Com ligação</h6>
                     <b><a href="{{route($msg['link'])}}" target="_blank">{{ $msg['link'] }}</a></b>
                  @else
                  <h6 style="margin: 0;">Sem ligação associada</h6>
                  @endif
               </div>
               <div class="col-md-2">
               <h6 style="margin: 0;">A mostrar</h6>
                  <b>{{ $msg['to_show'] }}</b>
               </div>
               <div class="col-md-1">
                  <a class="btn btn-danger btn-sm slide-in-blurred-top" style="width: 100%;margin-top: 10%;" 
                  @if(Auth::user()->user_type == "HELPDESK")
                  href="{{route('helpdesk.warnings.delete', $msg['id'] )}}"
                  @else
                  href="{{route('gestão.warnings.delete', $msg['id'] )}}"
                  @endif
                  > Remover</a>
               </div>
               <div style="margin: .5rem;"></div>
            </div>
            @endforeach
            <div style="margin: 1rem;"></div>
         @else 
            <h6>Actuamente, não existe nenhuma mensagem geral na plataforma.</h6>
            <div style="margin: 1rem;"></div>
         @endif
      </div>
   </div>
</div>
@endsection
@section('extra-scripts')

@endsection
