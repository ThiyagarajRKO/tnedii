<div>
    <select class="js-data-example-ajax form-control" name="role_id[]" multiple="multiple">


    </select>
</div>
<script>
    $(document).ready(function() {
    $('.js-data-example-ajax').select2({
    placeholder: "{{trans('core/acl::users.select_role')}}",
    ajax: {
    url: "{{ route('roles.roleList.json') }}",
            dataType: 'json'
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
    }
    });
    $('.js-example-basic-multiple').select2({
    placeholder: "{{trans('core/acl::users.select_role')}}"
            });
    });
</script>