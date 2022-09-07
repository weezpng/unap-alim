<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuiosqueController;

Route::post('/check/quiosque/time_availalable', [QuiosqueController::class, 'IsTime']);
Route::post('/check/quiosque/get_locais', [QuiosqueController::class, 'GetLocaisRef']);
Route::post('/check/quiosque/get_tags_count', [QuiosqueController::class, 'GetNumberTags']);
Route::post('/check/quiosque/prev_confec', [QuiosqueController::class, 'GetNumberConfecionadas']);
Route::post('/check/quiosque/NIM', [QuiosqueController::class, 'checkAndTag']);
Route::post('/check/quiosque/client/entry/', [QuiosqueController::class, 'ClientEntry']);

Route::post('/check/quiosque/count/marcadas', [QuiosqueController::class, 'GetNumberEntriesMarcada']);
Route::post('/check/quiosque/count/naomarcadas', [QuiosqueController::class, 'GetNumberEntriesNaoMarcadas']);

Route::post('/check/quiosque/client/is_local_messe', [QuiosqueController::class, 'IsLocalMesse']);
Route::post('/check/quiosque/client/get_hospe_quarto', [QuiosqueController::class, 'GetHospQuarto']);
Route::post('/check/quiosque/messe/UID', [QuiosqueController::class, 'CheckTagHosp']);

Route::post('/consult/quiosque/manager/uids', [QuiosqueController::class, 'GetIDs']);
Route::post('/consult/quiosque/manager/getDetailsFrom', [QuiosqueController::class, 'getDetailsFromID']);
Route::post('/check/quiosque/client/post_specials/', [QuiosqueController::class, 'PostSpecial']);
Route::post('/auth/quiosque/entry/login', [QuiosqueController::class, 'LoginSysQuiosque']);
Route::post('/auth/quiosque/entry/get_nim', [QuiosqueController::class, 'GetNIM']);
Route::post('/auth/quiosque/entry/get_tag', [QuiosqueController::class, 'GetTag']);
