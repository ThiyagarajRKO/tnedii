@php
$col = "col-md-12";
if (count($youtube_sliders) > 0 && count($image_sliders) > 0)
        $col = "col-md-6";
    elseif(count($youtube_sliders) > 0)
        $col = "col-md-12";
    elseif(count($youtube_sliders) > 0)
        $col = "col-md-12";
@endphp

@if (count($youtube_sliders) > 0 || count($image_sliders) > 0)

<section>
   
<div class="gallery-container-body" >
	<div class="gallery-container">
		<div class="swiper-container gallery-main">
			<div class="swiper-wrapper">
			    @foreach($image_sliders as $islider)
				<div class="swiper-slide">					
					@if ($islider['link']) <a href="{{ $islider['link'] }}" target="_blank"> @endif <img src="{{ RvMedia::getImageUrl($islider['image'], null, false, RvMedia::getDefaultImage()) }}" alt="{{ $islider['title'] }}" class="slider_image" style="object-fit:fill;">@if ($islider['link']) </a> @endif
				</div>
				@endforeach
			</div>
		</div>
		<div class="swiper-container gallery-thumbs">
			<div class="swiper-wrapper">
			    
                    @foreach($youtube_sliders as $isvslider)
			        @if($isvslider['link'])
                    <div class="swiper-slide">
                        <a href="{{ $isvslider['link'] }}" target="_blank">
    						<div class="row each-thumb">
    							<div class="col-md-4">
    								<img src="{{ RvMedia::getImageUrl($isvslider['image'], null, false, RvMedia::getDefaultImage()) }}" alt="{{ $isvslider['title'] }}" class="slider_thumb_image">
    							</div>
    							<div class="col-md-8">
    								<div class="slider-text">
    									<h4 >{{ $isvslider['title'] }}</h4>
    									<p >{!! \Illuminate\Support\Str::limit($isvslider['description'], 90, ' ...') !!}</p>
    								</div>
    							</div>
    						</div>
    					</a>
					</div>
					@endif
				    @endforeach
                
		    </div>
	    </div>
	    <div class="swiper-pagination"></div>
    </div>
</div>
<div class="" style="margin-top: 12px; display:none;">
    @if (count($video_sliders) > 0)
    <div class="{{ $col }}">
        <div class="owl-slider owl-carousel" data-owl-speed="5000" data-owl-gap="0" data-owl-nav="true" data-owl-dots="false" data-owl-item="1" data-owl-item-xs="1" data-owl-item-sm="1" data-owl-item-md="1" data-owl-item-lg="1" data-owl-duration="1000" data-owl-mousedrag="on">
            
            @foreach($video_sliders as $slider)
                <div class="item-video">
                    <div class="owl-video-wrapper" style="height: 420px;">
                        <a class="owl-video" href="{{ $slider['link'] }}"></a>
                        <!-- <div class="owl-video-play-icon" style="margin-top: unset !important;"></div> -->
                        <div class="owl-video-tn owl-lazy" srctype="{{ $slider['image'] }}"></div>
                    </div> 
                </div> 
            @endforeach
            
        </div>
    </div>
    @endif

    @if (count($image_sliders) > 0)
    <div class="{{ $col }}">
        <div class="owl-slider owl-carousel carousel--nav inside" data-owl-auto="true" data-owl-loop="true" data-owl-speed="5000" data-owl-gap="0" data-owl-nav="true" data-owl-dots="false" data-owl-item="1" data-owl-item-xs="1" data-owl-item-sm="1" data-owl-item-md="1" data-owl-item-lg="1" data-owl-duration="1000" data-owl-mousedrag="on">
            
            @foreach($image_sliders as $islider)
            @if ($islider['link'])
                <a class="btn--custom btn--rounded staggered-animation animated fadeInUp" href="{{ $islider['link'] }}" target="_blank" data-animation="fadeInUp" data-animation-delay="0.5s">
            @endif    
                <div class="slider-item">
                    <img src="{{ RvMedia::getImageUrl($islider['image'], null, false, RvMedia::getDefaultImage()) }}" alt="{{ $islider['title'] }}">
                    @if ($islider['title'] || $islider['description'])
                        <div class="slider__content">
                            <div class="slider__content__wrapper">
                                <div class="slider__content__wrapper__content">
                                    @if ($islider['title'])
                                        <h2 class="staggered-animation animated fadeInDown" data-animation="fadeInDown" data-animation-delay="0.3s">{{ $islider['title'] }}</h2>
                                    @endif
                                    @if ($islider['description'])
                                        <p class="staggered-animation animated fadeInUp" data-animation="fadeInUp" data-animation-delay="0.4s">{{ $islider['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @if ($islider['link'])
            </a>
            @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
</section>
@endif
<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.8/slick.min.css">
<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.8/slick-theme.min.css">
<style>

/* slider2 */

.slick-list.draggable {
    height:455px !important;
    width: 74% !important;
}
.containerslider {
  padding: 2rem;
  height: 500px !important;
  background-color:#021736;
}

.slider--vertical {
  transform-origin: center left;

  .slick-slide {
    font-size: 3rem;
    opacity: 1;
    text-transform: none;
    transform: scale(0.6, 0.6);
    transform-origin: center left;
    transition: opacity .1s ease, transform .1s ease;
    margin-top: -46px !important;
  }

  .slick-next {
    display : bla;
    margin-top: 23rem !important;
    margin-right: 89rem !important;
    transform: rotate(90deg) !important;
  }
  .slick-prev {
    margin-top: -24.7rem !important;
    margin-left: 26rem !important;
    transform: rotate(90deg) !important;
  }
}

.slick-arrow {
  color: #000;
}
.swiper-pagination-bullets.swiper-pagination-horizontal {
    right: 258p !important; 
    left: none !important;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  

<script>
/* slider2 */

$(function(){
  // Vertical slider
  var verticalSlider = $('.slider--vertical');
  verticalSlider.slick({
    arrows: true,
    autoplay: true,
    centerMode: true,
    infinite: true,
    rows: 0,
    slidesToShow: 8,
    vertical: true,
    verticalSwiping: true
  });

  /**
  * FIX JUMPING ANIMATION
  * Set special animation class on first or last clone.
  * https://github.com/kenwheeler/slick/issues/3419
  */
  verticalSlider.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
    var 
      direction,
      slideCountZeroBased = slick.slideCount - 1;

    if (nextSlide == currentSlide) {
      direction = "same";

    } else if (Math.abs(nextSlide - currentSlide) == 1) { // 1 or -1
      if (slideCountZeroBased === 1) { // If there's only two slides
        direction = "duo";
      } else { // More than two slides
        direction = (nextSlide - currentSlide > 0) ? "right" : "left"; 
      }
    } else { // e.g., slide 0 to slide 6
      direction = (nextSlide - currentSlide > 0) ? "left" : "right";
    }

    // Add a temp CSS class for the slide animation (.slick-current-clone-animate)
    if (direction == 'duo') {  
      $('.slick-cloned[data-slick-index="' + (nextSlide + slideCountZeroBased + 1) + '"]', sliders).addClass('slick-current-clone-animate');

      $('.slick-cloned[data-slick-index="' + (nextSlide - slideCountZeroBased - 1) + '"]', sliders).addClass('slick-current-clone-animate');
    }
    
    if (direction == 'right') {
      $('.slick-cloned[data-slick-index="' + (nextSlide + slideCountZeroBased + 1) + '"]', verticalSlider).addClass('slick-current-clone-animate');
    }

    if (direction == 'left') {
      $('.slick-cloned[data-slick-index="' + (nextSlide - slideCountZeroBased - 1) + '"]', verticalSlider).addClass('slick-current-clone-animate');
    }
  });

  verticalSlider.on('afterChange', function (event, slick, currentSlide, nextSlide) {
    $('.slick-current-clone-animate', verticalSlider).removeClass('slick-current-clone-animate');
    $('.slick-current-clone-animate', verticalSlider).removeClass('slick-current-clone-animate');
  });
});
</script>







