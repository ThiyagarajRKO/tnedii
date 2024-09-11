@if (function_exists('render_training_title'))
    <section class="section pt-50 pb-50">
        <div class="container">
            {!! render_training_title($limit ? $limit : 8) !!}
        </div>
    </section>
@endif

@php
@Theme::asset()
->container('footer')
->usePath(false)
->add('common_theme', asset('vendor/theme/js/common_theme_script.js'), ['jquery'], [], '1.0.0');
@endphp
