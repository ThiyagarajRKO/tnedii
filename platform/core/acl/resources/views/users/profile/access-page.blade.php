<form method="POST" action="{{route('users.entity-mapping', $id)}}" accept-charset="UTF-8" >
@if(request()->has('navback')) 
    <input type="hidden" name="navback" value="{{ request()->get('navback') }}"/>
@endif
    @csrf
    <div id="accessListBoxContainer">
    <div class="row">
        <div class="col-md-5">
            <label class="control-label">{{trans('core/acl::users.entity_list')}}</label>
            <div class="input-group">
                <input type="text" class="form-control listBoxSearchLeft"  placeholder="search">
                <span class="input-group-prepend">
                    <button class="btn default" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>

        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <label class="control-label">{{trans('core/acl::users.assigned_entity')}}</label>
            <div class="input-group">
                <input type="text" class="form-control listBoxSearchRight"  placeholder="search">
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
                <ul id='dls-auto-checkboxes' data-name='foo' class="connected-sortable draggable-left width-45 list-unstyled moduleListBox list-feature">
                    <li id="dls-mainNode">
                        <ul class="mainSortable connected-sortable draggable-left">
                           @if (isset($entities) && !empty($entities))
                            @foreach ($entities['root'] as $entity)
                            @if (!in_array($entity->id, $mappedEntities)) 
                            @if(isset($userEntities[$entity->id]))
                            @php  $mappedEntityTypes = $userEntities[$entity->id];@endphp
                            @endif
                            <li class="collapsed moduleList" id="node{{ $entity->id }}">
                                <input type="hidden" class="form-control entityIds"   value="{{ $entity->id }}" >
                                <input type="checkbox" class="hrv-checkbox" id="checkSelect{{ $entity->id }}" name="reference_types[{{ $entity->id }}]" value="{{ get_model_from_table($entity->module_db) }}" @if (isset($userEntities['entity_id']) && in_array($entity->id,$userEntities['entity_id'])) checked @endif>
                                       <label for="checkSelect{{ $entity->id }}" class="label label-warning" style="margin: 5px;">{{Str::title(str_replace('-', ' ', $entity->title)) }}</label>
                            @include("core/acl::users.profile.entity-lists")
                            @endif
                            @endforeach
                            @endif
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
            
                <ul id='dls-auto-checkboxes' data-name='foo' class="connected-sortable draggable-right width-45 list-unstyled moduleListBox list-featurelist-unstyled list-feature">
                    <li id="dls-mainNode">
                        <ul class="mainSortable connected-sortable draggable-right">
                           @if (isset($entities) && !empty($entities))
                            @foreach ($entities['root'] as $entity)
                            @if (in_array($entity->id, $mappedEntities))
                            @if(isset($userEntities[$entity->id]))
                            @php  $mappedEntityTypes = $userEntities[$entity->id];@endphp
                            @endif
                            <li class="collapsed moduleList" id="node{{ $entity->id }}">
                                <input type="hidden" class="form-control entityIds"   value="{{ $entity->id }}" name="entity_ids[]">
                                <input type="checkbox" class="hrv-checkbox" id="checkSelect{{ $entity->id }}" name="reference_types[{{ $entity->id }}]" value="{{ get_model_from_table($entity->module_db) }}" @if (isset($userEntities['entity_id']) && in_array($entity->id,$userEntities['entity_id'])) checked @endif>
                                       <label for="checkSelect{{ $entity->id }}" class="label label-warning" style="margin: 5px;">{{Str::title(str_replace('-', ' ', $entity->title)) }}</label>
                            @include("core/acl::users.profile.entity-lists")
                            @endif
                            @endforeach
                            @endif
                        </ul>
                    </li>
                </ul>      
        </div>
    </div>
        </div>
    <div class="form-group col-12">
        @include("core/acl::users.profile.actions")
    </div>
</form>
