@if(setting('enable_custom_captcha'))
<label class="loginlabel">{{ trans('core/acl::auth.captcha') }}</label>
<div class="row">
  <div class="col-md-5">
      <div class="customcaptcha">               
        <span>{!! customcaptcha_img('math') !!}</span>
      </div>
   </div>
   <div class="col-md-5">        
    <input id="customcaptcha" type="text" style="width: 163px; height: 36px;"class="form-control"  name="customcaptcha">
   </div>
   <div class="col-md-0">
        <button type="button" style="height: 36px;"class="btn btn-info" class="refresh-captcha" id="refresh-captcha">
        &#8634;
        </button>
    </div>
 </div>
</br>
 @elseif(setting('enable_captcha') && is_plugin_active('captcha'))
        <div class="contact-form-row">
            <div class="contact-column-12">
                <div class="contact-form-group">
                    {!! Captcha::display() !!}
                </div>
            </div>
        </div>
</br>
@endif
