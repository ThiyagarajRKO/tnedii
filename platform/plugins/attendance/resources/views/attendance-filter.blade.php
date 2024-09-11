<div class="wrapper-filter">
    <p>{{ trans('core/table::table.filters') }}</p>
    {{ Form::open(['method' => 'GET', 'class' => 'filter-form view-attendance-form']) }}
    <input type="hidden" class="filter-data-url" value="{{ route('tables.get-filter-input') }}">
    <input type="hidden" name="filter_table_id" class="filter-data-table-id" value="{{ $tableId }}">
    <input type="hidden" name="class" class="filter-data-class" value="{{ $class }}">
    <div class="row filter-item form-filter">
        
        
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="financial_year_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="financial_year_id" class="control-label required">Financial Year</label>
            <div class="form-group ui-select-wrapper wrapper-class" style="display:block;">
                <select name="filter_values[]" class="ui-select select-full ui-select financial_year_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(\Impiger\FinancialYear\Models\FinancialYear::pluck('session_year','id')->toArray() as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'financial_year_id' ), 'value' )==$columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required</span>
            </div>
        </div>

        @php 
        if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'financial_year_id'), 'value')){
            $actionPlanList = \Impiger\AnnualActionPlan\Models\AnnualActionPlan::where('financial_year_id',Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'financial_year_id'), 'value' ))->pluck('name','id')->toArray();
        } else {
            $actionPlanList = \Impiger\AnnualActionPlan\Models\AnnualActionPlan::pluck('name','id')->toArray();
        }
        @endphp

        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="annual_action_plan_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="annual_action_plan_id" class="control-label required">Training/Workshop/Program Name</label>
            <div class="form-group ui-select-wrapper wrapper-class" style="display:block;">
                <select name="filter_values[]" class="ui-select select-full ui-select annual_action_plan_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>                    
                    @foreach($actionPlanList as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'annual_action_plan_id'), 'value' )==$columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required</span>
            </div>
        </div>

        @php 
        if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'annual_action_plan_id'), 'value')){
            $trainingTitleList = \Impiger\TrainingTitle\Models\TrainingTitle::select(DB::raw("CONCAT(name,' - ',code) AS code"),'id')->where('annual_action_plan_id',Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'annual_action_plan_id'), 'value'))->pluck('code','id')->toArray();
        } else {
            $trainingTitleList = \Impiger\TrainingTitle\Models\TrainingTitle::select(DB::raw("CONCAT(name,' - ',code) AS code"),'id')->pluck('code','id')->toArray();
        }
        
        @endphp

        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="training_title_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="training_title_id" class="control-label required">Training Name & Code</label>
            <div class="form-group ui-select-wrapper wrapper-class" style="display:block;">
                <select name="filter_values[]" class="ui-select select-full ui-select training_title_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach($trainingTitleList as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'training_title_id'), 'value' )==$columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required</span>
            </div>
        </div>

        @if($tableId == "plugins-attendance-table")
        
        <div class="form-group col-md-3">
            <label for="attendance_date" class="control-label required">Attendance Date</label>
            <div class="col-md-10 form-group input-group" style="padding-left: 0px">
                <input class="form-control datepicker" id="attendance_date" data-date-end-date='0d' data-date-format="yyyy-mm-dd" name="attendance_date" value="<?php echo (request()->get('attendance_date')) ? request()->get('attendance_date') :  date('Y-m-d'); ?>" autocomplete="off">
                <span class="input-group-prepend">
                    <button class="btn default" type="button">
                        <i class="fa fa-calendar"></i>
                    </button>
                </span>
                <span class="invalid-feedback" style="display: none;">This field is required</span>
            </div>
            
        </div>

        @endif

        @if($tableId == "plugins-view-attendance-table")

        <div class="form-group col-md-3">
            <label for="attendance_startdate" class="control-label">From Date</label>
            <div class="col-md-10 form-group input-group"  style="padding-left: 0px">
                <input class="form-control datepicker" data-date-format="yyyy-mm-dd" name="attendance_startdate" id="attendance_startdate" value="<?php echo (request()->get('attendance_startdate')) ? request()->get('attendance_startdate') : date('Y-m-d', strtotime("-6 days")); ?>" autocomplete="off">
                <span class="input-group-prepend">
                    <button class="btn default" type="button">
                        <i class="fa fa-calendar"></i>
                    </button>
                </span>
                <span class="invalid-feedback" style="display: none;">Start date and End date should not exceed more than a week.</span>
            </div>
            
        </div>

        <div class="form-group col-md-3">
            <label for="attendance_enddate" class="control-label">To Date</label>
            <div class="col-md-10 form-group input-group"  style="padding-left: 0px">
                <input class="form-control datepicker" data-date-format="yyyy-mm-dd" data-date-end-date='0d' name="attendance_enddate" id="attendance_enddate" value="<?php echo (request()->get('attendance_enddate')) ? request()->get('attendance_enddate') :  date('Y-m-d'); ?>" autocomplete="off">
                <span class="input-group-prepend">
                    <button class="btn default" type="button">
                        <i class="fa fa-calendar"></i>
                    </button>
                </span>
                <span class="invalid-feedback" style="display: none;">Must be greater than or equal to From Date.</span>
            </div>
            
        </div>
        
        @endif

    </div>
    <div style="margin-top: 10px;">
        <a href="{{ URL::current() }}" class="btn btn-info @if (!request()->has('filter_table_id')) hidden @endif">{{ trans('core/table::table.reset') }}</a>
        <button type="submit" class="btn btn-primary btn-apply">{{ trans('core/table::table.apply') }}</button>
    </div>

    {{ Form::close() }}
</div>