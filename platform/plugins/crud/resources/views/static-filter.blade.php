        <?php $dbTablePrefix = ($dbTable) ? $dbTable."." : ""; ?>
        @if(Arr::has($columns, $dbTablePrefix.'academic_year_id'))

        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="{{$tableAlias}}.academic_year_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="academic_year_id" class="control-label @if(Arr::get($columns[$dbTablePrefix.'academic_year_id'], 'required')) required @endif">Academic Year</label>
            <div class="form-group ui-select-wrapper">
                <select name="filter_values[]" class="ui-select select-full ui-select academic_year_id" data-academic_option="academic_year_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(Arr::get($columns[$dbTablePrefix.'academic_year_id'], 'choices') as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'academic_year_id'), 'value') == $columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required.</span>
            </div>
        </div>
        @endif

        @php
        $user=Auth::user();
        $showFilters=TRUE;
        if($user){
            $userRoles = $user->roles;
        $roleSlugs = $userRoles->pluck('slug')->toArray();
        }
        @endphp
        @if($showFilters)
        @if(Arr::has($columns, $dbTablePrefix.'institute_id'))
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="{{$tableAlias}}.institute_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="academic_year_id" class="control-label  @if(Arr::get($columns[$dbTablePrefix.'institute_id'], 'required')) required @endif  ">Institution</label>
            <div class="form-group ui-select-wrapper">
                <select name="filter_values[]" class="ui-select select-full ui-select institute_id" data-academic_option="institute_id" data-dependent="department_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(Arr::get($columns[$dbTablePrefix.'institute_id'], 'choices') as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'institute_id'), 'value') == $columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required.</span>
            </div>
        </div>
        @endif

        @if(Arr::has($columns, $dbTablePrefix.'department_id'))
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="{{$tableAlias}}.department_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="academic_year_id" class="control-label  @if(Arr::get($columns[$dbTablePrefix.'department_id'], 'required')) required @endif  ">Department</label>
            <div class="form-group ui-select-wrapper">
                <select name="filter_values[]" class="ui-select select-full ui-select department_id" data-academic_option="department_id" data-dependent="training_program_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(Arr::get($columns[$dbTablePrefix.'department_id'], 'choices') as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'department_id'), 'value') ==  $columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required.</span>
            </div>
        </div>
        @endif

        @if(Arr::has($columns, $dbTablePrefix.'program_type_id'))
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="{{$tableAlias}}.program_type_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="academic_year_id" class="control-label  @if(Arr::get($columns[$dbTablePrefix.'program_type_id'], 'required')) required @endif  ">Program Type</label>
            <div class="form-group ui-select-wrapper">
                <select name="filter_values[]" class="ui-select select-full ui-select program_type_id" data-academic_option="program_type_id" data-dependent="training_program_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(Arr::get($columns[$dbTablePrefix.'program_type_id'], 'choices') as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'program_type_id'), 'value') == $columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required.</span>
            </div>
        </div>
        @endif

        @if(Arr::has($columns, $dbTablePrefix.'training_program_id'))
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="{{$tableAlias}}.training_program_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="training_program_id" class="control-label  @if(Arr::get($columns[$dbTablePrefix.'training_program_id'], 'required')) required @endif  ">Training Program</label>
            <div class="form-group ui-select-wrapper">
                <select name="filter_values[]" class="ui-select select-full ui-select training_program_id" data-academic_option="training_program_id" data-dependent="intake_id">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(Arr::get($columns[$dbTablePrefix.'training_program_id'], 'choices') as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'training_program_id'), 'value') == $columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required.</span>
            </div>
        </div>
        @endif

        @if(Arr::has($columns, $dbTablePrefix.'intake_id'))
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="{{$tableAlias}}.intake_id">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="intake_id" class="control-label  @if(Arr::get($columns[$dbTablePrefix.'intake_id'], 'required')) required @endif  ">Intake</label>
            <div class="form-group ui-select-wrapper">
                <select name="filter_values[]" class="ui-select select-full ui-select intake_id" data-academic_option="intake_id" data-dependent="term">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(Arr::get($columns[$dbTablePrefix.'intake_id'], 'choices') as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'intake_id'), 'value') == $columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required.</span>
            </div>
        </div>
        @endif
        @endif
        @if(Arr::has($columns, $dbTablePrefix.'term'))
        <div class="form-group col-md-3">
            <input type="hidden" name="filter_columns[]" value="{{$tableAlias}}.term">
            <input type="hidden" name="filter_operators[]" value="=">
            <label for="term" class="control-label  @if(Arr::get($columns[$dbTablePrefix.'term'], 'required')) required @endif  ">Term</label>
            <div class="form-group ui-select-wrapper">
                <select name="filter_values[]" class="ui-select select-full ui-select term" data-academic_option="term">
                    <option value="">{{ trans('plugins/crud::crud.select') }}</option>
                    @foreach(Arr::get($columns[$dbTablePrefix.'term'], 'choices') as $columnKey => $column)
                    <option value="{{ $columnKey }}" @if(Arr::get(\App\Utils\CrudHelper::filterByArrayValue($requestFilters, 'term'), 'value') == $columnKey) selected="selected" @endif>{{ $column }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                <span class="invalid-feedback" style="display: none;">This field is required.</span>
            </div>
        </div>
        @endif






