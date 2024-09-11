<?php

Route::group(['namespace' => 'Impiger\RequestLog\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'request-logs', 'as' => 'request-log.'], function () {
            Route::resource('', 'RequestLogController')
                ->only(['index', 'destroy'])->parameters(['' => 'request-log']);

            Route::get('widgets/request-errors', [
                'as'         => 'widget.request-errors',
                'uses'       => 'RequestLogController@getWidgetRequestErrors',
                'permission' => 'request-log.index',
            ]);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'RequestLogController@deletes',
                'permission' => 'request-log.destroy',
            ]);

            Route::get('items/empty', [
                'as'         => 'empty',
                'uses'       => 'RequestLogController@deleteAll',
                'permission' => 'request-log.destroy',
            ]);
	        /* @Customized By Ramesh Esakki  - Start -*/
            Route::get('detail', [
                'as'         => 'detail',
                'uses'       => 'RequestLogController@detail',
                'permission' => 'request-log.index'
            ]);
            /* @Customized By Ramesh Esakki  - End -*/
        });
    });
});
