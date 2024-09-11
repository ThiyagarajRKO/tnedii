<div id="listBoxContainer">
    <div class="row">
        <div class="col-md-5">
            <label class="control-label">{{trans('core/acl::users.role')}} List</label>
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
            <label class="control-label">Assigned {{trans('core/acl::users.role')}}</label>
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
            <ul class="width-45 moduleListBox connected-sortable draggable-left">
                @foreach($availableRoles as $role)
                @if (!in_array($role->id, $mappedRoles))
                <li class="listItems">
                    <input type='hidden' class="form-control" value='{{$role->id}}'>
                    <label class="control-label" style="margin: 5px;">{{ $role->name }}</label>
                </li>
                @endIf
                @endforeach
            </ul>

            <div class="width-10 listboxToolBarBtns">
                <div class="btn-grp hidden">
                    <button type="button" class="btn btn-default btn-block transferAllTo"><i class="fa fa-forward"></i></button>
                    <button type="button" class="btn btn-default btn-block transferTo"><i class="fa fa-caret-right"></i></button>
                    <button type="button" class="btn btn-default btn-block transferFrom"><i class="fa fa-caret-left"></i></button>
                    <button type="button" class="btn btn-default btn-block transferAllFrom"><i class="fa fa-backward"></i></button>
                </div>
            </div>

            <ul class="width-45 moduleListBox connected-sortable draggable-right">
                @foreach($roles as $role)
                @if (in_array($role->id, $mappedRoles))
                <li class="listItems">
                    <input type='hidden' class="form-control" name='roles[]' value='{{$role->id}}'>
                    <label class="control-label" style="margin: 5px;">{{ $role->name }}</label>
                </li>
                @endIf
                @endforeach
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#listBoxContainer').customListBox({
            nameKey: 'roles[]'
        });
    });
</script>