<?php

Route::group(['namespace' => 'Impiger\Crud\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'cruds', 'as' => 'crud.'], function () {
            Route::resource('', 'CrudController')->parameters(['' => 'crud']);
            Route::delete('cruds/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CrudController@destroy',
                'permission' => 'crud.destroy',
            ]);

            Route::get('rebuild/{any}', [
                'as'         => 'rebuild.view',
                'uses'       => 'CrudController@rebuild',
                'permission' => false,
            ]);
            Route::get('build/{any}', [
                'as'         => 'build.view',
                'uses'       => 'CrudController@getBuild',
                'permission' => false,
            ]);
            Route::get('config/{any}', [
                'as'         => 'config.view',
                'uses'       => 'CrudController@getConfig',
                'permission' => false,
            ]);
            Route::get('sql/{any}', [
                'as'         => 'sql.view',
                'uses'       => 'CrudController@getSql',
                'permission' => false,
            ]);
            Route::get('table/{any}', [
                'as'         => 'table.view',
                'uses'       => 'CrudController@getTable',
                'permission' => false,
            ]);
            Route::get('form/{any}', [
                'as'         => 'form.view',
                'uses'       => 'CrudController@getForm',
                'permission' => false,
            ]);
            Route::get('formdesign/{any}', [
                'as'         => 'formdesign.view',
                'uses'       => 'CrudController@getFormdesign',
                'permission' => false,
            ]);
            Route::get('subform/{any}', [
                'as'         => 'subform.view',
                'uses'       => 'CrudController@getSubform',
                'permission' => false,
            ]);
            Route::get('subformremove/{any}', [
                'as'         => 'subformremove.view',
                'uses'       => 'CrudController@getSubformremove',
                'permission' => false,
            ]);
            Route::get('sub/{any}', [
                'as'         => 'sub.view',
                'uses'       => 'CrudController@getSub',
                'permission' => false,
            ]);
            Route::get('stats/{any}', [
                'as'         => 'stats.view',
                'uses'       => 'CrudController@getStats',
                'permission' => false,
            ]);
            Route::get('reports/{any}', [
                'as'         => 'reports.view',
                'uses'       => 'CrudController@getReports',
                'permission' => false,
            ]);

//            Route::get('widgets/dashboard-stats', [
//                'as'         => 'widget.dashboard-stats',
//                'uses'       => 'CrudController@getDashboardWidgetContent',
//                'permission' => false,
//            ]);


            Route::get('removesub', [
                'as'         => 'removesub.view',
                'uses'       => 'CrudController@getRemovesub',
                'permission' => false,
            ]);
            Route::get('removestats', [
                'as'         => 'removestats.view',
                'uses'       => 'CrudController@removeStats',
                'permission' => false,
            ]);
            Route::get('permission/{any}', [
                'as'         => 'permission.view',
                'uses'       => 'CrudController@getPermission',
                'permission' => false,
            ]);
            Route::get('source/{any}', [
                'as'         => 'source.view',
                'uses'       => 'CrudController@getSource',
                'permission' => false,
            ]);
            Route::get('combotable', [
                'as'         => 'combotable.view',
                'uses'       => 'CrudController@getCombotable',
                'permission' => false,
            ]);
            Route::get('combotablefield', [
                'as'         => 'combotablefield.view',
                'uses'       => 'CrudController@getCombotablefield',
                'permission' => false,
            ]);
            Route::get('conn/{any}', [
                'as'         => 'conn.view',
                'uses'       => 'CrudController@getConn',
                'permission' => false,
            ]);
             Route::get('scheduler/{any}', [
                'as'         => 'scheduler.view',
                'uses'       => 'CrudController@getScheduler',
                'permission' => false,
            ]);
             Route::get('emailconfig/{any}', [
                'as'         => 'email.view',
                'uses'       => 'CrudController@getEmailConfig',
                'permission' => false,
            ]);

            /*post actions*/
            Route::post('saveconfig/{any}', [
                'as'         => 'saveconfig.view',
                'uses'       => 'CrudController@postSaveconfig',
                'permission' => false,
            ]);
            Route::post('savesetting/{any}', [
                'as'         => 'savesetting.view',
                'uses'       => 'CrudController@postSavesetting',
                'permission' => false,
            ]);
            Route::post('savetable/{any}', [
                'as'         => 'savetable.view',
                'uses'       => 'CrudController@postSavetable',
                'permission' => false,
            ]);
            Route::post('savesql/{any}', [
                'as'         => 'savesql.view',
                'uses'       => 'CrudController@postSavesql',
                'permission' => false,
            ]);
            Route::post('saveform/{any}', [
                'as'         => 'saveform.view',
                'uses'       => 'CrudController@postSaveform',
                'permission' => false,
            ]);
            Route::post('savesubform/{any}', [
                'as'         => 'savesubform.view',
                'uses'       => 'CrudController@postSavesubform',
                'permission' => false,
            ]);
            Route::post('formdesign/{any}', [
                'as'         => 'formdesign.view',
                'uses'       => 'CrudController@postFormdesign',
                'permission' => false,
            ]);
            Route::post('savesub/{any}', [
                'as'         => 'sub.view',
                'uses'       => 'CrudController@postSavesub',
                'permission' => false,
            ]);
            Route::post('savestats/{any}', [
                'as'         => 'stats.view',
                'uses'       => 'CrudController@postSaveStats',
                'permission' => false,
            ]);
            Route::post('savereports/{any}', [
                'as'         => 'reports.view',
                'uses'       => 'CrudController@postSaveReports',
                'permission' => false,
            ]);
            Route::get('editform/{any}', [
                'as'         => 'editform.view',
                'uses'       => 'CrudController@getEditform',
                'permission' => false,
            ]);
            Route::post('saveformfield/{any}', [
                'as'         => 'saveformfield.view',
                'uses'       => 'CrudController@postSaveformfield',
                'permission' => false,
            ]);
            Route::post('dobuild/{any}', [
                'as'         => 'dobuild.view',
                'uses'       => 'CrudController@postDobuild',
                'permission' => false,
            ]);
            Route::post('package', [
                'as'         => 'package.view',
                'uses'       => 'CrudController@postPackage',
                'permission' => false,
            ]);
            Route::post('dopackage', [
                'as'         => 'dopackage.view',
                'uses'       => 'CrudController@postDoPackage',
                'permission' => false,
            ]);
            Route::post('conn/{any}', [
                'as'         => 'dopackage.view',
                'uses'       => 'CrudController@postConn',
                'permission' => false,
            ]);

            Route::post('savescheduler/{any}', [
                'as'         => 'scheduler.view',
                'uses'       => 'CrudController@postSaveScheduler',
                'permission' => false,
            ]);
            Route::post('saveemailconfig/{any}', [
                'as'         => 'email.view',
                'uses'       => 'CrudController@postSaveEmailConfig',
                'permission' => false,
            ]);


        });
    });
});



// Route::group(['namespace' => 'Impiger\Media\Http\Controllers', 'middleware' => ['web', 'core']], function () {

//         Route::group(['prefix' => 'media', 'as' => 'media.', 'permission' => 'media.index'], function () {
//             Route::get('', [
//                 'as'   => 'index',
//                 'uses' => 'MediaController@getMedia',
//             ]);

//             Route::get('popup', [
//                 'as'   => 'popup',
//                 'uses' => 'MediaController@getPopup',
//             ]);

//             Route::get('list', [
//                 'as'   => 'list',
//                 'uses' => 'MediaController@getList',
//             ]);

//             Route::get('breadcrumbs', [
//                 'as'   => 'breadcrumbs',
//                 'uses' => 'MediaController@getBreadcrumbs',
//             ]);

//             Route::post('global-actions', [
//                 'as'   => 'global_actions',
//                 'uses' => 'MediaController@postGlobalActions',
//             ]);

//             Route::get('download', [
//                 'as'   => 'download',
//                 'uses' => 'MediaController@download',
//             ]);

//             Route::group(['prefix' => 'files'], function () {
//                 Route::post('upload', [
//                     'as'   => 'files.upload',
//                     'uses' => 'MediaFileController@postUpload',
//                 ]);

//                 Route::post('upload-from-editor', [
//                     'as'   => 'files.upload.from.editor',
//                     'uses' => 'MediaFileController@postUploadFromEditor',
//                 ]);
//             });

//             Route::group(['prefix' => 'folders'], function () {
//                 Route::post('create', [
//                     'as'   => 'folders.create',
//                     'uses' => 'MediaFolderController@store',
//                 ]);
//             });
//     });
// });






