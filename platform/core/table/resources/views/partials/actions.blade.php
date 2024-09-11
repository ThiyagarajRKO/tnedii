<div class="table-actions">
    {!! $extra !!}
    @if (!empty($edit) && isset($item->id))
        @if (Auth::user() && Auth::user()->hasPermission($edit))
        	{{-- @Customized by Vijayaragavan.Ambalam Start --}}
            @if (is_plugin_active('multidomain'))
                <a href="{{ route($edit, $item->id) }}?ref_domain={{Multidomain::getCurrentAdminDomainId()}}" class="btn btn-icon btn-sm btn-primary" data-toggle="tooltip" data-original-title="{{ trans('core/base::tables.edit') }}"><i class="fa fa-edit"></i></a>
            @else
                <a href="{{ route($edit, $item->id) }}" class="btn btn-icon btn-sm btn-primary" data-toggle="tooltip" data-original-title="{{ trans('core/base::tables.edit') }}"><i class="fa fa-edit"></i></a>
            @endif
            {{-- @Customized by Vijayaragavan.Ambalam End --}}
        @endif
    @endif 

    @if (!empty($delete) && isset($item->id))
        {{-- @Cutomized Ramesh Esakki  --}}
        @if (Auth::user() && Auth::user()->hasPermission($delete))
            <a href="#" class="btn btn-icon btn-sm btn-danger deleteDialog" data-toggle="tooltip" data-section="{{ route($delete, $item->id) }}" role="button" data-original-title="{{ trans('core/base::tables.delete_entry') }}" >
                <i class="fa fa-trash"></i>
            </a>
        @endif
    @endif
    {{-- @Cutomized Sabari Shankar Parthiban  --}}
        {!! $suffix !!}
    {{-- @Cutomized Sabari Shankar Parthiban  --}}
</div>