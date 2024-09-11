<div class="form-group">
    <label class="control-label">{{ trans('plugins/simple-slider::simple-slider.select_slider') }}</label>
    <select name="key" class="form-control" data-shortcode-attribute="key">
    @if(count($sliders) > 0)
        @foreach($sliders as $slider)
            <option value="{{ $slider->key }}">{{ $slider->name }}</option>
        @endforeach
        @else
        <option selected disabled> No Result Found</option>
        @endif

    </select>
</div>
