@if ($knowledge_partner)
    <div id="reply-wrapper">
        @if (count($knowledge_partner->replies) > 0)
            @foreach($knowledge_partner->replies as $reply)
                <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.time') }}: <i>{{ $reply->created_at }}</i></p>
                <p>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.content') }}:</p>
                <pre class="message-content">{!! clean($reply->message) !!}</pre>
            @endforeach
        @else
            <p>{{ trans('plugins/knowledge-partner::knowledge-partner.no_reply') }}</p>
        @endif
    </div>

    <p><button class="btn btn-info answer-trigger-button">{{ trans('plugins/knowledge-partner::knowledge-partner.reply') }}</button></p>

    <div class="answer-wrapper">
        <div class="form-group">
            {!! render_editor('message', null, false, ['without-buttons' => true, 'class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            <input type="hidden" value="{{ $knowledge_partner->id }}" id="input_knowledge_partner_id">
            <button class="btn btn-success answer-send-button"><i class="fas fa-reply"></i> {{ trans('plugins/knowledge-partner::knowledge-partner.send') }}</button>
        </div>
    </div>
@endif
