@extends('core/base::layouts.master')
@section('content')
    @if ($showStart)
        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
    @endif

    @php
        /* @Customized By Ramesh Esakki  - Start -*/
        $isViewPage = Arr::get($form->getFormOptions(), 'isView');
        $colGridCls = ($isViewPage) ? "col-md-12" : "col-md-9";
		/* @Customized By Ramesh Esakki  - End -*/
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel())
    @endphp
    <div class="row">
    {{-- @Cutomized Ramesh Esakki - Added class dynamically --}}
        <div class="col-md-12">
        {{-- @Cutomized Ramesh Esakki - End --}}
            @if ($showFields && $form->hasMainFields())
                <div class="main-form">
                    <div class="{{ $form->getWrapperClass() }}">
                        @foreach ($fields as $key => $field)
                            @if ($field->getName() == $form->getBreakFieldPoint())
                                @break
                            @else
                                @unset($fields[$key])
                            @endif
                            @if (!in_array($field->getName(), $exclude))
                                {!! $field->render() !!}
                                @if ($field->getName() == 'name' && defined('BASE_FILTER_SLUG_AREA'))
                                    {!! apply_filters(BASE_FILTER_SLUG_AREA, $form->getModel()) !!}
                                @endif
                            @endif
                        @endforeach
                        <div class="clearfix"></div>
                    </div>
                </div>
            @endif

            @foreach ($form->getMetaBoxes() as $key => $metaBox)
                {!! $form->getMetaBox($key) !!}
            @endforeach

            @php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp
        
            {!! $form->getActionButtons() !!}
            @php do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel()) @endphp

            @foreach ($fields as $field)
                @if (!in_array($field->getName(), $exclude))
                    <div class="widget meta-boxes">
                        <div class="widget-title">
                            <h4>{!! Form::customLabel($field->getName(), $field->getOption('label'), $field->getOption('label_attr')) !!}</h4>
                        </div>
                        <div class="widget-body">
                            {!! $field->render([], in_array($field->getType(), ['radio', 'checkbox'])) !!}
                        </div>
                    </div>
                @endif
            @endforeach

            @php do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel()) @endphp
        </div>
       
    </div>

    @if ($showEnd)
        {!! Form::close() !!}
    @endif

    @yield('form_end')
@stop

@if ($form->getValidatorClass())
    @if ($form->isUseInlineJs())
        {!! Assets::scriptToHtml('jquery') !!}
        {!! Assets::scriptToHtml('form-validation') !!}
        {!! $form->renderValidatorJs() !!}
    @else
        @push('footer')
            {!! $form->renderValidatorJs() !!}
            <script>
                let formData = {!! $form->getModel()->toJSON() !!};
                let resetAction  = {!! setting('reset_action') !!}
            </script>
        @endpush
    @endif
@endif
