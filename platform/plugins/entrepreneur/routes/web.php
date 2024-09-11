<?php

Route::group(['namespace' => 'Impiger\Entrepreneur\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'entrepreneurs', 'as' => 'entrepreneur.'], function () {
            Route::resource('', 'EntrepreneurController')->parameters(['' => 'entrepreneur']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'EntrepreneurController@deletes',
                'permission' => 'entrepreneur.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'EntrepreneurController@viewdetail',
                'permission' => 'entrepreneur.index',
            ]);
            
            
            
            Route::get('edit-profile/{any}', [
                'uses'       => 'EntrepreneurController@edit',
                'permission' => false,
            ]);

            Route::post('edit-profile/{any}', [
                'uses'       => 'EntrepreneurController@update',
                'permission' => false,
            ]);
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'EntrepreneurController@postImport',
                'permission' => 'entrepreneur.index',
				]);
        });
        Route::group(['prefix' => 'trainees', 'as' => 'trainee.'], function () {
            Route::resource('', 'TraineeController')->parameters(['' => 'trainee']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'TraineeController@deletes',
                'permission' => 'trainee.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'TraineeController@viewdetail',
                'permission' => 'trainee.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'TraineeController@postImport',
                'permission' => 'trainee.index',
				]);
            Route::post('generate-certificate', [
                'uses'       => 'TraineeController@generateCertificate',
                //'permission' => 'trainee.index',
				'permission' => false,
            ]);
            Route::post('regenerate-certificate', [
                'uses'       => 'TraineeController@regenerateCertificate',
                //'permission' => 'trainee.index',
				'permission' => false
            ]);

            Route::get('download-certificate/{any}', [
                'uses'       => 'TraineeController@downloadCertificate',
                //'permission' => 'trainee.index',
				'permission' => false
            ]);
        });
			#{submodule_routes}
        
        Route::get('/admin/Entrepreneur/create', 'platform\plugins\src\Http\Controllers\EntrepreneurController@create')->name('Entrepreneur.create');
 
        Route::get('/admin/Entrepreneur', 'platform\plugins\src\Http\Controllers\EntrepreneurController@index')->name('Entrepreneur.index');

        Route::post('/admin/Entrepreneur', 'platform\plugins\src\Http\Controllers\EntrepreneurController@store')->name('Entrepreneur.store');

        Route::get('/admin/Entrepreneur/{any}/edit', 'platform\plugins\src\Http\Controllers\EntrepreneurController@edit')->name('Entrepreneur.edit');

        Route::get('/admin/Entrepreneur/{any}', 'platform\plugins\src\Http\Controllers\EntrepreneurController@show')->name('Entrepreneur.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('entrepreneur/postdata', [
            'as'   => 'public.entrepreneur.postdata',
            'uses' => 'EntrepreneurPublicController@postData',
        ]);
        Route::post('entrepreneur/updatedata/{any}', [
            'as'   => 'public.entrepreneur.updatedata',
            'uses' => 'EntrepreneurPublicController@updateData',
        ]);
        Route::get('entrepreneur', [
            'as'   => 'public.entrepreneur.index',
            'uses' => 'EntrepreneurPublicController@index',
        ]);
        Route::post('trainee/postdata', [
            'as'   => 'public.trainee.postdata',
            'uses' => 'TraineePublicController@postData',
        ]);
        Route::post('trainee/updatedata/{any}', [
            'as'   => 'public.trainee.updatedata',
            'uses' => 'TraineePublicController@updateData',
        ]);
        Route::get('trainee', [
            'as'   => 'public.trainee.index',
            'uses' => 'TraineePublicController@index',
        ]);
			#{submodule_public_routes}
    });
});
