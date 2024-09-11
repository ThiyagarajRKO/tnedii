
    @if (isset($entities[$entity->module_db]))
    <ul>
        @foreach($entities[$entity->module_db]['data'] as $subElements)
        @if(isset($userEntities[$entity->id]["role_id"][$subElements->id]))
        @php  
        $mappedRoles = $userEntities[$entity->id]["role_id"][$subElements->id];
        @endphp
        @else@php  
        $mappedRoles = [];
        @endphp
        @endif
        <li class="collapsed" id="node_sub_{{ $entity->id  }}_{{ $subElements->id }}">
            <input type="checkbox" class="hrv-checkbox" id="dls_checkSelect_sub_{{ $entity->id  }}_{{ $subElements->id }}" name="reference_ids[{{$entity->id}}][]" value="{{$subElements->id}}" @if (isset($mappedEntityTypes) && in_array($subElements->id,$mappedEntityTypes)) checked @endif>
                   <label for="dls_checkSelect_sub_{{ $entity->id  }}_{{ $subElements->id }}" class="label label-primary nameMargin">{{ $subElements->name }}</label>
            <ul class="roleLists" >
                @foreach($userRoles as $userRole) 
                @if(in_array($userRole->id , $entities[$entity->module_db]['roles']))
                @php if(!is_array($mappedRoles)){$mappedRoles = array($mappedRoles);}@endphp
                <li>
                    <input type="checkbox" class="hrv-checkbox" id="dls_checkSelect_sub_{{ $entity->id  }}_{{ $subElements->id }}" name="role_ids[{{$entity->id}}][{{$subElements->id}}][]" value="{{ $userRole->id }}" @if (isset($mappedRoles) && in_array($userRole->id,$mappedRoles)) checked @endif>
                    <label for="dls_checkSelect_sub_{{ $entity->id  }}_{{ $subElements->id }}" class="label label-primary nameMargin">{{ $userRole->name }}</label>
                </li>
                @endIf
                @endforeach
            </ul>      

            @endforeach
    </ul>
    @endif




