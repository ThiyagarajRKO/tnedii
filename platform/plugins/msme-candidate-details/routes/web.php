<?php

Route::group(['namespace' => 'Impiger\MsmeCandidateDetails\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'msme-candidate-details', 'as' => 'msme-candidate-details.'], function () {
            Route::resource('', 'MsmeCandidateDetailsController')->parameters(['' => 'msme-candidate-details']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'MsmeCandidateDetailsController@deletes',
                'permission' => 'msme-candidate-details.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'MsmeCandidateDetailsController@viewdetail',
                'permission' => 'msme-candidate-details.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'MsmeCandidateDetailsController@postImport',
                'permission' => 'msme-candidate-details.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/MsmeCandidateDetails/create', 'platform\plugins\src\Http\Controllers\MsmeCandidateDetailsController@create')->name('MsmeCandidateDetails.create');
 
        Route::get('/admin/MsmeCandidateDetails', 'platform\plugins\src\Http\Controllers\MsmeCandidateDetailsController@index')->name('MsmeCandidateDetails.index');

        Route::post('/admin/MsmeCandidateDetails', 'platform\plugins\src\Http\Controllers\MsmeCandidateDetailsController@store')->name('MsmeCandidateDetails.store');

        Route::get('/admin/MsmeCandidateDetails/{any}/edit', 'platform\plugins\src\Http\Controllers\MsmeCandidateDetailsController@edit')->name('MsmeCandidateDetails.edit');

        Route::get('/admin/MsmeCandidateDetails/{any}', 'platform\plugins\src\Http\Controllers\MsmeCandidateDetailsController@show')->name('MsmeCandidateDetails.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('msme-candidate-details/postdata', [
            'as'   => 'public.msme-candidate-details.postdata',
            'uses' => 'MsmeCandidateDetailsPublicController@postData',
        ]);
        Route::post('msme-candidate-details/updatedata/{any}', [
            'as'   => 'public.msme-candidate-details.updatedata',
            'uses' => 'MsmeCandidateDetailsPublicController@updateData',
        ]);
        Route::get('msme-candidate-details', [
            'as'   => 'public.msme-candidate-details.index',
            'uses' => 'MsmeCandidateDetailsPublicController@index',
        ]);
        
    });
});
