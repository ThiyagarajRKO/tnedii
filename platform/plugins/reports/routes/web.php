<?php

Route::group(['namespace' => 'Impiger\Reports\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
            Route::resource('', 'ReportsController')->parameters(['' => 'reports']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ReportsController@deletes',
                'permission' => 'reports.destroy',
            ]);
            
            Route::get('district/abstract', [
                'as'         => 'district_abstract',
                'uses'       => 'ReportsController@getDistrictWiseBeneficariesCount',
                'permission' => 'reports.index',
            ]);
            Route::get('textual', [
                'as'         => 'report_textual',
                'uses'       => 'ReportsController@getDistrictWiseBeneficariesDetails',
                'permission' => 'reports.index',
            ]);
            Route::get('program/abstract', [
                'as'         => 'program_abstract',
                'uses'       => 'ReportsController@getProgramWiseBeneficariesCount',
                'permission' => 'reports.index',
            ]);
            Route::get('community/abstract', [
                'as'         => 'community_abstract',
                'uses'       => 'ReportsController@getCommunityWiseBeneficariesCount',
                'permission' => 'reports.index',
            ]);
            Route::get('religion/abstract', [
                'as'         => 'religion_abstract',
                'uses'       => 'ReportsController@getReligionWiseBeneficariesCount',
                'permission' => 'reports.index',
            ]);
            Route::get('pia/abstract', [
                'as'         => 'pia_abstract',
                'uses'       => 'ReportsController@getPiaWiseBeneficariesCount',
                'permission' => 'reports.index',
            ]);
           
            
        });        
    });

});
