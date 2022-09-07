<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\helpdeskController;
use App\Http\Controllers\QuiosqueController;

//PERMISSÕES MANAGER
Route::middleware([checkAccountLock::class])->group(function(){
  // PERMISSION MANAGER
  Route::get('helpdesk/permissões', [helpdeskController::class, 'gestao_permissoes_index'])->middleware('auth')->name('helpdesk.permissões.index');
  Route::POST('helpdesk/permissões/acc_type', [helpdeskController::class, 'gestao_permissoes_change'])->middleware('auth')->name('helpdesk.permissões.change');
  Route::POST('helpdesk/permissões/acc_level', [helpdeskController::class, 'change_permissao'])->middleware('auth')->name('helpdesk.permissões.specific.change');
  // AVISOS DE PLATAFORMA
  Route::get('helpdesk/avisos', [helpdeskController::class, 'appWarningsIndex'])->middleware('auth')->name('helpdesk.warnings.index');
  Route::POST('helpdesk/avisos/criar', [helpdeskController::class, 'appWarningsNew'])->middleware('auth')->name('helpdesk.warnings.new');
  Route::get('helpdesk/avisos/del/ {id}', [helpdeskController::class, 'appWarningsDel'])->middleware('auth')->name('helpdesk.warnings.delete');
  //CONSULTAS
  Route::get('helpdesk/consultas', [helpdeskController::class, 'gestao_consultas_index'])->middleware('auth')->name('helpdesk.consultas.index');
  Route::get('helpdesk/consultas/searchNIM', [helpdeskController::class, 'gestao_consultas_search'])->middleware('auth')->name('helpdesk.consultas.search');
  Route::get('helpdesk/consultas/{id}', [helpdeskController::class, 'gestao_consultas_result'])->middleware('auth')->name('helpdesk.consultas.result');
  // SETTINGS
  Route::get('helpdesk/definições', [helpdeskController::class, 'settings_index'])->middleware('auth')->name('helpdesk.settings.index');
  Route::POST('helpdesk/definições/bools', [helpdeskController::class, 'gestao_permissoes_change_bools'])->middleware('auth')->name('helpdesk.settings.change.bools');
  Route::POST('helpdesk/definições/int', [helpdeskController::class, 'gestao_permissoes_change_int'])->middleware('auth')->name('helpdesk.settings.change.int');
  // TOOLS
  Route::get('user/remove/{id}', [helpdeskController::class, 'helpdeskApagarUser'])->middleware('auth')->name('helpdesk.remove.user');
  Route::get('user/account_reset/{id}', [helpdeskController::class, 'resetUser'])->middleware('auth')->name('helpdesk.reset.user');
  // ACCOUNT TOOLS
  Route::get('user/reset_perms/{id}', [helpdeskController::class, 'takePerms'])->middleware('auth')->name('helpdesk.user.reset_perms');
  Route::get('user/reset_pedidos/{id}', [helpdeskController::class, 'takePendings'])->middleware('auth')->name('helpdesk.user.reset_pendings');
  Route::get('user/reset_pref/{id}', [helpdeskController::class, 'takePreferencias'])->middleware('auth')->name('helpdesk.user.reset_pref');
  Route::get('user/reset_all/{id}', [helpdeskController::class, 'clearAllPrefPerm'])->middleware('auth')->name('helpdesk.user.reset_all');
  Route::get('user/disable/{id}', [helpdeskController::class, 'disableAccount'])->middleware('auth')->name('helpdesk.user.disable');
  Route::get('user/block/{id}', [helpdeskController::class, 'blockAccount'])->middleware('auth')->name('helpdesk.user.block');
  Route::get('user/delete/{id}', [helpdeskController::class, 'deleteAccount'])->middleware('auth')->name('helpdesk.user.delete');
  Route::get('user/logoff/{id}', [helpdeskController::class, 'logOffAll'])->middleware('auth')->name('helpdesk.user.loggoff');
  // TAGS TOOLS
  Route::get('user/remove_ref/{id}/meal/{ref}', [helpdeskController::class, 'removeTags'])->middleware('auth')->name('helpdesk.user.del_tags');
  Route::get('user/remove_ref/{id}/all', [helpdeskController::class, 'removeAllTags'])->middleware('auth')->name('helpdesk.user.del_all_tags');
  Route::get('user/remove_quants/{id}/all', [helpdeskController::class, 'removeAllQuantByUsr'])->middleware('auth')->name('helpdesk.user.del_all_quant');
  Route::get('user/remove_tagoblig/{id}', [helpdeskController::class, 'userRemoveTagOblig'])->middleware('auth')->name('helpdesk.user.del_tag_oblig');
  // OTHERS TOOLS
  Route::get('user/remove_entries/{id}/meal/{ref}', [helpdeskController::class, 'removeEntries'])->middleware('auth')->name('helpdesk.user.del_entries');
  Route::get('user/remove_entries/{id}/all', [helpdeskController::class, 'removeAllEntries'])->middleware('auth')->name('helpdesk.user.del_all_entries');
  Route::get('user/remove_assoc/from/{id}', [helpdeskController::class, 'removeAssocFrom'])->middleware('auth')->name('helpdesk.user.assoc.from');
  Route::get('user/remove_assoc/to/{id}', [helpdeskController::class, 'removeAssocTo'])->middleware('auth')->name('helpdesk.user.assoc.to');
  Route::get('user/remove_nots/from/{id}', [helpdeskController::class, 'removeNotsFrom'])->middleware('auth')->name('helpdesk.user.nots.from');
  Route::get('user/remove_nots/to/{id}', [helpdeskController::class, 'removeNotsTo'])->middleware('auth')->name('helpdesk.user.nots.to');
  Route::get('user/remove_posts/{id}', [helpdeskController::class, 'removePosts'])->middleware('auth')->name('helpdesk.user.del.posts');
  Route::get('user/remove_warnings/{id}', [helpdeskController::class, 'removeWarnings'])->middleware('auth')->name('helpdesk.user.del.warnings');


});

// TESTING ROUTES (A NAO ATIVAR)

// Route::get('/get_quant', [helpdeskController::class, 'GenerateReport']);
// Route::get('/get_user', [helpdeskController::class, 'GenerateReport2']);
// Route::get('/get_general', [helpdeskController::class, 'GenerateReport3']);
// Route::get('/remove-perm-tagoblig', [helpdeskController::class, 'RemoveTagObligPERM']);
// Route::get('/download-all-profilepic', [helpdeskController::class, 'DownloadAllPics']);
 Route::get('/reset-all-passwords', [helpdeskController::class, 'resetAllPasswords']);
// Route::get('/reset-all-passwords', [helpdeskController::class, 'resetAllPasswords']);
// Route::get('/add-all-to-pedidos', [helpdeskController::class, 'addAllToQR']);
// Route::get('/get-marcs', [helpdeskController::class, 'getAllMarcações']);
// Route::get('/remove-must-reset-pw', [helpdeskController::class, 'RemovePasswordFieldReseter']);
// Route::get('/add_unidade_field', [helpdeskController::class, 'test']);
// Route::get('/add_unidade_field_quisoque', [helpdeskController::class, 'PopulateUnidade']);
// Route::get('/count_entradas', [helpdeskController::class, 'CountEntradas']);
// Route::get('/add_civil_qui', [helpdeskController::class, 'populateCivilPostoQuio']);
// Route::get('/populate_civil_tag', [helpdeskController::class, 'populateCivilPostoMarca']);
// Route::get('/premove_nots_field', [helpdeskController::class, 'DelNotsField']);
