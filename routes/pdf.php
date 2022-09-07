<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pdfGenerationController;

Route::middleware([checkAccountLock::class])->group(function(){
  Route::get('report/generate/civis_sem_confirmações', [pdfGenerationController::class, 'viewUsersMarcouNotConfirmed'])->name('generate.general.faturation.all');
  Route::get('/report/generate/removed_tags', [pdfGenerationController::class, 'DeletedTags'])->name('generate.general.removed.tags');
  Route::get('/report/generate/all_removed_tags', [pdfGenerationController::class, 'DeletedTagsByUnit'])->name('generate.general.removed.tags.quant');
  Route::POST('report/generate', [pdfGenerationController::class, 'getTypeOfReport'])->name('generate.whatToGenerate');
  Route::POST('childrenUser/report/generate', [pdfGenerationController::class, 'generateChildrenUserReport'])->name('generate.Children.User.Report');
  Route::POST('report/generate/timeframe', [pdfGenerationController::class, 'generateGeneralTimeframeReport'])->name('generate.general.timeframe.Report');
  Route::POST('report/generate/user', [pdfGenerationController::class, 'generateGeneralUserReport'])->name('generate.general.user.Report');
  Route::POST('report/generate/nominal', [pdfGenerationController::class, 'generateNominalListing'])->name('generate.general.nominal');
  Route::POST('report/generate/timeframe/log', [pdfGenerationController::class, 'generateMarcacaoPorPOC'])->name('generate.general.timeframe.ALT');
});
