<div class="widget meta-boxes form-actions form-actions-default action-{{ $direction ?? 'horizontal' }}">
    <div class="widget-title">
        <h4>
            <span>{{ trans('core/base::forms.actions') }}</span>
        </h4>
    </div>
    <div class="widget-body">
        <div class="btn-set">
            <button type="submit" name="submit" value="apply" class="btn btn-success">
                <i class="fa fa-check-circle"></i> {{ trans('core/base::forms.save') }}
            </button>
        </div>
    </div>
</div>
