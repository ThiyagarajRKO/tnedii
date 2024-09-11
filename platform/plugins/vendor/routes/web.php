<?php

Route::group(['namespace' => 'Impiger\Vendor\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'vendors', 'as' => 'vendor.'], function () {
            Route::resource('', 'VendorController')->parameters(['' => 'vendor']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'VendorController@deletes',
                'permission' => 'vendor.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'VendorController@viewdetail',
                'permission' => 'vendor.index',
            ]);
            
            
            
            Route::get('edit-profile/{any}', [
                'uses'       => 'VendorController@edit',
                'permission' => false,
            ]);

            Route::post('edit-profile/{any}', [
                'uses'       => 'VendorController@update',
                'permission' => false,
            ]);
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'VendorController@postImport',
                'permission' => 'vendor.index',
				]);
        });
        #{submodule_routes}
        
        Route::get('/admin/Vendor/create', 'platform\plugins\src\Http\Controllers\VendorController@create')->name('Vendor.create');
 
        Route::get('/admin/Vendor', 'platform\plugins\src\Http\Controllers\VendorController@index')->name('Vendor.index');

        Route::post('/admin/Vendor', 'platform\plugins\src\Http\Controllers\VendorController@store')->name('Vendor.store');

        Route::get('/admin/Vendor/{any}/edit', 'platform\plugins\src\Http\Controllers\VendorController@edit')->name('Vendor.edit');

        Route::get('/admin/Vendor/{any}', 'platform\plugins\src\Http\Controllers\VendorController@show')->name('Vendor.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('vendor/postdata', [
            'as'   => 'public.vendor.postdata',
            'uses' => 'VendorPublicController@postData',
        ]);
        Route::post('vendor/updatedata/{any}', [
            'as'   => 'public.vendor.updatedata',
            'uses' => 'VendorPublicController@updateData',
        ]);
        Route::get('vendor', [
            'as'   => 'public.vendor.index',
            'uses' => 'VendorPublicController@index',
        ]);
        
    });
});
