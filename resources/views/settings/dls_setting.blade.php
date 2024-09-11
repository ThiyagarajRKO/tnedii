<div class="flexbox-annotated-section">
    <div class="flexbox-annotated-section-annotation">
        <div class="annotated-section-title pd-all-20">
            <h2>{{ trans('common.settings.dls_title') }}</h2>
        </div>
        <div class="annotated-section-description pd-all-20 p-none-t">
            <p class="color-note">{{ trans('common.settings.dls_description') }}</p>
        </div>
    </div>

    <div class="flexbox-annotated-section-content">
        <div class="wrapper-content pd-all-20">
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.login.title') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="username_setting" class="hrv-radio"
                                                value="username"
                                                @if (setting('username_setting') == "username") checked @endif>{{ trans('common.settings.login.username') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="username_setting" class="hrv-radio"
                                                value="email"
                                                @if (setting('username_setting') == "email") checked @endif>{{ trans('common.settings.login.email') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="username_setting" class="hrv-radio"
                                                value="both"
                                                @if (setting('username_setting') == "both") checked @endif>{{ trans('common.settings.login.both') }}
                </label>
            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.enable_dls') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="enable_dls" class="hrv-radio"
                                                value="1"
                                                @if (setting('enable_dls')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="enable_dls" class="hrv-radio"
                                                value="0"
                                                @if (!setting('enable_dls')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="user_level_permission">{{ trans('common.settings.user_level_permission') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="user_level_permission" class="hrv-radio"
                                                value="1"
                                                @if (setting('user_level_permission')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="user_level_permission" class="hrv-radio"
                                                value="0"
                                                @if (!setting('user_level_permission')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.role_page_layout') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="role_form_custom_layout" class="hrv-radio"
                                                value="1"
                                                @if (setting('role_form_custom_layout')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="role_form_custom_layout" class="hrv-radio"
                                                value="0"
                                                @if (!setting('role_form_custom_layout')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.reset_action') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="reset_action" class="hrv-radio"
                                                value="1"
                                                @if (setting('reset_action')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="reset_action" class="hrv-radio"
                                                value="0"
                                                @if (!setting('reset_action')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.custom_stats_view') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="custom_stats_view" class="hrv-radio"
                                                value="1"
                                                @if (setting('custom_stats_view')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="custom_stats_view" class="hrv-radio"
                                                value="0"
                                                @if (!setting('custom_stats_view')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.enable_force_pwd_change') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="enable_force_pwd_change" class="hrv-radio"
                                                value="1"
                                                @if (setting('enable_force_pwd_change')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="enable_force_pwd_change" class="hrv-radio"
                                                value="0"
                                                @if (!setting('enable_force_pwd_change')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.enable_force_pwd_change_role') }}
                </label>

                <select class="js-data-example-ajax form-control" name="enable_force_pwd_change_roles[]" multiple="multiple">
                @php $roles = \Impiger\ACL\Models\Role::all() @endphp
                @foreach($roles as $role)
                <option value="{{$role->id}}">{{$role->name}}</option>
                @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.scheduler_time') }}
                </label>
                <div class="input-group">
                    <input class="form-control  time-picker timepicker timepicker-24" name="scheduler_daily_time" type="text" value="{{setting('scheduler_daily_time',NULL)}}">
                    <span class="input-group-prepend">
                        <button class="btn default" type="button">
                            <i class="fa fa-clock"></i>
                        </button>
                    </span>
                </div>
                
            </div>
        </div>
    </div>
</div>
<div class="flexbox-annotated-section">
    <div class="flexbox-annotated-section-annotation">
        <div class="annotated-section-title pd-all-20">
            <h2>{{ trans('common.settings.table.title') }}</h2>
        </div>
        <div class="annotated-section-description pd-all-20 p-none-t">
            <p class="color-note">{{ trans('common.settings.table.description') }}</p>
        </div>
    </div>

    <div class="flexbox-annotated-section-content">
        <div class="wrapper-content pd-all-20">
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.table.bulk_change') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="table_bulk_change" class="hrv-radio"
                                                value="1"
                                                @if (setting('table_bulk_change')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="table_bulk_change" class="hrv-radio"
                                                value="0"
                                                @if (!setting('table_bulk_change')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>

            </div>
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_captcha">{{ trans('common.settings.table.bulk_delete') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="table_bulk_delete" class="hrv-radio"
                                                value="1"
                                                @if (setting('table_bulk_delete')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="table_bulk_delete" class="hrv-radio"
                                                value="0"
                                                @if (!setting('table_bulk_delete')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>

            </div>

</div>
 <?php
Assets::addStylesDirectly('vendor/core/core/base/libraries/bootstrap-timepicker/css/bootstrap-timepicker.min.css')
        ->addScriptsDirectly('vendor/core/core/base/libraries/bootstrap-timepicker/js/bootstrap-timepicker.min.js');
?>       
<script>
    $(document).ready(function() {
     var selectedRoles = <?php echo json_encode(setting('enable_force_pwd_change_roles'))?>;
    $('.js-data-example-ajax').select2({
        placeholder: "{{trans('core/acl::users.select_role')}}"   
    });
   
    if(selectedRoles){
       $('.js-data-example-ajax').select2().val(selectedRoles).trigger('change')
    }

    });
</script>
