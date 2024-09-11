<div class="widget meta-boxes form-actions form-actions-default action-horizontal">
    <!-- <div class="widget-title">
        <h4><span>Calendar</span></h4>        
    </div> -->
    <!-- <div class="widget-body">
        <div id="calendar"></div>
    </div> -->
    

    <div class="row">
        <div class="col-md-3 col-lg-3">
            <div id="datepicker" style="" class="recent-tt-sc-calendar"></div>
        </div>
        <div class="col-md-9 col-lg-9">
            <div class="training-wrapper recent-training-sc-wrapper">
                <div class="flat-wrapper row" id="training_item_list">
                    @if (!$trainings->isEmpty())
                    @foreach ($trainings as $trining)
                    @php $month = strtoupper(date('M', strtotime($trining->training_start_date_time))); $date = date('d', strtotime($trining->training_start_date_time));  @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="rounded edii_workshop">
                            <div class="edii_workshop_info">
                                <div class="edii_workshop_date rounded">
                                    <p class="month">{{ $month }}</p>
                                    <p class="date">{{ $date }}</p>
                                </div>
                                <div class="edii_workshop_desc">
                                    <div>
                                        <h5><b>{{ $trining->name }}</b></h5>
                                    </div>
                                    <div class="readmore">
                                        <a style="width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" href="/training?id={{$trining->id}}" title="{{$trining->venue }}">Venue: {{ $trining->venue }}</a>
                                    </div>
                                </div>
                            </div>                
                        </div>            
                    </div>
                    @endforeach
                    @else
                    <div class="col-md-12 text-center">
                        <p class="custom-alert__message text-center"> Training program is not available for the future date </p>
                    </div>
                    @endif                    
                </div>
            </div>
        </div>
    </div>


</div>