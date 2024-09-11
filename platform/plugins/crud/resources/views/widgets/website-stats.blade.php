<div class="col-lg-3 col-md-4 col-sm-6">
    <div class="info-box">
        <div class="info-box-icon bg-yellow-casablanca font-white" style="background-color: {{ $widget->color }}; color: #fff">
            <i class="{{ $widget->icon }}"></i>
        </div>
        <div class="info-box-content">
            <span class="info-box-text" >{{ $widget->title }}</span>
            <span class="info-box-number" data-counter="counterup" data-value="{{ $widget->cnt }}">0</span>
        </div>
    </div>
</div>