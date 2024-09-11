<?php

Route::group(['namespace' => 'Impiger\IncubationCenter\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'incubation-centers', 'as' => 'incubation-center.'], function () {
            Route::resource('', 'IncubationCenterController')->parameters(['' => 'incubation-center']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'IncubationCenterController@deletes',
                'permission' => 'incubation-center.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'IncubationCenterController@viewdetail',
                'permission' => 'incubation-center.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'IncubationCenterController@postImport',
                'permission' => 'incubation-center.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/IncubationCenter/create', 'platform\plugins\src\Http\Controllers\IncubationCenterController@create')->name('IncubationCenter.create');
 
        Route::get('/admin/IncubationCenter', 'platform\plugins\src\Http\Controllers\IncubationCenterController@index')->name('IncubationCenter.index');

        Route::post('/admin/IncubationCenter', 'platform\plugins\src\Http\Controllers\IncubationCenterController@store')->name('IncubationCenter.store');

        Route::get('/admin/IncubationCenter/{any}/edit', 'platform\plugins\src\Http\Controllers\IncubationCenterController@edit')->name('IncubationCenter.edit');

        Route::get('/admin/IncubationCenter/{any}', 'platform\plugins\src\Http\Controllers\IncubationCenterController@show')->name('IncubationCenter.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('incubation-center/postdata', [
            'as'   => 'public.incubation-center.postdata',
            'uses' => 'IncubationCenterPublicController@postData',
        ]);
        Route::post('incubation-center/updatedata/{any}', [
            'as'   => 'public.incubation-center.updatedata',
            'uses' => 'IncubationCenterPublicController@updateData',
        ]);
        Route::get('incubation-center', [
            'as'   => 'public.incubation-center.index',
            'uses' => 'IncubationCenterPublicController@index',
        ]);
        
    });
});
