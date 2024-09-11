<?php

Route::group(['namespace' => 'Impiger\PasswordCriteria\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'password-criterias', 'as' => 'password-criteria.'], function () {
            Route::resource('', 'PasswordCriteriaController')->parameters(['' => 'password-criteria']);
            
            Route::post('save', [
                'as'         => 'save',
                'uses'       => 'PasswordCriteriaController@saveCriteria',
                'permission' => 'password-criteria.create',
            ]);
            Route::get('get_pwd_criteria', [
                'uses'       => 'PasswordCriteriaController@get_pwd_criteria',
                'permission' => false,
            ]);
            Route::post('idleTimeCheck', [
                'uses'       => 'PasswordCriteriaController@sessionIdleTimeCheck',
            ]);
        });

    });
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::get('get_criteria', [
                'uses'       => 'PublicController@get_criteria_validation',
                'permission' => false,
            ]);
    });

});
