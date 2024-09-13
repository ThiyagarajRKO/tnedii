<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Frontend
// Built-in subdomains

// This will be the route that checks expiration!
Route::post('session/idleTimeCheck', ['uses' => 'App\Http\Controllers\SessionController@sessionIdleTimeCheck']);

/* @customized by Sabari Shankar Parthiban start*/
Route::group(['namespace' => 'App\Utils', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix()], function () {
        Route::get('force-change/{id}', [
            'uses' => 'CrudHelper@forceToChangePassword',
            'permission' => false
        ])->name('access.force_change');
    });
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'cruds', 'as' => 'crud.'], function () {

            /*post actions*/

            Route::post('row_activation/{id}', [
                'uses' => 'CrudHelper@updateRowActivation',
                'permission' => false,
            ]);


            Route::post('subscription/{any}', [
                'uses' => 'CrudHelper@postSubscription',
                'permission' => false,
            ]);


            Route::post('getusers/', [
                'uses' => 'CrudHelper@getUsers',
                'permission' => false,
            ]);
            Route::get('widgets/dashboard-stats', [
                'as' => 'widget.dashboard-stats',
                'uses' => 'CrudHelper@getDashboardWidgetContent',
                'permission' => false,
            ]);
        });
        Route::post('/subscribe-to-event', [
            'uses' => 'CrudHelper@subscribeToEvent',
            'permission' => true,
        ]);

        // Route::post('razorpay-payment', [\App\Http\Controllers\RazorpayPaymentController::class, 'store', 'permission' => true,])->name('razorpay.payment.store');
        // Route::get('razorpay-payment-view', [\App\Http\Controllers\RazorpayPaymentController::class, 'index', 'permission' => true,])->name('razorpay.payment.view');
        // Route::get('razorpay/{any}', [\App\Http\Controllers\RazorpayPaymentController::class, 'razorpay'])->name('razorpay.payment.razorpay');
    });

    Route::post('cruds/get_dependant_dd_options', [
        'uses' => 'CrudHelper@getDependantDropdownOptions',
    ]);
    Route::post('cruds/get_academic_options', [
        'uses' => 'CrudHelper@getAcademicOptions',
    ]);
    Route::post('cruds/get_hubs_by_region', [
        'uses' => 'CrudHelper@getHubsByRegion',
    ]);

    Route::post('cruds/get_entity_options', [
        'uses' => 'CrudHelper@getEntityOptions'
    ]);
    /* Job Custom Action */

    Route::get('form-response', [
        'uses' => 'CrudHelper@formResponse'
    ]);


    Route::post('cruds/getOptions', [
        'uses' => 'CrudHelper@getOptions',
    ]);


    Route::get('/training-program/view-detail/{any}', ['uses' => 'CrudHelper@viewdetail']);


    Route::get('/get-annual-action-plan', ['uses' => 'CrudHelper@getAnnualActionPlan', 'permission' => false]);
    Route::get('/annual-action-plan/view-detail/{any}', ['uses' => 'CrudHelper@viewdetail', 'permission' => false,]);
    Route::get('/get-entrepreneur', ['uses' => 'CrudHelper@getEntrepreneur', 'permission' => false,]);
    Route::get('/get-entrepreneurs-list-by-search', ['uses' => 'CrudHelper@getEntrepreneursListBySearch', 'permission' => false,]);
    Route::get('/get-entrepreneur-by-id', ['uses' => 'CrudHelper@getEntrepreneurById', 'permission' => false,]);

    Route::post('cruds/get_spoke_student', [
        'uses' => 'CrudHelper@getSpokeStudentByEmail',
    ]);
    Route::post('cruds/get_msme_candidate_details', [
        'uses' => 'CrudHelper@getMSMECandidateDetails',
    ]);

    Route::get('/check-already-subscribed-to-event/{any}', ['uses' => 'CrudHelper@checkAlreadySubscribedToEvent', 'permission' => false,]);

    //Razorpay Payment Gateway
    Route::post('razorpay-payment', [\App\Http\Controllers\RazorpayPaymentController::class, 'store', 'permission' => true,])->name('razorpay.payment.store');
    Route::get('razorpay-payment-view', [\App\Http\Controllers\RazorpayPaymentController::class, 'index', 'permission' => true,])->name('razorpay.payment.view');
});
Route::group(['namespace' => 'Impiger\Media\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => 'media', 'as' => 'media.', 'permission' => 'media.index'], function () {
        Route::get('', [
            'as' => 'index',
            'uses' => 'MediaController@getMedia',
        ]);

        Route::get('popup', [
            'as' => 'popup',
            'uses' => 'MediaController@getPopup',
        ]);

        Route::get('list', [
            'as' => 'list',
            'uses' => 'MediaController@getList',
        ]);

        Route::get('breadcrumbs', [
            'as' => 'breadcrumbs',
            'uses' => 'MediaController@getBreadcrumbs',
        ]);

        Route::post('global-actions', [
            'as' => 'global_actions',
            'uses' => 'MediaController@postGlobalActions',
        ]);

        Route::get('download', [
            'as' => 'download',
            'uses' => 'MediaController@download',
        ]);

        Route::group(['prefix' => 'files'], function () {
            Route::post('upload', [
                'as' => 'files.upload',
                'uses' => 'MediaFileController@postUpload',
                'permission' => false
            ]);

            Route::post('upload-from-editor', [
                'as' => 'files.upload.from.editor',
                'uses' => 'MediaFileController@postUploadFromEditor',
                'permission' => false
            ]);
        });

        Route::group(['prefix' => 'folders'], function () {
            Route::post('create', [
                'as' => 'folders.create',
                'uses' => 'MediaFolderController@store',
            ]);
        });
    });
});
Route::group(['namespace' => 'App\Utils', 'middleware' => ['web', 'core']], function () {
    Route::post('subscribe', [
        'as' => 'crud.subscribe',
        'uses' => 'CrudHelper@subscribe',
    ]);

});


Route::get('/training/applicants', [\App\Http\Controllers\TrainingController::class, 'trainingApplicants', 'permission' => true,])->name('training_applicants');

// Route::get('/annual-action-plan/view-detail/{any}', ['uses' => 'CrudHelper@viewdetail']);
/* @customized by Sabari Shankar Parthiban end*/
