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
                        @section('main-table')
                        {!! $dataTable->table(compact('id', 'class'), false) !!}
                        @show
                    </div>
                </div>
            </div>
        </div>
        @include('core/table::modal')
        {{-- @Cutomized Sabari Shankar Parthiban start --}}
        @if(is_plugin_active('retirement-settings'))
        @include('plugins/retirement-settings::modal')
        @endif
        {{-- @Cutomized Sabari Shankar Parthiban end --}}
        @push('footer')
        {!! $dataTable->scripts() !!}
        @endpush

    {{-- @Cutomized Ramesh Esakki - Added the below code from rendering datatable in website --}}
    @if($table->getOption('shortcode'))
        @php
        \Theme::asset()->container('footer')->writeContent('custom', $dataTable->scripts(), ['shortcode-table1-js']);
        @endphp
    </div>
    @endif