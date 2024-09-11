<?php

namespace Impiger\CustomCaptcha;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_action(RENDER_CAPTCHA_FIELD, [$this, 'renderCaptchaTemplate'], 247);

    

    }

    /**
     * @param null $data
     * @return string
     * @throws \Throwable
     */
    public function renderCaptchaTemplate()
    {
        echo view('base.utility.captcha' )->render();
        // return view('packages/custom-captcha::captcha' )->render();

    }
}
