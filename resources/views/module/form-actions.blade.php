@if(!setting('reset_action'))
<div class="widget meta-boxes form-actions form-actions-default action-{{ $direction ?? 'horizontal' }}">
    <div class="widget-title">
        <h4>
            @if (isset($icon) && !empty($icon))
                <i class="{{ $icon }}"></i>
            @endif
            <span>{{ isset($title) ? $title : apply_filters(BASE_ACTION_FORM_ACTIONS_TITLE, trans('core/base::forms.actions')) }}</span>
        </h4>
    </div>
    <div class="widget-body">
        <div class="btn-set">
            @php do_action(BASE_ACTION_FORM_ACTIONS, 'default') @endphp
            <button type="submit" name="submit" value="save" class="btn btn-info">
                <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
            </button>
            @if (!isset($only_save) || $only_save == false)
                &nbsp;
            <button type="submit" name="submit" value="apply" class="btn btn-success">
                <i class="fa fa-check-circle"></i> {{ trans('core/base::forms.save_and_continue') }}
            </button>
            @endif
        </div>
    </div>
</div>
<div id="waypoint"></div>
<div class="form-actions form-actions-fixed-top hidden">
    {!! Breadcrumbs::render('main', page_title()->getTitle(false)) !!}
    <div class="btn-set">
        @php do_action(BASE_ACTION_FORM_ACTIONS, 'fixed-top') @endphp
        <button type="submit" name="submit" value="save" class="btn btn-info">
            <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
        </button>
        @if (!isset($only_save) || $only_save == false)
            &nbsp;
            <button type="submit" name="submit" value="apply" class="btn btn-success">
                <i class="fa fa-check-circle"></i> {{ trans('core/base::forms.save_and_continue') }}
            </button>
        @endif
    </div>
</div>
@endif
@if(setting('reset_action'))
<div class="widget meta-boxes form-actions form-actions-reset form-actions-default action-{{ $direction ?? 'horizontal' }} mt-10">

    <div class="widget-body form-actions-fixed-bottom">
        <div class="btn-set mt-0">
            @php do_action(BASE_ACTION_FORM_ACTIONS, 'default') @endphp
            @if (!isset($only_save) || $only_save == false)
                &nbsp;
            <button type="reset" name="reset" value="reset" id="resetBtn" class="btn btn-default">
                 {{ trans('common.reset') }}
            </button>
            @endif
            <button type="button"  class="btn btn-default cancelBtn" onClick="window.history.back()" style="display:none" >
                 {{ trans('common.cancel') }}
            </button>
            <button type="button" name="previous" value="previous" id="previousBtn" class="btn btn-info" style="display:none">
                 {{ trans('common.previous') }}
            </button>
            <button type="submit" name="submit" value="save" class="btn btn-info">
                 <i class="fa fa-check-circle"></i> {{ trans('common.submit') }}
            </button>

        </div>
    </div>
</div>
@endif
