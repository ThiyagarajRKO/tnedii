<?php

Route::group(['namespace' => 'Impiger\AnnualActionPlan\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'annual-action-plans', 'as' => 'annual-action-plan.'], function () {
            Route::resource('', 'AnnualActionPlanController')->parameters(['' => 'annual-action-plan']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AnnualActionPlanController@deletes',
                'permission' => 'annual-action-plan.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'AnnualActionPlanController@viewdetail',
                'permission' => 'annual-action-plan.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'AnnualActionPlanController@postImport',
                'permission' => 'annual-action-plan.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/AnnualActionPlan/create', 'platform\plugins\src\Http\Controllers\AnnualActionPlanController@create')->name('AnnualActionPlan.create');
 
        Route::get('/admin/AnnualActionPlan', 'platform\plugins\src\Http\Controllers\AnnualActionPlanController@index')->name('AnnualActionPlan.index');

        Route::post('/admin/AnnualActionPlan', 'platform\plugins\src\Http\Controllers\AnnualActionPlanController@store')->name('AnnualActionPlan.store');

        Route::get('/admin/AnnualActionPlan/{any}/edit', 'platform\plugins\src\Http\Controllers\AnnualActionPlanController@edit')->name('AnnualActionPlan.edit');

        Route::get('/admin/AnnualActionPlan/{any}', 'platform\plugins\src\Http\Controllers\AnnualActionPlanController@show')->name('AnnualActionPlan.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('annual-action-plan/postdata', [
            'as'   => 'public.annual-action-plan.postdata',
            'uses' => 'AnnualActionPlanPublicController@postData',
        ]);
        Route::post('annual-action-plan/updatedata/{any}', [
            'as'   => 'public.annual-action-plan.updatedata',
            'uses' => 'AnnualActionPlanPublicController@updateData',
        ]);
        Route::get('annual-action-plan', [
            'as'   => 'public.annual-action-plan.index',
            'uses' => 'AnnualActionPlanPublicController@index',
        ]);
        
    });
});
