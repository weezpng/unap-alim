@extends('layout.master')
@section('title','Exportação de relatórios')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
<li class="breadcrumb-item active">Gestão</li>
<li class="breadcrumb-item active">Estatísticas</li>
<li class="breadcrumb-item active">Exportações</li>
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
                        <label for="reportLocalSelect" class="col-sm-4 col-form-label">Selecione a mês</label>
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
                    <button type="button" onclick="DownloadMontlyReport();" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="exporterMonthBtn" style="width: 6rem;display:
                    none;">Gerar</button>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-dark" id="exportMontlhyReportClose">Cancelar</button>
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
                        <label for="reportLocalSelect" class="col-sm-4 col-form-label">Selecione a mês</label>
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
                    <button type="button" onclick="DownloadMontlyReportMesses();" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="exporterMonthBtnMesses" style="width: 6rem;display:
                    none;">Gerar</button>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-dark" id="exportMontlhyReportCloseMesses">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(Auth::check() && $GET_STATS_NOMINAL)
<div class="modal puff-in-center" id="generateReportQuant" tabindex="-1" role="dialog" aria-labelledby="generateReportQuant" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="overlay fade-in-fwd" id="exportReportOverlay2">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="">Exportar dados de consumo de pedidos quantitativos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exportQuantReportForm" name="exportQuantReportForm" method="POST" action="{{ route('gestão.estatisticas.export.quant') }}">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="export_quant_date" name="export_quant_date">
                    <div class="form-group row" id="customTimeInput2" name="customTimeInput2">
                        <label for="reportLocalSelect" class="col-sm-5 col-form-label">Selecione a data</label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" autocomplete="off" class="form-control float-right" id="ExportQuantDate" data-toggle="ExportQuantDate" data-target="#ExportQuantDate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="DownloadQuantReport();" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="exporterBtn2" style="width: 6rem;display:
                    none;">Gerar</button>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-dark" id="exportReportClose2">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(Auth::check() && $GET_STATS_NOMINAL)
<div class="modal puff-in-center" id="generateReportGeneral" tabindex="-1" role="dialog" aria-labelledby="generateReportGeneral" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="overlay fade-in-fwd" id="exportReportOverlayGeneral">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="">Exportar dados de marcação/consumo totais</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exportReportFormGeneral" name="exportReportFormGeneral" method="POST" action="{{ route('gestão.estatisticas.export.general') }}">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="export_date_general" name="export_date_general">
                    <div class="form-group row" id="customTimeInputGeneral" name="customTimeInputGeneral">
                        <label for="reportLocalSelect" class="col-sm-5 col-form-label">Selecione o periodo</label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" autocomplete="off" class="form-control float-right" id="ExportDateGeneral" data-toggle="ExportDateGeneral" data-target="#ExportDateGeneral">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="DownloadReportGeneral();" class="btn @if (Auth::user()->dark_mode=='Y') btn-dark @else btn-secondary @endif" id="exporterBtnGeneral" style="width: 6rem;display:
                    none;">Gerar</button>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-dark" id="exportReportCloseGeneral">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
                    <input type="hidden" id="export_date" name="export_date">
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
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-dark" id="exportReportClose">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="col-md-12">
    <div class="card">
        <div class="card-body">

            @if($GET_STATS_NOMINAL)

              <div class="comment-text">
                  <span class="username" style="display: block;">
                      <h5>
                        <i class="fa-solid fa-chevron-right" style="font-size: 1rem;margin-right: 0.5rem;"></i>
                        <b>Dados consumo geral</b>
                      </h5>
                  </span>
                  Neste relatório pode verificar, <b>num periodo de tempo</b>, em <b>todas as unidades</b> as <b>estatisticas de marcação e consumo</b>.<br>
                  Também pode verificar informação sobre os pedidos quantitativos
                  <br />
                    Modelo do relatório: <b>Web</b>
                  <br />
                    <a href="{{route('gestão.statsRemoved')}}">
                      <button type="button" class="btn btn-dark" style="width: 8rem; margin-top: 1rem;">Aceder</button>
                    </a>

              </div>
              <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid rgb(255 255 255 / 64%);">

              <div class="comment-text">
                  <span class="username" style="display: block;">
                      <h5>
                          <i class="fa-solid fa-chevron-right" style="font-size: 1rem;margin-right: 0.5rem;"></i>
                          <b>Dados consumo díario</b>
                      </h5>
                  </span>
                  Neste relatório pode verificar, <b>de um dia</b>, em <b>todas as unidades</b> as <b>estatisticas de marcação e consumo</b>.<br>
                  Também pode verificar informação sobre os pedidos quantitativos
                  <br />
                    Modelo do relatório: <b>Web</b>
                  <br />
                  <a href="{{route('gestão.statsUnitsRemoved')}}">
                    <button type="button" class="btn btn-dark" style="width: 8rem; margin-top: 1rem;">Aceder</button>
                  </a>
              </div>

              <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid rgb(255 255 255 / 64%);">

              <div class="comment-text">
                  <span class="username" style="display: block;">
                      <h5>
                        <i class="fa-solid fa-chevron-right" style="font-size: 1rem;margin-right: 0.5rem;"></i>
                        <b>Estatisticas totais</b>
                      </h5>
                  </span>
                  Neste relatório pode verificar <b>detalhadamente</b>, em <b>um perido de tempo</b> os <b>números de marcações e consumo</b>, <b>juntamente</b> com a informação dos <b>pedidos quantitativos</b>.
                  <br />
                    Modelo do relatório: <b>Excel</b>
                  <br />
                  <a href="#" data-toggle="modal" data-target="#generateReportGeneral">
                    <button type="button" class="btn btn-dark" style="width: 8rem; margin-top: 1rem;">Aceder</button>
                  </a>
              </div>

              <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid rgb(255 255 255 / 64%);">
              <div class="comment-text">
                  <span class="username" style="display: block;">
                      <h5>
                          <i class="fa-solid fa-chevron-right" style="font-size: 1rem;margin-right: 0.5rem;"></i>
                          <b>Estatisticas nominais</b>
                      </h5>
                  </span>
                  Neste relatório pode verificar <b>nominalmente</b>, de <b>um dia e uma unidade</b>, as <b>estatisticas de marcação e consumo</b>.
                  <br />
                    Modelo do relatório: <b>Excel</b>
                  <br />
                  <a href="#" data-toggle="modal" data-target="#generateReportTotal">
                    <button type="button" class="btn btn-dark" style="width: 8rem; margin-top: 1rem;">Aceder</button>
                  </a>
              </div>

              <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid rgb(255 255 255 / 64%);">
              <div class="comment-text">
                  <span class="username" style="display: block;">
                      <h5>
                          <i class="fa-solid fa-chevron-right" style="font-size: 1rem;margin-right: 0.5rem;"></i>
                          <b>Estatisticas quantitativas</b>
                      </h5>
                  </span>
                  Neste relatório pode verificar estatisticas de marcação e consumo de pedidos quantitativos</b>.
                  <br />
                    Modelo do relatório: <b>Excel</b>
                  <br />
                  <a href="#" data-toggle="modal" data-target="#generateReportQuant">
                    <button type="button" class="btn btn-dark" style="width: 8rem; margin-top: 1rem;">Aceder</button>
                  </a>
              </div>

              <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid rgb(255 255 255 / 64%);">
              @if($GET_STATS_OTHER_UNITS)
                  <div class="comment-text">
                      <span class="username" style="display: block;">
                          <h5>
                              <i class="fa-solid fa-chevron-right" style="font-size: 1rem;margin-right: 0.5rem;"></i>
                              <b>Estatisticas mensais</b>
                          </h5>
                      </span>
                      Neste relatório pode verificar <b>nominalmente</b> de <b>todas as unidades</b>, em <b>um mês</b> os <b>números de marcações e consumo</b>.
                      <br />
                        Modelo do relatório: <b>Excel</b>
                      <br />
                      <a href="#" data-toggle="modal" data-target="#generateReportMonthly">
                        <button type="button" class="btn btn-dark" style="width: 8rem; margin-top: 1rem;">Aceder</button>
                      </a>
                  </div>

            @endif
          @endif

          @if(Auth::user()->user_permission=="MESSES")

            <div class="comment-text">
                <span class="username" style="display: block;">
                    <h5>
                        <i class="fa-solid fa-chevron-right" style="font-size: 1rem;margin-right: 0.5rem;"></i>
                        <b>Estatisticas mensais de hóspedes</b>
                    </h5>
                </span>
                Neste relatório pode verificar, com base <b>por dia</b>, em <b>um mês</b> os <b>nominais de marcações e consumo</b>.
                <br />
                  Modelo do relatório: <b>Excel</b>
                <br />
                <a href="#" data-toggle="modal" data-target="#generateReportMonthlyMesses">
                  <button type="button" class="btn btn-dark" style="width: 8rem; margin-top: 1rem;">Aceder</button>
                </a>
            </div>
          @endif

        </div>
    </div>
</div>
@endsection
