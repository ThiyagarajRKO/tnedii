<?php

use Impiger\Table\Http\Controllers\TableController;

Route::group(['middleware' => ['web', 'core', 'auth'], 'prefix' => 'tables', 'permission' => false], function () {
    Route::get('bulk-change/data', [TableController::class, 'getDataForBulkChanges'])->name('tables.bulk-change.data');
    Route::post('bulk-change/save', [TableController::class, 'postSaveBulkChange'])->name('tables.bulk-change.save');
    Route::get('get-filter-input', [TableController::class, 'getFilterInput'])->name('tables.get-filter-input');
    /* @Customized By Ramesh Esakki */
    Route::post('bulk-change/inlinesave', [TableController::class, 'postSaveInlineBulkChange'])->name('tables.bulk-change.inlinesave');
    Route::post('bulk-change/saveattendance', [TableController::class, 'postAttendanceData'])->name('tables.bulk-change.saveattendance');

});
