<?php

Route::group(['namespace' => 'Impiger\TrainingTitleFinancialDetail\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'training-title-financial-details', 'as' => 'training-title-financial-detail.'], function () {
            Route::resource('', 'TrainingTitleFinancialDetailController')->parameters(['' => 'training-title-financial-detail']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'TrainingTitleFinancialDetailController@deletes',
                'permission' => 'training-title-financial-detail.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'TrainingTitleFinancialDetailController@viewdetail',
                'permission' => 'training-title-financial-detail.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'TrainingTitleFinancialDetailController@postImport',
                'permission' => 'training-title-financial-detail.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/TrainingTitleFinancialDetail/create', 'platform\plugins\src\Http\Controllers\TrainingTitleFinancialDetailController@create')->name('TrainingTitleFinancialDetail.create');
 
        Route::get('/admin/TrainingTitleFinancialDetail', 'platform\plugins\src\Http\Controllers\TrainingTitleFinancialDetailController@index')->name('TrainingTitleFinancialDetail.index');

        Route::post('/admin/TrainingTitleFinancialDetail', 'platform\plugins\src\Http\Controllers\TrainingTitleFinancialDetailController@store')->name('TrainingTitleFinancialDetail.store');

        Route::get('/admin/TrainingTitleFinancialDetail/{any}/edit', 'platform\plugins\src\Http\Controllers\TrainingTitleFinancialDetailController@edit')->name('TrainingTitleFinancialDetail.edit');

        Route::get('/admin/TrainingTitleFinancialDetail/{any}', 'platform\plugins\src\Http\Controllers\TrainingTitleFinancialDetailController@show')->name('TrainingTitleFinancialDetail.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('training-title-financial-detail/postdata', [
            'as'   => 'public.training-title-financial-detail.postdata',
            'uses' => 'TrainingTitleFinancialDetailPublicController@postData',
        ]);
        Route::post('training-title-financial-detail/updatedata/{any}', [
            'as'   => 'public.training-title-financial-detail.updatedata',
            'uses' => 'TrainingTitleFinancialDetailPublicController@updateData',
        ]);
        Route::get('training-title-financial-detail', [
            'as'   => 'public.training-title-financial-detail.index',
            'uses' => 'TrainingTitleFinancialDetailPublicController@index',
        ]);
        
    });
});
