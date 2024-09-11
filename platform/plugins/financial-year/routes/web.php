<?php

Route::group(['namespace' => 'Impiger\FinancialYear\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'financial-years', 'as' => 'financial-year.'], function () {
            Route::resource('', 'FinancialYearController')->parameters(['' => 'financial-year']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'FinancialYearController@deletes',
                'permission' => 'financial-year.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'FinancialYearController@viewdetail',
                'permission' => 'financial-year.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'FinancialYearController@postImport',
                'permission' => 'financial-year.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/FinancialYear/create', 'platform\plugins\src\Http\Controllers\FinancialYearController@create')->name('FinancialYear.create');
 
        Route::get('/admin/FinancialYear', 'platform\plugins\src\Http\Controllers\FinancialYearController@index')->name('FinancialYear.index');

        Route::post('/admin/FinancialYear', 'platform\plugins\src\Http\Controllers\FinancialYearController@store')->name('FinancialYear.store');

        Route::get('/admin/FinancialYear/{any}/edit', 'platform\plugins\src\Http\Controllers\FinancialYearController@edit')->name('FinancialYear.edit');

        Route::get('/admin/FinancialYear/{any}', 'platform\plugins\src\Http\Controllers\FinancialYearController@show')->name('FinancialYear.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('financial-year/postdata', [
            'as'   => 'public.financial-year.postdata',
            'uses' => 'FinancialYearPublicController@postData',
        ]);
        Route::post('financial-year/updatedata/{any}', [
            'as'   => 'public.financial-year.updatedata',
            'uses' => 'FinancialYearPublicController@updateData',
        ]);
        Route::get('financial-year', [
            'as'   => 'public.financial-year.index',
            'uses' => 'FinancialYearPublicController@index',
        ]);
        
    });
});
