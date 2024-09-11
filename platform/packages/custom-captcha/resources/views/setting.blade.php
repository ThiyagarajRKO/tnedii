<div class="flexbox-annotated-section">
    <div class="flexbox-annotated-section-annotation">
        <div class="annotated-section-title pd-all-20">
            <h2>Custom Captcha</h2>
        </div>
        <div class="annotated-section-description pd-all-20 p-none-t">
            <p class="color-note">Settings for Custom Captcha</p>
        </div>
    </div>

    <div class="flexbox-annotated-section-content">
        <div class="wrapper-content pd-all-20">
            <div class="form-group">
                <label class="text-title-field"
                       for="enable_custom_captcha">Enable Custom Captcha
                </label>
                <label class="hrv-label">
                    <input type="radio" name="enable_custom_captcha" class="hrv-radio"
                                                value="1"
                                                @if (setting('enable_custom_captcha')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="hrv-label">
                    <input type="radio" name="enable_custom_captcha" class="hrv-radio"
                                                value="0"
                                                @if (!setting('enable_custom_captcha')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>

            <div class="form-group">
                <label class="text-title-field"
                       for="captcha_type">Type
                </label>
                <label class="hrv-label">
                    <input type="radio" name="custom_captcha_type" class="hrv-radio"
                           value="true"
                           @if (setting('custom_captcha_type')=='true') checked @endif>Arithmetic Captcha
                </label>
                <label class="hrv-label">
                    <input type="radio" name="custom_captcha_type" class="hrv-radio"
                           value="false"
                           @if (setting('custom_captcha_type')=='false') checked @endif>Alphanumeric Captcha
                </label>
               
</div>
        </div>
    </div>
</div>
