@extends('core/base::layouts.base')

@section('body-class') login @stop
@section('body-style') background-image: url({{ get_login_background() }}); @stop

@push('header')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
@endpush
@section ('page')
    <div class="container-fluid">
        <div class="row">
            <div class="faded-bg animated"></div>
            <div class="hidden-xs col-md-6 col-lg-8 left-column p-0">
                <!--<div class="clearfix">-->
                    <!--<div class="col-sm-12 col-md-10 col-md-offset-2">-->
                    <div class="col-sm-12 p-0">
                        {{-- @Customized  Sabari Shankar - Start --}}
                        <div class="master-content" style="text-align: center">
                            <h1>Welcome!</h1>
                            <!--<p>{{setting('site_description')}}</p>-->
                            <div class="left-content">
                                <img src="/storage/image-1-1-1.png">
                            </div>
                        </div>
                        {{-- @Customized  Sabari Shankar - End --}}
                        <div class="logo-title-container">
                            <div class="copy animated fadeIn">
                                {{-- @Customized  Sabari Shankar - Start --}}
                                <div class="login-logo" style="padding:15px">
                                    <!-- <img src="/storage/emircom_white_logo.png" alt="logo"> -->
                                    <!--<img src="/storage/editn_logo.png" alt="logo">-->
                                </div>
                                <!-- <h1>{{ setting('admin_logo', config('core.base.general.base_name')) }}</h1> -->
                                {{-- @Customized  Sabari Shankar - End --}}
                                <p>{!! clean(trans('core/base::layouts.copyright', ['year' => now()->format('Y'), 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) !!}</p>
                            </div>
                        </div> <!-- .logo-title-container -->
                    </div>
                <!--</div>-->
            </div>

            <div class="col-xs-12 col-md-6 col-lg-4 login-sidebar">

                <div class="login-container">

                    @yield('content')

                    <div style="clear:both"></div>

                </div> <!-- .login-container -->

            </div> <!-- .login-sidebar -->
        </div> <!-- .row -->
    </div>
@stop
