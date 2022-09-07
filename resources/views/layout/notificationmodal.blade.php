<div class="modal puff-in-center" id="notitificationModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uniqueNotificationTitle" name="uniqueNotificationTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="uniqueNotificationText" name="uniqueNotificationText"></p>
        <p id="uniqueNotificationActionID" name="uniqueNotificationActionID"></p>
      </div>
      <div class="modal-footer">

        <div id="notification-has-action" style="display:none">
          <form id="has-action-accept-btn" action="#" method="POST" style="display: inline-block;">
            @csrf
            <input type="hidden" id="actionAcceptActionID" name="actionAcceptActionID" value="">
            <button type="submit" class="btn @if (Auth::check() && Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" style="width: 5rem;">Aceitar</button>
          </form>
          <form id="has-action-decline-btn" action="#" method="POST" style="display: inline-block;">
            @csrf
            <input type="hidden" id="actionDeclineActionID" name="actionAcceptActionID" value="">
            <button type="submit" class="btn btn-secondary" >Negar</button>
          </form>
        </div>
        <div id="no-action" style="display:none">
          <form class="no-action-close-btn" action="#" method="POST" style="display: inline-block;">
            @csrf
            <input type="hidden" id="notificationID" name="notificationID" value="">
            <button type="submit" class="btn @if (Auth::check() && Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif">&nbsp;&nbsp;OK&nbsp;&nbsp;</button>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
