<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
    <a href="javascript:;" class="dropdown-toggle dropdown-header-name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="icon-envelope-open"></i>
        <span class="badge badge-default"> {{ count($auditHistories) }} </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
        <li class="external">
            <h3>{!! clean(trans('plugins/audit-log::history.new_msg_notice', ['count' => count($auditHistories)])) !!}</h3>
            <a href="{{ route('audit-log.notification') }}">{{ trans('plugins/audit-log::history.view_all') }}</a>
            
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: {{ count($auditHistories) * 70 }}px;" data-handle-color="#637283">
                @foreach($auditHistories as $auditHistory)
                    <li>   
                        <a href="{{ route('audit-log.notification', ['arg1'=>$auditHistory->id,'arg2'=>$auditHistory->reference_id]) }}"> 
                           <span class="subject" style="position:relative;">
                                <img alt="{{ $auditHistory->user->user_name }}" style="height: 29px;margin-left: -50px;position: absolute;" class="rounded-circle" src="{{ $auditHistory->user->avatar_url }}" title="{{$auditHistory->user->name}}" />
                                <span class="from"> {{ \App\Utils\CrudHelper::formatEntityValue($auditHistory->crud_id,$auditHistory->reference_id) }} </span><span class="time">{{ BaseHelper::formatTime($auditHistory->created_at) }} </span></span>
                            <span class="message"> {{ $auditHistory->action }} </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
</li>
