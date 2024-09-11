
    
    <div class="row institution-wrap">
        @if (!$trainings->isEmpty())
        @foreach ($trainings as $training)
        @php $month = strtoupper(date('M', strtotime($training->training_start_date_time))); $date = date('d', strtotime($training->training_start_date_time));  @endphp
        <div class="col-md-6 col-lg-4">
            <div class="rounded edii_workshop">
                <div class="edii_workshop_info">
                    <div class="edii_workshop_date rounded">
                        <p class="month">{{ $month }}</p>
                        <p class="date">{{ $date }}</p>
                    </div>
                    <div class="edii_workshop_desc">
                        <div>
                            <h5 title="{{ $training->name }}" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;"><b>{{ $training->name }}</b></h5>
                        </div>
                        <div class="readmore">
                            <a style="width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" href="/training?id={{$training->id}}" title="{{$training->venue }}">Venue: {{ $training->venue }}</a>
                        </div>
                    </div>
                </div>                
            </div>            
        </div>
        @endforeach

    </div>
    <div class="row page-pagination text-right pt-20" id="institute_pagination">
        {!! $trainings->withQueryString()->links() !!}
    </div>
</div>
@else
<p class="custom-alert__message text-center"> Training not available for the selected criteria </p>
@endif

