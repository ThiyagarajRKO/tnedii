<ol class="dd-list">
    @foreach ($menuNodes as $key => $row)
    
        <li class="dd-item dd3-item @if (Arr::get($row, 'menu_id'))) post-item @endif" 
            data-id="{{ Arr::get($row, 'id') }}" data-title="{{ Arr::get($row, 'name') }}"
             data-referenceId="{{ Arr::get($row, 'menu_id') }}" data-referenceType="{{ Arr::get($row, 'url') }}"
            data-icon="{{ Arr::get($row, 'icon') }}" data-target="{{ Arr::get($row, 'target') }}" >
            <div class="dd-handle dd3-handle"></div>
            <div class="dd3-content">
                <span class="text float-left" data-update="title">{{ trans(Arr::get($row, 'name')) }}</span>
                <a href="#" title="" class="show-item-details"><i class="fa fa-angle-down"></i></a>
                <div class="clearfix"></div>
            </div>
            <div class="item-details">
                <label class="pad-bot-5">
                    <span class="text pad-top-5 dis-inline-block" data-update="title">{{ trans('packages/menu::menu.title') }}</span>
                    <input type="text" name="title" value="{{ trans(Arr::get($row, 'name')) }}"
                           data-old="{{ trans(Arr::get($row, 'name')) }}">
                </label>
                @if (!Arr::get($row, 'menu_id'))
                    <label class="pad-bot-5 dis-inline-block">
                        <span class="text pad-top-5" data-update="custom-url">{{ trans('packages/menu::menu.url') }}</span>
                        <input type="text" name="custom-url" value="{{ Arr::get($row, 'url') }}" data-old="{{ Arr::get($row, 'url') }}">
                    </label>
                @endif
                <label class="pad-bot-5 dis-inline-block">
                    <span class="text pad-top-5" data-update="icon-font">{{ trans('packages/menu::menu.icon') }}</span>
                    <input type="text" name="icon-font" value="{{ Arr::get($row, 'icon') }}" data-old="{{ Arr::get($row, 'icon') }}">
                </label>
                <label class="pad-bot-10">
                    <span class="text pad-top-5 dis-inline-block">{{ trans('packages/menu::menu.target') }}</span>
                    <div style="width: 228px; display: inline-block">
                        <div class="ui-select-wrapper">
                            <select name="target" class="ui-select" id="target" data-old="{{ Arr::get($row, 'target') }}">
                                <option value="_self" @if (Arr::get($row, 'target') == '_self') selected="selected" @endif>{{ trans('packages/menu::menu.self_open_link') }}
                                </option>
                                <option value="_blank" @if (Arr::get($row, 'target') == '_blank') selected="selected" @endif>{{ trans('packages/menu::menu.blank_open_link') }}
                                </option>
                            </select>
                            <svg class="svg-next-icon svg-next-icon-size-16">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                            </svg>
                        </div>
                    </div>
                </label>
                <div class="clearfix"></div>
                <div class="text-right" style="margin-top: 5px">
                    <a href="#" class="btn btn-danger btn-remove btn-sm">{{ trans('packages/menu::menu.remove') }}</a>
                    <a href="#" class="btn btn-primary btn-cancel btn-sm">{{ trans('packages/menu::menu.cancel') }}</a>
                </div>
            </div>
            <div class="clearfix"></div>
            @if (Arr::has($row, 'children.0'))
                {!!
                    BackendMenus::generateMenu([
                        'menu_nodes' => $row['children'],
                        'view'      => 'plugins/backend-menu::partials.menu',
                        'theme'     => false,
                        'active'    => false,
                        'slug' => 'Back End Menu'
                    ])
                !!}
            @endif
        </li>
    @endforeach
</ol>
