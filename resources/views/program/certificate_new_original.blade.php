<html>
    <head>
        
        <!-- <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="widtd=device-widtd, initial-scale=1.0"> -->
        <title>TNEDII</title>
        <link rel="stylesheet" href="{{public_path('/css/bootstrap.min.css')}}" id="bootstrap" />
        <link rel="stylesheet" href="{{public_path('/css/bootstrap-grid.css')}}" id="grid" />
        <link rel="stylesheet" type="text/css" href="{{public_path('/css/print.css')}}" id="print" />

        <style>
            .clear {
                clear: both;
            }
            .grid-col-5-percentage {
                width: 5%;
            }  
            .grid-col-10-percentage {
                width: 10%;
            }     
            .grid-col-50-percentage {
                width: 50%;
            }
            .row {
                display: flex;
            }  
			html {
				margin-top: 0px !important;
				margin-left: 0px !important;
				margin-right: 0px !important;
				margin-bottom: 0px !important;
			}
        </style>
        
    </head>
    <body style="background-image:url({{ public_path('/storage/TAHDCO EDP ERTIFICATE TEMPLATE PORTAL.jpg') }}) !important;background-size:contain; background-position:center center;border:0px solid red !important;padding:0px !important; margin:0px !important">       
			<div style="padding:0px 57px">
				<div style="height:362px;"></div>
				<div class="person" style="text-align:left; height:42px;">
					<h3 style="font-size:26px;"><span style="display:inline-block; width:215px"></span><i>{{ $value['prefix'] }}</i> <em><strong>{{ $value['entrepreneur_name'] }}</strong></em></h3>
				</div> 
				<div class="district" style="text-align:left; height:42px; font-size:22px !important; margin-top:8px">
					<span style="display:inline-block; width:565px; position:relative; margin-top:3px"><i>&nbsp;{{$value['care_of']}}</i>&nbsp; <b><em>{{ $value['care_name'] }}</em></b></span><span style="display:inline-block; margin-top:3px"><b> <em>{{ $value['district']}}</em></b></span>
				</div>
				@php
					/*if($value['scheme_name'] == 'NEEDS'){
						$value['start'] = $value['msme_start'];
						$value['end'] = date('Y-m-d', strtotime($value['start']. ' +14 days'));
					}
					if($value['scheme_name'] == 'UYEGP'){
						$value['start'] = $value['msme_start'];
						$value['end'] = date('Y-m-d', strtotime($value['start']. ' +3 days'));
					}
					if($value['scheme_name'] == 'AABCS'){
						$value['start'] = ($value['enroll_start_date']) ? $value['enroll_start_date'] : $value['msme_start'];
						$value['end'] = $value['enroll_to_date'];
					}*/
					$fdate = \Carbon\Carbon::parse($value['start'])->format('d.m.Y');
					$tdate = \Carbon\Carbon::parse($value['end'])->format('d.m.Y');
					$start_date = strtotime($fdate);
					$end_date = strtotime($tdate);
					$diff = ($end_date - $start_date)/60/60/24 + 1;
				@endphp
				<div class="dept_text" style="text-align:left;  height:42px; margin-top:-28px">
					<h2 style="font-size:22px !important;"><span style="display:inline-block; width:272px"></span> {{ $diff }} Days {{ $value['program'] }}</h2>
				</div>
				<div class="program" style="text-align:left; height:42px;">
					<h3 style="font-size:22px !important;"><span style="display:inline-block; width:80px"></span>{{ $value['remarks'] ?? $value['program'] }}</h3>
				</div>
				
				<div class="schedule" style="text-align:left;  height:42px; font-size:18px !important; margin-top:20px;">
					<span style="display:inline-block; width:40px"></span> <strong><em>From {{ \Carbon\Carbon::parse($value['start'])->format('d.m.Y') }} to {{ \Carbon\Carbon::parse($value['end'])->format('d.m.Y') }}</em></strong>
				</div>
				<div class="schedule" style="text-align:left;height:42px; font-size:18px !important; margin-top:115px;">
					<h5 style="font-size:14px !important"><span style="display:inline-block; width:140px"></span> <em>{{ $value['code'] }}</em></h5>
				</div>
			</div>               
		@php
			//if($value['scheme']){
			//	$photo = explode(".jpg",$value['photo_path']);
			//	$photo = $photo[0]."-150x150.jpg";
			//	$photo_path = '/storage/'.$photo;
			//}
			//else{
				$photo_path = '/storage/'.$value['photo_path'];
			//}
		@endphp
		<div class="profile">			
			<div class="profile-img" style="width:98px !important; height:109px !important; margin-top:91px; margin-left:24px !important; overflow:hidden; border-radius:10px;">
			    @if($value['photo_path'] != "" && file_exists(public_path($photo_path)))
				    <img src="{{ public_path($photo_path)}}" alt="" style="width:98px !important; height:109px !important; object-fit:contain !important">
				@endif
			</div>
		</div>

		<!-- <div class="division-department">
			<div style="text-align:right; margin-right: 50px !important;">
				{{-- <h3 class="">({{ $value['division_name'] }})</h3> --}}
				<h3 class="">(EDII)</h3>
			</div>                       
		</div> -->
    </body>
</html>