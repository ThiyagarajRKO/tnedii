{{-- 
<p><br></p>
--}}
<!-- service -->
<div class="post-group post-group--single col-md-12 service-ar">
	<div class="container">
		<div class="row">
			<div class="post-group__header clearfix col-md-12" style="margin-bottom:0px !important">
				<h3 class=" service-title"><span class="">OUR</span> SERVICES</h3>
				<p class="service-tag">Entrepreneurship Education and Self-employment Promotion <br>in the state of Tamil Nadu.</p>
			</div>
		</div>
		<div class="row">
			@foreach (get_post_by_category_our_services() as $post)
			@php 
			$osConfig = \Impiger\CustomField\Facades\CustomFieldSupportFacade::exportCustomFieldsData(get_class($post), $post->id,true);
			$config = Arr::get($osConfig, '0.items');
			$config = createHashMapArray($config, 'slug', true);
			$color = Arr::get($config, 'color.value');
			$fontCls = Arr::get($config, 'icon_class.value');
			$color = ($color) ? $color : theme_option('primary_color', '#bead8e');
			@endphp
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="our-services-container clearfix rounded">
					<!-- <span class="styled-line-strong" style="background-color: {{$color}} ;"></span> -->
					<div class="mini-stat clearfix rounded">
						<div class="icon-box">
							<div class="mini-stat-icon edii-msi-with-border1">
								<div class="rounded" style="background:none repeat scroll 0% 0% #fff !important">
									<i class="{{ $fontCls ?? 'fa fa-book'}}"></i>
								</div>
							</div>
						</div>
						<div class="mini-stat-info">
							<h4>{{$post->name}}</h4>
							<!-- <span class="styled-line" style="background-color: {{$color}} ;"></span> -->
						</div>
					</div>
					<div class="box-content" id="service_content">
						<p class="box-service-text">{{ $post->description }}</p>
						<p class="">
							<a href="{{ $post->url }}">Read more</a>
						</p>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</div>
<div class="clearfix"></div>
<!-- service end -->
<!-- START :: ABOUT US SECTION -->
<section class="about-us-section">
	<div class="col-lg-6 about-image">
		<img src="{{asset('images/about.jpg')}}">					
	</div>
	<div class="container">
		<div class="main-wrapper">
			<div class="row">
				<div class="col-lg-6 align-self-center">
					<h2><span>ABOUT</span> EDII</h2>
					<p style="margin-bottom:20px">Established in 2001, the Entrepreneurship Development and Innovation Institute(EDll), Chennal is an apex organisation in the field of entrepreneurship education and self-employment promotion in the state of Tamil Nadu.</p>
					<p>EDll was constituted by Government of Tamil Nadu as a not-for- profit society and is administered by Department of Micro, Small and Medium Enterprises (MSME).Headed by the Director of the Institute, EDIl is managed under the superintendenceof a Governing Council, appointed by the Government of Tamil Nadu.</p>
					<button class="know-more-btn">Know More</button>
				</div>
				<!-- <div class="col-lg-6">
					<img src="{{asset('images/about.jpg')}}">					
				</div> -->
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</section>
<!-- END :: ABOUT US SECTION -->
@if (function_exists('get_post_by_category_name'))
    @php
        $review_posts = get_post_by_category_name("Review", 0, 0);
        //print_r($review_posts);
    @endphp
    @if (!empty($review_posts))
        <div style="background:#fff; padding-bottom:70px; padding-top:100px">
            <div class="container">
                <div class="row">
                    @foreach($review_posts as $key => $review)
                    <div class="@php echo($key == 0 ? "col-md-12" : "col-md-6") @endphp" @php echo($key == 0 ? 'style="margin-bottom:30px;"' : 'style="margin-bottom:30px;"') @endphp>
                        <div class="div1 eachdiv" @php echo($key == 0 ? 'style="min-height:0px;"' : '') @endphp>
                            <div class="userdetails">
                              <div class="imgbox">
                                <img src="{{ RvMedia::getImageUrl($review->image, '', false, RvMedia::getDefaultImage()) }}" alt="{{ $review->name }}">
                              </div>
                              <div class="detbox">
                                <p class="name">{{ $review->name }}</p>
                                <p class="designation">{{ $review->description }}</p>
                              </div>
                            </div>
                            <div class="review">
                              {!! $review->content !!}</p>
                            </div>
                          </div>    
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <style>
        .eachdiv
        {
        	padding: 16px 32px;
            border-radius: 10px;
            box-shadow: 5px 5px 20px #6d6b6b6b;
            color: white;
        }
        .div1
        {
        	background:#021736;
            grid-column: 1/3;
            grid-row: 1/2;
            background-repeat: no-repeat;
            background-position-x: 80%;
            min-height: 341px;
        }
        .userdetails
        {
        	display: flex;
        	margin-bottom:15px;
        }
        .imgbox
        {
        	margin-right: 16px;
        }
        .imgbox img
        {
        	border-radius: 50%;
        	width: 70px;
        	height: 70px;
        	border: 2px solid #cec5c5;
        	object-fit:cover;
        }
        .detbox
        {
        	display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .detbox p
        {
        	margin: 0;
        }
        .detbox .name
        {
        	color: #ffffff;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .detbox .name.dark
        {
        	color: #49505A;
        }
        .detbox .designation
        {
        	color: #ffffff;
            opacity: 50%;
            font-size: 13px;
        }
        .detbox .designation.dark
        {
        	color: #49505A;
        }
        .review h4
        {
        	font-size: 22px;
        	color: #ffffff;
            font-weight: 600;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        .review.dark h4{
        	color:#4B5258;
        }
        .review p
        {
        	font-size: 16px;
            color: #ffffff;
            font-weight: 500;
            opacity: 80%;
            line-height: 1.5;
        }
        .review.dark p {
        	color: #0e0e0e;
        }
        .attribution
        {
        	font-size: 16px;
            line-height: 1.5;
            position: fixed;
            bottom: 10px;
            right: 10px;
            text-align: right;
        }
        .attribution a
        {
        	text-decoration: none;
        }
        
        @media only screen and (max-width: 1000px)
        {
        	.innerdiv
        	{
        		transform: scale(0.7);
        	}
        }
        @media only screen and (max-width: 800px)
        {
        	.innerdiv
        	{
        		transform: scale(0.6);
        	}
        }
        @media only screen and (max-width: 600px)
        {
        	
        }
        </style>
    @endif
@endif
<!-- START :: OUR MINISTER INTRO -->
<section class="our-minister-intro">
	<div class="container">
		<div class="main-wrapper">
			<div class="row">
				<div class="col-lg-6">
					<img src="{{asset('images/minister.png')}}">
				</div>
				<div class="col-lg-6 text-center align-self-center">
					<h3>Thiru T.M Anbarasan</h3>
					<h4>Minister for Micro,Small and Medium Enterprises</h4>
					<button class="know-more-btn">Know More</button>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- END :: OUR MINISTER INTRO -->
@if (is_plugin_active('blog'))
<section class="section footer-section news-ar">
	<div class="container">
		<div class="row">
			<div class="col-lg-6">
				<div class="post-group post-group--single">
					<div class="post-group__header">
						<h3 class="post-group__title">LATEST VIDEO</h3>
					</div>
					<div class="video-slider">
						<div class="owl-carousel video-carousel">
							<div>
								<div class="youtube-video-slider w-100 h-100">
									<div class="youtube-video-area w-100">
										<iframe height="660" width="" 
											src="https://www.youtube.com/embed/il_t1WVLNxk"> 
										</iframe> 
									</div>
									<div class="video-content text-left">
										<h3>INNOVATION VOUCHER PROGRAM</h3>
										<p>Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries.</p>
									</div>
								</div>
							</div>		
							<div>
								<div class="youtube-video-slider w-100 h-100">
									<div class="youtube-video-area w-100">
										<iframe height="660" width="" 
											src="https://www.youtube.com/embed/il_t1WVLNxk"> 
										</iframe> 
									</div>
									<div class="video-content text-left">
										<h3>INNOVATION VOUCHER PROGRAM</h3>
										<p>Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries.</p>
									</div>
								</div>
							</div>		
							<div>
								<div class="youtube-video-slider w-100 h-100">
									<div class="youtube-video-area w-100">
										<iframe height="660" width="" 
											src="https://www.youtube.com/embed/il_t1WVLNxk"> 
										</iframe> 
									</div>
									<div class="video-content text-left">
										<h3>INNOVATION VOUCHER PROGRAM</h3>
										<p>Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries.</p>
									</div>
								</div>
							</div>		
						</div>					
					</div>					
				</div>
			</div>
			<div class="col-lg-6">
				<div class="page-content">
					<div class="post-group post-group--single">
						<div class="post-group__header">
							<h3 class="post-group__title">{!! clean($title) !!}</h3>
						</div>
						<div class="post-group__content">
							<div class="row">
							    <div style="display:none">
								@foreach (get_latest_posts(10, [], ['slugable']) as $post)
                                @if($post->id == 18 || $post->id == 19 || $post->id == 20)
                                @else
								<div class="col-md-12 col-sm-6 col-xs-12">									
									<article class="post post__horizontal post__horizontal--single mb-20 clearfix">
										<div class="post__thumbnail">
											<img src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}"><a href="{{ $post->url }}" class="post__overlay"></a>
										</div>
										<div class="post__content-wrap">
											<header class="post__header">
												<h3 class="post__title"><a href="{{ $post->url }}">{{ $post->name }}</a></h3>
												<p data-number-line="4">{{ $post->description }}</p>
												<div class="post__meta"><span class="post__created-at">{{ $post->created_at->format('M d, Y') }}</span></div>
											</header>
										</div>
									</article>
									
									
								</div>
								@endif
								@endforeach
								</div>
								@if (function_exists('get_post_by_category_name'))
								     @php
                                        $review_posts = get_post_by_category_name("Default", 0, 0, 'DESC');
                                        //print_r($review_posts);
                                    @endphp
                                         @foreach($review_posts as $key => $review)
                                            <div class="col-md-12 col-sm-6 col-xs-12">									
            									<article class="post post__horizontal post__horizontal--single mb-20 clearfix">
            										<div class="post__thumbnail">
            											<img src="{{ RvMedia::getImageUrl($review->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="{{ $review->name }}"><a href="{{ $review->url }}" class="post__overlay"></a>
            										</div>
            										<div class="post__content-wrap">
            											<header class="post__header">
            												<h3 class="post__title"><a href="{{ $review->url }}">{{ $review->name }}</a></h3>
            												<p data-number-line="4">{{ $review->description }}</p>
            												<div class="post__meta"><span class="post__created-at">{{ $review->created_at->format('M d, Y') }}</span></div>
            											</header>
            										</div>
            									</article>
            								</div>
                                         @endforeach
                                    @if (!empty($review_posts))
                                    @endif
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- <div class="col-lg-3">
				<div class="page-sidebar">
					<div class="quick-form">
						<div class="post-group post-group--single">
							<div class="post-group__header">
								<h4 class="post-group__title">Newsletter</h4>
							</div>
							@if ($errors->any())
							<div class="alert alert-danger">
								<ul>
									@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
							@endif
							@if(session()->has('error_msg'))
							<div class="alert alert-danger">
								{{session()->get('error_msg')}}
							</div>
							@endif
							@if(session()->has('success_msg'))                            
							<div class="alert alert-success">
								{{session()->get('success_msg')}}
							</div>
							@endif
							<p>Signup for our monthly newsletter to get the latest news deliverd directly in your inbox.</p>
							<p>
							</p>
							<form id="request-call-back" method="post" action="{{route('crud.subscribe')}}" novalidate="novalidate">
								@csrf
								<span><input id="subscriptionemail" name="email_id" class="form-control" type="text" value="" placeholder="Enter Your Email" required="required" onblur="$('#subscriptionemail').val($(this).val())" onkeyup="$('#subscriptionemail').val($(this).val())"></span>
								<span class="form-submit"><input type="submit" value="Subscribe" onclick="subscribe();" class="btn btn-success"></span>
							</form>
							<p></p>
						</div>
					</div>
				</div>
			</div> -->
		</div>
	</div>
</section>
@endif
