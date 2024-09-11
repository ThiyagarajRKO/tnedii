<?php

use Intervention\Image\ImageManager;

if (!function_exists('customcaptcha')) {
    /**
     * @param string $config
     * @return array|ImageManager|mixed
     * @throws Exception
     */
    function customcaptcha(string $config = 'default')
    {
        return app('customcaptcha')->create($config);
    }
}

if (!function_exists('customcaptcha_src')) {
    /**
     * @param string $config
     * @return string
     */
    function customcaptcha_src(string $config = 'default'): string
    {
        return app('customcaptcha')->src($config);
    }
}

if (!function_exists('customcaptcha_img')) {

    /**
     * @param string $config
     * @return string
     */
    function customcaptcha_img(string $config = 'default'): string
    {
        return app('customcaptcha')->img($config);
    }
}

if (!function_exists('customcaptcha_check')) {
    /**
     * @param string $value
     * @return bool
     */
    function customcaptcha_check(string $value): bool
    {
        return app('customcaptcha')->check($value);
    }
}

if (!function_exists('customcaptcha_api_check')) {
    /**
     * @param string $value
     * @param string $key
     * @param string $config
     * @return bool
     */
    function customcaptcha_api_check(string $value, string $key, string $config = 'default'): bool
    {
        return app('customcaptcha')->check_api($value, $key, $config);
    }
}

if (!function_exists('customcaptcha_validation')) {
    function customcaptcha_validation()
    {
        $captcha_validation = [];
        if (setting('enable_custom_captcha')) {

            $captcha_validation = [
                'customcaptcha' => 'sometimes|required|customcaptcha',
            ];
        } elseif (setting('enable_captcha') && is_plugin_active('captcha')) {

            $captcha_validation = [
                'g-recaptcha-response' => 'sometimes|required|captcha',

            ];
        } else {
            $captcha_validation = [];
        }
        return $captcha_validation;
    }
}
