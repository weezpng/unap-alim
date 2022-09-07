<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\checkIsTagOblig;
use App\Http\Middleware\POCCheck;

use App\Http\Controllers\POC_Controller;

Route::middleware('auth')->group(function(){
  Route::middleware([POCCheck::class])->group(function(){
    Route::middleware([checkIsTagOblig::class])->group(function(){
      Route::middleware([checkAccountLock::class])->group(function(){
        Route::get('/poc-control-center', [POC_Controller::class, 'control_center_index'])->name('poc.index');
        Route::post('/poc-control-center/marcar', [POC_Controller::class, 'marcar_2ref'])->name('poc.marcar');
        Route::post('/poc-control-center/desmarcar', [POC_Controller::class, 'remove_2ref'])->name('poc.remove');
        Route::post('/poc-control-center/change_loc', [POC_Controller::class, 'changeLocRef'])->name('poc.change_loc');

         Route::get('/ferias', [POC_Controller::class, 'poc_ferias_index'])->name('poc.ferias.index');
      });
    });
  });
});
