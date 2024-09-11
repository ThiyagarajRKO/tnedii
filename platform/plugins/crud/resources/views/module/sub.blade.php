@extends('core/base::layouts.master')

@section('content')
<div class="page-header">
  <h2> {{ $pageTitle }} <small>Configuration</small> </h2>
</div>



@include('plugins/crud::module.tab',array('active'=>'sub','type'=> $type ))


{!! Form::open(array('url'=>'admin/cruds/savesub/'.$module_name, 'class'=>'form-horizontal ' ,'id'=>'fSub')) !!}

<input type='text' name='master' id='master' value='{{ $row->module_name }}' style="display:none;" />
<input type='text' name='id' id='id' value='{{ $row->id }}' style="display:none;" />

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Link Title <code>*</code></label>
  <div class="col-md-8">
    {!! Form::text('title', Arr::get($subform_detail, 'title'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' ,'required'=>'true')) !!}
    <i class="text-danger"> Important ! , <small> Do not use white space </small></i>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4">Master Key <code>*</code></label>
  <div class="col-md-8">

    <select name="master_key" id="master_key" required="true" class="form-control form-control-sm">
      <?php foreach ($fields as $field) { ?>
        <option value="<?php echo $field['field']; ?>" <?php if (Arr::get($subform_detail, 'field') == $field['field']) {
                                                        echo 'selected="selected"';
                                                      } ?>><?php echo $field['field']; ?></option>
      <?php } ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Module Target </label>
  <div class="col-md-8">
    <select name="module" <?php if (Arr::get($subform_detail, 'module')) {
                            echo "disabled";
                          } ?> id="module" required="true" class="form-control form-control-sm">
      <option value="">-- Select Module --</option>
      <?php foreach ($modules as $module) { ?>
        <option value="<?php echo $module['module_name']; ?>" <?php if (Arr::get($subform_detail, 'module') == $module['module_name']) {
                                                                echo 'selected="selected"';
                                                              } ?>><?php echo $module['module_title']; ?></option>
      <?php } ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4">DB Table Module Target <code>*</code></label>
  <div class="col-md-8">
    <select name="table" id="table" required="true" class="form-control form-control-sm">
    </select>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4">Detail Key <code>*</code></label>
  <div class="col-md-8">
    <select name="key" id="key" required="true" class="form-control form-control-sm">
    </select>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4">Field After</label>
  <div class="col-md-8">

    <select name="field_after" id="field_after" class="form-control form-control-sm">
      <option value="">-- Select --</option>
      <?php foreach ($fields as $field) { ?>
        <option value="<?php echo $field['field']; ?>" <?php if (Arr::get($subform_detail, 'field_after') == $field['field']) {
                                                        echo 'selected="selected"';
                                                      } ?>><?php echo $field['field']; ?></option>
      <?php } ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-md-4"> Is New Tab? </label>
  <div class="col-md-8">
    {!! Form::checkbox('is_new_tab', 1, Arr::get($subform_detail, 'is_new_tab')) !!}
  </div>
</div>
<?php $selectedValue = (Arr::get($subform_detail, 'module_relation')) ? Arr::get($subform_detail, 'module_relation') : 'single'; 
$singleSelected = ($selectedValue == 'single') ? true : false;
$manySelected = ($selectedValue == 'many') ? true : false;
$customSingleSelected = ($selectedValue == 'custom_single') ? true : false;

?>

<div class="form-group">
  <label class=" control-label col-md-4"> Module Relation </label>
  <div class="col-md-8">
    <label class="checkbox-inline">
      {!! Form::radio("module_relation", 'single', $singleSelected) !!} Single
    </label>
    <label class="checkbox-inline">
      {!! Form::radio("module_relation", 'many', $manySelected) !!} Many
    </label>
    <label class="checkbox-inline">
      {!! Form::radio("module_relation", 'custom_single', $customSingleSelected) !!} Custom Single
    </label>
  </div>
</div>

<div class="form-group custom_group_key" style="display: none;">
  <label for="ipt" class=" control-label col-md-4">Custom Group Key </label>
  <div class="col-md-8">
    <select name="custom_group_key" id="custom_group_key" required="true" class="form-control form-control-sm">
    </select>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-md-4">Hide Header Label</label>
  <div class="col-md-8">
    {!! Form::checkbox('hide_header_label', 1, Arr::get($subform_detail, 'hide_header_label')) !!}
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Wrapper Class</label>
  <div class="col-md-8">
    {!! Form::text('wrapper_cls', Arr::get($subform_detail, 'wrapper_cls'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' )) !!}
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
    <label class=" control-label col-md-4"> Prevent Delete  </label>
    <div class="col-md-8">
        <label class="checkbox-inline">
            {!! Form::checkbox("prevent_delete[front_end]", 1, Arr::get($subform_detail, 'prevent_delete.front_end')) !!} Front End
        </label> &nbsp;
        <label class="checkbox-inline">
            {!! Form::checkbox("prevent_delete[back_end]", 1, Arr::get($subform_detail, 'prevent_delete.back_end')) !!} Back End
        </label>
    </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"></label>
  <div class="col-md-8">
    <button name="submit" type="submit" class="btn btn-primary btn-sm"> <?php if (Arr::get($subform_detail, 'module')) {
                                                                          echo "Update";
                                                                        } else {
                                                                          echo "Save";
                                                                        } ?> Master Detail </button>
    <?php if (Arr::get($subform_detail, 'module')) {
      echo '<a name="submit" href="/admin/cruds/sub/' . $row->module_name . '" class="btn btn-primary btn-sm">Reset</a>';
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
        <th>Master Key</th>
        <th>Module Class</th>
        <th data-hide="phone">Database Table</th>
        <th data-hide="phone">Relation Key</th>
        <th data-hide="phone">Field After</th>
        <th data-hide="phone">Is New Tab</th>
        <th data-hide="phone">Module Relation</th>
        <th data-hide="phone">Action</th>
      </tr>
    </thead>
    <tbody class="no-border-x no-border-y">
      @foreach($subs as $rows)
      <tr>
        <td><?php echo $rows['title']; ?></td>
        <td><?php echo $rows['master_key']; ?></td>
        <td><?php echo $rows['module']; ?></td>
        <td><?php echo $rows['table']; ?></td>
        <td><?php echo $rows['key']; ?></td>
        <td><?php echo isset($rows['field_after']) ? $rows['field_after'] : ""; ?></td>
        <td><?php echo isset($rows['is_new_tab']) ? "Yes" : "No"; ?></td>
        <td><?php echo isset($rows['module_relation']) ? $rows['module_relation'] : "single"; ?></td>
        <td><a href="{{'/admin/cruds/sub/'.$row->module_name.'/?mod='.$rows['module']}}" class="btn "><i class="fa fa-edit"></i> </a>
          <a href="javascript:void(0)" onclick="SximoConfirmDelete('{{ URL::to('admin/cruds/removesub?id='.$row->id.'&mod='.$rows['module']) }}');" class="btn "><i class="fa fa-trash"></i> </a>
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

    $(document).on('change', '[name="module_relation"]', function() {
      if ($('[name="module_relation"]:checked').val() == "custom_single") {
			  $('#custom_group_key').prop('required', true);
        $('.custom_group_key').show();
      } else {
        $('.custom_group_key').hide();
        $('#custom_group_key').removeAttr('required');
      }
    });

    $('[name="module_relation"]').trigger('change');

    <?php echo sjForm('fSub', true, true); ?>
    setTimeout(function() {
      $('.js-data-example-ajax').select2({
        placeholder: "Select Role",
        width: '100%',
      });
    }, 500)

  })
</script>
@stop