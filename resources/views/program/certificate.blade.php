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
            
        </style>
        
    </head>
    <body>
        <section class="main-section">
           <div class="wrapper">
                <div class="container">
                    <div class="header row">
                        <div class="header-container">
                            {{-- <img src="{{ asset('/storage/image-1-1-1.png') }}" alt=""> --}}
                            <img src="{{ public_path('/storage/image-certificate-header.png') }}" alt="">
                        </div>
                    </div>
                   
                    <div class="marquee">
                        Awards this Certificate to
                    </div>
        
                    <div class="person">
                        <h3><i>{{ $value['prefix'] }}</i> {{ $value['entrepreneur_name'] }}</h3>
                    </div>

                    <div class="care_of">
                        <i>{{$value['care_of']}}</i> <b>{{ $value['care_name'] }}</b>
                    </div>

                    <div class="district">
                        <i>of </i><b> {{ $value['district']}} District</b>
                    </div>

                    <div class="completion">
                        <i>on successful completion of</i>
                    </div>

                    <div class="dept_text">
                        <h2>Entrepreneurship Development Programme</h2>
                    </div>
                    
                    <div class="under">
                        <i>under</i>
                    </div>

                    <div class="program">
                        <h3>"{{ $value['program'] }}"</h3>
                    </div>
                    @php
                        if($value['scheme_name'] == 'NEEDS'){
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
                        }
                    @endphp
                    <div class="schedule">
                        held from {{ \Carbon\Carbon::parse($value['start'])->format('d.m.Y') }} to {{ \Carbon\Carbon::parse($value['end'])->format('d.m.Y') }}
                    </div>

                </div>
                @php
                    if($value['scheme']){
                        $photo = explode(".jpg",$value['photo_path']);
                        $photo = $photo[0]."-150x150.jpg";
                        $photo_path = '/storage/'.$photo;
                    }
                    else{
                        $photo_path = '/storage/'.$value['photo_path'];
                    }
                @endphp
                <div class="profile">
                    <h5>{{ $value['candidate_msme_ref_id'] ?? $value['code'] }}</h5>
                    <div class="profile-img">
                        <img src="{{ public_path($photo_path)}}" alt="">
                    </div>
                </div>

                <div class="division-department">
                    <div style="text-align:right; margin-right: 50px !important;">
                        {{-- <h3 class="">({{ $value['division_name'] }})</h3> --}}
                        <h3 class="">(EDII)</h3>
                    </div>                       
                </div>
           </div>
            
            <footer>
                <div>Note: This Certificate is computer generated and doesn't require any Seal/Signature in original. The
                    authenticity of this certificate can be verified at www.editn.in</div>
            </footer>
        </section>
        
    </body>
</html>