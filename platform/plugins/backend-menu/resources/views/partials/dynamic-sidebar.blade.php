@foreach ($menuNodes as $menu)
@php $menu = apply_filters(BASE_FILTER_DASHBOARD_MENU, $menu); @endphp
<li class="nav-item @if ($menu['active']) active @endif" id="{{ $menu['id'] }}">
    <a href="{{ $menu['url'] }}" class="nav-link nav-toggle">
    @if (!$isSubmenu)
        <i class="{{ $menu['icon'] }}"></i>
    @endif
        <span class="title">
            {{ !is_array(trans($menu['name'])) ? trans($menu['name']) : null }}
            {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $menu['id']) !!}</span>
        @if (isset($menu['children']) && count($menu['children']))
        {{-- @Customized  Sabari Shankar Parthiban - Start --}}
            <span class="badge badge-primary">{{count($menu['children'])}}</span>
        {{-- @Customized  Sabari Shankar Parthiban - End --}}
        <span class="arrow @if ($menu['active']) open @endif"></span> @endif
    </a>
    @if (isset($menu['children']) && count($menu['children']))
    <ul class="sub-menu @if (!$menu['active']) hidden-ul @endif">
        {!!
        app(\Impiger\BackendMenu\BackendMenu::class)->renderDynamicMenus([
        'view' => 'plugins/backend-menu::partials.dynamic-sidebar',
        'menu_nodes' => $menu['children'],
        'is_submenu' => true
        ])
        !!}
    </ul>
    @endif
</li>
@endforeach

<script type="text/javascript">
    $(document).ready(function() {
        if ($('ul.sub-menu li.active').length) {
            let selectedMenu = $('ul.sub-menu li.active').parents('li');
            selectedMenu.addClass('active open');
            selectedMenu.find('.arrow').addClass('open');
        }
    })
</script>