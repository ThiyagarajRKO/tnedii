@extends('core/base::layouts.master')

@section('content')
<div class="page-header">
    <h2> {{ $pageTitle }} <small>Configuration</small> </h2>
</div>



@include('plugins/crud::module.tab',array('active'=>'scheduler','type'=> $type ))


{!! Form::open(array('url'=>'admin/cruds/savescheduler/'.$module_name, 'class'=>'form-horizontal ' ,'id'=>'fSub')) !!}

<input type='text' name='scheduler' id='scheduler' value='{{ $row->module_name }}' style="display:none;" />
<input type='text' name='id' id='id' value='{{ $row->id }}' style="display:none;" />

<div class="form-group">
    <label for="ipt" class=" control-label col-md-4"> Scheduler Title <code>*</code></label>
    <div class="col-md-8">
        {!! Form::text('title', Arr::get($scheduler_detail, 'title'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' ,'required'=>'true')) !!}
        <i class="text-danger"> Important ! , <small> Do not use white space </small></i>
    </div>
</div>

<div class="form-group">
    <label for="ipt" class=" control-label col-md-4">Status Change To <code>*</code></label>
    <div class="col-md-8">

        <select name="status_change_to" id="status_change_to" required="true" class="form-control form-control-sm">
            <option value="">Select</option>
            <?php foreach ($fields as $field) { ?>
                <option value="<?php echo $field['field']; ?>" <?php
                if (Arr::get($scheduler_detail, 'status_change_to') == $field['field']) {
                    echo 'selected="selected"';
                }
                ?>><?php echo $field['field']; ?></option>
<?php } ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="ipt" class=" control-label col-md-4"> Status Change Value </label>
    <div class="col-md-8">
        {!! Form::text('status_change_value', Arr::get($scheduler_detail, 'status_change_value'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' ,'required'=>'true')) !!}
    </div>
</div>

<div class="form-group">
    <label for="ipt" class=" control-label col-md-4">SQL <code>*</code></label>
    <div class="col-md-8">
        <textarea name="sql_where" rows="5" id="sql_where" class="form-control form-control-sm" placeholder="SQL Where Statement" ><?php echo Arr::get($scheduler_detail, 'sql_where') ?: "" ?></textarea>
    </div>
</div>

<div class="form-group col-md-4">

    <label for="notification" class="control-label  ">Notification</label>

    <div class="onoffswitch">
        <input type="hidden" name="notification" value="0">
        <input type="checkbox" name="notification" class="onoffswitch-checkbox" id="notification" value="1" <?php echo Arr::get($scheduler_detail, 'notification') ? 'checked' : ""; ?>>
        <label class="onoffswitch-label" for="notification">
            <span class="onoffswitch-inner"></span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>
</div>
<div class="form-group">

    <label for="ipt" class=" control-label col-md-4">Notification Subject </label>
    <div class="col-md-8">
        {!! Form::text('notification_subject', Arr::get($scheduler_detail, 'notification_subject'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' ,'required'=>'true')) !!}
    </div>
</div>
<div class="form-group">

    <label for="ipt" class=" control-label col-md-4">Notification Message Content </label>
    <div class="col-md-8">
        <textarea name="notification_message" rows="5" id="notification_message" class="form-control form-control-sm" placeholder="Message content" ><?php echo Arr::get($scheduler_detail, 'notification_message') ?: "" ?></textarea>
    </div>
</div>



<div class="form-group col-md-4">

    <label for="prior_notification" class="control-label  ">Prior Notification</label>

    <div class="onoffswitch">
        <input type="hidden" name="prior_notification" value="0">
        <input type="checkbox" name="prior_notification" class="onoffswitch-checkbox" id="prior_notification" value="1" <?php echo Arr::get($scheduler_detail, 'prior_notification') ? 'checked=' : ""; ?>>
        <label class="onoffswitch-label" for="prior_notification">
            <span class="onoffswitch-inner"></span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>
</div>
<!--<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Prior Notification Start </label>
  <div class="col-md-8">
    {!! Form::text('prior_notification_start', Arr::get($scheduler_detail, 'prior_notification_start'),array('class'=>'form-control form-control-sm', 'placeholder'=>'')) !!}
  </div>
</div>
<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Prior Notification Stop </label>
  <div class="col-md-8">
    {!! Form::text('prior_notification_stop', Arr::get($scheduler_detail, 'prior_notification_stop'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' )) !!}
  </div>
</div>
<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Recurring </label>
  <div class="col-md-8">
    {!! Form::text('recurring', Arr::get($scheduler_detail, 'recurring'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' )) !!}
  </div>
</div>
<div class="form-group">
  <label for="ipt" class=" control-label col-md-4">Prior Check Field </label>
  <div class="col-md-8">

    <select name="prior_check_field" id="prior_check_field"  class="form-control form-control-sm">
        <option value="">Select</option>
<?php foreach ($fields as $field) { ?>
            <option value="<?php echo $field['field']; ?>" <?php
    if (Arr::get($scheduler_detail, 'prior_check_field') == $field['field']) {
        echo 'selected="selected"';
    }
    ?>><?php echo $field['field']; ?></option>
<?php } ?>
    </select>
  </div>
</div>-->
<div class="form-group">
    <label for="ipt" class=" control-label col-md-4">Prior Check SQL </label>
    <div class="col-md-8">
        <textarea name="prior_check" rows="5" id="prior_check" class="form-control form-control-sm" placeholder="SQL Where Statement for prior notify" ><?php echo Arr::get($scheduler_detail, 'prior_check') ?: "" ?></textarea>
    </div>
</div>                                                    
<div class="form-group">

    <label for="ipt" class=" control-label col-md-4">Prior Notification Subject </label>
    <div class="col-md-8">
        {!! Form::text('prior_notification_subject', Arr::get($scheduler_detail, 'prior_notification_subject'),array('class'=>'form-control form-control-sm', 'placeholder'=>'')) !!}
    </div>
</div>
<div class="form-group">

    <label for="ipt" class=" control-label col-md-4">Prior Notification Message Content </label>
    <div class="col-md-8">
        <textarea name="prior_notification_message" rows="5" id="prior_notification_message" class="form-control form-control-sm" placeholder="Message content" ><?php echo Arr::get($scheduler_detail, 'notification_message') ?: "" ?></textarea>
    </div>
</div>
<?php
$selectedValue = (Arr::get($scheduler_detail, 'send_to')) ? Arr::get($scheduler_detail, 'send_to') : 'roles';
$basedOnFields = ($selectedValue == 'based_on_fields') ? true : false;
$rolesSelected = ($selectedValue == 'roles') ? true : false;
?>
<div class="form-group">
    <label class=" control-label col-md-4"> Send To </label>
    <div class="col-md-8">
        <label class="checkbox-inline">
            {!! Form::radio("send_to", 'based_on_fields', $basedOnFields) !!} Based on fields
        </label>
        <label class="checkbox-inline">
            {!! Form::radio("send_to", 'roles', $rolesSelected) !!} Roles
        </label>
    </div>
</div>

<div class="form-group">
    <label for="ipt" class=" control-label col-md-4">Reciever <code>*</code></label>
    <div class="col-md-8">

        <select name="reciever" id="fieldReciever" required="true" class="form-control form-control-sm js-data-example-ajax">
            <option value="">Select a reciever</option>
                    <?php foreach ($fields as $field) { ?>
                <option value="<?php echo $field['field']; ?>" <?php
                    if (Arr::get($scheduler_detail, 'reciever') == $field['field']) {
                        echo 'selected="selected"';
                    }
                        ?>><?php echo $field['field']; ?></option>
            <?php } ?>
        </select>
        <select name="reciever" id="roleReciever" required="true" class="form-control form-control-sm js-data-example-ajax">
            <option value="">Select a reciever</option>
<?php foreach ($roles as $role) { ?>
                <option value="<?php echo $role->id; ?>" <?php
    if (Arr::get($scheduler_detail, 'reciever') == $role->id) {
        echo 'selected="selected"';
    }
    ?>><?php echo $role->name; ?></option>
<?php } ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="ipt" class=" control-label col-md-4">Default Reciever <code>*</code></label>
    <div class="col-md-8">
        <select name="default_reciever" id="defaultReciever" required="true" class="form-control form-control-sm js-data-example-ajax">
            <option value="">Select a reciever</option>
<?php foreach ($roles as $role) { ?>
                <option value="<?php echo $role->id; ?>" <?php
    if (Arr::get($scheduler_detail, 'default_reciever') == $role->id) {
        echo 'selected="selected"';
    }
    ?>><?php echo $role->name; ?></option>
<?php } ?>
        </select>
    </div>
</div>






<div class="form-group">
    <label for="ipt" class=" control-label col-md-4"></label>
    <div class="col-md-8">
        <button name="submit" type="submit" class="btn btn-primary btn-sm"> <?php
if (Arr::get($scheduler_detail, 'module')) {
    echo "Update";
} else {
    echo "Save";
}
?> Scheduler Detail </button>
<?php
if (Arr::get($scheduler_detail, 'title')) {
    echo '<a name="submit" href="/admin/cruds/scheduler/' . $row->module_name . '" class="btn btn-primary btn-sm">Reset</a>';
}
?>
    </div>
</div>

{!! Form::close() !!}



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
    $(document).ready(function () {
        $("#table").jCombo("{{ url('admin/cruds/combotable') }}", {
            selected_value: "{{ Arr::get($scheduler_detail, 'table') }}"
        });
        $("#key").jCombo("{{ url('admin/cruds/combotablefield') }}?table=", {
            parent: "#table",
            selected_value: "{{ Arr::get($scheduler_detail, 'key') }}"
        });
        $("#custom_group_key").jCombo("{{ url('admin/cruds/combotablefield') }}?table=", {
            parent: "#table",
            selected_value: "{{ Arr::get($scheduler_detail, 'custom_group_key') }}"
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {

        $(document).on('change', '[name="send_to"]', function () {
            if ($('[name="send_to"]:checked').val() == "based_on_fields") {
                $('#fieldReciever').show();
                $('#fieldReciever').attr('required', true);
                $('#roleReciever').hide();
                $('#roleReciever').attr('required', false);
            } else {
                $('#roleReciever').show();
                $('#roleReciever').attr('required', true);
                $('#fieldReciever').hide();
                $('#fieldReciever').attr('required', false);
            }
        });

        $('[name="send_to"]').trigger('change');

<?php echo sjForm('fSub', true, true); ?>
//    setTimeout(function() {
//      $('.js-data-example-ajax').select2({
//        placeholder: "Select Reciever",
//        width: '100%',
//      });
//    }, 500);


    })
</script>
@stop