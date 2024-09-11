<?php

Route::group(['namespace' => 'Impiger\SpokeRegistration\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'spoke-registrations', 'as' => 'spoke-registration.'], function () {
            Route::resource('', 'SpokeRegistrationController')->parameters(['' => 'spoke-registration']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'SpokeRegistrationController@deletes',
                'permission' => 'spoke-registration.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'SpokeRegistrationController@viewdetail',
                'permission' => 'spoke-registration.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'SpokeRegistrationController@postImport',
                'permission' => 'spoke-registration.index',
				]);
        });
        Route::group(['prefix' => 'spoke-ecells', 'as' => 'spoke-ecells.'], function () {
            Route::resource('', 'SpokeEcellsController')->parameters(['' => 'spoke-ecells']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'SpokeEcellsController@deletes',
                'permission' => 'spoke-ecells.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'SpokeEcellsController@viewdetail',
                'permission' => 'spoke-ecells.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'SpokeEcellsController@postImport',
                'permission' => 'spoke-ecells.index',
				]);
        });
			#{submodule_routes}
        
        Route::get('/admin/SpokeRegistration/create', 'platform\plugins\src\Http\Controllers\SpokeRegistrationController@create')->name('SpokeRegistration.create');
 
        Route::get('/admin/SpokeRegistration', 'platform\plugins\src\Http\Controllers\SpokeRegistrationController@index')->name('SpokeRegistration.index');

        Route::post('/admin/SpokeRegistration', 'platform\plugins\src\Http\Controllers\SpokeRegistrationController@store')->name('SpokeRegistration.store');

        Route::get('/admin/SpokeRegistration/{any}/edit', 'platform\plugins\src\Http\Controllers\SpokeRegistrationController@edit')->name('SpokeRegistration.edit');

        Route::get('/admin/SpokeRegistration/{any}', 'platform\plugins\src\Http\Controllers\SpokeRegistrationController@show')->name('SpokeRegistration.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('spoke-registration/postdata', [
            'as'   => 'public.spoke-registration.postdata',
            'uses' => 'SpokeRegistrationPublicController@postData',
        ]);
        Route::post('spoke-registration/updatedata/{any}', [
            'as'   => 'public.spoke-registration.updatedata',
            'uses' => 'SpokeRegistrationPublicController@updateData',
        ]);
        Route::get('spoke-registration', [
            'as'   => 'public.spoke-registration.index',
            'uses' => 'SpokeRegistrationPublicController@index',
        ]);
        
    });
});
