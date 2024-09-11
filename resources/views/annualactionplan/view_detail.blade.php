@php 
$url = '/admin/training-titles/viewdetail/'.$trainings[0]['id'];
$method = "POST";
/* $url = '/admin/training-titles/subscribe-to-event'.$trainings[0]['id']; */
$buttonTxt = "Apply";
$buttonIcon = "fa fa-check-circle";

if($trainings[0]['fee_paid'] == 2 && $trainings[0]['fee_amount']){
    $buttonTxt = "Subscribe - ".$trainings[0]['fee_amount']; 
    $buttonIcon = "fa fa-money"; 
    $method = "GET";
    if(Auth::id()) {
        $url = '/razorpay-payment-view?amount='.$trainings[0]['fee_amount'].'&id='.$trainings[0]['id'];
    } else {
        $url = '/admin/training-titles/viewdetail/'.$trainings[0]['id'];
    }
}   


if($trainings[0]['fee_paid'] == 1 && !isset($trainings[0]['entrepreneur_id']) && !isset($trainings[0]['trainee_id'])){
        /*
        if(Auth::id()) {
            $url = '/admin/training-titles/subscribe-to-event/'.$trainings[0]['id'];
        }
        */
        $method = "GET";
        $buttonTxt = "Apply";
        $buttonIcon = "fa fa-check-circle";
}
if(isset($trainings[0]['entrepreneur_id']) && isset($trainings[0]['trainee_id'])){
    $buttonTxt = "Apply";
    $buttonIcon = "fa fa-check-circle";
    $method = "GET";
    $url = '/admin/training-titles/viewdetail/'.$trainings[0]['id'];
}
@endphp     

<!-- http://tnediimis.com/admin/subscribe-to-event -->

<!-- <pre>
    {{$trainings[0]}}
</pre> -->

    <form method="{{$method}}" action="{{$url}}" accept-charset="UTF-8" id="impiger-training-title-forms-training-title-form" class="viewForm" novalidate="novalidate">    
        <div class="row">

            <div class="col-md-12">

                <div class="main-form">
                    <div class="form-body">
                        <div class="row">
                            
                            <div class="col-md-2"></div>
                            <div class="col-md-8 grouppedLayout training_title">
                                <!-- @if($errors->any())
                                    {{ implode('', $errors->all('<div>:message</div>')) }}
                                @endif -->
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-danger alert-dismissable custom-success-box">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                            <strong> {{ $error }} </strong>
                                        </div>
                                    @endforeach
                                @endif
                                <fieldset>
                                    <div class="row">
                                        @if($method == 'POST')                 
                                        <input type="hidden" name="division_id" value="{{$trainings[0]['division_id']}}" />
                                        <input type="hidden" name="financial_year_id" value="{{$trainings[0]['financial_year_id']}}" />
                                        <input type="hidden" name="annual_action_plan_id" value="{{$trainings[0]['annual_action_plan_id']}}" />
                                        <input type="hidden" name="training_title_id" value="{{$trainings[0]['id']}}" />
                                        <input type="hidden" name="user_id" value="{{Auth::id()}}" />
                                        <input name="_token" type="hidden" value="{{csrf_token()}}">   
                                        @endif   
                                        <div class="form-group col-md-4">
                                            <label for="annual_action_plan_id" class="control-label ">Training/Workshop/Program Name</label>
                                            <div class="customStaticCls" id="annual_action_plan_id">{{$trainings[0]['name']}}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="venue" class="control-label ">Venue</label>
                                            <div class="customStaticCls" id="venue">{{ $trainings[0]['venue'] }}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="code" class="control-label ">Code</label>
                                            <div class="customStaticCls" id="code">{{$trainings[0]['code']}}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="email" class="control-label ">Contact Email</label>
                                            <div class="customStaticCls" id="email">{{$trainings[0]['email']}}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="phone" class="control-label ">Contact Phone</label>
                                            <div class="customStaticCls" id="phone">{{$trainings[0]['phone']}}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="fee_paid" class="control-label ">Training Module</label>
                                            <div class="customStaticCls" id="fee_paid">{{ $trainings[0]['training_module'] }}</div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="fee_paid" class="control-label ">Training Version</label>
                                            <div class="customStaticCls" id="fee_paid">{{ $trainings[0]['training_version'] }}</div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="fee_paid" class="control-label ">Training Fee Amount</label>
                                            <div class="customStaticCls" id="fee_paid">{{ $trainings[0]['fee_amount'] }}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="private_workshop" class="control-label ">Private Workshop</label>
                                            <div class="customStaticCls" id="private_workshop">{{$trainings[0]['private_workshop']}}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="training_start_date" class="control-label ">Training Start Date</label>
                                            <div class="customStaticCls" id="training_start_date">{{ $trainings[0]['start'] }}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="training_end_date" class="control-label ">Training End Date</label>
                                            <div class="customStaticCls" id="training_end_date">{{ $trainings[0]['end'] }}</div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="duration" class="control-label ">Duration</label>
                                            <div class="customStaticCls" id="duration">{{ $trainings[0]['duration'] }}</div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="webinar_link" class="control-label ">Webinar Link</label>
                                            <div class="customStaticCls" id="webinar_link"></div>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="small_content" class="control-label ">Small Content</label>
                                            <div class="customStaticCls" id="small_content">{{ $trainings[0]['small_content'] }}</div>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="description" class="control-label ">Description</label>
                                            <div class="customStaticCls" id="description">{{ $trainings[0]['description'] }}</div>
                                        </div>

                                        <span class="layoutDisplayType" data-display_type="vertical"></span> 

                                    </div>
                                </fieldset>
                                <div class="meta-boxes form-actions form-actions-reset form-actions-default action-horizontal mt-10">

                                    <div class="widget-body form-actions-fixed-bottom">
                                        <div class="btn-set mt-0 text-right">
                                            &nbsp;
                                            <button type="button" class="btn btn-default cancelBtn" onclick="window.history.back()" >
                                                Cancel
                                            </button>                                       
                                            @if(isset($trainings[0]['entrepreneur_id']) && isset($trainings[0]['trainee_id']))
                                            <button type="submit" name="submit" value="save" class="btn btn-success">
                                                <i class="{{$buttonIcon}}"></i> {{$buttonTxt}} 
                                            </button>
                                            @else
                                            <a href="{{$url}}" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Payment Gateway"><i class="{{$buttonIcon}}"></i> {{$buttonTxt}} </a>
                                            @endif
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>

                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>
                
            </div>

            <div class="col-md-3 right-sidebar"></div>

        </div>
    </form> 
</div>
