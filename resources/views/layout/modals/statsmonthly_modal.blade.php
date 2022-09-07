@if(Auth::check() && $GET_STATS_NOMINAL)
  <div class="modal puff-in-center" id="generateReportMonthly" tabindex="-1" role="dialog" aria-labelledby="generateReportMonthly" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="overlay fade-in-fwd" id="exporMonthReportOverlay">
             <i class="fas fa-2x fa-sync fa-spin"></i>
          </div>
           <div class="modal-header">
              <h5 class="modal-title" id="">Exportar numeros mensais marcações/consumos</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <form id="exportMontlhyReportForm" name="exportReportForm" method="POST" action="{{ route('gestão.estatisticas.export.monthly') }}">
              <div class="modal-body">
                 @csrf
                 <div class="form-group row">
                   <label for="reportLocalSelect" class="col-sm-4 col-form-label">Selecione a  mês</label>
                   <div class="col-sm-8">
                     <select class="custom-select" name="month_select" id="month_select" onchange="ShowExportBtn();">
                       <option value="0" selected disabled>Selecione o mês</option>
                       <option value="01">Janeiro</option>
                       <option value="02">Fevereiro</option>
                       <option value="03">Março</option>
                       <option value="04">Abril</option>
                       <option value="05">Maio</option>
                       <option value="06">Junho</option>
                       <option value="07">Julho</option>
                       <option value="08">Agosto</option>
                       <option value="09">Setembro</option>
                       <option value="10">Outubro</option>
                       <option value="11">Novembro</option>
                       <option value="12">Dezembro</option>
                     </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                 <button type="button" onclick="DownloadMontlyReport();" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="exporterMonthBtn" style="width: 6rem;display: none;">Gerar</button>
                  <button type="button"  data-dismiss="modal" aria-label="Close" class="btn btn-dark" id="exportMontlhyReportClose">Cancelar</button>
              </div>
           </form>
        </div>
     </div>
  </div>

@elseif(Auth::check() && (Auth::user()->user_permission=="MESSES"))

  <div class="modal puff-in-center" id="generateReportMonthlyMesses" tabindex="-1" role="dialog" aria-labelledby="generateReportMonthly" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="overlay fade-in-fwd" id="exporMonthReportOverlayMesses">
             <i class="fas fa-2x fa-sync fa-spin"></i>
          </div>
           <div class="modal-header">
              <h5 class="modal-title" id="">Exportar numeros mensais marcações/consumos</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <form id="exportMontlhyReportFormMesses" name="exportReportForm" method="POST" action="{{ route('gestão.estatisticas.export.monthly.messes') }}">
              <div class="modal-body">
                 @csrf
                 <div class="form-group row">
                   <label for="reportLocalSelect" class="col-sm-4 col-form-label">Selecione a  mês</label>
                   <div class="col-sm-8">
                     <select class="custom-select" name="month_select" id="month_select" onchange="ShowExportBtnMesses();">
                       <option value="0" selected disabled>Selecione o mês</option>
                       <option value="01">Janeiro</option>
                       <option value="02">Fevereiro</option>
                       <option value="03">Março</option>
                       <option value="04">Abril</option>
                       <option value="05">Maio</option>
                       <option value="06">Junho</option>
                       <option value="07">Julho</option>
                       <option value="08">Agosto</option>
                       <option value="09">Setembro</option>
                       <option value="10">Outubro</option>
                       <option value="11">Novembro</option>
                       <option value="12">Dezembro</option>
                     </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                 <button type="button" onclick="DownloadMontlyReportMesses();" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="exporterMonthBtnMesses" style="width: 6rem;display: none;">Gerar</button>
                  <button type="button"  data-dismiss="modal" aria-label="Close" class="btn btn-dark" id="exportMontlhyReportCloseMesses">Cancelar</button>
              </div>
           </form>
        </div>
     </div>
  </div>
  
@endif
