<script src="//cdnjs.cloudflare.com/ajax/libs/jQuery.Marquee/1.5.0/jquery.marquee.min.js"></script>
<div style="background:#fff">
    <div class="container" style="overflow:hidden; position:relative;height:30px;">
        <div id="breaking-news-container">
          <div id="breaking-news-colour" class="slideup animated">
          </div>  
           <span class="breaking-news-title delay-animated slidein">
              // BREAKING //
            </span> 
            <div class="breaking-news-headline delay-animated2 fadein marquee">
                <div class="marquee_text">
                    <ul class='mtext'>
                        <li><a href="#">'RJD will fight for reservation from streets to Parliament': Tejashwi Yadav</a></li>
                        <li><a href="#">MP: 4 children killed, 2 injured after wall of house collapses in Rewa, CM expresses grief | VIDEO</a></li>
                        <li><a href="#">Swati Maliwal assault case: 'Bibhav Kumar's arrest was necessary as per law', says Delhi High Court</a></li>
                        <li><a href="#">Yamini Krishnamurti, Padma Vibhushan Bharatanatyam legend, dies at 84</a></li>
                    </ul>
                </div>
            </div>  
        </div>  
    </div>
</div>

<style>
.breaking-news-headline {
  display: block;
  position: absolute;
  font-family: arial;
  font-size: 13px;
  margin-top: -22px;
  color: white;
  margin-left: 150px;
}

.breaking-news-title {
    background-color: #15bf5e;
    display: block;
    width: 90px;
    font-family: arial;
    font-size: 11px;
    position: absolute;
    top: 0px;
    margin-top: 0px;
    margin-left: 20px;
    padding-top: 10px;
    padding-left: 10px;
    z-index: 3;
    padding-bottom: 10px;
    color: #fff;
}
.breaking-news-title:before {
  content: "";
  position: absolute;
  display: block;
  width: 0px;
  height: 0px;
  top: 0;
  left: -12px;
  border-left: 12px solid transparent;
  border-right: 0px solid transparent;
  border-bottom: 30px solid #15bf5e;
}
.breaking-news-title:after {
  content: "";
  position: absolute;
  display: block;
  width: 0px;
  height: 0px;
  right: -12px;
  top: 0;
  border-right: 12px solid transparent;
  border-left: 0px solid transparent;
  border-top: 30px solid #15bf5e;
}

#breaking-news-colour {
  height: 30px;
  width: 100%;
  background-color: #021736;
}

#breaking-news-container {
  height: 30px;
  width: calc(100% - 30px);
  overflow: hidden;
  position: absolute;
}
#breaking-news-container:before {
  content: "";
  width: 30px;
  height: 30px;
 background-color: #021736;
  position: absolute;
  z-index: 2;
}
/******************/
.marquee_text {
    font-size: 13px;
    font-weight: bold;
    line-height: 17px;
    padding-bottom: 0px;
    background: none;
    color: #fff;
    width: 100%;
    overflow: hidden;
}
.mtext{
    list-style:none;
    margin:0px;
    padding:0px;
    display:table;
}
.mtext li{
    border-right: 1px solid #15bf5e;
    padding: 0px 35px;
    display:table-cell;
    white-space:nowrap;
    
}
.mtext li ar{
   color: #fff;
}
.mtext li a:hover{
   color: #15bf5e;
}
</style>
<script>
$('.marquee_text').marquee({
    direction: 'left',
    duration: 90000,
    gap: 0,
    delayBeforeStart: 0,
    duplicated: true,
    startVisible: true
});

</script>

@if (function_exists('render_recent_trainings'))
<section class="tab-area">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div>
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Training Programs</a></li>
						<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">News & Events</a></li>
					</ul>
					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">
							<h2>Upcoming Training Programs</h2>
							<div id="owl-example" class="owl-carousel tab-carousel">
							    @if (!empty(render_recent_trainings()))
							    @foreach (render_recent_trainings() as $training)
								<div>
								    <a href="/training?id={{$training->id}}">
									<div class="each-date-box d-flex flex-column justify-content-between">
										<div class="box-top-content" style="height: 150px; word-wrap: break-word;color: inherit;">
											<div class="date d-flex justify-content-between align-items-center">
												<div class="row">
													<div class="col-xs-6" >
														<h3>{{$training->day}}</h3>
													</div>
													<div class="col-xs-6 text-right" >
														<h5>{{$training->month}} {{$training->year}}</h5>
													</div>
												</div>
											</div>
											<p>{{ $training->name }}</p>
										</div>
										<div class="box-button-area text-right">
											<a href="/training?id={{$training->id}}" title="{{$training->venue }}">Readmore</a>
										</div>
									</div>
									</a>
								</div>
								@endforeach
								@else
								<div class="col-md-12 text-center">
									<p class="custom-alert__message text-center"> Training program is not available for the future date </p>
								</div>
								@endif   
							</div>
						</div>
						<div role="tabpanel" class="tab-pane" id="profile">
							<h2>Latest News & Events</h2>
							<div id="owl-example" class="owl-carousel tab-carousel">
								@foreach (get_latest_posts(50, [], ['slugable']) as $post)
								<div>
								    <a href="{{ $post->url }}">
									<div class="each-date-box d-flex flex-column justify-content-between">
										<div class="box-top-content" style="height: 150px; word-wrap: break-word;">
											<div class="date d-flex justify-content-between align-items-center">
												<div class="row">
													<div class="col-xs-6" >
														<h3>{{ $post->created_at->format('d') }}</h3>
													</div>
													<div class="col-xs-6 text-right" >
														<h5>{{ $post->created_at->format('M Y') }}</h5>
													</div>
												</div>
											</div>
											<p>{{ $post->name }}</p>
										</div>
										<div class="box-button-area text-right">
											<a href="{{ $post->url }}">Readmore</a>
										</div>
									</div>
									</a>
								</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


    <section class="section p-30" style="display:none;">
        
        <div class="post-group post-group--single col-md-12 pt-50 training-calendar">
            <div class="row">
                <div class="col-md-6">
                    <h4>&nbsp;<i class="fa fa-calendar" style="color: #084371;"></i><b>&nbsp;Training Programs</b></h4>
                </div>
                <div class="col-md-6">
                    <div class="pull-right training-view-all" style="float:right;">
                        <a href="/training-list"><b>View All</b></a>
                    </div>
                </div>
            </div>

            {!! render_recent_training_title($limit ? $limit : 8) !!}
            
        </div>
    </section>
@endif

@php
Theme::asset()->container('footer')->writeScript('customScript', "var ImpigerVariables = {};"
            . "                                       ImpigerVariables.languages = {
            notices_msg: ".json_encode(trans('core/base::notices'), JSON_HEX_APOS)." ,
        };      ;");
Theme::asset()
    ->container('footer')
    ->usePath(false)    
    ->add('theme-form2-js', asset('vendor/core/core/base/libraries/bootstrap-datepicker/js/bootstrap-datepicker.min.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form5-js', asset('vendor/core/plugins/crud/js/crud_utils.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form7-js', asset('vendor/core/core/base/js/core.js'), ['jquery'], [], '1.0.0')    
    ->add('training-events-js', asset('vendor/core/core/base/js/training_events.js'), ['jquery'], [], '1.0.0');
Theme::asset()
    ->usePath(false)
    ->add('theme-form4-css', asset('vendor/core/core/base/libraries/bootstrap-datepicker/css/bootstrap-datepicker3.min.css'), [], [], '1.0.0')
     ->add('core-css', asset('vendor/core/core/dashboard/css/dashboard.css'), [], [], '1.0.0')
    ->add('theme-form5-css', asset('vendor/core/plugins/crud/css/module_custom_styles.css'), [], [], '1.0.0');
@endphp

<div style="background:#fff; padding-bottom:70px;">
<div class="container">
    <div class="row">
        <div class="col-md-12" style="margin-bottom:30px;">
            <div class="div1 eachdiv" style="min-height:0px;">
                <div class="userdetails">
                  <div class="imgbox">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fd/Hon_CM_Photo.jpg" alt="">
                  </div>
                  <div class="detbox">
                    <p class="name">Thiru. M.K. Stalin</p>
                    <p class="designation">Chief Minister of Tamil Nadu</p>
                  </div>
                </div>
                <div class="review">
                  <h4>CM Message</h4>
                <p>“ Gujarat was the first state to realize the importance of entrepreneurship.  A need was felt for a national entrepreneurship development organization to promote to concept of entrepreneurship across the country and EDI was established in 1983 as a national resources organization in entrepreneurship education, research and training.  ”</p>
                </div>
              </div>    
        </div>
        <div class="col-md-6">
            <div class="div1 eachdiv">
                <div class="userdetails">
                  <div class="imgbox">
                    <img src="https://www.fiten.org/wp-content/uploads/2022/12/anbarasan_line_rem.jpg" alt="">
                  </div>
                  <div class="detbox">
                    <p class="name">Thiru T.M Anbarasan</p>
                    <p class="designation">Minister for Micro,Small and Medium Enterprises</p>
                  </div>
                </div>
                <div class="review">
                  <h4>President's Message</h4>
                <p>“Entrepreneurship awareness must Permeate into folds of every section of society;  and one way of achieving this is by disseminating its advantages through the medium of training and education. EDII understands this well its success stories are the testimonies of this phenomenan.”</p>
                </div>
              </div>    
        </div>
        <div class="col-md-6">
            <div class="div1 eachdiv">
                <div class="userdetails">
                  <div class="imgbox">
                    <img src="https://www.tnpowerfinance.com/assets/images/directors/CMD.jpg" alt="">
                  </div>
                  <div class="detbox">
                    <p class="name">Thiru. R. Ambalavanan, I.A & A.S</p>
                    <p class="designation">Director, EDII-TN</p>
                  </div>
                </div>
                <div class="review">
                  <h4>Director  Message</h4>
                <p>“Well-groomed and trained entrepreneurs have opportunities galore and immense options to make it big. So, for anyone who decides to pursue entrepreneurship in the present times, it would be a rewarding decision. The prospects for first generation entrepreneurs are indeed brilliant. And for this it is important to create awareness about learning entrepreneurship like any other discipline and adopting it as a career.”</p>
                </div>
              </div>    
        </div>
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
