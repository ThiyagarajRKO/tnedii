<?php

Route::group(['namespace' => 'Impiger\AuditLog\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::resource('audit-logs', 'AuditLogController', ['names' => 'audit-log'])->only(['index', 'destroy']);

        Route::group(['prefix' => 'audit-logs'], function () {
            Route::get('widgets/activities', [
                'as'         => 'audit-log.widget.activities',
                'uses'       => 'AuditLogController@getWidgetActivities',
                'permission' => 'audit-log.index',
            ]);

            Route::delete('items/destroy', [
                'as'         => 'audit-log.deletes',
                'uses'       => 'AuditLogController@deletes',
                'permission' => 'audit-log.destroy',
            ]);

            Route::get('items/empty', [
                'as'         => 'audit-log.empty',
                'uses'       => 'AuditLogController@deleteAll',
                'permission' => 'audit-log.destroy',
            ]);
			/* @Customized By Ramesh Esakki  - Start -*/
            Route::get('detail', [
                'as'         => 'audit-log.detail',
                'uses'       => 'AuditLogController@detail',
                'permission' => 'audit-log.index'
            ]);
            Route::get('notification', [
                'as'         => 'audit-log.notification',
                'uses'       => 'AuditLogController@notification',
                'permission' => 'audit-log.index'
            ]);
            /* @Customized By Ramesh Esakki  - End -*/
        });
    });
});
