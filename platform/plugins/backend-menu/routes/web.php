<?php

Route::group(['namespace' => 'Impiger\BackendMenu\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'backend-menus', 'as' => 'backend-menu.'], function () {
            Route::resource('', 'BackendMenuController')->parameters(['' => 'backend-menu']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'BackendMenuController@deletes',
                'permission' => 'backend-menu.destroy',
            ]);

            Route::get('savemenu', [
                'as'         => 'getmenu',
                'uses'       => 'BackendMenuController@getMenu',
                'permission' => 'backend-menu.index',
            ]);

            Route::post('savemenu', [
                'uses'       => 'BackendMenuController@saveMenu',
                'permission' => 'backend-menu.index',
            ]);
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'BackendMenuController@postImport',
                'permission' => 'backend-menu.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/BackendMenu/create', 'platform\plugins\src\Http\Controllers\BackendMenuController@create')->name('BackendMenu.create');
 
        Route::get('/admin/BackendMenu', 'platform\plugins\src\Http\Controllers\BackendMenuController@index')->name('BackendMenu.index');

        Route::post('/admin/BackendMenu', 'platform\plugins\src\Http\Controllers\BackendMenuController@store')->name('BackendMenu.store');

        Route::get('/admin/BackendMenu/{any}/edit', 'platform\plugins\src\Http\Controllers\BackendMenuController@edit')->name('BackendMenu.edit');

        Route::get('/admin/BackendMenu/{any}', 'platform\plugins\src\Http\Controllers\BackendMenuController@show')->name('BackendMenu.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('backend-menu/postdata', [
            'as'   => 'public.backend-menu.postdata',
            'uses' => 'BackendMenuPublicController@postData',
        ]);
        Route::post('backend-menu/updatedata/{any}', [
            'as'   => 'public.backend-menu.updatedata',
            'uses' => 'BackendMenuPublicController@updateData',
        ]);
        Route::get('backend-menu', [
            'as'   => 'public.backend-menu.index',
            'uses' => 'BackendMenuPublicController@index',
        ]);
        
    });
});
