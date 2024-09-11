@if (Arr::get($metaBox, 'before_wrapper'))
    {!! Arr::get($metaBox, 'before_wrapper') !!}
@endif

@if (Arr::get($metaBox, 'wrap', true))
    <div class="widget meta-boxes" {{ Html::attributes(Arr::get($metaBox, 'attributes', [])) }}>
        <div class="widget-title">
            <h4>
                <span> {{ Arr::get($metaBox, 'title') }}</span>
            </h4>
			{{-- @Cutomized Ramesh Esakki - Start --}}
            @if (Arr::get($metaBox, 'searchPlaceholder'))
            <div  style="float: right;">
                <div class="input-group">
                    <input type="text" class="form-control {{Arr::get($metaBox, 'searchClass', true)}}" placeholder="search">
                    <span class="input-group-prepend">
                        <button class="btn default" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            @endif
            {{-- @Cutomized Ramesh Esakki - End --}}

        </div>
        <div class="widget-body">
            {!! Arr::get($metaBox, 'content') !!}
        </div>
    </div>
@else
    {!! Arr::get($metaBox, 'content') !!}
@endif

@if (Arr::get($metaBox, 'after_wrapper'))
    {!! Arr::get($metaBox, 'after_wrapper') !!}
@endif
