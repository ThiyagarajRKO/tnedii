@if (empty($widgetSetting) || $widgetSetting->status == 1)
<div class="col-lg-3 col-md-4 col-sm-6">
    <a class="widget-anchor" href="{{$widget->route}}">
    <div class="info-box">
        <div class="info-box-icon font-white" style="background-color: {{ $widget->color }}; color: #fff">
            <i class="{{ $widget->icon }}"></i>
        </div>
        <div class="info-box-content">
            <span class="info-box-text" >{{ $widget->title }}</span>
            <span class="info-box-number" data-counter="counterup" data-value="{{ $widget->statsTotal }}">0</span>
        </div>
    </div></a>
</div>
@endif