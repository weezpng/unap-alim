@if(is_array($notifications))
  <li id="notsBtn" class="nav-item dropdown">
    @if ($howManyNotifications>0)
      <a id="notsDrop" class="nav-link" data-toggle="dropdown" href="#">
        <i class="fas fa-bell"></i>
        <span class="badge badge-primary navbar-badge" id="howManyNots">{{$howManyNotifications}}</span>
      </a>
    @else
    @endif
    @if($howManyNotifications>0)
    <div id="notsBar" class="dropdown-menu dropdown-menu-lg dropdown-menu-right swing-in-top-fwd " style="height: 50vh !important; overflow-x: hidden; width: 370px;">
        @foreach($notifications as $notification)
          @if($notification['notification_seen']!="Y")

              <div class="media" id="not{{ $notification['id'] }}">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                  <a href="#" id="NOTIFICATION{{ $notification['id'] }}" data-title="{{ $notification['notification_title'] }}"
                    data-id="{{ $notification['id'] }}" data-text="{{ $notification['notification_text'] }}"
                    @if (isset($notification['action']))
                      data-action-id="{{ $notification['action']['id'] }}" data-action-type="{{ $notification['action']['action_type'] }}"
                    @endif
                    data-toggle="modal" data-target="#notitificationModal" class="dropdown-item dropdown-item-notification-href"
                    style="margin-bottom: .25rem; padding: 0.5rem 0.6rem;">
                    @if($notification['notification_type']=="NORMAL")
                        <i class="far fa-bell text-danger" style="margin: .5em;"></i>
                      @else
                        <i class="fas fa-exclamation text-danger" style="margin: .5em;"></i>
                      @endif
                    {{ $notification['notification_title'] }}
                    </a>
                    <span class="float-right text-sm text-danger" style="padding-bottom: 1em; padding-right: 0.5rem; padding-left: 0.25rem;">
                    <form>
                      <button type="button" onclick="delNotToUser('{{ $notification['id'] }}');" style="background-color: transparent; border: none; margin-top: -0.25rem;"><i class="fas fa-times" style="margin: .5em; color: #1b814c;"></i></button>
                    </form>
                    </span>
                  </h3>
                  <p class="text-sm" style="margin: 0rem 0rem 1rem 1rem;">
                    {{\Illuminate\Support\Str::limit($notification['notification_text'], 100, $end='...')}}
                    <hr style="margin-top: 0rem; margin-bottom: 0rem; border: 0; border-top: 5px solid rgba(0,0,0,.1);">
                </div>
              </div>

          @endif
        @endforeach
    @endif
  </li>
@endif
