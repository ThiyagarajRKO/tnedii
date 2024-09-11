<?php

Route::group(['namespace' => 'Impiger\TrainingTitle\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'training-titles', 'as' => 'training-title.'], function () {
            Route::resource('', 'TrainingTitleController')->parameters(['' => 'training-title']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'TrainingTitleController@deletes',
                'permission' => 'training-title.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'TrainingTitleController@viewdetail',
                'permission' => 'training-title.index',
            ]);
            
            Route::get('subscribe-to-event/{any}', [
                'uses'       => 'TrainingTitleController@subscribeToEvent',
                'permission' => 'training-title.index',
            ]);
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'TrainingTitleController@postImport',
                'permission' => 'training-title.index',
				]);

            Route::post('cruds/get_trainings_list_gallery', [
                'uses'       => 'CrudController@getTrainingsListGallery',
            ]);
            Route::post('cruds/get_trainings', [
                'uses'       => 'CrudController@getTrainings',
            ]);
        });
        Route::group(['prefix' => 'online-training-sessions', 'as' => 'online-training-session.'], function () {
            Route::resource('', 'OnlineTrainingSessionController')->parameters(['' => 'online-training-session']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'OnlineTrainingSessionController@deletes',
                'permission' => 'online-training-session.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'OnlineTrainingSessionController@viewdetail',
                'permission' => 'online-training-session.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'OnlineTrainingSessionController@postImport',
                'permission' => 'online-training-session.index',
				]);
        });
			#{submodule_routes}
        
        Route::get('/admin/TrainingTitle/create', 'platform\plugins\src\Http\Controllers\TrainingTitleController@create')->name('TrainingTitle.create');
 
        Route::get('/admin/TrainingTitle', 'platform\plugins\src\Http\Controllers\TrainingTitleController@index')->name('TrainingTitle.index');

        Route::post('/admin/TrainingTitle', 'platform\plugins\src\Http\Controllers\TrainingTitleController@store')->name('TrainingTitle.store');

        Route::get('/admin/TrainingTitle/{any}/edit', 'platform\plugins\src\Http\Controllers\TrainingTitleController@edit')->name('TrainingTitle.edit');

        Route::get('/admin/TrainingTitle/{any}', 'platform\plugins\src\Http\Controllers\TrainingTitleController@show')->name('TrainingTitle.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('training-title/postdata', [
            'as'   => 'public.training-title.postdata',
            'uses' => 'TrainingTitlePublicController@postData',
        ]);
        Route::post('training-title/updatedata/{any}', [
            'as'   => 'public.training-title.updatedata',
            'uses' => 'TrainingTitlePublicController@updateData',
        ]);
        Route::get('training-title', [
            'as'   => 'public.training-title.index',
            'uses' => 'TrainingTitlePublicController@index',
        ]);
        
    });
});
