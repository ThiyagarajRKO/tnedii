@php $workflow = [];
        if (is_plugin_active('workflows') ) {
            $workflowStates = \CustomWorkflow::getWorkflowAllStates('tnsi_startup');
            foreach ($workflowStates as $key => $value) {
                $workflow[$value] = ucfirst($value);
            }
        }
        $ideas =\Impiger\MasterDetail\Models\MasterDetail::where(['attribute' => 'tnsi_ideas'])->orderBy('name')->pluck('name','id')->toArray();
        @endphp
<div class="wrapper-filter">
    <p>{{ trans('core/table::table.filters') }}</p>
    {{ Form::open(['method' => 'GET', 'class' => 'filter-form ']) }}
    <input type="hidden" class="filter-data-url" value="{{ route('tables.get-filter-input') }}">
    <input type="hidden" name="filter_table_id" class="filter-data-table-id" value="{{ $tableId }}">
    <input type="hidden" name="class" class="filter-data-class" value="{{ $class }}">
    <div class="row filter-item form-filter">

        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="region_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="trainer_id" class="control-label">Region</label><br>
            <div class="form-group ui-select-wrapper wrapper-class">
                <select name="filter_values[]" class="ui-select select-full ui-select region_id" id="region_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    
                    @foreach(\Impiger\MasterDetail\Models\Region::where(['is_enabled' => 1])->orderBy('name')->pluck('name','id')->toArray() as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\Impiger\Crud\Http\Controllers\CrudController::filterByArrayValue($requestFilters, 'region_id' ), 'value' )==$columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;"></span>
            </div>
        </div>
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="hub_institution_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="hub_institution_id" class="control-label">Hub Institution</label><br>
            <div class="form-group ui-select-wrapper wrapper-class">
                <select name="filter_values[]" class="ui-select select-full ui-select hub_institution_id" id="hub_institution_id" data-academic_option="hub_institution_id" data-dependent="spoke_registration_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(\Impiger\HubInstitution\Models\HubInstitution::where(['is_enabled' => 1])->orderBy('name')->pluck('name','id')->toArray() as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\Impiger\Crud\Http\Controllers\CrudController::filterByArrayValue($requestFilters, 'hub_institution_id' ), 'value' )==$columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                    
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;"></span>
            </div>
        </div>
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="spoke_registration_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="spoke_registration_id" class="control-label">College Name</label><br>
            <div class="form-group ui-select-wrapper wrapper-class">
                <select name="filter_values[]" class="ui-select select-full ui-select spoke_registration_id" id="spoke_registration_id" data-academic_option="spoke_registration_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(\Impiger\SpokeRegistration\Models\SpokeRegistration::where(['is_enabled' => 1])->pluck('name','id')->toArray() as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\Impiger\Crud\Http\Controllers\CrudController::filterByArrayValue($requestFilters, 'spoke_registration_id' ), 'value' )==$columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                    
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;"></span>
            </div>
        </div>
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="wf_status">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="status" class="control-label">Status</label><br>
            <div class="form-group ui-select-wrapper wrapper-class">
                <select name="filter_values[]" class="ui-select select-full ui-select status" id="status" >
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach($workflow as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\Impiger\Crud\Http\Controllers\CrudController::filterByArrayValue($requestFilters, 'status' ), 'value' )==$columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                    
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;"></span>
            </div>
        </div>

        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="idea_about">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="idea_about" class="control-label">Idea About</label><br>
            <div class="form-group ui-select-wrapper wrapper-class">
                <select name="filter_values[]" class="ui-select select-full ui-select status" id="idea_about" >
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach($ideas as $columnKey => $column)
                        <option value="{{ $columnKey }}" @if(Arr::get(\Impiger\Crud\Http\Controllers\CrudController::filterByArrayValue($requestFilters, 'idea_about'), 'value') == $columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;"></span>
            </div>
        </div>
        

        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="created_at">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="created_at" class="control-label">Application Submitted Date</label>
            <div class="col-md-10 form-group input-group"  style="padding-left: 0px">
                <input class="form-control datepicker" data-date-format="yyyy-mm-dd" name="filter_values[]" id="created_at" value="<?php echo (request()->get('created_at')) ? request()->get('created_at') : '' ?>" autocomplete="off">
                <span class="input-group-prepend">
                    <button class="btn default" type="button">
                        <i class="fa fa-calendar"></i>
                    </button>
                </span>
                <span class="invalid-feedback" style="display: none;"></span>
            </div>
            
        </div>

    </div>
    <div style="margin-top: 10px;">
        <a href="{{ URL::current() }}" class="btn btn-info @if (!request()->has('filter_table_id')) hidden @endif">{{ trans('core/table::table.reset') }}</a>
        <button type="button" class="btn btn-primary btn-apply">{{ trans('core/table::table.apply') }}</button>
    </div>

    {{ Form::close() }}
</div>