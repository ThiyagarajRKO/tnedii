@if (empty($widgetSetting) || $widgetSetting->status == 1)
<div class="{{ $widget->column }} col-12 widget_item" id="{{ $widget->name }}" data-url="{{ $widget->route }}">
    <div class="portlet light bordered portlet-no-padding @if ($widget->hasLoadCallback) widget-load-has-callback @endif">
        <div class="portlet-title">
            <div class="caption">
                <i class="{{ $widget->icon }} font-dark" style="font-weight: 700;"></i>
                <span class="caption-subject font-dark">{{ str_replace(["- Bar Chart","- Pie Chart", "- Table"], ["","",""],$widget->title) }}</span>
            </div>
            @include('core/dashboard::partials.tools', ['settings' => !empty($widgetSetting) ? $widgetSetting->settings : []])
        </div>
        <div class="portlet-body @if ($widget->isEqualHeight) equal-height @endif widget-content {{ $widget->bodyClass }} {{ Arr::get(!empty($widgetSetting) ? $widgetSetting->settings : [], 'state') }}">
            @if(in_array($widget->statsDisplayType, ["pie", "bar"]))
            <div class="kt-widget14__content scroller">
                <div class="kt-widget14__stat text-center">
                    <span class="text-center no-text hidden"> No records found.</span>
                </div>
                <div class="kt-widget14__stat stats_header">
                </div>
                <div class="kt-widget14__stat kt-mt-45">
                    <span class="dashboard-chart-total"></span>
                </div>
                <canvas id="dashboard-chart{{$widget->key}}" style="height: 250px; width: 250px;"></canvas>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<script type="text/javascript">
    if (typeof(dashboardStatsConfig) == "undefined") {
        var dashboardStatsConfig = {};
    }
    var slug = "<?php echo $widget->key; ?>";
    dashboardStatsConfig[slug] = {};
    dashboardStatsConfig[slug]['module'] = "<?php echo $widget->module; ?>";
    dashboardStatsConfig[slug]['subModule'] = "<?php echo $widget->module; ?>";
    dashboardStatsConfig[slug]['statsConfig'] = <?= json_encode($widget->statsConfig); ?>;
    dashboardStatsConfig[slug]['data'] = <?= json_encode($widget->subStats); ?>;
    dashboardStatsConfig[slug]['type'] = "<?php echo $widget->statsDisplayType; ?>";
</script>

<?php
Assets::addScriptsDirectly(['vendor/core/plugins/crud/js/chartJs/dist/Chart.bundle.js']);
Assets::addScriptsDirectly(['vendor/core/plugins/crud/js/dashboard-widgets.js']);
?>