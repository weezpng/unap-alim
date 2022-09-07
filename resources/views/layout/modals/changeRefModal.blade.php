@if(Auth::check() && $CHANGE_MEAL_TIMES)

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

<div class="modal fade" id="mealTime" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xs" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Horários de refeição</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('gestão.horario.save')}}" method="post" id="refTimesForm">
                <div class="modal-body" id="mealTimeForms">
                    <p class="text-sm">
                        Defina os hórarios de refeição abaixo.<br />
                        A <b>1ºrefeição</b> pode ser definida entre as <b>06:00h</b> e as <b>10:00h</b>, a <b>2º refeição</b> das <b>11:00h</b> às <b>15:00h</b> e a <b>3º refeição</b> das <b>18:00h</b> às <b>21:00h</b>.
                    </p>
                    <hr />
                    <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">1ºRefeição</label>
                        <div class="col-sm-4 offset-sm-1">
                            <div class="input-group date" id="timepicker1">
                                <input type="time" class="form-control" value="{{ $_1REF['time_start'] }}"  name="timepicker1">
                                <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <h6 style="margin: 0 auto; margin-top: 0.5rem;">até</h6>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group date" id="timepicker1_2">
                                <input type="time" class="form-control" value="{{ $_1REF['time_end'] }}"  name="mepicker1_2">
                                <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr >
                    <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">2ºRefeição</label>
                        <div class="col-sm-4 offset-sm-1">
                            <div class="input-group date" id="timepicker2">
                                <input type="time" class="form-control" value="{{ $_2REF['time_start'] }}"  name="timepicker2">
                                <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <h6 style="margin: 0 auto; margin-top: 0.5rem;">até</h6>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group date" id="timepicker2_2">
                                <input type="time" class="form-control" value="{{ $_2REF['time_end'] }}"  name="mepicker2_2">
                                <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr >
                    <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">3ºRefeição</label>
                        <div class="col-sm-4 offset-sm-1">
                            <div class="input-group date" id="timepicker3">
                                <input type="time" class="form-control" value="{{ $_3REF['time_start'] }}"  name="timepicker3">
                                <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <h6 style="margin: 0 auto; margin-top: 0.5rem;">até</h6>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group date" id="timepicker3_2">
                                <input type="time" class="form-control" value="{{ $_3REF['time_end'] }}"  name="mepicker3_2">
                                <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="changeRefTime();" class="btn btn-primary slide-in-blurred-top">Guardar</button>
                    <button type="button" class="btn btn-dark slide-in-blurred-top" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
