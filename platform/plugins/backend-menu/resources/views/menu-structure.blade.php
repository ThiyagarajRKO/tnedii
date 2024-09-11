<div class="wrapper-filter">
    <div>
		<div class="input-group">
			<input type="text" class="form-control menuSearch" placeholder="search">
			<span class="input-group-prepend">
				<button class="btn default" type="button">
					<i class="fa fa-search"></i>
				</button>
			</span>
		</div>
	</div>
</div>

    <input type="hidden" name="deleted_nodes">
    <textarea name="menu_nodes" id="nestable-output" class="form-control hidden"></textarea>
    <div class="row widget-menu">
        <div class="col-md-4">
            <div class="panel-group" id="accordion">

            @foreach ($menus = dashboard_menu()->getAll() as $menu)
            @php $menu = apply_filters(BASE_FILTER_DASHBOARD_MENU, $menu); @endphp
            <div class="widget meta-boxes">
                    <a data-toggle="collapse" data-parent="#accordion" href="#pages-{{$menu['id']}}">
                        <h4 class="widget-title">
                            <span>{{ !is_array(trans($menu['name'])) ? trans($menu['name']) : null }}</span>
                            <i class="fa fa-angle-down narrow-icon"></i>
                        </h4>
                    </a>

                    <div id="pages-{{$menu['id']}}" class="panel-collapse collapse">
                        <div class="widget-body">
                            <div class="box-links-for-menu">
                                <div class="the-box">
                                    <ul class="list-item mCustomScrollbar _mCS_1 mCS-autoHide mCS_no_scrollbar" style="position: relative; overflow: visible; padding: 0px;">
                                    <div id="mCSB_1" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical_horizontal mCSB_outside" style="max-height: 168px;" tabindex="0">
                                        <div id="mCSB_1_container" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y mCS_x_hidden mCS_no_scrollbar_x" style="position:relative; top:0; left:0;" dir="ltr">
                                            @if (isset($menu['children']) && count($menu['children']))
                                                @foreach ($menu['children'] as $item)
                                                <li>
                                                    <label for="menu-id-{{ $item['id'] }}" data-title="{{ trans($item['name']) }}" data-reference-id="{{ $item['id'] }}" data-reference-type="{{ $item['url'] }}" data-icon-font="{{ $item['icon'] }}">
                                                        <input id="menu-id-{{ $item['id'] }}" name="menu_id" type="checkbox" value="{{ $item['id'] }}">
                                                        {{ trans($item['name']) }}
                                                    </label>
                                                </li>
                                                @endforeach
                                            @else
                                                <li>
                                                    <label for="menu-id-{{ $menu['id'] }}-parent" data-title="{{ trans($menu['name']) }}" data-reference-id="{{ $menu['id'] }}" data-reference-type="{{ $menu['url'] }}" data-icon-font="{{ $menu['icon'] }}">
                                                        <input id="menu-id-{{ $menu['id'] }}-parent" name="menu_id" type="checkbox" value="{{ $menu['id'] }}">
                                                        {{ trans($menu['name']) }}
                                                    </label>
                                                </li>
                                            @endif     
                                    </div>
                                    </div>
                                <div id="mCSB_1_scrollbar_vertical" class="mCSB_scrollTools mCSB_1_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical" style="display: none;"><div class="mCSB_draggerContainer">
                                    <div id="mCSB_1_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 50px; top: 0px;" oncontextmenu="return false;"><div class="mCSB_dragger_bar" style="line-height: 50px;"></div><div class="mCSB_draggerRail">
                                    
                                    </div>
                                </div>
                    <div id="mCSB_1_scrollbar_horizontal" class="mCSB_scrollTools mCSB_1_scrollbar mCS-minimal-dark mCSB_scrollTools_horizontal" style="display: none;"><div class="mCSB_draggerContainer"><div id="mCSB_1_dragger_horizontal" class="mCSB_dragger" style="position: absolute; min-width: 50px; left: 0px;" oncontextmenu="return false;"><div class="mCSB_dragger_bar"></div><div class="mCSB_draggerRail"></div></div></div></div></div></div></ul>

                                    <div class="text-right">
                                        <div class="btn-group btn-group-devided">
                                            <a href="#" class="btn-add-to-menu btn btn-primary">
                                                <span class="text"><i class="fa fa-plus"></i> Add to menu</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            @endforeach

                <div class="widget meta-boxes">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseCustomLink">
                        <h4 class="widget-title">
                            <span>{{ trans('packages/menu::menu.add_link') }}</span>
                            <i class="fa fa-angle-down narrow-icon"></i>
                        </h4>
                    </a>
                    <div id="collapseCustomLink" class="panel-collapse collapse">
                        <div class="widget-body">
                            <div class="box-links-for-menu">
                                <div id="external_link" class="the-box">
                                    <div class="node-content">
                                        <div class="form-group">
                                            <label for="node-title">{{ trans('packages/menu::menu.title') }}</label>
                                            <input type="text" class="form-control" id="node-title" autocomplete="false">
                                        </div>
                                        <div class="form-group">
                                            <label for="node-url">{{ trans('packages/menu::menu.url') }}</label>
                                            <input type="text" class="form-control" id="node-url" placeholder="http://" autocomplete="false">
                                        </div>
                                        <div class="form-group">
                                            <label for="node-icon">{{ trans('packages/menu::menu.icon') }}</label>
                                            <input type="text" class="form-control" id="node-icon" placeholder="fa fa-home" autocomplete="false">
                                        </div>
                                        <div class="form-group">
                                            <label for="target">{{ trans('packages/menu::menu.target') }}</label>
                                            <div class="ui-select-wrapper">
                                                <select name="target" class="ui-select" id="target">
                                                    <option value="_self">{{ trans('packages/menu::menu.self_open_link') }}</option>
                                                    <option value="_blank">{{ trans('packages/menu::menu.blank_open_link') }}</option>
                                                </select>
                                                <svg class="svg-next-icon svg-next-icon-size-16">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="text-right form-group node-actions hidden">
                                            <a class="btn red btn-remove" href="#">{{ trans('packages/menu::menu.remove') }}</a>
                                            <a class="btn blue btn-cancel" href="#">{{ trans('packages/menu::menu.cancel') }}</a>
                                        </div>

                                        <div class="form-group">
                                            <div class="text-right add-button">
                                                <div class="btn-group">
                                                    <a href="#" class="btn-add-to-menu btn btn-primary"><span class="text"><i class="fa fa-plus"></i> {{ trans('packages/menu::menu.add_to_menu') }}</span></a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('packages/menu::menu.structure') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="dd nestable-menu" id="nestable" data-depth="0">
                        {!!
                            BackendMenus::generateMenu([
                                'slug' => 'Backend Menu',
                                'view' => 'plugins/backend-menu::partials.menu',
                                'theme' => false,
                                'active' => false,
                             ])
                        !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
    $(document).ready(function(){
        $(document).on("submit", ".form-save-menu", (e) => {
            // $("#nestable-output").val("[]");
            $('#nestable ol.dd-list > li').each(() => {
                let data = $(this).data();
            })
        });
        
        $('.menuSearch').keyup(function () {
            var valThis = $(this).val();
            $(".widget-menu .panel-group .widget .widget-title span").each(function () {
                var text = $(this).text().toLowerCase();
                let parentEl = $(this).parents('.widget:first');
                (text.indexOf(valThis) != -1) ? $(parentEl).show() : $(parentEl).hide();
            });
        });
    })
    </script>

    <style>
        .mCustomScrollBox{
            overflow: auto!important;
        }
    </style>
    
