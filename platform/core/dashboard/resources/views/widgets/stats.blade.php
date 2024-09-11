@if (empty($widgetSetting) || $widgetSetting->status == 1)
    <!-- @Customized by Ramesh Esakki - Start -->
    <div class="col-lg-3 col-md-3 col-sm-6 col-12 widget_item" id="{{ $widget->name }}">
        <a class="portlet dashboard-stat dashboard-stat-v2" style="background-color: {{ $widget->color }}; color: #fff" href="{{ $widget->route }}">
    <!-- @Customized by Ramesh Esakki - End -->    
        <div class="visual portlet-title">
                <i class="{{ $widget->icon }}" style="opacity: .1;"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{ $widget->statsTotal }}">0</span>
                </div>
                <div class="desc"> {{ $widget->title }} </div>
            </div>
        </a>
    </div>
@endif
