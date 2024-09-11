<?php

Route::group(['namespace' => 'Impiger\Workflows\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'workflows', 'as' => 'workflows.'], function () {
            Route::resource('', 'WorkflowsController')->parameters(['' => 'workflows']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'WorkflowsController@deletes',
                'permission' => 'workflows.destroy',
            ]);

            Route::post('apply_workflow', [
                'uses' => 'WorkflowsController@applyWorkflow',
                'permission' => 'workflows.edit',
            ]);

            Route::get('get_workflow_details/{any}', [
                'uses'       => 'WorkflowsController@getWorkflowDetails',
                'permission' => false,
            ]);

            Route::post('update_permission/{any}', [
                'uses'       => 'WorkflowsController@updateWorkflowPermission',
                'permission' => false,
            ]);

            Route::get('update_permission/{any}', [
                'uses'       => 'WorkflowsController@getWorkflowPermission',
                'permission' => 'workflows.create',
            ]);

            Route::post('row_activation/{id}', [
                'uses'       => 'WorkflowsController@updateRowActivation',
                'permission' => 'workflows.create',
            ]);
        });

        Route::group(['prefix' => 'workflow-permissions', 'as' => 'workflow-permission.'], function () {
            Route::resource('', 'WorkflowPermissionController')->parameters(['' => 'workflow-permission']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'WorkflowPermissionController@deletes',
                'permission' => 'workflow-permission.destroy',
            ]);
        });

        Route::group(['prefix' => 'workflow-transitions', 'as' => 'workflow-transition.'], function () {
            Route::resource('', 'WorkflowTransitionController')->parameters(['' => 'workflow-transition']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'WorkflowTransitionController@deletes',
                'permission' => 'workflow-transition.destroy',
            ]);
        });
    });
    
    Route::post('workflows/apply_workflow', [
        'uses'       => 'PublicController@applyWorkflow',
    ]);
    Route::post('workflows/getOptions', [
        'uses'       => 'PublicController@getOptions',
    ]);
});
