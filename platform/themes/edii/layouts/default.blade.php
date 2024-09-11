{!! Theme::partial('header') !!}
@if (Theme::get('section-name'))
    <section data-background="{{ Theme::asset()->url('images/page-intro-02.jpg') }}" class="section page-intro pt-50 pb-50 bg-cover">
        <div style="opacity: 0.7" class="bg-overlay"></div>
        <div class="container">
            <h3 class="page-intro__title">{{ Theme::get('section-name') }}</h3>
            {!! Theme::breadcrumb()->render() !!}
        </div>
    </section>
@endif
{{-- @customized by sabari shankar parthiban start--}}
<section class="section pt-50 pb-30">
{{-- @customized by sabari shankar parthiban start--}}
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div class="page-content">
                    {!! Theme::content() !!}
                </div>
            </div>
            <div class="col-lg-3">
                <div class="page-sidebar">
                    {!! Theme::partial('sidebar') !!}
                </div>
            </div>
        </div>
    </div>
</section>
{!! Theme::partial('footer') !!}


