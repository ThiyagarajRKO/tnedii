{!! Theme::partial('header') !!}
@if (Theme::get('section-name'))
    {{-- @Customized by sabari shankar parthiban start --}}
    <section data-background="{{ Theme::asset()->url('images/page-intro-02.jpg') }}" class="section page-intro pt-50 pb-30 bg-cover">
     {{-- @Customized by sabari shankar parthiban end --}}   
        <div style="opacity: 0.7" class="bg-overlay"></div>
        <div class="container">
            <h3 class="page-intro__title">{{ Theme::get('section-name') }}</h3>
            {!! Theme::breadcrumb()->render() !!}
        </div>
    </section>
@endif
{!! Theme::content() !!}
{!! Theme::partial('footer') !!}


