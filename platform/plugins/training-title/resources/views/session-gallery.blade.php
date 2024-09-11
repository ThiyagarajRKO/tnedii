<div class="table-wrapper">
    <div class="row">
        <div class="col-md-12">
            
        </div>
        <div class="col-md-12">
            @if($sessions && count($sessions) > 0 && $isMsmeCandidate) 
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @php $index = 1; @endphp
                @foreach($sessions as $key => $session) 
                
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ ($index == 1) ? 'active' : '' }}" id="{{$key}}-tab" data-toggle="tab" data-target="#{{$key}}" type="button" role="tab" aria-controls="home" aria-selected="{{ ($index == 1) ? 'true' : 'false' }}">{{Arr::get($sessionHeaders, $key)}}</button>
                </li>
                @php $index++; @endphp
                @endforeach
            </ul>
            <div class="tab-content" id="myTabContent">
                @php $index = 1; @endphp
                @foreach($sessions as $key => $session)
                <div class="tab-pane fade show {{ ($index == 1) ? 'active' : '' }}" id="{{$key}}" role="tabpanel" aria-labelledby="{{$key}}-tab">
                    <div class="row row-cols-1 row-cols-md-3">
                        @foreach($session as $k => $item)
                        <div class="col mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item['title'] }}</h5>
                                    <p class="card-text">{{ $item['sub_title'] }}</p>
                                </div>
                                <div class="card h-100">
                                    <iframe src="{{ $item['url'] }}" frameborder="0" allowfullscreen="allowfullscreen" style="height:275px"></iframe>
                                </div>
                            </div> 
                        </div> 
                        @endforeach                       
                    </div>
                </div>
                @php $index++; @endphp
                @endforeach
            </div>
            @else 
            <div class="text-center">
                <!-- <p>You are not eligible for online sessions</p> -->
                <div class="alert alert-danger alert-dismissable custom-success-box">
                    <!-- <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> -->
                    <strong> You are not eligible for online sessions </strong>
                </div>
            </div>
            @endif
        </div>
    </div>        
    
</div>


