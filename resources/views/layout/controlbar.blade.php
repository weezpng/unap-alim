<aside class="control-sidebar @if(Auth::user()->dark_mode=='Y') control-sidebar-dark @else control-sidebar-light @endif elevation-4">
    <!-- Control sidebar content goes here -->
    <div class="p-3 control-sidebar-content" id="control_sidebar">

        <h3 class="dropdown-item-title uppercase-only text-center">
            <p class="text-sm text-muted user_type_label">{{ auth()->user()->name }}<br><span class="text-xs">{{ auth()->user()->posto }}</span> </p>
        </h3>

        @php
        $_me_id = Auth::user()->id;
        while ((strlen((string)$_me_id))
        < 8) { $_me_id=0 . (string)$_me_id; } $filename="assets/profiles/" .$_me_id . ".JPG" ; $filename_png="assets/profiles/" .$_me_id . ".PNG" ; @endphp

        <a href="{{route('profile.index')}}">
            <div class="image_navbar" style="margin: 6.5vh !important; margin-bottom: 0 !important; margin-top: 0 !important; padding-bottom: 0.75rem;">
                @if (file_exists(public_path($filename)))
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle image__img" src="{{ asset($filename) }}" alt="User profile picture" @if (Auth::user()->lock=='N') style="border: 2px solid #6c757d !important;"
                    @else style="border: 2px solid #d14351 !important;" @endif>
                </div>
                @elseif (file_exists(public_path($filename_png)))
                <div class="text-center" style="padding-top: 1rem;">
                    <img class="profile-user-img img-fluid img-circle image__img" src="{{ asset($filename_png) }}" alt="User profile picture" @if (Auth::user()->lock=='N') style="border: 2px solid #6c757d !important;"
                    @else style="border: 2px solid #d14351 !important;" @endif>
                </div>
                @else
                @php $filename2 = "assets/icons/default.jpg";
                @endphp
                <div class="text-center" style="padding-top: 1rem;">
                    @php $filename2 = "https://cpes-wise2/Unidades/Fotos/". $_me_id . ".JPG";
                    @endphp
                    <img class="profile-user-img img-fluid img-circle image__img" src="{{ asset($filename2) }}" alt="Default profile picture" @if (Auth::user()->lock=='N') style="border: 2px solid #6c757d !important;"
                    @else style="border: 2px solid #d14351 !important;" @endif>
                </div>
                @endif
                @if(Auth::user()->lock=='N')
                    <div class="image__overlay image__overlay--primary">
                        <p class="image__description-link uppercase-only">
                            Ver perfil
                        </p>
                    </div>
                    @endif
            </div>
        </a>

        <div class="navbar-user-useroptions text-xs" >
          <a href="#" data-toggle="modal" data-target="#qrModal" class="dropdown-item dropdown-footer navbar-user-contextbtn text-xs">
            <i class="fa-solid fa-qrcode"></i>&nbsp;&nbsp; Meu código QR
          </a>
        </div>

        <div class="navbar-user-useroptions text-xs" style="margin-top: .25rem;">
          <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item dropdown-footer navbar-user-contextbtn text-xs">
            <i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp; Terminar sessão
          </a>
        </div>

        <hr style="margin-bottom: 1.25rem;">

        <h3 class="dropdown-item-title uppercase-only text-center">
            <p class="text-sm text-muted user_type_label"> CONFIGURAÇÕES </p>
        </h3>

        <div class="form-group text-sm" style="display: inline-flex; margin-top: .75rem; width: 100%; margin-bottom: .25rem;">
          <p style="padding-left: .5rem !important; padding-right: 1rem !important; margin-bottom: 0 !important; width: 90%;">
             <i class="fas fa-moon"></i> &nbsp; Modo <strong>escuro</strong>
             <br><span style="font-size: .8rem;">Define a aparência da aplicação como predominantemente escura.</span>
          </p>
           <div>
             <input type="checkbox" value="1" onclick='toggleDarkMode(this);' class="mr-1" @if (Auth::user()->dark_mode=='Y') checked @endif>
           </div>
        </div>

        <hr>

        <div class="form-group text-sm" style="display: inline-flex; margin-top: .25rem; width: 100%; margin-bottom: .5rem;">
          <p style="padding-left: .5rem !important; padding-right: 1rem !important; margin-bottom: 0 !important; width: 90%;">
            <i class="fas fa-compress"></i> &nbsp; Modo <strong>compacto</strong>
            <br><span style="font-size: .8rem;">Reduzir o tamanho de todo o conteúdo do site. Ideal para monitores mais pequenos.</span>
          </p>
           <div>
             <input type="checkbox" value="1" onclick='toggleCompactMode(this);' class="mr-1" @if (Auth::user()->compact_mode=='Y') checked @endif>
           </div>
        </div>

        <hr>

        <div class="form-group text-sm" style="display: inline-flex; margin-top: .25rem; width: 100%; margin-bottom: .5rem;">
          <p style="padding-left: .5rem !important; padding-right: 1rem !important; margin-bottom: 0 !important; width: 90%;">
            <i class="fas fa-ticket-alt"></i> &nbsp; Barra lateral <strong>flat</strong>
            <br><span style="font-size: .8rem;">Uma aparência diferente para a barra de navegação lateral.</span>
          </p>
           <div>
             <input type="checkbox" value="1" onclick='toggleFlatMode(this);' class="mr-1" @if (Auth::user()->flat_mode=='Y') checked @endif>
           </div>
        </div>

        <hr>

        <div class="form-group text-sm" style="display: inline-flex; margin-top: .25rem; width: 100%; margin-bottom: .5rem;">
          <p style="padding-left: .5rem !important; padding-right: 1rem !important; margin-bottom: 0 !important; width: 90%;">
            <i class="fas fa-icons"></i> &nbsp; <strong>Icones</strong> de barra lateral
            <br><span style="font-size: .8rem;">Mostrar icones descritivos na barra de navegação lateral.</span>
          </p>
           <div>
             <input type="checkbox" value="1" onclick='toggleIcons(this);' class="mr-1" @if (Auth::user()->use_icons=='Y') checked @endif>
           </div>
        </div>

        <hr>

        <div class="form-group text-sm" style="display: inline-flex; margin-top: .25rem; width: 100%; margin-bottom: .5rem;">
          <p style="padding-left: .5rem !important; padding-right: 1rem !important; margin-bottom: 0 !important; width: 90%;">
            <i class="fas fa-feather-alt"></i> &nbsp; Modo <strong>lite</strong>
            <br><span style="font-size: .8rem;">Remove a maior parte das animações, ideial para computadores mais lentos.</span>
          </p>
           <div>
             <input type="checkbox" value="1" onclick='toggleLiteMode(this);' class="mr-1" @if (Auth::user()->lite_mode=='Y') checked @endif>
           </div>
        </div>

        <hr>

        <div class="form-group text-sm" style="display: inline-flex; margin-top: .25rem; width: 100%; margin-bottom: .5rem;">
          <p style="padding-left: .5rem !important; padding-right: 1rem !important; margin-bottom: 0 !important; width: 90%;">
            <i class="far fa-minus-square"></i> &nbsp; Modo <strong>colapso automático</strong>
            <br><span style="font-size: .8rem;">A barra de navegação lateral irá sempre estar fechada ao carregar uma página, maximizando o espaço para conteúdo.</span>
          </p>
           <div>
             <input type="checkbox" value="1" onclick='toggleAutoCollapseMode(this);' class="mr-1" @if (Auth::user()->auto_collapse=='Y') checked @endif>
           </div>
        </div>

        <hr>

        <div class="form-group text-sm" style="display: inline-flex; margin-top: .25rem; width: 100%; margin-bottom: .5rem;">
          <p style="padding-left: .5rem !important; padding-right: 1rem !important; margin-bottom: 0 !important; width: 90%;">
            <i class="far fa-window-maximize"></i> &nbsp; Navegação <strong>persistente</strong>
            <br><span style="font-size: .8rem;">Manter a barra de navegação superior sempre visivel no ecrã.</span>
          </p>
           <div>
             <input type="checkbox" value="1" onclick='toggleStickyTop(this);' class="mr-1" @if (Auth::user()->sticky_top=='Y') checked @endif>
           </div>
        </div>

        <hr>

        <div class="form-group text-sm" style="display: inline-flex; margin-top: .25rem; width: 100%; margin-bottom: .5rem;">
          <p style="padding-left: .5rem !important; padding-right: 1rem !important; margin-bottom: 0 !important; width: 90%;">
            <i class="fas fa-arrows-alt-v"></i> &nbsp; <strong>Redimensionar</strong> caixas de conteúdo
            <br><span style="font-size: .8rem;">Reduzir o tamanho das caixas de conteúdo de forma a evitar scroll da página.</span>
          </p>
           <div>
             <input type="checkbox" value="1" onclick='toggleResizeBox(this);' class="mr-1" @if (Auth::user()->resize_box=='Y') checked @endif>
           </div>
        </div>

        <hr>

    </div>
</aside>
