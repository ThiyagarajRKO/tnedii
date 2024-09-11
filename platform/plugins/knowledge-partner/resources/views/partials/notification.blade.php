<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
    <a href="javascript:;" class="dropdown-toggle dropdown-header-name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="icon-envelope-open"></i>
        <span class="badge badge-default"> {{ count($knowledge_partners) }} </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
        <li class="external">
            <h3>{!! clean(trans('plugins/knowledge-partner::knowledge-partner.new_msg_notice', ['count' => count($knowledge_partners)])) !!}</h3>
            <a href="{{ route('knowledge-partners.index') }}">{{ trans('plugins/knowledge-partner::knowledge-partner.view_all') }}</a>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: {{ count($knowledge_partners) * 70 }}px;" data-handle-color="#637283">
                @foreach($knowledge_partners as $knowledge_partner)
                    <li>
                        <a href="{{ route('knowledge-partners.edit', $knowledge_partner->id) }}">
                            <span class="photo">
                                <img src="{{ (new \Impiger\Base\Supports\Avatar)->create($knowledge_partner->name)->toBase64() }}" class="rounded-circle" alt="{{ $knowledge_partner->name }}">
                            </span>
                            <span class="subject"><span class="from"> {{ $knowledge_partner->name }} </span><span class="time">{{ Carbon\Carbon::parse($knowledge_partner->created_at)->toDateTimeString() }} </span></span>
                            <span class="message"> {{ $knowledge_partner->phone }} - {{ $knowledge_partner->email }} </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
</li>
