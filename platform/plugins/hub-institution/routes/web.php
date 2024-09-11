<?php

Route::group(['namespace' => 'Impiger\HubInstitution\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'hub-institutions', 'as' => 'hub-institution.'], function () {
            Route::resource('', 'HubInstitutionController')->parameters(['' => 'hub-institution']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'HubInstitutionController@deletes',
                'permission' => 'hub-institution.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'HubInstitutionController@viewdetail',
                'permission' => 'hub-institution.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'HubInstitutionController@postImport',
                'permission' => 'hub-institution.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/HubInstitution/create', 'platform\plugins\src\Http\Controllers\HubInstitutionController@create')->name('HubInstitution.create');
 
        Route::get('/admin/HubInstitution', 'platform\plugins\src\Http\Controllers\HubInstitutionController@index')->name('HubInstitution.index');

        Route::post('/admin/HubInstitution', 'platform\plugins\src\Http\Controllers\HubInstitutionController@store')->name('HubInstitution.store');

        Route::get('/admin/HubInstitution/{any}/edit', 'platform\plugins\src\Http\Controllers\HubInstitutionController@edit')->name('HubInstitution.edit');

        Route::get('/admin/HubInstitution/{any}', 'platform\plugins\src\Http\Controllers\HubInstitutionController@show')->name('HubInstitution.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('hub-institution/postdata', [
            'as'   => 'public.hub-institution.postdata',
            'uses' => 'HubInstitutionPublicController@postData',
        ]);
        Route::post('hub-institution/updatedata/{any}', [
            'as'   => 'public.hub-institution.updatedata',
            'uses' => 'HubInstitutionPublicController@updateData',
        ]);
        Route::get('hub-institution', [
            'as'   => 'public.hub-institution.index',
            'uses' => 'HubInstitutionPublicController@index',
        ]);
        
    });
});
