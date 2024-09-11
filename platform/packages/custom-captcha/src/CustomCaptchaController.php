<?php

namespace Impiger\CustomCaptcha;

use Exception;
use Illuminate\Routing\Controller;

/**
 * Class CustomCaptchaController
 * @package Impiger\CustomCaptcha
 */
class CustomCaptchaController extends Controller
{
    /**
     * get CAPTCHA
     *
     * @param CustomCaptcha $customcaptcha
     * @param string $config
     * @return array|mixed
     * @throws Exception
     */
    public function getCustomCaptcha(CustomCaptcha $customcaptcha, string $config = 'default')
    {
        if (ob_get_contents()) {
            ob_clean();
        }
        return $customcaptcha->create($config);
    }

    /**
     * get CAPTCHA api
     *
     * @param CustomCaptcha $customcaptcha
     * @param string $config
     * @return array|mixed
     * @throws Exception
     */
    public function getCustomCaptchaApi(CustomCaptcha $customcaptcha, string $config = 'default')
    {
        return $customcaptcha->create($config, true);
    }

}
