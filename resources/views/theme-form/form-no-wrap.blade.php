<div id="main">
@if ($showStart)
    {!! Form::open(Arr::except($formOptions, ['template'])) !!}
@endif

@php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel()) @endphp

@if ($showFields)
    @foreach ($fields as $field)
        @if (!in_array($field->getName(), $exclude))
            {!! $field->render() !!}
            @if ($field->getName() == 'name' && defined('BASE_FILTER_SLUG_AREA'))
                {!! apply_filters(BASE_FILTER_SLUG_AREA, $form->getModel()) !!}
            @endif
        @endif
    @endforeach
@endif
<div class="clearfix"></div>

@foreach ($form->getMetaBoxes() as $key => $metaBox)
    {!! $form->getMetaBox($key) !!}
@endforeach

@php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp

{!! $form->getActionButtons() !!}

@if ($showEnd)
    {!! Form::close() !!}
@endif
</div>
<?php
    Theme::asset()->container('footer')->writeScript('customScript', "var ImpigerVariables = {};"
            . "                                       ImpigerVariables.languages = {
            notices_msg: ".json_encode(trans('core/base::notices'), JSON_HEX_APOS)." ,
        };      ;");

    Theme::asset()
    ->usePath(false)
    ->add('theme-form1-css', asset('vendor/core/core/base/libraries/jquery-steps/css/jquery.steps.css'), [], [], '1.0.0')
    ->add('theme-form2-css', asset('vendor/core/core/base/libraries/select2/css/select2.min.css'), [], [], '1.0.0')
    ->add('theme-form3-css', asset('vendor/core/core/base/libraries/select2/css/select2-bootstrap.min.css'), [], [], '1.0.0')
    ->add('theme-form4-css', asset('vendor/core/core/base/libraries/bootstrap-datepicker/css/bootstrap-datepicker3.min.css'), [], [], '1.0.0')
    ->add('theme-form-toastr-css', asset('vendor/core/core/base/libraries/toastr/toastr.min.css'), [], [], '1.0.0')
    ->add('theme-form5-css', asset('vendor/core/plugins/crud/css/module_custom_styles.css'), [], [], '1.0.0');
    Theme::asset()
    ->container('footer')
    ->usePath(false)
    ->add('theme-form1-js', asset('vendor/core/core/js-validation/js/js-validation.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form4-js', asset('vendor/core/core/base/libraries/select2/js/select2.min.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form2-js', asset('vendor/core/core/base/libraries/bootstrap-datepicker/js/bootstrap-datepicker.min.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form3-js', asset('vendor/core/core/base/libraries/jquery-steps/js/jquery.steps.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form6-js', asset('vendor/core/plugins/crud/js/jquery_steps_init.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form05-js', asset('vendor/core/plugins/crud/js/custom_save_storage.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form5-js', asset('vendor/core/plugins/crud/js/crud_utils.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form-tostr-js', asset('vendor/core/core/base/libraries/toastr/toastr.min.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form7-js', asset('vendor/core/core/base/js/core.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form8-js', asset('vendor/core/core/base/js/common_utils.js'), ['jquery'], [], '1.0.0')
   
    ;
    Theme::asset()->container('footer')->writeContent('customScript1',  $form->renderValidatorJs() ); 
?>
<script>
    let formData = {!! $form->getModel()->toJSON() !!};
</script>
