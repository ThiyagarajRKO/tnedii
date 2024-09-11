<?php

namespace Impiger\CustomCaptcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Impiger\CustomCaptcha\CustomCaptcha
 */
class CustomCaptcha extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'customcaptcha';
    }
}
