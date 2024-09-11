@if (empty($widgetSetting) || $widgetSetting->status == 1)
<div class="col-lg-3 col-md-3 col-sm-6 col-12 widget_item" id="{{ $widget->name }}">
    <div class="dashboard-stat portlet" style="background-color: {{ $widget->color }}; color: #fff">
        <a class="dashboard-stat dashboard-stat-v2 " href="{{ $widget->route }}">
            <div class="kt-portlet__body kt-padding-5">
                <div class="kt-iconbox__body kt-pb-10">
                    <div class="visual portlet-title">
                        <i class="{{ $widget->icon }}" style="opacity: .1;"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span data-counter="counterup" data-value="{{ $widget->statsTotal }}">0</span>
                        </div>
                        <div class="desc"> {{ $widget->title }} </div>
                    </div>
                </div>
            </div>
        </a>

        <?php
        $count = (Arr::has($widget->subStats, 0) && count($widget->subStats)) ? count($widget->subStats) : 0;
        if ($count > 0) { ?>
            <div class="dashboard-sub-stats">
                <a href="">
                </a>
                <div class="row">
                    <?php
                    $blockConfig = [1 => 'col-md-12', 2 => 'col-md-6', 3 => 'col-md-4', 4 => 'col-md-4'];
                    $cls = (Arr::has($blockConfig, $count)) ? Arr::get($blockConfig, $count) : "";
                    foreach ($widget->subStats as $k => $val) {
                        $borderCls = ($k > 0) ? "dashboard-Lborder" : "";
                    ?>
                        <div class="text-center {{$cls}} {{$borderCls}}">
                            <a href="{{(Arr::get($val, 'route')) ? Arr::get($val, 'route') : $widget->route}}">
                                <div class="card-type">{{Arr::get($val, 'title')}}</div>
                                <div class="institute-card-count" data-counter="counterup" data-value="{{ Arr::get($val, 'cnt') }}">0</div>
                            </a>
                        </div>

                    <?php
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

    </div>
</div>
@endif