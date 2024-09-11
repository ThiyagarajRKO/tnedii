<?php

Route::group(['namespace' => 'Impiger\InnovationVoucherProgram\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'innovation-voucher-programs', 'as' => 'innovation-voucher-program.'], function () {
            Route::resource('', 'InnovationVoucherProgramController')->parameters(['' => 'innovation-voucher-program']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'InnovationVoucherProgramController@deletes',
                'permission' => 'innovation-voucher-program.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'InnovationVoucherProgramController@viewdetail',
                'permission' => 'innovation-voucher-program.index',
            ]);
            
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'InnovationVoucherProgramController@postImport',
                'permission' => 'innovation-voucher-program.index',
				]);
        });
        Route::group(['prefix' => 'ivp-company-details', 'as' => 'ivp-company-details.'], function () {
            Route::resource('', 'IvpCompanyDetailsController')->parameters(['' => 'ivp-company-details']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'IvpCompanyDetailsController@deletes',
                'permission' => 'ivp-company-details.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'IvpCompanyDetailsController@viewdetail',
                'permission' => 'ivp-company-details.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'IvpCompanyDetailsController@postImport',
                'permission' => 'ivp-company-details.index',
				]);
        });
			Route::group(['prefix' => 'ivp-knowledge-partners', 'as' => 'ivp-knowledge-partner.'], function () {
            Route::resource('', 'IvpKnowledgePartnerController')->parameters(['' => 'ivp-knowledge-partner']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'IvpKnowledgePartnerController@deletes',
                'permission' => 'ivp-knowledge-partner.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'IvpKnowledgePartnerController@viewdetail',
                'permission' => 'ivp-knowledge-partner.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'IvpKnowledgePartnerController@postImport',
                'permission' => 'ivp-knowledge-partner.index',
				]);
        });
			#{submodule_routes}
        
        Route::get('/admin/InnovationVoucherProgram/create', 'platform\plugins\src\Http\Controllers\InnovationVoucherProgramController@create')->name('InnovationVoucherProgram.create');
 
        Route::get('/admin/InnovationVoucherProgram', 'platform\plugins\src\Http\Controllers\InnovationVoucherProgramController@index')->name('InnovationVoucherProgram.index');

        Route::post('/admin/InnovationVoucherProgram', 'platform\plugins\src\Http\Controllers\InnovationVoucherProgramController@store')->name('InnovationVoucherProgram.store');

        Route::get('/admin/InnovationVoucherProgram/{any}/edit', 'platform\plugins\src\Http\Controllers\InnovationVoucherProgramController@edit')->name('InnovationVoucherProgram.edit');

        Route::get('/admin/InnovationVoucherProgram/{any}', 'platform\plugins\src\Http\Controllers\InnovationVoucherProgramController@show')->name('InnovationVoucherProgram.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('innovation-voucher-program/postdata', [
            'as'   => 'public.innovation-voucher-program.postdata',
            'uses' => 'InnovationVoucherProgramPublicController@postData',
        ]);
        Route::post('innovation-voucher-program/updatedata/{any}', [
            'as'   => 'public.innovation-voucher-program.updatedata',
            'uses' => 'InnovationVoucherProgramPublicController@updateData',
        ]);
        Route::get('innovation-voucher-program', [
            'as'   => 'public.innovation-voucher-program.index',
            'uses' => 'InnovationVoucherProgramPublicController@index',
        ]);
        Route::post('ivp-company-details/postdata', [
            'as'   => 'public.ivp-company-details.postdata',
            'uses' => 'IvpCompanyDetailsPublicController@postData',
        ]);
        Route::post('ivp-company-details/updatedata/{any}', [
            'as'   => 'public.ivp-company-details.updatedata',
            'uses' => 'IvpCompanyDetailsPublicController@updateData',
        ]);
        Route::get('ivp-company-details', [
            'as'   => 'public.ivp-company-details.index',
            'uses' => 'IvpCompanyDetailsPublicController@index',
        ]);
			Route::post('ivp-knowledge-partner/postdata', [
            'as'   => 'public.ivp-knowledge-partner.postdata',
            'uses' => 'IvpKnowledgePartnerPublicController@postData',
        ]);
        Route::post('ivp-knowledge-partner/updatedata/{any}', [
            'as'   => 'public.ivp-knowledge-partner.updatedata',
            'uses' => 'IvpKnowledgePartnerPublicController@updateData',
        ]);
        Route::get('ivp-knowledge-partner', [
            'as'   => 'public.ivp-knowledge-partner.index',
            'uses' => 'IvpKnowledgePartnerPublicController@index',
        ]);
			#{submodule_public_routes}
    });
});
