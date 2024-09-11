@if (function_exists('get_training_title'))
@php 

@Theme::asset()
->container('footer')
->usePath(false)
->add('common_theme', asset('vendor/theme/js/common_theme_script.js'), ['jquery'], [], '1.0.0');


@endphp
<section class="section">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="paginate" value="{{ $limit }}">

    <div class="container-fluid">
        <div class="page-content">
            <div class="post-group post-group--single">
                
                <div class="post-group__content">
                    
                    <!-- <div class="row">
                        <div class='col-sm-4'></div>
                        <div class='gridLayout  col-sm-4'>
                            <div class="form-group">
                                <div class="input-group">
                                    <input class="form-control datepicker" data-date-format="yyyy-mm-dd" required="required" name="tsd" type="text" value="<?php echo request()->get('tsd'); ?>" id="tsd">
                                    <span class="input-group-prepend">
                                        <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-sm-4'></div>
                    </div> -->

                  
                                      
                    <div id="institution_lists">
                        @include('plugins/crud::training-title', ['trainings' => $trainings])
                    </div>
            </div>
        </div>
    </div>
    
</section>


@endif

@php
Theme::asset()->container('footer')->writeScript('customScript', "var ImpigerVariables = {};"
            . "                                       ImpigerVariables.languages = {
            notices_msg: ".json_encode(trans('core/base::notices'), JSON_HEX_APOS)." ,
        };      ;");
Theme::asset()
    ->container('footer')
    ->usePath(false)    
    ->add('theme-form2-js', asset('vendor/core/core/base/libraries/bootstrap-datepicker/js/bootstrap-datepicker.min.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form5-js', asset('vendor/core/plugins/crud/js/crud_utils.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form7-js', asset('vendor/core/core/base/js/core.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form7-js', asset('vendor/core/core/base/libraries/fancybox/jquery.fancybox.min.js'), ['jquery'], [], '1.0.0')
    ->add('moment', asset('vendor/core/core/base/libraries/moment/min/moment.min.js'), ['jquery'], [], '1.0.0')
    ->add('main', asset('vendor/core/core/base/libraries/fullcalendar/main.min.js'), ['jquery'], [], '1.0.0')
    ->add('full_cal', asset('vendor/core/plugins/full-calendar/js/calendar.js'), ['jquery'], [], '1.0.0')
    ->add('common_theme', asset('vendor/theme/js/common_theme_script.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form8-js', asset('vendor/core/core/base/js/common_utils.js'), ['jquery'], [], '1.0.0');
Theme::asset()
    ->usePath(false)
    ->add('theme-form4-css', asset('vendor/core/core/base/libraries/bootstrap-datepicker/css/bootstrap-datepicker3.min.css'), [], [], '1.0.0')
    ->add('theme-fancybox-css', asset('vendor/core/core/base/libraries/fancybox/jquery.fancybox.min.css'), [], [], '1.0.0')
    /* ->add('core-css', asset('vendor/core/core/base/css/core.css'), [], [], '1.0.0') */
     ->add('core-css', asset('vendor/core/core/dashboard/css/dashboard.css'), [], [], '1.0.0')
    ->add('theme-form5-css', asset('vendor/core/plugins/crud/css/module_custom_styles.css'), [], [], '1.0.0')
    ->add('fullcalendar-css', asset('vendor/core/core/base/libraries/fullcalendar/main.min.css'), [], [], '1.0.0');
@endphp
