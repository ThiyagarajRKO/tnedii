<div class="widget meta-boxes form-actions form-actions-default action-{{ $direction ?? 'horizontal' }}">
       <div class="widget-body">
        <div class="btn-set pull-right">
            @php do_action(BASE_ACTION_FORM_ACTIONS, 'default') @endphp
            <button type="submit" name="submit" value="save" class="btn btn-info">
                <i class="fa fa-save"></i> {{ trans('common.submit') }}
            </button>
            
        </div>
    </div>
</div>
