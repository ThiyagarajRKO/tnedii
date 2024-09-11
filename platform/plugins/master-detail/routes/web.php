<?php

Route::group(['namespace' => 'Impiger\MasterDetail\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'master-details', 'as' => 'master-detail.'], function () {
            Route::resource('', 'MasterDetailController')->parameters(['' => 'master-detail']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'MasterDetailController@deletes',
                'permission' => 'master-detail.destroy',
            ]);

            Route::get('viewdetail/{any}', [
                'uses'       => 'MasterDetailController@viewdetail',
                'permission' => 'master-detail.index',
            ]);
            
            
            Route::post('import', [
                'as'         => 'import',
                'uses'       => 'MasterDetailController@postImport',
                'permission' => 'master-detail.index',
				]);
        });
        Route::group(['prefix' => 'countries', 'as' => 'country.'], function () {
            Route::resource('', 'CountryController')->parameters(['' => 'country']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CountryController@deletes',
                'permission' => 'country.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'CountryController@viewdetail',
                'permission' => 'country.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'CountryController@postImport',
                'permission' => 'country.index',
				]);
        });
			Route::group(['prefix' => 'districts', 'as' => 'district.'], function () {
            Route::resource('', 'DistrictController')->parameters(['' => 'district']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'DistrictController@deletes',
                'permission' => 'district.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'DistrictController@viewdetail',
                'permission' => 'district.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'DistrictController@postImport',
                'permission' => 'district.index',
				]);
        });
			Route::group(['prefix' => 'counties', 'as' => 'county.'], function () {
            Route::resource('', 'CountyController')->parameters(['' => 'county']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CountyController@deletes',
                'permission' => 'county.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'CountyController@viewdetail',
                'permission' => 'county.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'CountyController@postImport',
                'permission' => 'county.index',
				]);
        });
			Route::group(['prefix' => 'subcounties', 'as' => 'subcounty.'], function () {
            Route::resource('', 'SubcountyController')->parameters(['' => 'subcounty']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'SubcountyController@deletes',
                'permission' => 'subcounty.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'SubcountyController@viewdetail',
                'permission' => 'subcounty.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'SubcountyController@postImport',
                'permission' => 'subcounty.index',
				]);
        });
			Route::group(['prefix' => 'parishes', 'as' => 'parish.'], function () {
            Route::resource('', 'ParishController')->parameters(['' => 'parish']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ParishController@deletes',
                'permission' => 'parish.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'ParishController@viewdetail',
                'permission' => 'parish.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'ParishController@postImport',
                'permission' => 'parish.index',
				]);
        });
			Route::group(['prefix' => 'villages', 'as' => 'village.'], function () {
            Route::resource('', 'VillageController')->parameters(['' => 'village']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'VillageController@deletes',
                'permission' => 'village.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'VillageController@viewdetail',
                'permission' => 'village.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'VillageController@postImport',
                'permission' => 'village.index',
				]);
        });
			
			Route::group(['prefix' => 'branches', 'as' => 'branch.'], function () {
            Route::resource('', 'BranchController')->parameters(['' => 'branch']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'BranchController@deletes',
                'permission' => 'branch.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'BranchController@viewdetail',
                'permission' => 'branch.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'BranchController@postImport',
                'permission' => 'branch.index',
				]);
        });
			Route::group(['prefix' => 'divisions', 'as' => 'division.'], function () {
            Route::resource('', 'DivisionController')->parameters(['' => 'division']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'DivisionController@deletes',
                'permission' => 'division.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'DivisionController@viewdetail',
                'permission' => 'division.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'DivisionController@postImport',
                'permission' => 'division.index',
				]);
        });
			Route::group(['prefix' => 'industries', 'as' => 'industry.'], function () {
            Route::resource('', 'IndustryController')->parameters(['' => 'industry']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'IndustryController@deletes',
                'permission' => 'industry.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'IndustryController@viewdetail',
                'permission' => 'industry.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'IndustryController@postImport',
                'permission' => 'industry.index',
				]);
        });
			Route::group(['prefix' => 'specializations', 'as' => 'specializations.'], function () {
            Route::resource('', 'SpecializationsController')->parameters(['' => 'specializations']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'SpecializationsController@deletes',
                'permission' => 'specializations.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'SpecializationsController@viewdetail',
                'permission' => 'specializations.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'SpecializationsController@postImport',
                'permission' => 'specializations.index',
				]);
        });
			Route::group(['prefix' => 'experiences', 'as' => 'experience.'], function () {
            Route::resource('', 'ExperienceController')->parameters(['' => 'experience']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ExperienceController@deletes',
                'permission' => 'experience.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'ExperienceController@viewdetail',
                'permission' => 'experience.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'ExperienceController@postImport',
                'permission' => 'experience.index',
				]);
        });
			Route::group(['prefix' => 'qualifications', 'as' => 'qualifications.'], function () {
            Route::resource('', 'QualificationsController')->parameters(['' => 'qualifications']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'QualificationsController@deletes',
                'permission' => 'qualifications.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'QualificationsController@viewdetail',
                'permission' => 'qualifications.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'QualificationsController@postImport',
                'permission' => 'qualifications.index',
				]);
        });
			Route::group(['prefix' => 'milestones', 'as' => 'milestone.'], function () {
            Route::resource('', 'MilestoneController')->parameters(['' => 'milestone']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'MilestoneController@deletes',
                'permission' => 'milestone.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'MilestoneController@viewdetail',
                'permission' => 'milestone.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'MilestoneController@postImport',
                'permission' => 'milestone.index',
				]);
        });
			Route::group(['prefix' => 'hub-types', 'as' => 'hub-type.'], function () {
            Route::resource('', 'HubTypeController')->parameters(['' => 'hub-type']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'HubTypeController@deletes',
                'permission' => 'hub-type.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'HubTypeController@viewdetail',
                'permission' => 'hub-type.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'HubTypeController@postImport',
                'permission' => 'hub-type.index',
				]);
        });
			Route::group(['prefix' => 'regions', 'as' => 'region.'], function () {
            Route::resource('', 'RegionController')->parameters(['' => 'region']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'RegionController@deletes',
                'permission' => 'region.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'RegionController@viewdetail',
                'permission' => 'region.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'RegionController@postImport',
                'permission' => 'region.index',
				]);
        });        
                       
			Route::group(['prefix' => 'holidays', 'as' => 'holiday.'], function () {
            Route::resource('', 'HolidayController')->parameters(['' => 'holiday']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'HolidayController@deletes',
                'permission' => 'holiday.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => 'HolidayController@viewdetail',
                'permission' => 'holiday.index',
            ]);
            
             Route::post('import', [
                'as'         => 'import',
                'uses'       => 'HolidayController@postImport',
                'permission' => 'holiday.index',
				]);
        });
			#{submodule_routes}
        
        Route::get('/admin/MasterDetail/create', 'platform\plugins\src\Http\Controllers\MasterDetailController@create')->name('MasterDetail.create');
 
        Route::get('/admin/MasterDetail', 'platform\plugins\src\Http\Controllers\MasterDetailController@index')->name('MasterDetail.index');

        Route::post('/admin/MasterDetail', 'platform\plugins\src\Http\Controllers\MasterDetailController@store')->name('MasterDetail.store');

        Route::get('/admin/MasterDetail/{any}/edit', 'platform\plugins\src\Http\Controllers\MasterDetailController@edit')->name('MasterDetail.edit');

        Route::get('/admin/MasterDetail/{any}', 'platform\plugins\src\Http\Controllers\MasterDetailController@show')->name('MasterDetail.show');
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::post('master-detail/postdata', [
            'as'   => 'public.master-detail.postdata',
            'uses' => 'MasterDetailPublicController@postData',
        ]);
        Route::post('master-detail/updatedata/{any}', [
            'as'   => 'public.master-detail.updatedata',
            'uses' => 'MasterDetailPublicController@updateData',
        ]);
        Route::get('master-detail', [
            'as'   => 'public.master-detail.index',
            'uses' => 'MasterDetailPublicController@index',
        ]);
        Route::post('country/postdata', [
            'as'   => 'public.country.postdata',
            'uses' => 'CountryPublicController@postData',
        ]);
        Route::post('country/updatedata/{any}', [
            'as'   => 'public.country.updatedata',
            'uses' => 'CountryPublicController@updateData',
        ]);
        Route::get('country', [
            'as'   => 'public.country.index',
            'uses' => 'CountryPublicController@index',
        ]);
			Route::post('district/postdata', [
            'as'   => 'public.district.postdata',
            'uses' => 'DistrictPublicController@postData',
        ]);
        Route::post('district/updatedata/{any}', [
            'as'   => 'public.district.updatedata',
            'uses' => 'DistrictPublicController@updateData',
        ]);
        Route::get('district', [
            'as'   => 'public.district.index',
            'uses' => 'DistrictPublicController@index',
        ]);
			Route::post('county/postdata', [
            'as'   => 'public.county.postdata',
            'uses' => 'CountyPublicController@postData',
        ]);
        Route::post('county/updatedata/{any}', [
            'as'   => 'public.county.updatedata',
            'uses' => 'CountyPublicController@updateData',
        ]);
        Route::get('county', [
            'as'   => 'public.county.index',
            'uses' => 'CountyPublicController@index',
        ]);
			Route::post('subcounty/postdata', [
            'as'   => 'public.subcounty.postdata',
            'uses' => 'SubcountyPublicController@postData',
        ]);
        Route::post('subcounty/updatedata/{any}', [
            'as'   => 'public.subcounty.updatedata',
            'uses' => 'SubcountyPublicController@updateData',
        ]);
        Route::get('subcounty', [
            'as'   => 'public.subcounty.index',
            'uses' => 'SubcountyPublicController@index',
        ]);
			Route::post('parish/postdata', [
            'as'   => 'public.parish.postdata',
            'uses' => 'ParishPublicController@postData',
        ]);
        Route::post('parish/updatedata/{any}', [
            'as'   => 'public.parish.updatedata',
            'uses' => 'ParishPublicController@updateData',
        ]);
        Route::get('parish', [
            'as'   => 'public.parish.index',
            'uses' => 'ParishPublicController@index',
        ]);
			Route::post('village/postdata', [
            'as'   => 'public.village.postdata',
            'uses' => 'VillagePublicController@postData',
        ]);
        Route::post('village/updatedata/{any}', [
            'as'   => 'public.village.updatedata',
            'uses' => 'VillagePublicController@updateData',
        ]);
        Route::get('village', [
            'as'   => 'public.village.index',
            'uses' => 'VillagePublicController@index',
        ]);
			
			Route::post('branch/postdata', [
            'as'   => 'public.branch.postdata',
            'uses' => 'BranchPublicController@postData',
        ]);
        Route::post('branch/updatedata/{any}', [
            'as'   => 'public.branch.updatedata',
            'uses' => 'BranchPublicController@updateData',
        ]);
        Route::get('branch', [
            'as'   => 'public.branch.index',
            'uses' => 'BranchPublicController@index',
        ]);
			Route::post('division/postdata', [
            'as'   => 'public.division.postdata',
            'uses' => 'DivisionPublicController@postData',
        ]);
        Route::post('division/updatedata/{any}', [
            'as'   => 'public.division.updatedata',
            'uses' => 'DivisionPublicController@updateData',
        ]);
        Route::get('division', [
            'as'   => 'public.division.index',
            'uses' => 'DivisionPublicController@index',
        ]);
			Route::post('industry/postdata', [
            'as'   => 'public.industry.postdata',
            'uses' => 'IndustryPublicController@postData',
        ]);
        Route::post('industry/updatedata/{any}', [
            'as'   => 'public.industry.updatedata',
            'uses' => 'IndustryPublicController@updateData',
        ]);
        Route::get('industry', [
            'as'   => 'public.industry.index',
            'uses' => 'IndustryPublicController@index',
        ]);
			Route::post('specializations/postdata', [
            'as'   => 'public.specializations.postdata',
            'uses' => 'SpecializationsPublicController@postData',
        ]);
        Route::post('specializations/updatedata/{any}', [
            'as'   => 'public.specializations.updatedata',
            'uses' => 'SpecializationsPublicController@updateData',
        ]);
        Route::get('specializations', [
            'as'   => 'public.specializations.index',
            'uses' => 'SpecializationsPublicController@index',
        ]);
			Route::post('experience/postdata', [
            'as'   => 'public.experience.postdata',
            'uses' => 'ExperiencePublicController@postData',
        ]);
        Route::post('experience/updatedata/{any}', [
            'as'   => 'public.experience.updatedata',
            'uses' => 'ExperiencePublicController@updateData',
        ]);
        Route::get('experience', [
            'as'   => 'public.experience.index',
            'uses' => 'ExperiencePublicController@index',
        ]);
			Route::post('qualifications/postdata', [
            'as'   => 'public.qualifications.postdata',
            'uses' => 'QualificationsPublicController@postData',
        ]);
        Route::post('qualifications/updatedata/{any}', [
            'as'   => 'public.qualifications.updatedata',
            'uses' => 'QualificationsPublicController@updateData',
        ]);
        Route::get('qualifications', [
            'as'   => 'public.qualifications.index',
            'uses' => 'QualificationsPublicController@index',
        ]);
			Route::post('milestone/postdata', [
            'as'   => 'public.milestone.postdata',
            'uses' => 'MilestonePublicController@postData',
        ]);
        Route::post('milestone/updatedata/{any}', [
            'as'   => 'public.milestone.updatedata',
            'uses' => 'MilestonePublicController@updateData',
        ]);
        Route::get('milestone', [
            'as'   => 'public.milestone.index',
            'uses' => 'MilestonePublicController@index',
        ]);
			Route::post('hub-type/postdata', [
            'as'   => 'public.hub-type.postdata',
            'uses' => 'HubTypePublicController@postData',
        ]);
        Route::post('hub-type/updatedata/{any}', [
            'as'   => 'public.hub-type.updatedata',
            'uses' => 'HubTypePublicController@updateData',
        ]);
        Route::get('hub-type', [
            'as'   => 'public.hub-type.index',
            'uses' => 'HubTypePublicController@index',
        ]);
        Route::post('region/postdata', [
            'as'   => 'public.region.postdata',
            'uses' => 'RegionPublicController@postData',
        ]);
        Route::post('region/updatedata/{any}', [
            'as'   => 'public.region.updatedata',
            'uses' => 'RegionPublicController@updateData',
        ]);
        Route::get('region', [
            'as'   => 'public.region.index',
            'uses' => 'RegionPublicController@index',
        ]);        
			Route::post('holiday/postdata', [
            'as'   => 'public.holiday.postdata',
            'uses' => 'HolidayPublicController@postData',
        ]);
        Route::post('holiday/updatedata/{any}', [
            'as'   => 'public.holiday.updatedata',
            'uses' => 'HolidayPublicController@updateData',
        ]);
        Route::get('holiday', [
            'as'   => 'public.holiday.index',
            'uses' => 'HolidayPublicController@index',
        ]);
			#{submodule_public_routes}
    });
});
