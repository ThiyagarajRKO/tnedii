<?php

Route::group(['namespace' => 'Impiger\KnowledgePartner\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'knowledge-partners', 'as' => 'knowledge-partner.'], function () {

            Route::resource('', 'KnowledgePartnerController')->parameters(['' => 'knowledge-partner']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'KnowledgePartnerController@deletes',
                'permission' => 'knowledge-partner.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'KnowledgePartnerController@viewdetail',
                'permission' => 'knowledge-partner.index',
            ]);
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'KnowledgePartnerController@postImport',
                'permission' => 'knowledge-partner.index',
			]);

        });
        
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('knowledge-partner/send', [
            'as'   => 'public.send.knowledge-partner',
            'uses' => 'PublicKnowledgePartnerController@postSendKnowledgePartner',
        ]);
    });
});
