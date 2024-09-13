<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::POST('/ivp/register', ['as' => 'public.user.postdata', 'uses' => 'Impiger\User\Http\Controllers\UserPublicController@registerIVP']);
Route::post('/ivp/register', [\Impiger\User\Http\Controllers\UserPublicController::class, 'registerIVP'])
    ->name('public.user.postdata');

Route::get('/training/title', [\Impiger\TrainingTitle\Http\Controllers\TrainingTitlePublicController::class, 'getTrainingTitles'])
    ->name('public.training-title.trainingTitleData');


Route::get('/district', [\App\Utils\CrudHelper::class, 'getDistricts']);

Route::get('/traning/applicants', [\App\Http\Controllers\TrainingController::class, 'getTrainingApplicants'])->name('get_training_applicants');

Route::post('/traning/applicants', [\App\Http\Controllers\TrainingController::class, 'addTrainingApplicant'])->name('add_training_applicant');