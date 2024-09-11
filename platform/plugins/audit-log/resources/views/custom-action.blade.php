<span class="log-icon log-icon-{{ $history->type }}"></span>
<span>
    @if (Lang::has('plugins/audit-log::history.' . $history->action)) {{ trans('plugins/audit-log::history.' . $history->action) }} @else {{ $history->action }} @endif
    @if ($history->module)
        @if (Lang::has('plugins/audit-log::history.' . $history->module)) {{ trans('plugins/audit-log::history.' . $history->module) }} @else @endif
    @endif
    @if ($history->reference_name)
        @if (empty($history->user) || $history->user->getFullName() != $history->reference_name)
            <br/>Ref:"{{ Str::limit($history->reference_name, 40) }}"
        @endif
    @endif
    .
</span>