@if ($knowledge_partner)
    <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.time') }}: <i>{{ $knowledge_partner->created_at }}</i></p>
    <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.full_name') }}: <i>{{ $knowledge_partner->name }}</i></p>
    <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.email') }}: <i><a href="mailto:{{ $knowledge_partner->email }}">{{ $knowledge_partner->email }}</a></i></p>
    <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.phone') }}: <i>@if ($knowledge_partner->phone) <a href="tel:{{ $knowledge_partner->phone }}">{{ $knowledge_partner->phone }}</a> @else N/A @endif</i></p>
    <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.address') }}: <i>{{ $knowledge_partner->address ? $knowledge_partner->address : 'N/A' }}</i></p>
    <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.subject') }}: <i>{{ $knowledge_partner->subject ? $knowledge_partner->subject : 'N/A' }}</i></p>
    <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.content') }}:</p>
    <pre class="message-content">{{ $knowledge_partner->content ? $knowledge_partner->content : '...' }}</pre>
@endif
