<?php

Route::group(['namespace' => 'Impiger\Mentee\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'mentees', 'as' => 'mentee.'], function () {
            Route::resource('', 'MenteeController')->parameters(['' => 'mentee']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'MenteeController@deletes',
                'permission' => 'mentee.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'MenteeController@viewdetail',
                'permission' => 'mentee.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'MenteeController@postImport',
                'permission' => 'mentee.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/Mentee/create', 'platform\plugins\src\Http\Controllers\MenteeController@create')->name('Mentee.create');
 
        Route::get('/admin/Mentee', 'platform\plugins\src\Http\Controllers\MenteeController@index')->name('Mentee.index');

        Route::post('/admin/Mentee', 'platform\plugins\src\Http\Controllers\MenteeController@store')->name('Mentee.store');

        Route::get('/admin/Mentee/{any}/edit', 'platform\plugins\src\Http\Controllers\MenteeController@edit')->name('Mentee.edit');

        Route::get('/admin/Mentee/{any}', 'platform\plugins\src\Http\Controllers\MenteeController@show')->name('Mentee.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('mentee/postdata', [
            'as'   => 'public.mentee.postdata',
            'uses' => 'MenteePublicController@postData',
        ]);
        Route::post('mentee/updatedata/{any}', [
            'as'   => 'public.mentee.updatedata',
            'uses' => 'MenteePublicController@updateData',
        ]);
        Route::get('mentee', [
            'as'   => 'public.mentee.index',
            'uses' => 'MenteePublicController@index',
        ]);
        
    });
});
