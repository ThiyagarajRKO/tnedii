<?php

Route::group(['namespace' => 'Impiger\User\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'users', 'as' => 'user.'], function () {
            Route::resource('', 'UserController')->parameters(['' => 'user']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'UserController@deletes',
                'permission' => 'user.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses' => 'UserController@viewdetail',
                'permission' => 'user.index',
            ]);



            Route::get('edit-profile/{any}', [
                'uses' => 'UserController@edit',
                'permission' => false,
            ]);

            Route::post('edit-profile/{any}', [
                'uses' => 'UserController@update',
                'permission' => false,
            ]);
            Route::post('import', [
                'as' => 'import',
                'uses' => 'UserController@postImport',
                'permission' => 'user.index',
            ]);
        });
        Route::group(['prefix' => 'user-addresses', 'as' => 'user-address.'], function () {
            Route::resource('', 'UserAddressController')->parameters(['' => 'user-address']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'UserAddressController@deletes',
                'permission' => 'user-address.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses' => 'UserAddressController@viewdetail',
                'permission' => 'user-address.index',
            ]);

            Route::post('import', [
                'as' => 'import',
                'uses' => 'UserAddressController@postImport',
                'permission' => 'user-address.index',
            ]);
        });
        Route::group(['prefix' => 'education-infos', 'as' => 'education-info.'], function () {
            Route::resource('', 'EducationInfoController')->parameters(['' => 'education-info']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'EducationInfoController@deletes',
                'permission' => 'education-info.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses' => 'EducationInfoController@viewdetail',
                'permission' => 'education-info.index',
            ]);

            Route::post('import', [
                'as' => 'import',
                'uses' => 'EducationInfoController@postImport',
                'permission' => 'education-info.index',
            ]);
        });
        Route::group(['prefix' => 'experience-infos', 'as' => 'experience-info.'], function () {
            Route::resource('', 'ExperienceInfoController')->parameters(['' => 'experience-info']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'ExperienceInfoController@deletes',
                'permission' => 'experience-info.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses' => 'ExperienceInfoController@viewdetail',
                'permission' => 'experience-info.index',
            ]);

            Route::post('import', [
                'as' => 'import',
                'uses' => 'ExperienceInfoController@postImport',
                'permission' => 'experience-info.index',
            ]);
        });
        Route::group(['prefix' => 'next-kin-details', 'as' => 'next-kin-details.'], function () {
            Route::resource('', 'NextKinDetailsController')->parameters(['' => 'next-kin-details']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'NextKinDetailsController@deletes',
                'permission' => 'next-kin-details.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses' => 'NextKinDetailsController@viewdetail',
                'permission' => 'next-kin-details.index',
            ]);

            Route::post('import', [
                'as' => 'import',
                'uses' => 'NextKinDetailsController@postImport',
                'permission' => 'next-kin-details.index',
            ]);
        });
        #{submodule_routes}

        Route::get('/admin/User/create', 'platform\plugins\src\Http\Controllers\UserController@create')->name('User.create');

        Route::get('/admin/User', 'platform\plugins\src\Http\Controllers\UserController@index')->name('User.index');

        Route::post('/admin/User', 'platform\plugins\src\Http\Controllers\UserController@store')->name('User.store');

        Route::get('/admin/User/{any}/edit', 'platform\plugins\src\Http\Controllers\UserController@edit')->name('User.edit');

        Route::get('/admin/User/{any}', 'platform\plugins\src\Http\Controllers\UserController@show')->name('User.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('user/postdata', [
            'as' => 'public.user.postdata',
            'uses' => 'UserPublicController@postData',
        ]);

        Route::post('user/updatedata/{any}', [
            'as' => 'public.user.updatedata',
            'uses' => 'UserPublicController@updateData',
        ]);
        Route::get('user', [
            'as' => 'public.user.index',
            'uses' => 'UserPublicController@index',
        ]);
        Route::post('user-address/postdata', [
            'as' => 'public.user-address.postdata',
            'uses' => 'UserAddressPublicController@postData',
        ]);
        Route::post('user-address/updatedata/{any}', [
            'as' => 'public.user-address.updatedata',
            'uses' => 'UserAddressPublicController@updateData',
        ]);
        Route::get('user-address', [
            'as' => 'public.user-address.index',
            'uses' => 'UserAddressPublicController@index',
        ]);
        Route::post('education-info/postdata', [
            'as' => 'public.education-info.postdata',
            'uses' => 'EducationInfoPublicController@postData',
        ]);
        Route::post('education-info/updatedata/{any}', [
            'as' => 'public.education-info.updatedata',
            'uses' => 'EducationInfoPublicController@updateData',
        ]);
        Route::get('education-info', [
            'as' => 'public.education-info.index',
            'uses' => 'EducationInfoPublicController@index',
        ]);
        Route::post('experience-info/postdata', [
            'as' => 'public.experience-info.postdata',
            'uses' => 'ExperienceInfoPublicController@postData',
        ]);
        Route::post('experience-info/updatedata/{any}', [
            'as' => 'public.experience-info.updatedata',
            'uses' => 'ExperienceInfoPublicController@updateData',
        ]);
        Route::get('experience-info', [
            'as' => 'public.experience-info.index',
            'uses' => 'ExperienceInfoPublicController@index',
        ]);
        Route::post('next-kin-details/postdata', [
            'as' => 'public.next-kin-details.postdata',
            'uses' => 'NextKinDetailsPublicController@postData',
        ]);
        Route::post('next-kin-details/updatedata/{any}', [
            'as' => 'public.next-kin-details.updatedata',
            'uses' => 'NextKinDetailsPublicController@updateData',
        ]);
        Route::get('next-kin-details', [
            'as' => 'public.next-kin-details.index',
            'uses' => 'NextKinDetailsPublicController@index',
        ]);
        #{submodule_public_routes}
    });
});
