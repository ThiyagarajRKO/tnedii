@if (function_exists('get_trainings'))
@php 

@Theme::asset()
->container('footer')
->usePath(false)
->add('common_theme', asset('vendor/theme/js/common_theme_script.js'), ['jquery'], [], '1.0.0');
@endphp
<section class="section">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="paginate" value="{{ $limit }}">

    <div class="container">
        <div class="page-content">
            <div class="post-group post-group--single">
                
                <div class="post-group__content">
                    <div class="text-center institute-type-block">
                        @php $selectedType =  request()->get('type'); @endphp
                    </div>
                    <!-- <div id="datepicker" style="" class="recent-tt-sc-calendar"></div> -->
                    <div class="text-center alphabet-href">
                        <?php
                        $selectedChar = request()->get('char');
                        ?>
                        <a data-href="" class="@if(!$selectedChar) checked @endif">All</a>
                        <?php
                        $alphas = range('A', 'Z');
                        foreach ($alphas as $key) { ?>
                            <a data-href="<?php echo $key  ?>" class="@if($selectedChar == $key) checked @endif"><?php echo $key ?></a>
                        <?php } ?>
                    </div>
                    <div id="institution_lists">
                        @include('training-title.training-list-gallery-view', ['trainings' => $trainings])
                    </div>
            </div>
        </div>
    </div>
</section>
@endif

@php
@Theme::asset()
->container('footer')
->usePath(false)
->add('common_theme', asset('vendor/theme/js/common_theme_script.js'), ['jquery'], [], '1.0.0')
->add('theme-form2-js', asset('vendor/core/core/base/libraries/bootstrap-datepicker/js/bootstrap-datepicker.min.js'), ['jquery'], [], '1.0.0')
->add('theme-form5-js', asset('vendor/core/plugins/crud/js/crud_utils.js'), ['jquery'], [], '1.0.0')
->add('theme-form7-js', asset('vendor/core/core/base/js/core.js'), ['jquery'], [], '1.0.0')  
->add('training-events-js', asset('vendor/core/core/base/js/training_events.js'), ['jquery'], [], '1.0.0');
@endphp
