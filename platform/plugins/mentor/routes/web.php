<?php

Route::group(['namespace' => 'Impiger\Mentor\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'mentors', 'as' => 'mentor.'], function () {
            Route::resource('', 'MentorController')->parameters(['' => 'mentor']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'MentorController@deletes',
                'permission' => 'mentor.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'MentorController@viewdetail',
                'permission' => 'mentor.index',
            ]);
            
            
            
            Route::get('edit-profile/{any}', [
                'uses'       => 'MentorController@edit',
                'permission' => false,
            ]);

            Route::post('edit-profile/{any}', [
                'uses'       => 'MentorController@update',
                'permission' => false,
            ]);
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'MentorController@postImport',
                'permission' => 'mentor.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/Mentor/create', 'platform\plugins\src\Http\Controllers\MentorController@create')->name('Mentor.create');
 
        Route::get('/admin/Mentor', 'platform\plugins\src\Http\Controllers\MentorController@index')->name('Mentor.index');

        Route::post('/admin/Mentor', 'platform\plugins\src\Http\Controllers\MentorController@store')->name('Mentor.store');

        Route::get('/admin/Mentor/{any}/edit', 'platform\plugins\src\Http\Controllers\MentorController@edit')->name('Mentor.edit');

        Route::get('/admin/Mentor/{any}', 'platform\plugins\src\Http\Controllers\MentorController@show')->name('Mentor.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('mentor/postdata', [
            'as'   => 'public.mentor.postdata',
            'uses' => 'MentorPublicController@postData',
        ]);
        Route::post('mentor/updatedata/{any}', [
            'as'   => 'public.mentor.updatedata',
            'uses' => 'MentorPublicController@updateData',
        ]);
        Route::get('mentor', [
            'as'   => 'public.mentor.index',
            'uses' => 'MentorPublicController@index',
        ]);
        
    });
});
