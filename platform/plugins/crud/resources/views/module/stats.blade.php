@extends('core/base::layouts.master')

@section('content')
<div class="page-header">
  <h2> {{ $pageTitle }} <small>Configuration</small> </h2>
</div>



@include('plugins/crud::module.tab',array('active'=>'stats','type'=> $type ))


{!! Form::open(array('url'=>'admin/cruds/savestats/'.$module_name, 'class'=>'form-horizontal ' ,'id'=>'fStats')) !!}

<input type='text' name='id' id='id' value='{{ $row->id }}' style="display:none;" />
<input type='hidden' name='slug' id='slug' value='{{ Arr::get($subform_detail, "slug") }}'  />

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Stats Title <code>*</code></label>
  <div class="col-md-8">
    {!! Form::text('title', Arr::get($subform_detail, 'title'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' ,'required'=>'true')) !!}
    <i class="text-danger"> Important ! , <small> Do not use white space </small></i>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-md-4"> Is Sub Stats? </label>
  <div class="col-md-8">
    {!! Form::checkbox('is_sub_stats', 1, Arr::get($subform_detail, 'is_sub_stats')) !!}
  </div>
</div>

<div class="form-group parentStatsBlock">
  <label for="ipt" class=" control-label col-md-4">Parent Stats</label>
  <div class="col-md-8">

    <select name="parent_stats_id" id="parent_stats_id" class="form-control form-control-sm">
      <option value="">-- Select --</option>
      <?php foreach ($stats as $stat) { 
        if(!Arr::get($stat, 'is_sub_stats')) {?>
        <option value="<?php echo Arr::get($stat, 'slug'); ?>" <?php if (Arr::get($subform_detail, 'parent_stats_id') == Arr::get($stat, 'slug')) {
                                                        echo 'selected="selected"';
                                                      } ?>>
          <?php echo $stat['title']; ?></option>
      <?php }} ?>
    </select>
  </div>
</div>


<div class="form-group widgetTypeBlock">
  <label for="ipt" class=" control-label col-md-4"> Widget Type <code>*</code> </label>
  <div class="col-md-8">
    <select class="js-data-example-ajax form-control required" name="stats_type[]" multiple="multiple">
      <?php
      $statsTypeID = Arr::get($subform_detail, 'stats_type');
      $statsTypeID = (Arr::get($statsTypeID, 0)) ? $statsTypeID : [];
      foreach (DASHBOARD_STATS_TYPE as $typeKey => $type) {
      ?> <option value="<?php echo $typeKey; ?>" <?php if (in_array($typeKey, $statsTypeID))
                                                    echo 'selected="selected"'; ?>><?php echo $type; ?></option>';
      <?php }
      ?>
    </select>
  </div>
</div>
<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Operation Type <code>*</code> </label>
  <div class="col-md-8">
    <select class="js-data-example-ajax form-control required" name="operation_type" >
      <?php
      $optTypeID = Arr::get($subform_detail, 'operation_type');
      foreach (DASHBOARD_OPERATION_TYPE as $typeKey => $type) {
      ?> <option value="<?php echo $typeKey; ?>" <?php if ($typeKey == $optTypeID)
                                                    echo 'selected="selected"'; ?>><?php echo $type; ?></option>';
      <?php }
      ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label  col-md-4">Field</label>
  <div class="col-md-8">
    <textarea name="field" rows="5" id="field" class="tab_behave form-control form-control-sm" placeholder="">{{Arr::get($subform_detail, 'field')}}</textarea>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label  col-md-4">Join</label>
  <div class="col-md-8">
    <textarea name="sql_join" rows="5" id="sql_join" class="tab_behave form-control form-control-sm" placeholder="">{{Arr::get($subform_detail, 'sql_join')}}</textarea>
  </div>
</div>
<div class="form-group">
  <label for="ipt" class=" control-label  col-md-4">WorkflowMeta</label>
  <div class="col-md-8">
      <select class="js-data-example-ajax form-control required" name="workflow_meta" >
          <option>Select</option>
      <?php
      $metaID = Arr::get($subform_detail, 'workflow_meta');      
      foreach ($workflow_meta_data as $metaData) {
      ?> <option value="<?php echo $metaData->id; ?>" <?php if ($metaData->id == $metaID)
                                                    echo 'selected="selected"'; ?>><?php echo $metaData->transition_name; ?></option>';
      <?php }
      ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label  col-md-4">Condition</label>
  <div class="col-md-8">
    <textarea name="sql_cndn" rows="5" id="sql_cndn" class="tab_behave form-control form-control-sm" placeholder="">{{Arr::get($subform_detail, 'sql_cndn')}} 
    </textarea>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label  col-md-4">Group By</label>
  <div class="col-md-8">
    <textarea name="sql_group_by" rows="5" id="sql_group_by" class="tab_behave form-control form-control-sm" placeholder="">{{Arr::get($subform_detail, 'sql_group_by')}} 
    </textarea>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Route</label>
  <div class="col-md-8">
    {!! Form::text('route', Arr::get($subform_detail, 'route'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' )) !!}
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Icon</label>
  <div class="col-md-8">
    {!! Form::text('icon', Arr::get($subform_detail, 'icon'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' )) !!}
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Color</label>
  <div class="col-md-8">
    {!! Form::text('color', Arr::get($subform_detail, 'color'),array('class'=>'form-control form-control-sm', 'placeholder'=>'')) !!}
  </div>
</div>

<?php
$restrictToChecked = (Arr::get($subform_detail, 'field_access_type') == 1) ? true : false;
$accessToChecked = (Arr::get($subform_detail, 'field_access_type') == 2) ? true : false;
?>
<div class="form-group">
  <label class=" control-label col-md-4"> Field Access Type </label>
  <div class="col-md-8">
    <label class="checkbox-inline">
      {!! Form::radio("field_access_type", 1, $restrictToChecked) !!} Restrict To
    </label>
    <label class="checkbox-inline">
      {!! Form::radio("field_access_type", 2, $accessToChecked) !!} Access To
    </label>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Restrict to </label>
  <div class="col-md-8">
    <select class="js-data-example-ajax form-control" name="restricted_role_id[]" multiple="multiple">
      <?php
      $restrictedRoleID = Arr::get($subform_detail, 'restricted_role_id');
      $restrictedRoleID = (Arr::get($restrictedRoleID, 0)) ? $restrictedRoleID : [];
      foreach ($roles as $role) {
      ?> <option value="<?php echo $role->id; ?>" <?php if (in_array($role->id, $restrictedRoleID))
                                                    echo 'selected="selected"'; ?>><?php echo $role->name; ?></option>';
      <?php }
      ?>
    </select>
  </div>
</div>

<div class="form-group">
		<label class=" control-label col-md-4"> Show  </label>
		<div class="col-md-8">
			<label class="checkbox-inline">
			{!! Form::checkbox("show_backend", 1, Arr::get($subform_detail, 'show_backend')) !!} Backend
			</label>
			<label class="checkbox-inline">
			{!! Form::checkbox("show_frontend", 1, Arr::get($subform_detail, 'show_frontend')) !!} Frontend
			</label>
		</div>
</div>
<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Order</label>
  <div class="col-md-8">
    {!! Form::number('order', Arr::get($subform_detail, 'order'),array('class'=>'form-control form-control-sm', 'placeholder'=>'','min'=>0)) !!}
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"></label>
  <div class="col-md-8">
    <button name="submit" type="submit" class="btn btn-primary btn-sm"> <?php if (Arr::get($subform_detail, 'slug')) {
                                                                          echo "Update";
                                                                        } else {
                                                                          echo "Save";
                                                                        } ?> Stats </button>
    <?php if (Arr::get($subform_detail, 'slug')) {
      echo '<a name="submit" href="/admin/cruds/stats/' . $row->module_name . '" class="btn btn-primary btn-sm">Reset</a>';
    }
    ?>
  </div>
</div>

{!! Form::close() !!}


<div class="table-responsive" style="margin-bottom:40px;">

  <table class="table table-striped">
    <thead class="no-border">
      <tr>
        <th>Title</th>
        <th>Field</th>
        <th data-hide="phone">Is Sub Stats</th>
        <th data-hide="phone">Parent Stats</th>
        <th>Icon</th>
        <th>Color</th>
        <th data-hide="phone">Action</th>
      </tr>
    </thead>
    <tbody class="no-border-x no-border-y">
      @foreach($stats as $rows)
      <tr>
        <td><?php echo $rows['title']; ?></td>
        <td><?php echo $rows['field']; ?></td>
        <td><?php echo isset($rows['is_sub_stats']) ? "Yes" : "No"; ?></td>
        <td><?php echo isset($rows['parent_stats_id']) ? $rows['parent_stats_id'] : ""; ?></td>
        <td><?php echo Arr::get($rows, 'icon'); ?></td>
        <td><?php echo Arr::get($rows, 'color'); ?></td>
        <td><a href="{{'/admin/cruds/stats/'.$row->module_name.'/?mod='.Arr::get($rows, 'slug').'&parent_mod='.Arr::get($rows, 'parent_stats_id')}}" class="btn "><i class="fa fa-edit"></i> </a>
          <a href="javascript:void(0)" onclick="SximoConfirmDelete('{{ URL::to('admin/cruds/removestats?id='.$row->id.'&mod='.Arr::get($rows, 'slug').'&parent_mod='.Arr::get($rows, 'parent_stats_id')) }}');" class="btn "><i class="fa fa-trash"></i> </a>
        </td>

      </tr>
      @endforeach


    </tbody>

  </table>

</div>
<div class="modal" id="sximo-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
  <div class="modal-dialog  " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel1" style="color:#fff;font-size: 20px;">New message</h4>
        <button type="button" class="btn_remove_image" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>-->
      </div>
      <div class="modal-body" id="sximo-modal-content">

      </div>

    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $("#table").jCombo("{{ url('admin/cruds/combotable') }}", {
      selected_value: "{{ Arr::get($subform_detail, 'table') }}"
    });
    $("#key").jCombo("{{ url('admin/cruds/combotablefield') }}?table=", {
      parent: "#table",
      selected_value: "{{ Arr::get($subform_detail, 'key') }}"
    });
    $("#custom_group_key").jCombo("{{ url('admin/cruds/combotablefield') }}?table=", {
      parent: "#table",
      selected_value: "{{ Arr::get($subform_detail, 'custom_group_key') }}"
    });
  });
</script>

<script type="text/javascript">
  $(document).ready(function() {

    $(document).on('change', '[name="is_sub_stats"]', function() {
      if ($('[name="is_sub_stats"]').is(':checked')) {
        $('#parent_stats_id').prop('required', true);
        $('.parentStatsBlock').show();
        $('.widgetTypeBlock').hide();
      } else {
        $('.parentStatsBlock').hide();
        $('#parent_stats_id').removeAttr('required');
        $('.widgetTypeBlock').show();
      }
    });

    $('[name="is_sub_stats"]').trigger('change');

    <?php echo sjForm('fStats', true, true); ?>
    setTimeout(function() {
      $('.js-data-example-ajax').select2({
        placeholder: "Select Role",
        width: '100%',
      });
    }, 500)

  })
</script>
@stop