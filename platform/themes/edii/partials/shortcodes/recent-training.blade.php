
<script src="//cdnjs.cloudflare.com/ajax/libs/jQuery.Marquee/1.5.0/jquery.marquee.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/moment@5.11.3/main.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/icalendar@5.11.3/main.global.min.js"></script>
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css" rel="stylesheet" type="text/css">
<link href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css" rel="stylesheet" type="text/css">
@php
$all_traning = render_recent_trainings();
//echo('<pre>');
//print_r($all_traning);
//echo('</pre>');
@endphp
<script>
$('.marquee_text').marquee({
    direction: 'left',
    duration: 90000,
    gap: 0,
    delayBeforeStart: 0,
    duplicated: true,
    startVisible: true
});
/*********** FULL CALENDAR ***************/
    document.addEventListener('DOMContentLoaded', function() {
		var calendarEl = document.getElementById('calendar');

		var calendar = new FullCalendar.Calendar(calendarEl, {
			headerToolbar: {
				start: '',
				center: '',
				end: 'prev,title,next'
			},
			timeZone: 'Asia/India',
			weekNumbers: false,
			initialView: 'dayGridMonth',
			eventContent: function( arg ) {
				return { html: arg.event.title };
			},
			/* eventTimeFormat: {
				hour: '2-digit',
				minute: '2-digit',
				hour12: false
			},
			views: {
				dayGridWeek: {
				titleFormat: '{DD.{MM.}}YYYY'
			},
			listWeek: {
				titleFormat: '{DD.{MM.}}YYYY'
			}*/
			@if (!empty($all_traning))
			events: [
				@foreach ($all_traning as $training)
				{
					title: '<div class="each-event" style="background-image:url({{ RvMedia::getImageUrl($training->training_background_image_name, '', false, RvMedia::getDefaultImage()) }})"><div class="event-inner"><a href="{{$training->training_gallery_url_en}}" class="gal">View Gallery</a><h3><a href="/training?id={{$training->id}}" style="color:inherit;font-weight:bold">{{$training->name}}</a></h3><h4>VENUE : {{$training->venue}}</h4></div></div>',
					start: '{{ $training->training_start_date}}',
					kind: '{{ $training->fee_paid == 1 ? "free" : "paid" }}',
					state: 'all'
				},
				@endforeach
			],
			@endif
			eventClassNames: function(info) {
				var result = "hidden";
				/*var states = [];
				var kinds = [];
				// Find all checkbox that are event filters and enabled and save the values.
				$("input[name='event_filter_sel']:checked").each(function () {
					// Saving each type separately
					if ($(this).data('type') == 'state') {
						states.push($(this).val());
					}
					else if ($(this).data('type') == 'kind') {
						kinds.push($(this).val());
					}
				});
				// If there are locations to check
				if (states.length) {
					result = result && states.indexOf(info.event.extendedProps.state) >= 0;
					//console.log(states);
					//console.log(result);
				}
				// If there are specific types of events
				if (kinds.length) {
					result = result && kinds.indexOf(info.event.extendedProps.kind) >= 0 || info.event.extendedProps.kind == 'holiday';
				}
				if (!result) {
					result = "hidden";
				}*/
				//console.log("ALL" + $('input[name="event_filter_sel"][value="all"]').is(":checked"));
				//console.log(info.event.title);
				//console.log(info.event.extendedProps.kind);
				if($('input[name="event_filter_sel"][value="all"]').is(":checked"))
				{
				    result = true;
				}
				else if($('input[name="event_filter_sel"]:checked').val() == info.event.extendedProps.kind)
				{
				    result = true;
				}
				//console.log(info.event.title);
				console.log(result);
				return result;
			},
			windowResize: function(view) {
				var current_view = view.type;
				var expected_view = $(window).width() > 800 ? 'dayGridMonth' : 'listWeek';
				if (current_view !== expected_view) {
					calendar.changeView(expected_view);
				}
			},
		});
		calendar.render();
        $('input[name="event_filter_sel"][value="all"]').change(function() {
            if($(this).is(":checked"))
            {
                $('input[name="event_filter_sel"][value="paid"], input[name="event_filter_sel"][value="free"]').prop("checked", true);
            }
            else
            {
                $('input[name="event_filter_sel"][value="paid"], input[name="event_filter_sel"][value="free"]').prop("checked", false);
            }
            calendar.render();
        });
        $('input[name="event_filter_sel"][value="all"]').prop("checked", true).trigger("change");
        $('input[name="event_filter_sel"][value="paid"], input[name="event_filter_sel"][value="free"]').change(function() {
            if($('input[name="event_filter_sel"][value="paid"]').is(":checked") && $('input[name="event_filter_sel"][value="free"]').is(":checked"))
            {
                $('input[name="event_filter_sel"][value="all"]').prop("checked", true);
            }
            else
            {
                $('input[name="event_filter_sel"][value="all"]').prop("checked", false);
            }
            calendar.render();
        });
		if ($(window).width() < 800) {
			calendar.changeView('listWeek');
		}
	});
$(document).ready(function(){
    var weekday=new Array(7);
    weekday[0]="Sunday";
    weekday[1]="Monday";
    weekday[2]="Tuesday";
    weekday[3]="Wednesday";
    weekday[4]="Thursday";
    weekday[5]="Friday";
    weekday[6]="Saturday";
    
    var d = new Date();
    var n = weekday[d.getDay()];
    console.log(n);
    $('.cal-wrapper').addClass(n);
    $("#list-view-switch").click(function(){
        $("#home").toggleClass('show_datatable');
    })
    if($("#list-view-switch").is(":checked"))
    {
        $("#home").addClass('show_datatable');
    }
    /********** DATA TABLE *************/
    var training_datatable_list = new DataTable('#example', {
        ajax: {
            url: '/training-title-data',
            type: 'POST',
            data: function (d) {
				d._token = "{{ csrf_token() }}";
				d.selected_year = $("#selected_year").val();
				d.selected_month = $("#selected_month").val();
				//console.log(JSON.stringify(d, null, 4));
			},
			complete: function(response){
    			//console.clear();
    			//console.log(JSON.stringify(response.responseText, null, 4));
    			//console.log(response);
			},
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'training_start_date' },
            { data: 'training_end_date' },
            { data: 'venue'},
            { data: null },
            { data: 'fee_amount' },
            { data: null },
        ],
        columnDefs: [
            {
                targets: 5,
                render: function (data, type, row) {
                    //console.log(JSON.stringify(data, null, 4));
                    var return_html = (data.fee_paid == 2 ? "Paid" : "Free");
                    return return_html;
                },
            },
            {
                targets: 7,
                render: function (data, type, row) {
                    //console.log(JSON.stringify(data, null, 4));
                    var return_html = '<a href="/training?id='+data.id+'" target="_blank" style="color:#f5613b">Click Here</a>';
                    return return_html;
                },
            },
        ],
        searching: false,
        responsive: true,
        processing: true,
        serverSide: true,
        order: [[0, 'desc']],
    });
    $("#selected_year, #selected_month").change(function() {
        training_datatable_list.ajax.reload();
    });
})


</script>
<style>
.tab-area .tab-content {
    overflow: hidden;
}
.cal-wrapper.Sunday th.fc-day-sun > div, .cal-wrapper.Monday th.fc-day-mon > div, .cal-wrapper.Tuesday th.fc-day-tue > div, .cal-wrapper.Wednesday th.fc-day-wed > div, .cal-wrapper.Thursday th.fc-day-thu > div, .cal-wrapper.Friday th.fc-day-fri > div, .cal-wrapper.Saturday th.fc-day-sat > div {
    background: #f55f3c;
    color: #fff;
    border-radius: 5px;
    margin: 4px;
}
th .fc-scrollgrid-sync-inner {
    margin: 4px;
}
.cal-wrapper{
    padding:0px 15px;
}
.fc-toolbar-chunk > div{
    display:table;
    border:1px solid #ccc;
    border-radius:5px;
}
.fc-toolbar-chunk > div > button{
    display:table-cell;
    border:0px !important;
    background:none !important;
    outline:none !important;
}
.fc-toolbar-chunk > div > h2{
    display:table-cell;
    padding:0px 30px !important;
    font-size:15px !important;
    position:relative;
    bottom:-2px;
    line-height:45px;
}
.fc .fc-button .fc-icon {
    background: #001736;
    border-radius:50%;
    overflow:hidden;
    font-size: 15px;
}
.fc .fc-button-primary {
    background:none;
}
.filter {
    display: table;
    width: calc(100% - 239px);
    position: relative;
    margin-bottom: -41px;
}
.filter > div{
    display:table-cell;
   vertical-align:middle;
   white-space:nowrap;
   width:100px;
}
.filter > div:first-child{
    width:66%;
}
.filter > div input[type=checkbox], input[type=radio] {
    height: 16px;
    width: 16px;
    position: relative;
    bottom: -3px;
    margin-right: 4px;
}
.fc-theme-standard td{
    background: #f8f9fd;
}
.fc-theme-standard th {
    background: #fff;
}
.fc-scrollgrid-sync-inner{
    padding:10px 0px;
}
.each-event{
    display: table;
    height: 110px;
    background-color: #f8f9fd;
    white-space: break-spaces;
    margin: -1px;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    position:relative;
}
.each-event > div{
    display: table-cell;
    vertical-align:bottom;
    padding:10px;
    background: rgb(248,249,253);
   
}
.each-event > div:hover{
    background: linear-gradient(0deg, rgba(248,249,253,1) 0%, rgba(9,9,121,0) 82%);
}
.each-event h3{
    font-size: 11px;
    font-weight: bold;
    color: #012679;
}
.each-event h4{
    font-size: 11px;
    font-weight: bold;
    color: #f6603b;
    margin-bottom:0px;
}
/*.fc .fc-daygrid-day-frame {
    height: 153px;
}*/
.gal {
    position: absolute;
    right: 5px;
    top: 5px;
    background: #012679;
    color: #fff;
    display: inline-block;
    border-radius: 5px;
    padding: 4px 6px 2px;
    opacity:0;
    transition:all .5s;
}
.gal:hover{
    color: #fff;
}
.each-event:hover .gal{
    opacity:1;
}
.cal_datatable{
    padding:0px 15px;
    display:none;
}
.cal_datatable td, .cal_datatable th{
    text-align:left !important;
}
.show_datatable .fc-view-harness-active{
    display:none;
}
.show_datatable .cal_datatable{
    display:block;
}
.show_datatable .filter{
   width:100%;
}
.filter > div:nth-child(5){
    display:none !important;
}
.show_datatable .filter > div:nth-child(2), .show_datatable .filter > div:nth-child(3), .show_datatable .filter > div:nth-child(4){
    display:none;
}
.show_datatable .filter > div:nth-child(5){
    display:table-cell !important;
}
.show_datatable .fc-header-toolbar{
   pointer-events: none;
   opacity:0;
}
.month-andyear{
    display:table;
    float: right;
}
.month-andyear > div{
    display:table-cell;
}
.month-andyear > div select{
    background:none;
    border:1px solid #ccc;
    border-radius:5px;
    padding:10px 15px;
    margin-left:30px;
    font-size: 18px;
    font-weight: bold;
}
div.dt-container select.dt-input {
    padding: 4px;
    border: 1px solid #ccc;
    padding: 8px;
    margin-right: 15px;
}
.breaking-news-title{
    display:none;
}
#breaking-news-container:before {
    display:none;
}
.breaking-news-headline {
    margin-left: 0px;
}
.fc-scrollgrid-sync-inner {
    padding: 0px 0px;
}
</style>
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
							<h2><strong>TRAINING CALENDAR</strong></h2>
							 <!-- START:: FULL CALENDAR -->
							<div class="cal-wrapper">
                            	<!--  Work in progress, filter not working with v5 right now  -->
                            	<div class="filter">
                            		<div class="event_filter_wrapper">
                            			<input id="list-view-switch" class="event_filter" name="event_filter_sel" type="checkbox" />
                            			<label for="list-view-switch">LIST VIEW</label>
                            		</div>
                            		<div class="event_filter_wrapper">
                            			<input id="party" class="event_filter" name="event_filter_sel"  type="checkbox" value="all" data-type="state"/>
                            			<label for="party">ALL</label>
                            		</div>
                            		<div class="event_filter_wrapper">
                            			<input id="concert" class="event_filter" name="event_filter_sel" type="checkbox" value="paid" data-type="kind"/>
                            			<label for="concert">PAID</label>
                            		</div>
                            		<div class="event_filter_wrapper">
                            			<input id="festival" class="event_filter" name="event_filter_sel" type="checkbox" value="free" data-type="kind"/>
                            			<label for="festival">FREE</label>
                            		</div>
                            		<div class="event_filter_wrapper">
                            			<div class="month-andyear">
                                    	 <div id="months">
                                    	     <select name="selected_month" id="selected_month">
                                    	         <option value="01" @php echo(date("m") == "01" ? 'selected="selected"' : ''); @endphp>January</option>
                                    	         <option value="02" @php echo(date("m") == "02" ? 'selected="selected"' : ''); @endphp>February</option>
                                    	         <option value="03" @php echo(date("m") == "03" ? 'selected="selected"' : ''); @endphp>March</option>
                                    	         <option value="04" @php echo(date("m") == "04" ? 'selected="selected"' : ''); @endphp>April</option>
                                    	         <option value="05" @php echo(date("m") == "05" ? 'selected="selected"' : ''); @endphp>May</option>
                                    	         <option value="06" @php echo(date("m") == "06" ? 'selected="selected"' : ''); @endphp>June</option>
                                    	         <option value="07" @php echo(date("m") == "07" ? 'selected="selected"' : ''); @endphp>July</option>
                                    	         <option value="08" @php echo(date("m") == "08" ? 'selected="selected"' : ''); @endphp>August</option>
                                    	         <option value="09" @php echo(date("m") == "09" ? 'selected="selected"' : ''); @endphp>September</option>
                                    	         <option value="10" @php echo(date("m") == "10" ? 'selected="selected"' : ''); @endphp>October</option>
                                    	         <option value="11" @php echo(date("m") == "11" ? 'selected="selected"' : ''); @endphp>November</option>
                                    	         <option value="12" @php echo(date("m") == "12" ? 'selected="selected"' : ''); @endphp>December</option>
                                    	     </select>
                                    	 </div>
                                    	  <div id="year">
                                    	        <select name="selected_year" id="selected_year">
                                        	        @php
                                        	            $current_year = date("Y");
                                        	            $start_year = $current_year - 20;
                                        	            $end_year = $current_year + 20;
                                        	            for($i = $start_year; $i <= $end_year; $i++)
                                        	            {
                                        	                echo '<option value="' . $i . '" ' . ($current_year == $i ? 'selected="selected"' : '') . '>' . $i . '</option>';
                                        	            }
                                        	        @endphp
                                    	        </select>
                                    	  </div>
                                    	</div>
                            		</div>
                            	</div>
                            	<div id="calendar"></div>
                            </div>
                            <!-- END:: FULL CALENDAR -->
                            <!-- Start:: DATA TABLE -->
                            <div class="cal_datatable">
                                <table id="example" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Training Title</th>
                                            <th>Training Start Date</th>
                                            <th>Training End Date</th>
                                            <th>Venue</th>
                                            <th>Free Type</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- END:: DATA TABLE -->
                            
						</div>
						<div role="tabpanel" class="tab-pane" id="profile">
							<h2>Latest News & Events</h2>
							<div id="owl-example" class="owl-carousel tab-carousel">
							    @if (function_exists('get_post_by_category_name'))
                                @php
                                    $review_posts = get_post_by_category_name("Default", 0, 0, 'DESC');
                                    //print_r($review_posts);
                                @endphp
                                @if (!empty($review_posts))
								@foreach($review_posts as $key => $review)
								<div>
								    <a href="{{ $review->url }}">
									<div class="each-date-box d-flex flex-column justify-content-between">
										<div class="box-top-content" style="height: 150px; word-wrap: break-word;">
											<div class="date d-flex justify-content-between align-items-center">
												<div class="row">
													<div class="col-xs-6" >
														<h3>{{ $review->created_at->format('d') }}</h3>
													</div>
													<div class="col-xs-6 text-right" >
														<h5>{{ $review->created_at->format('M Y') }}</h5>
													</div>
												</div>
											</div>
											<p>{{ $review->name }}</p>
										</div>
										<div class="box-button-area text-right">
											<a href="{{ $review->url }}">Readmore</a>
										</div>
									</div>
									</a>
								</div>
								@endforeach
								@endif
								@endif
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
