<style>
.row-main{
  margin:10px 10px 0 10px;
} 

</style>
<div class="row">
    <div class="col-md-6">
        <label class="control-label"><b>{{trans("plugins/usergroups::usergroups.entity_mapping.entities")}}</b></label>
    </div>
    <div class="col-md-6">
        <label class="control-label"><b>{{trans("plugins/usergroups::usergroups.name")}}</b></label>
    </div>
</div>
<hr>
@if(empty($entities))
<div>No entities are found please create entity</div>
@elseif(empty($userGroups))
<div>No usergroups are found please create Usergroups</div>
@else
@foreach($entities as  $entity)
<div class="row row-main">
    <div class="col-md-5">
        <label class="control-label">{{Str::title(str_replace('-', ' ', $entity->module_title))}}</label>
        <input type="hidden" name="crud_id[]" value="{{$entity->id}}">
    </div>
    <div class="col-md-1">
<!--        <label class="control-label"> : </label>-->
    </div>
    <div class="col-md-5">
        <select class="form-control js-example-basic-single" name='usergroup_id[{{$entity->id}}]'>
            <option value=""></option>
            @foreach($userGroups as $usergroup)
            <option value="{{$usergroup->id}}" @if (isset($mappedEntities[$entity->id]) && in_array($usergroup->id,$mappedEntities[$entity->id])) selected @endif>{{$usergroup->name}}</option>
            @endforeach
        </select>
    </div>
</div>
@endforeach
@endif
<script>
    $(document).ready(function(){
        $('.js-example-basic-single').select2({
            placeholder: 'Select an option'
        });
    });
</script>