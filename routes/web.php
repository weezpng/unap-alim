<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\checkAssociationRequests;
use App\Http\Middleware\checkIsTagOblig;
use App\Http\Controllers\URLHandler;
use App\Http\Controllers\marcacoesHandlerController;
use App\Http\Controllers\userProfileHandlerController;
use App\Http\Controllers\gestaoHandlerController;
use App\Http\Controllers\ementaHandlerController;
use App\Http\Controllers\notificationsHandler;
use App\Http\Controllers\QuiosqueController;

// BASIC

Route::get('/el', [URLHandler::class, 'login_RDE'])->name('express.login');

Route::middleware([checkAssociationRequests::class])->group(function(){
  Route::get('locked', [URLHandler::class, 'accountLocked'])->name('locked');
  Route::middleware([checkAccountLock::class])->group(function(){
    Route::middleware([checkIsTagOblig::class])->group(function(){
      Route::get('/', [URLHandler::class, 'index'])->name('index');
      Route::get('/check-timeout', [URLHandler::class, 'check_timeout'])->name('check-timeout');

      Route::POST('/redirect-lockscreen', [URLHandler::class, 'lockscreen'])->name('redirect-lockscreen');

      Route::get('/QRcode/download', [URLHandler::class, 'qr_download'])->middleware('auth')->name('qr.download');
      Route::POST('/QRcode/send', [URLHandler::class, 'send_mail'])->middleware('auth')->name('qr.send_to_email');
      Route::get('/QRcode/request', [URLHandler::class, 'qr_code_request'])->middleware('auth')->name('qr.request_pess');
      Route::get('perfil/quiosque', [URLHandler::class, 'viewQuiosqueInfo'])->middleware('auth')->name('perfil.quiosque');
      // MARCAcÕES ROUTES (AUTH REQUIRED)
      Route::get('marcacao', [URLHandler::class, 'marcacaoIndex'])->middleware('auth')->name('marcacao.index');
      Route::get('marcacao/minhas_marcacoes', [marcacoesHandlerController::class, 'verMinhasMarcacoes'])->middleware('auth')->name('marcacao.minhas');
      Route::post('marcacao/marcar_ref', [marcacoesHandlerController::class, 'store'])->middleware('auth')->name('marcacao.store');
      Route::post('marcacao/change_ref', [marcacoesHandlerController::class, 'change_ref'])->middleware('auth')->name('marcacao.change');
      Route::post('marcacao/marcar_ref/children', [marcacoesHandlerController::class, 'store_children'])->middleware('auth')->name('marcacao.store.children');
      Route::post('marcacao/remove_ref', [marcacoesHandlerController::class, 'destroy'])->middleware('auth')->name('marcacao.destroy');
      // PROFILE ROUTES
      Route::get('perfil', [userProfileHandlerController::class, 'profile_index'])->middleware(['auth'])->name('profile.index');
      Route::post('perfil/settings/save', [userProfileHandlerController::class, 'profile_settings_save'])->middleware(['auth'])->name('profile.settings.save');
      Route::post('perfil/upload_picture', [userProfileHandlerController::class, 'profile_picture_upload'])->middleware('auth')->name('profile.picture.upload');
      // CONFIRMATION ROUTES
      Route::get('marcacao/confirmacoes', [marcacoesHandlerController::class, 'conf_index'])->name('confirmacoes.index')->middleware(['auth']);
      Route::post('marcacao/confirmacoes/confirm', [marcacoesHandlerController::class, 'conf_post'])->name('confirmacoes.post')->middleware(['auth']);
      // ASSOCIATION CONFIRMATION ROUTES (REQUESTED BY SUPER)
      Route::POST('association/request', [userProfileHandlerController::class, 'association_request'])->name('profile.association.request')->middleware(['auth']);
      Route::get('association/confirm', [userProfileHandlerController::class, 'association_confirm'])->name('profile.association.confirm')->middleware(['auth']);
      Route::get('association/decline', [userProfileHandlerController::class, 'association_decline'])->name('profile.association.decline')->middleware(['auth']);
      Route::get('association/cancel', [userProfileHandlerController::class, 'association_cancel'])->name('profile.association.cancel')->middleware(['auth']);
      // ASSOCIATION CONFIRMATION ROUTES (REQUESTED BY USER)
      Route::POST('association/confirm', [userProfileHandlerController::class, 'association_by_USER_confirm'])->name('profile.association.by_user.confirm')->middleware(['auth']);
      Route::POST('association/decline', [userProfileHandlerController::class, 'association_by_USER_decline'])->name('profile.association.by_user.decline')->middleware(['auth']);
      // TOGGLE SETTINGS
      Route::POST('settings/toggle/theme', [userProfileHandlerController::class, 'toggle_dark_mode'])->name('profile.dark_mode.toggle')->middleware(['auth']);
      Route::POST('settings/toggle/compact', [userProfileHandlerController::class, 'toggle_compact_mode'])->name('profile.compact_mode.toggle')->middleware(['auth']);
      Route::POST('settings/toggle/flat', [userProfileHandlerController::class, 'toggle_flat_mode'])->name('profile.flat_mode.toggle')->middleware(['auth']);
      Route::POST('settings/toggle/icons', [userProfileHandlerController::class, 'toggle_icons'])->name('profile.icons.toggle')->middleware(['auth']);
      Route::POST('settings/toggle/lite', [userProfileHandlerController::class, 'toggle_lite_mode'])->name('profile.lite_mode.toggle')->middleware(['auth']);
      Route::POST('settings/toggle/auto_collapse', [userProfileHandlerController::class, 'toggle_auto_collapse_mode'])->name('profile.auto_collapse.toggle')->middleware(['auth']);
      Route::POST('settings/toggle/sticky_top', [userProfileHandlerController::class, 'toggle_sticky_nav_mode'])->name('profile.sticky_top.toggle')->middleware(['auth']);
      Route::POST('settings/toggle/resize_box', [userProfileHandlerController::class, 'toggle_resizer_mode'])->name('profile.resize_box.toggle')->middleware(['auth']);
        // SETTING NO AUTH
      Route::POST('settings/toggle/theme/noauth', [URLHandler::class, 'toggle_dark_mode_noauth'])->name('noauth.dark_mode.toggle');
      // NOTIFICATIONS CHECK
      Route::POST('notifications/seen', [URLHandler::class, 'notification_check_seen'])->name('notifications.check.seen')->middleware(['auth']);
      Route::POST('notifications/deltouser', [URLHandler::class, 'notification_del_toUser'])->name('notifications.del.to_user')->middleware(['auth']);

      Route::get('/FAQ', [URLHandler::class, 'FAQ'])->middleware(['auth'])->name('help.faq');
    });
  });
  Route::get('ementa', [URLHandler::class, 'ementa_index'])->name('ementa.index');
});
// PROFILE AUTH ROUTES
require __DIR__.'/auth.php';
require __DIR__.'/helpdeskroutes.php';
require __DIR__.'/adminroutes.php';
require __DIR__.'/superroutes.php';
require __DIR__.'/pocroutes.php';
require __DIR__.'/pdf.php';
require __DIR__.'/quiosque.php';
