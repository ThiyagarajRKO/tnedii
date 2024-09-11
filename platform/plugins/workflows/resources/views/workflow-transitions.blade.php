@if(!empty($transitions) && is_array($transitions))
<div class="btn-group" role="group">
    <div class="dropdown">
        <button class="btn btn-success dropdown-toggle js-rv-media-change-filter-group" type="button" data-toggle="dropdown">
            <i class="fa"></i> {{ ucfirst($item->{$property}) }} <span class="js-rv-media-filter-current"></span>
        </button>
        <ul class="dropdown-menu">
            @foreach ($transitions as $transition)
            @php
                $customInput = "";
                $transitionMetadata = $workflow->getMetadataStore()->getTransitionMetadata($transition); // transition object
                if(!empty($transitionMetadata) && \Arr::get($transitionMetadata,'workflow_input')){
                    $customInput = json_encode($transitionMetadata['workflow_input']);
                }
            @endphp
                <li>
                    <a href="#" class="apply-workflow-process" data-class="{{get_class($item)}}" data-id="{{ $item->id }}" data-value="{{$transition->getName()}}" data-from = "{{ucfirst($transition->getFroms()[0])}}"
                       data-to = "{{ucfirst($transition->getTos()[0])}}" data-custom_input = "{{$customInput}}">
                        <i class="fa"></i> {{ucfirst($transition->getTos()[0])}}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@elseif(!empty($transitions) && !is_array($transitions))
@php $labelClass = Arr::get($labelClass,$transitions) ? : 'label-default' @endphp
<span class="{{$labelClass}} status-label">{{ucfirst($transitions)}}</span>
@else
@php $labelClass = Arr::get($labelClass,$item->{$property}) ? : 'label-default' @endphp
<span class="{{$labelClass}} status-label">{{ucfirst($item->{$property})}}</span>
@endif