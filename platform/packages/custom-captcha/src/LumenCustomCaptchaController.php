<?php

namespace Impiger\CustomCaptcha;

use Exception;
use Laravel\Lumen\Routing\Controller;

/**
 * Class CustomCaptchaController
 * @package Impiger\CustomCaptcha
 */
class LumenCustomCaptchaController extends Controller
{
    /**
     * get CAPTCHA
     *
     * @param CustomCaptcha $customcaptcha
     * @param string $config
     * @return array|mixed
     * @throws Exception
     */
    public function getCustomCaptcha(CustomCaptcha $customcaptcha, $config = 'default')
    {
        return $customcaptcha->create($config);
    }
}
