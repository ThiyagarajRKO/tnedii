<?php

Route::group(['namespace' => 'Impiger\Attendance\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'attendances', 'as' => 'attendance.'], function () {
            Route::resource('', 'AttendanceController')->parameters(['' => 'attendance']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AttendanceController@deletes',
                'permission' => 'attendance.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'AttendanceController@viewdetail',
                'permission' => 'attendance.index',
            ]);
            
            Route::get('view', [
                'as'         => 'view',
                'uses'       => 'AttendanceController@viewAttendance',
                'permission' => 'attendance.view',
            ]);

            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'AttendanceController@postImport',
                'permission' => 'attendance.index',
			]);
            
            Route::post('saveattendance', [
                'as'         => 'saveattendance',
                'uses'       => 'AttendanceController@postAttendanceData',
                'permission' => 'attendance.index',
            ]);
            Route::post('get_annual_action_plan_list', [
                'uses'       => 'AttendanceController@getAnnualActionPlanList',
                'permission' => 'attendance.index',
            ]);
            Route::post('get_training_program_list', [
                'uses'       => 'AttendanceController@getTrainingProgramList',
                'permission' => 'attendance.index',
            ]);
            Route::get('get_training_program_schedule', [
                'uses' => 'AttendanceController@getTrainingProgramSchedule',
                'permission' => 'attendance.index',
            ]);

        });
        
			Route::group(['prefix' => 'attendance-remarks', 'as' => 'attendance-remark.'], function () {
            Route::resource('', 'AttendanceRemarkController')->parameters(['' => 'attendance-remark']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AttendanceRemarkController@deletes',
                'permission' => 'attendance-remark.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'AttendanceRemarkController@viewdetail',
                'permission' => 'attendance-remark.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'AttendanceRemarkController@postImport',
                'permission' => 'attendance-remark.index',
				]);
        });
		
			#{submodule_routes}
        
        Route::get('/admin/Attendance/create', 'platform\plugins\src\Http\Controllers\AttendanceController@create')->name('Attendance.create');
 
        Route::get('/admin/Attendance', 'platform\plugins\src\Http\Controllers\AttendanceController@index')->name('Attendance.index');

        Route::post('/admin/Attendance', 'platform\plugins\src\Http\Controllers\AttendanceController@store')->name('Attendance.store');

        Route::get('/admin/Attendance/{any}/edit', 'platform\plugins\src\Http\Controllers\AttendanceController@edit')->name('Attendance.edit');

        Route::get('/admin/Attendance/{any}', 'platform\plugins\src\Http\Controllers\AttendanceController@show')->name('Attendance.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('attendance/postdata', [
            'as'   => 'public.attendance.postdata',
            'uses' => 'AttendancePublicController@postData',
        ]);
        Route::post('attendance/updatedata/{any}', [
            'as'   => 'public.attendance.updatedata',
            'uses' => 'AttendancePublicController@updateData',
        ]);
        Route::get('attendance', [
            'as'   => 'public.attendance.index',
            'uses' => 'AttendancePublicController@index',
        ]);
        
    });
});
