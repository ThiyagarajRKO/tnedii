@extends('core/base::layouts.master')

@section('content')
<div class="page-header">
    <h2> {{ $pageTitle }} <small>Configuration</small> </h2>
</div>



@include('plugins/crud::module.tab',array('active'=>'email','type'=> $type ))


{!! Form::open(array('url'=>'admin/cruds/saveemailconfig/'.$module_name, 'class'=>'form-horizontal ' ,'id'=>'fSub')) !!}

<input type='text' name='email_config' id='email_config' value='{{ $row->module_name }}' style="display:none;" />
<input type='text' name='id' id='id' value='{{ $row->id }}' style="display:none;" />
<div class="form-group">
    <div class="col-md-8">
        <fieldset>
            <legend>Mail Trigger On</legend>
        <label class="checkbox-inline">
            {!! Form::checkbox("create", 1, Arr::get($email_config, 'create',1)) !!} Create
        </label>
        <label class="checkbox-inline">
            {!! Form::checkbox("edit", 1, Arr::get($email_config, 'edit',1)) !!} Edit
        </label>
       </fieldset>
    </div>
</div>
<div class="form-group">

    <label for="ipt" class=" control-label col-md-4">Subject </label>
    <div class="col-md-8">
        {!! Form::text('subject', Arr::get($email_config, 'subject'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' ,'required'=>'true')) !!}
    </div>
</div>
<div class="form-group">

    <label for="ipt" class=" control-label col-md-4">Message Content </label>
    <div class="col-md-8">
        <textarea name="message" rows="5" id="message" class="form-control form-control-sm" placeholder="Content" ><?php echo Arr::get($email_config, 'message') ?: "" ?></textarea>
        <i class="text-danger"> Important ! , <small> use field name for mail content </small></i>
    </div>
</div>
<?php
$selectedValue = (Arr::get($email_config, 'send_to')) ? Arr::get($email_config, 'send_to') : 'roles';
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

        <select name="reciever_field" id="emailFieldReciever"  class="form-control form-control-sm js-data-example-ajax">
            <option value="">Select a reciever</option>
            <?php foreach ($fields as $field) { ?>
                <option value="<?php echo $field['field']; ?>" <?php
                if (Arr::get($email_config, 'reciever_field') == $field['field']) {
                    echo 'selected="selected"';
                }
                ?>><?php echo $field['field']; ?></option>
<?php } ?>
        </select>
        <select name="reciever_role" id="emailRoleReciever"  class="form-control form-control-sm js-data-example-ajax" style="display:none">
            <option value="">Select a reciever</option>
            <?php foreach ($roles as $role) { ?>
                <option value="<?php echo $role->slug; ?>" <?php
                        if (Arr::get($email_config, 'reciever_role') == $role->slug) {
                            echo 'selected="selected"';
                        }
                        ?>><?php echo $role->name; ?></option>
<?php } ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label for="ipt" class=" control-label col-md-4">Default Reciever </label>
    <div class="col-md-8">
        <select name="default_reciever" id="defaultReciever"  class="form-control form-control-sm js-data-example-ajax">
            <option value="">Select a reciever</option>
                    <?php foreach ($roles as $role) { ?>
                <option value="<?php echo $role->slug; ?>" <?php
                    if (Arr::get($email_config, 'default_reciever') == $role->slug) {
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
        if (Arr::get($email_config, 'module')) {
            echo "Update";
        } else {
            echo "Save";
        }
        ?> Email Detail </button>
<?php
if (Arr::get($email_config, 'title')) {
    echo '<a name="submit" href="/admin/cruds/emailconfig/' . $row->module_name . '" class="btn btn-primary btn-sm">Reset</a>';
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
            selected_value: "{{ Arr::get($email_config, 'table') }}"
        });
        $("#key").jCombo("{{ url('admin/cruds/combotablefield') }}?table=", {
            parent: "#table",
            selected_value: "{{ Arr::get($email_config, 'key') }}"
        });
        $("#custom_group_key").jCombo("{{ url('admin/cruds/combotablefield') }}?table=", {
            parent: "#table",
            selected_value: "{{ Arr::get($email_config, 'custom_group_key') }}"
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {

        $(document).on('change', '[name="send_to"]', function () {
            if ($('[name="send_to"]:checked').val() == "based_on_fields") {
                $('#emailFieldReciever').show();
                $('#emailFieldReciever').attr('required', true);
                $('#emailRoleReciever').hide();
                $('#emailRoleReciever').attr('required', false);
            } else {
                $('#emailRoleReciever').show();
                $('#emailRoleReciever').attr('required', true);
                $('#emailFieldReciever').hide();
                $('#emailFieldReciever').attr('required', false);
            }
        });

        $('[name="send_to"]').trigger('change');

<?php echo sjForm('fSub', true, true); ?>


    })
</script>
@stop