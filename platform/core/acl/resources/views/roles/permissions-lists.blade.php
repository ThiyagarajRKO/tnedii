<div id="listBoxContainer">
    <div class="row">
        <div class="col-md-5">
            <label class="control-label">{{trans('core/acl::permissions.module_list')}}</label>
            <div class="input-group">
                <input type="text" class="form-control listBoxSearchLeft" placeholder="search">
                <span class="input-group-prepend">
                    <button class="btn default" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>

        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <label class="control-label">{{trans('core/acl::permissions.with_permissions')}} </label>
            <div class="input-group">
                <input type="text" class="form-control listBoxSearchRight" placeholder="search">
                <span class="input-group-prepend">
                    <button class="btn default" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <div class="draggable-main">
        <div class='row'></div>
        <div class='row'>
            <ul id='auto-checkboxes' data-name='foo' class="connected-sortable draggable-left width-45 list-unstyled moduleListBox list-feature">
                <li id="mainNode">
                    <ul class="mainSortable connected-sortable draggable-left">
                        @foreach ($children['root'] as $elementKey => $element)
                        @if (!in_array($flags[$element]['flag'], $active))
                        @include("core/acl::roles.module-lists")
                        @endif
                        @endforeach
                    </ul>
                </li>
            </ul>

            <div class="width-10 listboxToolBarBtns">
                <div class="btn-grp hidden">
                    <button type="button" class="btn btn-default btn-block transferAllTo"><i class="fa fa-forward"></i></button>
                    <button type="button" class="btn btn-default btn-block transferTo"><i class="fa fa-caret-right"></i></button>
                    <button type="button" class="btn btn-default btn-block transferFrom"><i class="fa fa-caret-left"></i></button>
                    <button type="button" class="btn btn-default btn-block transferAllFrom"><i class="fa fa-backward"></i></button>
                </div>
            </div>

            <ul id='auto-checkboxes' data-name='foo' class="connected-sortable draggable-right width-45 list-unstyled moduleListBox list-feature ">
                <li id="mainNode">
                    <ul class="mainSortable connected-sortable draggable-right">
                        @foreach ($children['root'] as $elementKey => $element)
                        @if (in_array($flags[$element]['flag'], $active))
                        @include("core/acl::roles.module-lists")
                        @endif
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<script>


</script>