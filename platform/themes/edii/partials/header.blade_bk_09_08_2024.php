<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" lang="{{ app()->getLocale() }}">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="{{ app()->getLocale() }}">
<![endif]-->
<!--[if IE 9]>
<html class="ie ie9" lang="{{ app()->getLocale() }}">
<![endif]-->
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>
		<meta name="format-detection" content="telephone=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<!-- Fonts-->
		<link href="https://fonts.googleapis.com/css?family={{ urlencode(theme_option('primary_font', 'Roboto')) }}" rel="stylesheet" type="text/css">
		<!-- CSS Library-->
		<style>
			:root {
			--color-1st: {{ theme_option('primary_color', '#bead8e') }};
			--primary-font: '{{ theme_option('primary_font', 'Roboto') }}', sans-serif;
			}
			/* Customized by sabari shankar parthiban start 
			.nav-top{
			background-color: {{ theme_option('primary_color', '#f5f5f5') }}!important;
			color: #fff!important;
			}
			/* Customized by sabari shankar parthiban end */
		</style>
		{!! Theme::header() !!}
		<link media="all" type="text/css" rel="stylesheet" href="{{asset('css/theme_changes.css')}}">
		<!--HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries-->
		<!--WARNING: Respond.js doesn't work if you view the page via file://-->
		<!--[if lt IE 9]><script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->
	</head>
	<!--[if IE 7]>
	<body class="ie7 lt-ie8 lt-ie9 lt-ie10">
		<![endif]-->
		<!--[if IE 8]>
		<body class="ie8 lt-ie9 lt-ie10">
			<![endif]-->
			<!--[if IE 9]>
			<body class="ie9 lt-ie10">
				<![endif]-->
				<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>
				{!! apply_filters(THEME_FRONT_BODY, null) !!}
				<header class="header" id="header" style="display:none;">
					<div class="header-wrap">
						<nav class="nav-top">
							<div class="container">
								<div class="pull-left">
									<div class="header-top-left">
										<ul class="heading">
											<!-- <li><a id="decfont">A-</a></li>
											<li><a id="normfont">A</a></li>
											<li><a id="incfont">A+</a></li> -->
											<!-- <li><a href="#skip_cont">Skip to main content</a></li>
												<li><a href="https://www.editn.in/pages/view/screen-reader-access">Screen Reader Access</a></li>
												<li><a href="https://www.editn.in/pages/view/sitemap">Sitemap</a></li> -->
										</ul>
									</div>
								</div>
								<div class="pull-right">
									<div class="hi-icon-wrap hi-icon-effect-3 hi-icon-effect-3a">
										@if (theme_option('facebook'))
										<a href="{{ theme_option('facebook') }}" title="Facebook" class="hi-icon fa fa-facebook" target="_blank"></a>
										@endif
										@if (theme_option('twitter'))
										<a href="{{ theme_option('twitter') }}" title="Twitter" class="hi-icon fa fa-twitter" target="_blank"></a>
										@endif
										{{-- @Customized by Vijayaragavan.Ambalam Start --}}
										@if (theme_option('instagram'))
										<a href="{{ theme_option('instagram') }}" title="Instagram" class="hi-icon fa fa-instagram" target="_blank"></a>
										@endif
										@if (theme_option('googleplus'))
										<a href="{{ theme_option('googleplus') }}" title="Google Plus" class="hi-icon fa fa-google-plus" target="_blank"></a>
										@endif
										@if (theme_option('linkedin'))
										<a href="{{ theme_option('linkedin') }}" title="LinkedIn" class="hi-icon fa fa-linkedin" target="_blank"></a>
										@endif
										{{-- @Customized by Vijayaragavan.Ambalam End --}}
										@if (theme_option('youtube'))
										<a href="{{ theme_option('youtube') }}" title="Youtube" class="hi-icon fa fa-youtube" target="_blank"></a>
										@endif
									</div>
									{{-- @Customized by Sabari Shankar Parthiban Start --}} 
									@if(is_plugin_active('multidomain')) 
									<div class="pull-left">
										<div class="pull-right">
											<span class="field-content">
												<div class="institute_name">
													<h5 class=""><i class="fa fa-university fa-md"></i>  {{(app(\Impiger\Multidomain\Multidomain::class)->getInstituteNameByCurrentDomainId()) ? : "TVET"}}</h5>
												</div>
											</span>
										</div>
									</div>
									@endif
									<!-- {{-- @Customized by Sabari Shankar Parthiban End --}}
										@if (is_plugin_active('member'))
											<ul class="pull-left">
												@if (auth('member')->check())
													<li><a href="{{ route('public.member.dashboard') }}" rel="nofollow"><img src="{{ auth('member')->user()->avatar_url }}" class="img-circle" width="20" alt="{{ auth('member')->user()->name }}"> &nbsp;<span>{{ auth('member')->user()->name }}</span></a></li>
													<li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" rel="nofollow"><i class="fa fa-sign-out"></i> {{ __('Logout') }}</a></li>
												@else
													<li><a href="{{ route('public.member.login') }}" rel="nofollow"><i class="fa fa-sign-in"></i> {{ __('Login') }}</a></li>
												@endif
											</ul>
											@if (auth('member')->check())
												<form id="logout-form" action="{{ route('public.member.logout') }}" method="POST" style="display: none;">
													@csrf
												</form>
											@endif
											{{-- @Customized by Sabari Shankar.Parthiban start --}}
											@else
											<ul class="pull-left">
												<li><a href="{{ route('access.login') }}" rel="nofollow"><i class="fa fa-sign-in"></i> {{ __('Login') }}</a></li>
											</ul>
											{{-- @Customized by Sabari Shankar.Parthiban end --}}
										@endif -->
									<div class="pull-left">
										<div class="pull-right">
											<!-- <div class="language-wrapper">
												{!! apply_filters('language_switcher') !!}
											</div> -->
                        					@php
                        					    $current_lang = "en";
                        					    if(Session::has('lang'))
                                                {
                                                    $current_lang = Session::get('lang');
                                                }
                        					@endphp
											<div class="language-wrapper">
										        <ul class="language_bar_list ">
                                                    <li class="{{ $current_lang == "ta" ? "active_lang" : ""  }}">
                                                        <a rel="alternate" hreflang="ta" href="{{ route('public.single') . "/?lang=ta" }}">
                                                            <img src="{{ route('public.single') . '/vendor/core/core/base/images/flags/in.svg' }}" title="தமிழ்" width="16" alt="தமிழ்">
                                                            <span class="notranslate">தமிழ்</span>
                                                        </a>
                                                    </li>
                                                    <li class="{{ $current_lang == "en" ? "active_lang" : ""  }}">
                                                        <a rel="alternate" hreflang="en" href="{{ route('public.single') . "/?lang=en" }}">
                                                            <img src="{{ route('public.single') . '/vendor/core/core/base/images/flags/us.svg' }}" title="English" width="16" alt="English">             
                                                            <span class="notranslate">English</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div class="clearfix"></div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
						</nav>
					</div>
				</header>
				<div id="google_translate_element"></div>
                <script type="text/javascript">
                function googleTranslateElementInit() {
                  new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'en,ta', multilanguagePage: true, autoDisplay: true}, 'google_translate_element');
                }
                </script>
                <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
				<header data-sticky="false" data-sticky-checkpoint="200" data-responsive="991" class="page-header page-header--light">
					<div class="">
						<!-- LOGO-->
						<div class="page-header__center logo-section">
							<div class="container">
								<div class="top-menu-area">
									<p class="header-phone" ><i class="fa fa-phone"></i>{!! getMobileNumbers() !!}</p>
									<p class="header-email"><i class="ion-email"></i>{!! getEmailIds() !!}</p>
									<!-- <div class="language-wrapper header-lawraper">
										{!! apply_filters('language_switcher') !!}
									</div> -->
                					<style type="text/css">
                					    body {
	                                        top: 0 !important;
                					    }
                					    #google_translate_element{
                                        	height: 0;
                                            overflow: hidden;
                                        }
                                        .skiptranslate{
                                            height: 0;
                                            overflow: hidden;
                                            visibility: hidden !important;
                                        }
                						.language_bar_list li {
                							display: block;
                						}
                						.language_bar_list li.active_lang {
                							display: none;
                						}
                					</style>
									<div class="language-wrapper header-lawraper">
								        <ul class="language_bar_list ">
                                            <li class="{{ $current_lang == "ta" ? "active_lang" : ""  }}">
                                                <a rel="alternate" hreflang="ta" href="{{ route('public.single') . "/?lang=ta" }}">
                                                    <img src="{{ route('public.single') . '/vendor/core/core/base/images/flags/in.svg' }}" title="தமிழ்" width="16" alt="தமிழ்">
                                                    <span class="notranslate">தமிழ்</span>
                                                </a>
                                            </li>
                                            <li class="{{ $current_lang == "en" ? "active_lang" : ""  }}">
                                                <a rel="alternate" hreflang="en" href="{{ route('public.single') . "/?lang=en" }}">
                                                    <img src="{{ route('public.single') . '/vendor/core/core/base/images/flags/us.svg' }}" title="English" width="16" alt="English">             
                                                    <span class="notranslate">English</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
									<div class="social-menu">
										@if (theme_option('facebook'))
										<a href="{{ theme_option('facebook') }}" title="Facebook" class="hi-icon fa fa-facebook" target="_blank"></a>
										@endif
										@if (theme_option('twitter'))
										<a href="{{ theme_option('twitter') }}" title="Twitter" class="hi-icon fa fa-twitter" target="_blank"></a>
										@endif
										{{-- @Customized by Vijayaragavan.Ambalam Start --}}
										@if (theme_option('instagram'))
										<a href="{{ theme_option('instagram') }}" title="Instagram" class="hi-icon fa fa-instagram" target="_blank"></a>
										@endif
										@if (theme_option('googleplus'))
										<a href="{{ theme_option('googleplus') }}" title="Google Plus" class="hi-icon fa fa-google-plus" target="_blank"></a>
										@endif
										@if (theme_option('linkedin'))
										<a href="{{ theme_option('linkedin') }}" title="LinkedIn" class="hi-icon fa fa-linkedin" target="_blank"></a>
										@endif
										{{-- @Customized by Vijayaragavan.Ambalam End --}}
										@if (theme_option('youtube'))
										<a href="{{ theme_option('youtube') }}" title="Youtube" class="hi-icon fa fa-youtube" target="_blank"></a>
										@endif
									</div>
								</div>
								<div class="text-center">
									<a href="{{ route('public.single') }}" class="page-logo">
									@if (theme_option('logo'))
									<img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" >
									@endif
									</a>
								</div>
							</div>
						</div>
						<div class="page-header__center nav-area">
							<!-- MOBILE MENU-->
							<div class="container">
								<div class="navigation-toggle navigation-toggle--dark" style="display: none"><span></span></div>
								<div class="">
									<!-- SEARCH-->
									<!-- <div class="search-btn c-search-toggler"><i class="fa fa-search close-search"></i></div> -->
									<!-- NAVIGATION-->
									<nav class="navigation navigation--light navigation--fade navigation--fadeLeft">
										{!!
										Menu::renderMenuLocation('main-menu', [
										'options' => ['class' => 'menu sub-menu--slideLeft'],
										'view'    => 'main-menu',
										])
										!!}
									</nav>
									<span class="form-submit header-login"><a href="{{route('access.login')}}"><input type="button" value="LOGIN" class="btn btn-success" id="login"></a></span>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
						<!-- <nav class="navbar navbar-default">
							<div class="container text-center">
								<div class="navbar-header">
									<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span> 
									</button>
									<a class="navbar-brand" href="#">Title</a>
								</div>
								<div id="navbar" class="collapse navbar-collapse">
									<ul class="nav navbar-nav navbar-center">
										<li><a href="#">Link</a></li>
										<li><a href="#">Link</a></li>
										<li><a href="#">Link</a></li>
										<li><a href="#">Link</a></li>
									</ul>
								</div>
							</div>
							</nav> -->
					</div>
					@if (is_plugin_active('blog'))
					<div class="super-search hide" data-search-url="{{ route('public.ajax.search') }}">
						<form class="quick-search" action="{{ route('public.search') }}">
							<input type="text" name="q" placeholder="{{ __('Type to search...') }}" class="form-control search-input" autocomplete="off">
							<span class="close-search">&times;</span>
						</form>
						<div class="search-result"></div>
					</div>
					@endif
				</header>
				<div id="page-wrap">
