<div class="widget-inner">
    <div class="form-group">
        <label for="current_status" class="control-label  ">Current Status</label>
        <div class="customStaticCls" id="current_status">{{ucfirst(Arr::get($data, $property))}}</div>
    </div>
    @if(!empty($transitions) && is_array($transitions))

    <div class="form-group">
        <label for="next_state" class="control-label required">Change Status</label>
        <div class="ui-select-wrapper">
            <select name="next_state" id="next_state" class="form-control ui-select">
                <option value="">Select</option>
                @foreach($transitions as $key => $trans)
                <option value="{{ $key }}" {{ $key == Arr::get($data, $property) ? ' selected' : '' }} data-input=@if(Arr::get($trans,'custom_input')) true @else false @endif>
                    {{$trans['name']}}
                </option>
                @endforeach
            </select>
            <svg class="svg-next-icon svg-next-icon-size-16">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
            </svg>
        </div>
    </div>
    @foreach($transitions as $key => $trans)
       @if($trans['custom_input'])        
        <div class='customInput' style="display: none">
            @include("plugins/workflows::partials.".$trans['custom_input'])
        </div>
       @endif
    @endforeach
    <div class="form-group">
        <label for="approver_comments" class="control-label required">Remarks</label>
        <textarea name="approver_comments" rows="1" class="form-control" id="approver_comments"></textarea>
    </div>
    @endif
    @if(!empty($histories) && is_array($histories))
    <div class="workflow_p form-group">
        <label for="current_status" class="control-label  ">Workflow</label>

        <div class="inner-widget-body">
            <ul class="timeline">

                @foreach($histories as $k => $history)
                @php $request = json_decode($history->request, 1); $liCls = ($k == 0) ? "top-text" : "more-text"; @endphp
                <li class="{{$liCls}}">
                    <div class="direction-left">
                        <div class="box-wrapper">
                            <div class="color-circle" style="background-color:#005789;"></div>
                            <div class="inner-box">
                                <span class="title">{{ucfirst($history->transition_name)}}</span>
                                <span class="time-wrapper"><span class="time">{{BaseHelper::formatTime($history->created_at)}}</span></span>
                                <div class="media">
                                    <img alt="{{ $history->user_name }}" style="height: 29px;" class="rounded-circle" src="{{ $history->avatar_url }}" />
                                    <div class="media-body">
                                        <h5 class="m-0">{{$history->user_name}}</h5>
                                        <span class="user-desg">{{$history->roles}}</span>
                                    </div>
                                </div>
                                <span class="flow-status" style="background-color:#005789;">
                                    <span>{{$history->transition_state}}</span></span>

                                <div class="show-more-area mt-2 border-top">
                                    @if(Arr::get($request,'custom_input.req'))
                                        @foreach($request['custom_input']['req'] as $key => $value)
                                            @if($key == "attachment")
                                                <div class="inner-head mt-2">{{ucfirst($key)}}</div>
                                                <div class="inner-content pt-1"><a href="/storage/{{$value}}" target="_blank">{{$value}}</a></div>
                                            @else
                                                <div class="inner-head mt-2">{{ucfirst($key)}}</div>
                                                <div class="inner-content pt-1">{{$value}}</div>
                                            @endif
                                        @endforeach
                                        
                                    @endif
                                    <div class="inner-head mt-2">Remarks</div>
                                    <div class="inner-content pt-1" title='{{Arr::get($request, 'remarks')}}'>{{Arr::get($request, 'remarks')}}</div>
                                </div>
                                <a class="showless-button" href="#">Show More</a>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>

<div class="">
    @if(count($histories)> 1)
    <a class="moreless-button" href="#">All History</a>
    @endif
    @if(!empty($transitions) && is_array($transitions))
    <div style="margin-top: 10px;">
        <a href="{{ URL::current() }}" class="btn btn-default resetBtn">{{ trans('core/table::table.cancel') }}</a>
        <button type="button" class="btn btn-info btn-workflow-apply" data-id="{{Arr::get($data, 'id')}}" data-class="{{$className}}">Submit</button>
    </div>
    @endif
</div>


<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(function() {
            $(window).off('beforeunload');
        }, 100)

        $(document).on('click', '.btn-workflow-apply', function() {
            let data = {
                id: $(this).data('id'),
                class: $(this).data('class'),
                change_status: $('#next_state').val(),
                remarks: $('#approver_comments').val()
            };
            let errorObj = [];
            $.each($(document).find('#workflow_wrap .form-group input,#workflow_wrap .form-group select,#workflow_wrap .form-group textarea'), function (i, v) {
                if(!$(v).val() && $(v).is(":visible")){
                let label=$(v).prev('label').text();
                label=(label)?label:$(v).parent().prev().text();
                errorObj.push({key:$(document).find(v), text: label+' field is required.'});
                // return false;
                }
                data[$(v).attr('name')] = $(v).val();
            
            });

            console.log(errorObj);
            $('#workflow_wrap .invalid-feedback').hide();

            if(CustomScript.isValidArray(errorObj)) {               
                CustomScript.showValidationError(errorObj[0]['key'],errorObj[0]['text']);
                return false;
            }

            // return false;

            if(!CustomScript.isValidArray(errorObj)) {
            $.ajax({
                url: '/workflows/apply_workflow',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                dataType: 'json',
                success: res => {
                    if (res.error) {
                        Impiger.showError(res.message)
                    } else {
                        if(res.data && res.data.previous_url){
                            location.href=res.data.previous_url;
                        }else{
                           location.reload();
                        }                        
                        Impiger.showSuccess(res.message);
                    }
                },
                error: data => {
                    var error = eval("(" + data.responseText + ")");
                    Impiger.showError(error.message)
                }
            });
        }
        })

        $('.moreless-button').click(function() {
            $('.more-text').slideToggle();
            if ($('.moreless-button').text() == "All History") {
                $(".top-text").addClass("all-list");
                $(this).text("Show Less")
            } else {
                $(this).text("All History")
                $(".top-text").removeClass("all-list");
            }
        });

        $('.showless-button').click(function() {
            let parentEl = $(this).parents('.inner-box:first');
            parentEl.find('.show-more-area').slideToggle();
            if (parentEl.find('.showless-button').text() == "Show More") {
                $(this).text("Show Less")
            } else {
                $(this).text("Show More")
            }
        });
        
        $(document).on('change','#next_state',function(){
           let isCustomInputs =$('#next_state option:selected').attr("data-input");
           if(isCustomInputs === "true"){
               $('.customInput').show();
           }else{
               $('.customInput').hide();
           }
        });
        $(document).find("#workflow_wrap .image-box-actions, .btn_remove_image").css("display","block")
        $(document).find("#workflow_wrap .image-box").css("pointer-events","unset")

    })
</script>