@extends('core/base::layouts.base')

@section ('page')
    @include('core/base::layouts.partials.svg-icon')

    <div class="page-wrapper">

        @include('core/base::layouts.partials.top-header')
        <div class="clearfix"></div>
        <div class="page-container">
            <div class="page-sidebar-wrapper">
                <div class="page-sidebar navbar-collapse collapse">
                    <div class="sidebar">
                        <div class="sidebar-content">
                            @include('core/base::layouts.partials.sidebar-search')
                            <ul class="page-sidebar-menu page-header-fixed page-sidebar-menu-closed" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                                  <li class="nav-item">
                                    <a href="{{ url('/training/applicants') }}" class="nav-link nav-toggle">
                                        <i class="fa fa-users"></i>
                                        <span class="title">Training Applicants</span>
                                    </a>
                                </li>
                                {{-- @Customized Ramesh Esakki - Start --}}
                                    @if(!is_plugin_active('backend-menu') || (auth() && auth()->user() && auth()->user()->manage_supers))
                                        @include('core/base::layouts.partials.sidebar')
                                    @else
                                        {!!
        app(\Impiger\BackendMenu\BackendMenu::class)->renderDynamicMenus([
            'view' => 'plugins/backend-menu::partials.dynamic-sidebar'
        ])
                                        !!}
                                    @endif
                                {{-- @Customized Ramesh Esakki - End --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-content-wrapper">
                <div class="page-content @if (Route::currentRouteName() == 'media.index') rv-media-integrate-wrapper @endif" style="min-height: 100vh">
                    {!! Breadcrumbs::render('main', page_title()->getTitle(false)) !!}
                    <div class="clearfix"></div>
                    <div id="main">
                        @yield('content')
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        @include('core/base::layouts.partials.footer')
    </div>
@stop

@section('javascript')
    @include('core/media::partials.media')
    
{{-- @Customized Sabari Shankar - Start --}}
     @include('base.script-variable')
{{-- @Customized Sabari Shankar - End --}}
@endsection

@push('footer')
    @routes
@endpush
