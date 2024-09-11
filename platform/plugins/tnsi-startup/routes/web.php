<?php

Route::group(['namespace' => 'Impiger\TnsiStartup\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'tnsi-startups', 'as' => 'tnsi-startup.'], function () {
            Route::resource('', 'TnsiStartupController')->parameters(['' => 'tnsi-startup']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'TnsiStartupController@deletes',
                'permission' => 'tnsi-startup.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'TnsiStartupController@viewdetail',
                'permission' => 'tnsi-startup.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'TnsiStartupController@postImport',
                'permission' => 'tnsi-startup.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/TnsiStartup/create', 'platform\plugins\src\Http\Controllers\TnsiStartupController@create')->name('TnsiStartup.create');
 
        Route::get('/admin/TnsiStartup', 'platform\plugins\src\Http\Controllers\TnsiStartupController@index')->name('TnsiStartup.index');

        Route::post('/admin/TnsiStartup', 'platform\plugins\src\Http\Controllers\TnsiStartupController@store')->name('TnsiStartup.store');

        Route::get('/admin/TnsiStartup/{any}/edit', 'platform\plugins\src\Http\Controllers\TnsiStartupController@edit')->name('TnsiStartup.edit');

        Route::get('/admin/TnsiStartup/{any}', 'platform\plugins\src\Http\Controllers\TnsiStartupController@show')->name('TnsiStartup.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('tnsi-startup/postdata', [
            'as'   => 'public.tnsi-startup.postdata',
            'uses' => 'TnsiStartupPublicController@postData',
        ]);
        Route::post('tnsi-startup/updatedata/{any}', [
            'as'   => 'public.tnsi-startup.updatedata',
            'uses' => 'TnsiStartupPublicController@updateData',
        ]);
        Route::get('tnsi-startup', [
            'as'   => 'public.tnsi-startup.index',
            'uses' => 'TnsiStartupPublicController@index',
        ]);
        
    });
});
