@if($table->getOption('shortcode'))
    <div class="page-content">
    @endif
        <div class="table-wrapper">
            @if ($table->isHasFilter())
            <div class="table-configuration-wrap" @if ($table->hasStaticFilter() || request()->has('filter_table_id')) style="display: block;" @endif>
                <span class="configuration-close-btn btn-show-table-options"><i class="fa fa-times"></i></span>
                {!! $table->renderFilter() !!}
            </div>
            @endif
            <div class="portlet light bordered portlet-no-padding">
                <div class="portlet-title">
                    <div class="caption">
                        <div class="wrapper-action">
                            @if ($actions)
                            <div class="btn-group">
                                <a class="btn btn-secondary dropdown-toggle" href="#" data-toggle="dropdown">{{ trans('core/table::table.bulk_actions') }}
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach ($actions as $action)
                                    <li>
                                        {!! $action !!}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            @if ($table->isHasFilter())
                            <button class="btn btn-primary btn-show-table-options">{{ trans('core/table::table.filters') }}</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-responsive @if ($actions) table-has-actions @endif @if ($table->isHasFilter()) table-has-filter @endif">
                       
                        <table class="table table-striped table-hover dataTable no-footer" id="plugins-view-attendance-table" role="grid">
                                <thead>
                                   
                                            @php $count = 0;
                                            @endphp
                                            @foreach($table->getColumns() as $key => $c)
                                                @if(preg_match("/[a-z]/i", $key))
                                                    @php $count++; @endphp
                                                @endif
                                            @endforeach
                                            
                                            @if($count < count($table->getColumns()))
                                                <tr role="row">
                                                    @foreach($table->getColumns() as $key => $c)
                                                        @if($key == 'registration_number' || $key == 'student_name')
                                                        <th rowspan="2" style="outline: auto !important;outline-color: #f4f4f4 !important;">{{$c['name']}}</th>
                                                        @endif
                                                    @endforeach
                                                    @php
                                                        $begin = strtotime(request()->get('attendance_startdate'));
                                                        $end = strtotime(request()->get('attendance_enddate'));
                                                    @endphp
                                                    @for($i = $begin; $i <= $end; $i = $i + 86400)
                                                        @php
                                                            $colspan = \Impiger\Attendance\Http\Controllers\AttendanceController::getColumnCount(date('Y-m-d', $i)); 
                                                        @endphp
                                                        @if($colspan > 0)
                                                        <th colspan="{{$colspan}}" style="outline: auto !important;outline-color: #f4f4f4 !important;pointer-events: none;">{{date('D d/m', $i)}}</th>
                                                        @else
                                                        <th rowspan="2" style="outline: auto !important;outline-color: #f4f4f4 !important;pointer-events: none;">{{date('D d/m', $i)}}</th>
                                                        @endif
                                                    @endfor
                                                </tr>
                                                <tr role="row">
                                                    @php $count = 0;
                                                    dd($table->getColumns());
                                                    @endphp
                                                    @foreach($table->getColumns() as $key => $c)
                                                        @if(preg_match("/[a-z]/i", $key))
                                                            @php $count++;
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                    @if($count == count($table->getColumns()))
                                                        <th style="display: none; outline: auto !important;outline-color: #f4f4f4 !important;pointer-events: none;">{{$c['name']}}</th>
                                                    @else
                                                        @foreach($table->getColumns() as $key => $c)
                                                            @if(!preg_match("/[a-z]/i", $key))
                                                                <th style="outline: auto !important;outline-color: #f4f4f4 !important;pointer-events: none;">{{$c['name']}}</th>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </tr>
                                            @else
                                                <tr>
                                                    @foreach($table->getColumns() as $key => $c)
                                                        <th style="outline: auto !important;outline-color: #f4f4f4 !important;pointer-events: none;">{{$c['name']}}</th>
                                                    @endforeach
                                                </tr>
                                            @endif
                                    
                                </thead>
                            </table>
                        </div>
                        <!-- @section('main-table')
                        {!! $dataTable->table(compact('id', 'class'), true) !!}
                        @show -->
                    </div>
            </div>
        </div>
        @push('footer')
        {!! $dataTable->scripts() !!}
        <!-- Customized by Harish Muraleetharan start -- MB-9934. -->
        <script>
            var maxExportLength = "{{ env('EXPORT_RECORDS_CONFIG') }}";
        </script>
        <!-- Customized by Harish Muraleetharan end -->
        @endpush
    {{-- @Cutomized Ramesh Esakki - Added the below code from rendering datatable in website --}}
    @if($table->getOption('shortcode'))
        @php
        \Theme::asset()->container('footer')->writeContent('custom', $dataTable->scripts(), ['shortcode-table1-js']);
        @endphp
    </div>
    @endif