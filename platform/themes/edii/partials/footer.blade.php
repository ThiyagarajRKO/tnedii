<div class="footer-map">
	<div class="inside-map">
		<div class="container widget__footer">
			<div class="widget__header">
				<h3 class="widget__title">{{ __('Contact Us') }}</h3>
			</div>
			<div class="widget__content over-map">
				<div class="person-detail">
					<p><i class="fa fa-map-marker"></i>{{ theme_option('address') }}</p>
					<p><i class="fa fa-phone"></i>{!! getMobileNumbers() !!}</p>                            
					<p><i class="ion-email"></i>{!! getEmailIds() !!}</p>
				</div>
			</div>
		</div>
	</div>
	<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d37245.91537472624!2d80.20562774595703!3d13.046550948966502!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a52673d393a8027%3A0x82925df4d559a903!2sEntrepreneurship%20Development%20And%20Innovation%20Institute!5e0!3m2!1sen!2sin!4v1577428361764!5m2!1sen!2sin" width="100%" height="150" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
</div>
<footer class="page-footer bg-dark pt-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <aside class="widget widget--transparent widget__footer widget__about">
                    <div class="widget__header">
                        <h3 class="widget__title">{{ __('About EDII') }}</h3>
                    </div>
                    <div class="widget__content">
                        <p>{{ theme_option('site_description') }}</p>
                        <div class="person-detail">
                        </div>
                    </div>
                </aside>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <aside class="widget widget--transparent widget__footer useful-wedget">
                    <div class="widget__header">
                        <h3 class="widget__title">{{  __('Useful Links') }}</h3>
                    </div>
                    <div class="widget__content">
                        {!!
                            Menu::getPartnersWidget([
                                'slug'    => 'favorite-websites',
                                'options' => ['class' =>  'list list--fadeIn list--light'  ]
                            ])
                        !!}
                    </div>
                </aside>
            </div>
            <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <aside class="widget widget--transparent widget__footer widget__about">
                    <div class="widget__header">
                        <h3 class="widget__title">{{ __('Contact Us') }}</h3>
                    </div>
                    <div class="widget__content">
                        <div class="person-detail">
                            <p><i class="fa fa-map-marker"></i>{{ theme_option('address') }}</p>
                            <p><i class="fa fa-phone"></i>{!! getMobileNumbers() !!}</p>                            
                            <p><i class="ion-email"></i>{!! getEmailIds() !!}</p>
                        </div>
                    </div>
                </aside>
            </div> -->
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <aside class="widget widget--transparent widget__footer widget__about">
                    <div class="widget__header">                        
                        <h3 class="widget__title">{{ __('FOLLOW US') }}</h3>
                    </div>
                    <div class="widget__content">
					<div class="social-menu footer-social">
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
                    <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d37245.91537472624!2d80.20562774595703!3d13.046550948966502!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a52673d393a8027%3A0x82925df4d559a903!2sEntrepreneurship%20Development%20And%20Innovation%20Institute!5e0!3m2!1sen!2sin!4v1577428361764!5m2!1sen!2sin" width="100%" height="150" frameborder="0" style="border:0;" allowfullscreen=""></iframe> -->
                    </div>
                </aside>
            </div>
            <!-- <div class="col-md-2 mx-auto text-center text-white">              
				<img src="https://counter5.optistats.ovh/private/freecounterstat.php?c=cua7ur18esj7awatuyd5dpu2eudatktt" border="0" title="free hit counter" alt="free hit counter">
				<small>Total Visitors</small>
            </div> -->
            {!! dynamic_sidebar('footer_sidebar') !!}
        </div>
    </div>
    <div class="page-footer__bottom" style="display: none !important">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <div class="page-footer__social">
                        <ul class="social social--simple">
                            @if (theme_option('facebook'))
                                <li>
                                    <a href="{{ theme_option('facebook') }}" title="Facebook"><i class="hi-icon fa fa-facebook"></i></a>
                                </li>
                            @endif
                            @if (theme_option('twitter'))
                                <li>
                                    <a href="{{ theme_option('twitter') }}" title="Twitter"><i class="hi-icon fa fa-twitter"></i></a>
                                </li>
                            @endif
                            {{-- @Customized by Vijayaragavan.Ambalam Start --}}
                            @if (theme_option('instagram'))
                                <li>
                                    <a href="{{ theme_option('instagram') }}" title="Instagram"><i class="hi-icon fa fa-instagram"></i></a>
                                </li>
                            @endif
                            @if (theme_option('googleplus'))
                                <li>
                                    <a href="{{ theme_option('googleplus') }}" title="Google Plus"><i class="hi-icon fa fa-google-plus"></i></a>
                                </li>
                            @endif
                            @if (theme_option('linkedin'))
                                <li>
                                    <a href="{{ theme_option('linkedin') }}" title="LinkedIn"><i class="hi-icon fa fa-linkedin"></i></a>
                                </li>
                            @endif
                            {{-- @Customized by Vijayaragavan.Ambalam End --}}
                            @if (theme_option('youtube'))
                                <li>
                                    <a href="{{ theme_option('youtube') }}" title="Youtube"><i class="hi-icon fa fa-youtube"></i></a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                {{-- @Customized by Vijayaragavan.Ambalam Start --}}
                <div class="col-md-8 col-sm-6">
                    <div class="page-copyright">
                        <p>{!! clean(theme_option('copyright')) !!}| Powered by <a href="{{ config('core.base.general.powered_url') }}" target="_blank"><img src={{ url(config('core.base.general.powered_logo')) }}></a></p>
                    </div>
                </div>             
                {{-- @Customized by Vijayaragavan.Ambalam End --}}   
            </div>
        </div>
    </div>
	<div class="bottom-footer">
		<div class="container">
			<div class="col-md-6 copy-right-text">
				<p>Copyright @ 2023 All Rights Reserved by EDII</p>
			</div>
			<div class="col-md-6 design-by">
				<p>Maintenance by <a href="">EDII TN</a></p>
			</div>
		</div>
	</div>
</footer>
<div id="back2top"><i class="fa fa-angle-up"></i></div>

<!-- JS Library-->
{!! Theme::footer() !!}

@if (theme_option('facebook_comment_enabled_in_post', 'yes') == 'yes' || (theme_option('facebook_chat_enabled', 'yes') == 'yes' && theme_option('facebook_page_id')))
    <div id="fb-root"></div>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                xfbml            : true,
                version          : 'v7.0'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

    @if (theme_option('facebook_chat_enabled', 'yes') == 'yes' && theme_option('facebook_page_id'))
        <div class="fb-customerchat"
             attribution="install_email"
             page_id="{{ theme_option('facebook_page_id') }}"
             theme_color="{{ theme_option('primary_color', '#ff2b4a') }}">
        </div>
    @endif
@endif
{{-- @Customized Sabari Shankar - Start --}}
    @include('core/media::partials.media')
     @include('base.script-variable')
{{-- @Customized Sabari Shankar - End --}}
<link media="all" type="text/css" rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<link media="all" type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Epilogue:wght@900&amp;display=swap">
<script type='text/javascript' src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src='https://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.js'></script>
<script >
    
</script>

<script>
	$(document).ready(function () {
		$(".video-carousel").owlCarousel({
		  autoplay: true,
		  rewind: true, /* use rewind if you don't want loop */
		  margin: 20,
		   /*
		  animateOut: 'fadeOut',
		  animateIn: 'fadeIn',
		  */
		  responsiveClass: true,
		  autoHeight: true,
		  autoplayTimeout: 7000,
		  smartSpeed: 800,
		  nav: true,
		  responsive: {
			0: {
			  items: 1
			}
		  }
		});
		$(".tab-carousel").owlCarousel({
			items:6,
			loop:true,
			margin:10,
			//autoplay:true,
			nav: true,
			//autoplayTimeout:3000,
			//autoplayHoverPause:true
            responsive: {
                0: {
                    items: 1
                },
                640: {
                    items: 2
                },
                795: {
                    items: 3
                },
                1000: {
                    items:4
                },
                1300: {
                    items:6
                }
            }
		});
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			e.target // newly activated tab
			e.relatedTarget // previous active tab
			$(".tab-carousel").trigger('refresh.owl.carousel');
		});	
		var galleryThumbs = new Swiper(".gallery-thumbs", {   
		  slidesPerView: 4,
		  watchOverflow: true,
		  watchSlidesVisibility: true,
		  watchSlidesProgress: true,
		  direction: 'vertical'
		});

		var galleryMain = new Swiper(".gallery-main", {
		  watchOverflow: true,
		  watchSlidesVisibility: true,
		  watchSlidesProgress: true,
		  preventInteractionOnTransition: true,
		  navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		  },
		  pagination: {
				el: ".swiper-pagination",
				clickable: true
			},
		  effect: 'fade',
			fadeEffect: {
			crossFade: true
		  },
		  thumbs: {
			swiper: galleryThumbs
		  }
		});

		galleryMain.on('slideChangeTransitionStart', function() {
		  galleryThumbs.slideTo(galleryMain.activeIndex);
		});

		galleryThumbs.on('transitionStart', function(){
		  galleryMain.slideTo(galleryThumbs.activeIndex);
		});
	});

	
    // Multiple Video Upload
    $(document)
      .on("click", ".video_container .video-add", function(e) {
        e.preventDefault();
        var current_obj = $(this).closest(".video_container");
        var cloned_obj = $(current_obj.clone())
          .insertAfter(current_obj)
          .find('input[type="file"]')
          .val("");
    
        current_obj
          .find(".fa-plus")
          .removeClass("fa-plus")
          .addClass("fa-minus");
    
        current_obj
          .find(".btn-success")
          .removeClass("btn-success")
          .addClass("btn-danger");
    
        current_obj
          .find(".video-add")
          .removeClass("video-add")
          .addClass("video-del");
      })
      .on("click", ".video-del", function(e) {
        e.preventDefault();
        $(this)
          .closest(".video_container")
          .remove();
        return false;
  });

	
</script>
</body>
</html>
