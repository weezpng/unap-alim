<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\URLHandler;
use App\Http\Controllers\marcacoesHandlerController;
use App\Http\Controllers\userProfileHandlerController;
use App\Http\Controllers\gestaoHandlerController;
use App\Http\Controllers\ementaHandlerController;
use App\Http\Controllers\superUserHandlerController;
use App\Http\Controllers\helpdeskController;
// GESTÃO ROUTES
Route::middleware([checkAccountLock::class])->group(function(){
  Route::get('gestão', [gestaoHandlerController::class, 'gestao_index'])->middleware('auth')->name('gestao.index');
  Route::get('partner', [gestaoHandlerController::class, 'viewParelhaProfile'])->middleware('auth')->middleware('auth')->name('user.profile.parelha');

  #Ementa
  Route::get('gestão/ementa', [URLHandler::class, 'gestao_ementa_index'])->middleware('auth')->middleware('auth')->name('gestao.ementa.index');
  Route::POST('gestão/ementa/nova', [ementaHandlerController::class, 'newEmenta'])->middleware('auth')->name('gestao.ementa.novaEmenta');
  Route::POST('gestão/ementa/create', [ementaHandlerController::class, 'CreateEmentaEntry'])->middleware('auth')->name('gestao.ementa.create');
  Route::POST('gestão/ementa/update/meal/0', [ementaHandlerController::class, 'updateAlmoço'])->middleware('auth')->name('gestao.ementa.update.meal0');
  Route::POST('gestão/ementa/update/meal/1', [ementaHandlerController::class, 'updateJantar'])->middleware('auth')->name('gestao.ementa.update.meal1');
  Route::post('gestão/ementa/nova/post', [ementaHandlerController::class, 'postEmentaEntry'])->middleware('auth')->name('gestao.postarEmentaPost');
  Route::post('gestão/ementa/trade', [ementaHandlerController::class, 'tradeEmentaNextDay'])->middleware('auth')->name('gestao.tradeementa.nextday');

  # Estatisticas
  Route::get('gestão/estatisticas', [gestaoHandlerController::class, 'statsAdmin'])->middleware('auth')->name('gestão.statsAdmin');
  Route::get('gestão/estatisticas/day', [gestaoHandlerController::class, 'statsAdminDay'])->middleware('auth')->name('gestão.statsAdminDay');
  Route::get('gestão/estatisticas/consumption', [gestaoHandlerController::class, 'statsRemoved'])->middleware('auth')->name('gestão.statsRemoved');
  Route::POST('gestão/estatisticas/consumption', [gestaoHandlerController::class, 'statsAdminRemoved'])->middleware('auth')->name('gestão.statsAdminRemoved');
  Route::get('gestão/estatisticas/consumption/unit', [gestaoHandlerController::class, 'statsUnit'])->middleware('auth')->name('gestão.AdminUnit');
  Route::POST('gestão/estatisticas/consumption/unit', [gestaoHandlerController::class, 'statsAdminUnit'])->middleware('auth')->name('gestão.AdminUnitLoad');
  Route::get('gestão/estatisticas/last', [gestaoHandlerController::class, 'statsUnitsRemoved'])->middleware('auth')->name('gestão.statsUnitsRemoved');
  Route::POST('gestão/estatisticas/last', [gestaoHandlerController::class, 'statsAdminUnits'])->middleware('auth')->name('gestão.statsAdminLast');

  # Gestão de utilizadores
  Route::get('gestão/quiosque', [gestaoHandlerController::class, 'viewQuiosqueInfo'])->middleware('auth')->name('gestão.quiosqueAdmin');
  Route::get('gestão/utilizadores', [gestaoHandlerController::class, 'usersAdmin'])->middleware('auth')->name('gestão.usersAdmin');
  Route::get('gestão/utilizadores/associados', [gestaoHandlerController::class, 'associatedUsersAdmin'])->middleware('auth')->name('gestão.associatedUsersAdmin');
  Route::get('gestão/utilizadores/adicionar', [gestaoHandlerController::class, 'getGroups'])->middleware('auth')->name('gestão.getUserGroups');
  Route::POST('gestão/utilizadores/remover', [userProfileHandlerController::class, 'newUsersReject'])->middleware('auth')->name('gestão.destroyUser');
  Route::get('gestão/novos_utilizadores', [userProfileHandlerController::class, 'newUsersAdmin'])->middleware('auth')->name('gestão.newUsersAdmin');
  Route::POST('gestão/transferencia_utilizadores/aceitar', [gestaoHandlerController::class, 'movedUsersConfirm'])->middleware('auth')->name('gestão.movedUsersConfirm');
  Route::POST('gestão/transferencia_utilizadores/rejeitar', [gestaoHandlerController::class, 'movedUsersReject'])->middleware('auth')->name('gestão.movedUsersReject');
  Route::POST('gestão/novos_utilizadores/aceitar', [userProfileHandlerController::class, 'newUsersConfirm'])->middleware('auth')->name('gestão.newUsersConfirm');
  Route::POST('gestão/novos_utilizadores/rejeitar', [userProfileHandlerController::class, 'newUsersReject'])->middleware('auth')->name('gestão.newUsersReject');
  Route::POST('gestão/novos_utilizadores/criar_token', [userProfileHandlerController::class, 'newUsersExpressToken'])->middleware('auth')->name('gestão.userCreateToken');
  Route::get('user/{id}', [gestaoHandlerController::class, 'viewUserProfile'])->middleware('auth')->middleware('auth')->name('user.profile');
  Route::POST('user/{id}/tagmeal', [gestaoHandlerController::class, 'SecLogTagMeal'])->middleware('auth')->middleware('auth')->name('user.profile.tag_meal');

  Route::post('user/settings/save', [gestaoHandlerController::class, 'profile_settings_save'])->middleware('auth')->name('profile.admin.save');
  Route::get('search/NIM', [gestaoHandlerController::class, 'NIM_search'])->middleware('auth')->name('NIM.search');
  Route::get('search/NAME', [gestaoHandlerController::class, 'NAME_search'])->middleware('auth')->name('NAME.search');
  Route::get('user/password_reset/{id}', [gestaoHandlerController::class, 'resetPassword'])->middleware('auth')->name('user.reset.password');
  Route::get('search/associateUser/NIM', [gestaoHandlerController::class, 'searchUserAssociate'])->middleware('auth')->name('search.User.Associate');
  Route::get('gestão/associar/{usr}/{to}', [gestaoHandlerController::class, 'associateUser'])->middleware('auth')->name('user.Associate.To');
  Route::POST('gestão/transferir', [gestaoHandlerController::class, 'transferUsers'])->middleware('auth')->name('user.transfer.assoc');
  Route::POST('gestão/transfer_delete', [userProfileHandlerController::class, 'transferUsersAndDeleteOriginal'])->middleware('auth')->name('user.transfer.andDelete');
  Route::get('report/searchuser', [gestaoHandlerController::class, 'searchUserForReport'])->middleware('auth')->name('report.search.user');
  Route::POST('user/remove_tagobl', [gestaoHandlerController::class, 'RemoveIsTagOb'])->middleware('auth')->name('user.removeTagOblig');
  Route::POST('user/lock_account', [gestaoHandlerController::class, 'lockUser'])->middleware('auth')->name('user.lock');
  Route::POST('user/unlock_account', [gestaoHandlerController::class, 'unlockUser'])->middleware('auth')->name('user.unlock');
  Route::POST('gestão/utilizadores', [gestaoHandlerController::class, 'filterUsers'])->middleware('auth')->name('gestao.filterUsers');
  Route::POST('user/tagoblg', [gestaoHandlerController::class, 'marcarUserConfRef'])->middleware('auth')->name('user.tagoblg');

  # RGT
  #Route::POST('gestão/utilizadores/carregamento', [gestaoHandlerController::class, 'loadUsers'])->middleware('auth')->name('gestao.users.loadusers');
  #Route::POST('gestão/utilizadores/carregamento/remove_user', [gestaoHandlerController::class, 'remove_user_fromfile'])->middleware('auth')->name('gestao.users.loadusers.remove');
  #Route::POST('gestão/utilizadores/carregamento/update_user', [gestaoHandlerController::class, 'update_user_fromfile'])->middleware('auth')->name('gestao.users.loadusers.update');
  #Route::POST('gestão/utilizadores/carregamento/create_user', [gestaoHandlerController::class, 'create_user_fromfile'])->middleware('auth')->name('gestao.users.loadusers.create');

  # Marcações quantitativas
  Route::get('marcacao/quantitativas', [marcacoesHandlerController::class, 'marcacoesNotNominalIndex'])->middleware('auth')->name('marcacao.non_nominal');
  Route::POST('marcacao/quantitativas/add', [marcacoesHandlerController::class, 'marcacoesNotNominal_add'])->middleware('auth')->name('marcacao.non_nominal_add');
  Route::POST('marcacao/quantitativas/destroy', [marcacoesHandlerController::class, 'marcacoesNotNominal_destroy'])->middleware('auth')->name('marcacao.non_nominal_remove');

  # Definições de plataforma
  Route::get('gestão/definições', [gestaoHandlerController::class, 'settings_index'])->middleware('auth')->name('gestao.settings');
  Route::POST('gestão/definições/bools', [gestaoHandlerController::class, 'gestao_permissoes_change_bools'])->middleware('auth')->name('gestao.settings.change.bools');
  Route::POST('gestão/definições/int', [gestaoHandlerController::class, 'gestao_permissoes_change_int'])->middleware('auth')->name('gestao.settings.change.int');

  # HOSPEDES
  Route::get('gestão/hóspedes', [gestaoHandlerController::class, 'hospedes_index'])->middleware('auth')->name('gestao.hospedes');
  Route::get('gestão/hóspedes/tagscenter', [gestaoHandlerController::class, 'hospedes_marca'])->middleware('auth')->name('gestao.hospedes.marccenter');
  Route::POST('gestão/hóspedes/tagscenter/tag', [gestaoHandlerController::class, 'TagCenter_Marcar'])->middleware('auth')->name('gestao.htagcenter.marcar');
  Route::POST('gestão/hóspedes/tagscenter/untag', [gestaoHandlerController::class, 'TagCenter_Desmarcar'])->middleware('auth')->name('gestao.htagcenter.desmarcar');
  Route::get('gestão/hóspedes/{id}', [gestaoHandlerController::class, 'hospede_profile'])->middleware('auth')->name('gestao.hospedes.profile');
  Route::POST('gestão/hóspedes/novo', [gestaoHandlerController::class, 'hospede_new'])->middleware('auth')->name('gestao.hospedes.new');
  Route::POST('gestão/hóspedes/guardar', [gestaoHandlerController::class, 'hospede_save'])->middleware('auth')->name('gestao.hospedes.save');
  Route::POST('gestão/hóspedes/remover', [gestaoHandlerController::class, 'hospede_remove'])->middleware('auth')->name('gestao.hospedes.remover');
  Route::POST('gestão/hóspedes/marcarref', [marcacoesHandlerController::class, 'hospede_marcar'])->middleware('auth')->name('marcacao.store.hospede');
  Route::POST('gestão/hóspedes/desmarcarref', [marcacoesHandlerController::class, 'hospede_desmarcar'])->middleware('auth')->name('marcacao.destroy.hospede');
  Route::POST('gestão/hóspedes/pedir_qr', [gestaoHandlerController::class, 'hospedes_request_qr'])->middleware('auth')->name('gestao.hospedes.QRs');
  Route::get('search/HOSPEDE/QUARTO', [gestaoHandlerController::class, 'QUARTO_search'])->middleware('auth')->name('HOSPEDE.QUARTO.search');
  Route::get('search/HOSPEDE/NAME', [gestaoHandlerController::class, 'HOSPEDE_search'])->middleware('auth')->name('HOSPEDE.NAME.search');

  # Dietas
  Route::get('gestão/users/dietas', [gestaoHandlerController::class, 'dietas_index'])->middleware('auth')->name('gestao.dieta.index');
  Route::POST('gestão/users/dietas/remove', [gestaoHandlerController::class, 'dieta_remove'])->middleware('auth')->name('gestao.dieta.remove');
  Route::POST('gestão/users/dietas/add', [gestaoHandlerController::class, 'dieta_create'])->middleware('auth')->name('gestao.dieta.add');

  # Geração de QRs
  Route::get('gestão/utilizadores/qrs/mass', [gestaoHandlerController::class, 'generate_mass_qrs'])->middleware('auth')->name('gestao.qrs.mass');
  Route::get('gestão/utilizadores/qrs/user/{id}', [gestaoHandlerController::class, 'generate_QR_USER'])->middleware('auth')->name('gestao.qrs.user');
  Route::get('gestão/utilizadores/qrs/pcs', [gestaoHandlerController::class, 'generate_QR_PCS'])->middleware('auth')->name('gestao.qrs.pcs');
  Route::get('gestão/utilizadores/qrs/ddn', [gestaoHandlerController::class, 'generate_QR_DDN'])->middleware('auth')->name('gestao.qrs.ddn');
  Route::get('gestão/utilizadores/qrs/dlg', [gestaoHandlerController::class, 'generate_QR_DLG'])->middleware('auth')->name('gestao.qrs.dlg');

  # Férias
  Route::get('gestão/users/férias', [gestaoHandlerController::class, 'marcar_ferias_index'])->middleware('auth')->name('gestao.ferias.index');
  Route::POST('gestão/users/férias/remove', [gestaoHandlerController::class, 'marcar_ferias_remove'])->middleware('auth')->name('gestao.ferias.remove');
  Route::POST('gestão/users/férias/add', [gestaoHandlerController::class, 'marcar_ferias_create'])->middleware('auth')->name('gestao.ferias.add');

  # Avisos de plataforma
  Route::get('gestão/avisos', [helpdeskController::class, 'appWarningsIndex'])->middleware('auth')->name('gestão.warnings.index');
  Route::POST('gestão/avisos/criar', [helpdeskController::class, 'appWarningsNew'])->middleware('auth')->name('gestão.warnings.new');
  Route::get('gestão/avisos/del/ {id}', [helpdeskController::class, 'appWarningsDel'])->middleware('auth')->name('gestão.warnings.delete');

  # Locais de ref
  Route::get('gestão/locais', [gestaoHandlerController::class, 'locaisRef_Index'])->middleware('auth')->name('gestão.locais.index');
  Route::POST('gestão/locais/criar', [gestaoHandlerController::class, 'locaisRef_Save'])->middleware('auth')->name('gestão.locais.save');
  Route::POST('gestão/locais/guardar', [gestaoHandlerController::class, 'locaisRef_SaveEdit'])->middleware('auth')->name('gestão.locais.edit');
  Route::POST('gestão/locais/del', [gestaoHandlerController::class, 'locaisRef_Del'])->middleware('auth')->name('gestão.locais.delete');

  # Unidade
  Route::get('gestão/unidades', [gestaoHandlerController::class, 'Unidades_Index'])->middleware('auth')->name('gestão.unidades.index');
  Route::POST('gestão/unidades/criar', [gestaoHandlerController::class, 'Unidades_Save'])->middleware('auth')->name('gestão.unidades.save');
  Route::POST('gestão/unidades/guardar', [gestaoHandlerController::class, 'Unidades_SaveEdit'])->middleware('auth')->name('gestão.unidades.edit');
  Route::POST('gestão/unidades/del', [gestaoHandlerController::class, 'Unidades_Del'])->middleware('auth')->name('gestão.unidades.delete');

  # Horários de REF
  Route::POST('gestão/horario/save', [gestaoHandlerController::class, 'Horario_Save'])->middleware('auth')->name('gestão.horario.save');

  # Equipa
  Route::get('gestão/equipa', [gestaoHandlerController::class, 'Equipa_Index'])->middleware('auth')->name('gestão.equipa.index');
  Route::post('gestão/equipa/enviar_ping', [gestaoHandlerController::class, 'Equipa_EnviarPing_User'])->middleware('auth')->name('gestão.equipa.ping-user');
  Route::get('gestão/equipa/posts', [gestaoHandlerController::class, 'Equipa_Posts'])->middleware('auth')->name('gestão.equipa.posts');
  Route::post('gestão/equipa/posts/criar', [gestaoHandlerController::class, 'Post_Save'])->middleware('auth')->name('gestão.equipa.create-post');
  Route::POST('gestão/equipa/posts/del', [gestaoHandlerController::class, 'Post_Delete'])->middleware('auth')->name('gestão.equipa.del-post');

  # Exportações de relatórios avançados
  Route::get('gestão/estatisticas/exportacoes', [gestaoHandlerController::class, 'ExportsPage'])->middleware('auth')->name('gestão.estatisticas.allexports');
  Route::POST('gestão/estatisticas/export_general', [gestaoHandlerController::class, 'ExportTotalGeneral'])->middleware('auth')->name('gestão.estatisticas.export.general');
  Route::POST('gestão/estatisticas/export_total', [gestaoHandlerController::class, 'ExportTotalExcel'])->middleware('auth')->name('gestão.estatisticas.export.total');
  Route::POST('gestão/estatisticas/export_month', [gestaoHandlerController::class, 'ExportMonthyExcel'])->middleware('auth')->name('gestão.estatisticas.export.monthly');
  Route::POST('gestão/estatisticas/export_month/messes', [gestaoHandlerController::class, 'ExportMonthyExcelMesses'])->middleware('auth')->name('gestão.estatisticas.export.monthly.messes');
  Route::POST('gestão/estatisticas/export_quant', [gestaoHandlerController::class, 'ExportQuantExcel'])->middleware('auth')->name('gestão.estatisticas.export.quant');
});
