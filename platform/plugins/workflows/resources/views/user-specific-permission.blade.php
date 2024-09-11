@php
$users = (\Arr::has($mappedIds,$k.'.'.WORKFLOW_PERMISSION_SPECIFIC_TO_USER)) ? \Impiger\Acl\Models\User::select(['id', DB::raw('CONCAT_WS(" ",users.first_name, last_name) AS text')])->whereIn('id', $mappedIds[$k][WORKFLOW_PERMISSION_SPECIFIC_TO_USER])->get() : null;
$roles = (\Arr::has($mappedIds,$k.'.'.WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE)) ? \Impiger\Acl\Models\Role::select(['id', 'name'])->whereIn('id', $mappedIds[$k][WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE])->get() : null;
@endphp
<div class="row">
    <div class="form-group col-md-6 pl-10">

        <label for="permission_specific_to{{$k}}" class="control-label" aria-required="true">Permission Specific To</label>

        <div class="mt-radio-list"> <label>
                <input type="radio" value="{{WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE}}" name="permission_specific_to{{$k}}">
                Role
            </label>
            <label>
                <input type="radio" value="{{WORKFLOW_PERMISSION_SPECIFIC_TO_USER}}" name="permission_specific_to{{$k}}">
                User
            </label>
        </div>

    </div>

    <div class="col-md-6 pl-10  user-specific-block " style="display: none ;">
        <label for="role" class="control-label">Role</label>
        <div class="ui-select-wrapper form-group">
            <select class="select-full ui-select ui-select select2-hidden-accessible" id="role_{{$k}}" name="role">
                <option selected=""></option>
                @foreach($availableRoles as $role)
                <option value="{{$role->id}}">{{$role->name}}</option>
                @endforeach
            </select>
            <svg class="svg-next-icon svg-next-icon-size-16">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
            </svg>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-5 pl-10">
        <label class="control-label">User List</label>
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
    <div class="col-md-5 pl-10">
        <label class="control-label">Assigned User</label>
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
        </ul>
        <div class="width-10 listboxToolBarBtns">
            <div class="btn-grp hidden">
                <button type="button" class="btn btn-default btn-block transferAllTo"><i class="fa fa-forward"></i></button>
                <button type="button" class="btn btn-default btn-block transferTo"><i class="fa fa-caret-right"></i></button>
                <button type="button" class="btn btn-default btn-block transferFrom"><i class="fa fa-caret-left"></i></button>
                <button type="button" class="btn btn-default btn-block transferAllFrom"><i class="fa fa-backward"></i></button>
            </div>
        </div>
        <ul class="width-45 connected-sortable moduleListBox draggable-right">
            @if($roles)
                @foreach($roles as $role)
                <li class="listItems">
                    <input type='hidden' class="form-control" name='user_permissions[{{$k}}][{{WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE}}][]' value='{{$role->id}}'>
                    <label class="control-label" style="margin: 5px;">{{ $role->name }}</label>
                </li>
                @endforeach
            @endif

            @if($users)
            @foreach($users as $user)
            <li class="listItems">
                <input type='hidden' class="form-control" name='user_permissions[{{$k}}][{{WORKFLOW_PERMISSION_SPECIFIC_TO_USER}}][]' value='{{$user->id}}'>
                <label class="control-label" style="margin: 5px;">{{ $user->text }}</label>
            </li>
            @endforeach
            @endif
        </ul>
    </div>
</div>