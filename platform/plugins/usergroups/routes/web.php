<?php

Route::group(['namespace' => 'Impiger\Usergroups\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => config('core.base.general.admin_dir'), 'middleware' => 'auth'], function () {
    Route::group(['prefix' => 'system'], function () {

        Route::group(['prefix' => 'usergroups', 'as' => 'usergroups.'], function () {
            Route::resource('', 'UsergroupsController')->parameters(['' => 'usergroups']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'UsergroupsController@deletes',
                'permission' => 'usergroups.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'UsergroupsController@viewdetail',
                'permission' => 'usergroups.index',
            ]);
        });
          
    });
    /*
     * @date 23-04-2021
     * @Customized sabari shankar parthiban
     */
    Route::group(['prefix' => 'crud'], function () {
         Route::group(['prefix' => 'usergroupsentity', 'as' => 'usergroupsentity.'], function () {
            Route::resource('', 'UsergroupEntityController')
                ->parameters(['' => 'usergroups']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'UsergroupEntityController@deletes',
                'permission' => 'usergroupsentity.destroy',
            ]);
        });
    });
   
    });

});
