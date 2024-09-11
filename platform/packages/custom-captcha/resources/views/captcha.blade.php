@if(setting('enable_custom_captcha'))
 <div class="form-group mt-3 mb-3">
                <div class="customcaptcha">
                <button type="button" class="btn btn-danger" class="refresh-captcha" id="refresh-captcha">
                        &#8634;
                    </button>
<span>{!! customcaptcha_img('math') !!}</span>
                    
                </div>
            </div>
            <div class="form-group mb-4">
                <input id="customcaptcha" type="text" class="form-control" placeholder="Enter Captcha" name="customcaptcha">
            </div> 
            
            @elseif(setting('enable_captcha') && is_plugin_active('captcha'))

        <div class="contact-form-row">
            <div class="contact-column-12">
                <div class="contact-form-group">
                    {!! Captcha::display() !!}
                </div>
            </div>
        </div>
@endif
