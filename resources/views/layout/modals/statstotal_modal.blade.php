@if(Auth::check() && $GET_STATS_NOMINAL)
  <div class="modal puff-in-center" id="generateReportTotal" tabindex="-1" role="dialog" aria-labelledby="generateReportTotal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="overlay fade-in-fwd" id="exportReportOverlay">
             <i class="fas fa-2x fa-sync fa-spin"></i>
          </div>
           <div class="modal-header">
              <h5 class="modal-title" id="">Exportar dados de marcação/consumo</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <form id="exportReportForm" name="exportReportForm" method="POST" action="{{ route('gestão.estatisticas.export.total') }}">
              <div class="modal-body">
                 @csrf
                 <input type="hidden" id="export_date" name="export_date" >
                   <div class="form-group row" id="customTimeInput2" name="customTimeInput2">
                    <label for="reportLocalSelect" class="col-sm-5 col-form-label">Selecione a data</label>
                    <div class="col-sm-7">
                       <div class="input-group">
                          <div class="input-group-prepend">
                             <span class="input-group-text">
                             <i class="far fa-calendar-alt"></i>
                             </span>
                          </div>
                          <input type="text" autocomplete="off" class="form-control float-right" id="ExportDate" data-toggle="ExportDate" data-target="#ExportDate">
                       </div>
                    </div>
                 </div>
              </div>
              <div class="modal-footer">
                 <button type="button" onclick="DownloadReport();" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="exporterBtn" style="width: 6rem;display: none;">Gerar</button>
                  <button type="button"  data-dismiss="modal" aria-label="Close" class="btn btn-dark" id="exportReportClose">Cancelar</button>
              </div>
           </form>
        </div>
     </div>
  </div>
@endif
