<?php

namespace Impiger\CustomCaptcha;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Impiger\Base\Supports\Helper;
// use CustomCaptcha;


/**
 * Class CustomCaptchaServiceProvider
 * @package Impiger\CustomCaptcha
 */
class CustomCaptchaServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
    
        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSettings'], 300);
        $this->setNamespace('packages/custom-captcha')
        // ->loadAndPublishConfigurations(['customcaptcha'])
        ->loadAndPublishViews()
        ->loadAndPublishTranslations()
        ->publishAssets();

        // Publish configuration files
        $this->publishes([
            __DIR__ . '/../config/customcaptcha.php' => config_path('customcaptcha.php')
        ], 'config');

        // HTTP routing
        if (strpos($this->app->version(), 'Lumen') !== false) {
            /* @var Router $router */
            $router = $this->app;
            $router->get('customcaptcha[/api/{config}]', 'Impiger\CustomCaptcha\LumenCustomCaptchaController@getCustomCaptchaApi');
            $router->get('customcaptcha[/{config}]', 'Impiger\CustomCaptcha\LumenCustomCaptchaController@getCustomCaptcha');
        } else {
            /* @var Router $router */
            $router = $this->app['router'];
            if ((double)$this->app->version() >= 5.2) {
                $router->get('customcaptcha/api/{config?}', '\Impiger\CustomCaptcha\CustomCaptchaController@getCustomCaptchaApi')->middleware('web');
                $router->get('customcaptcha/{config?}', '\Impiger\CustomCaptcha\CustomCaptchaController@getCustomCaptcha')->middleware('web');
            } else {
                $router->get('customcaptcha/api/{config?}', '\Impiger\CustomCaptcha\CustomCaptchaController@getCustomCaptchaApi');
                $router->get('customcaptcha/{config?}', '\Impiger\CustomCaptcha\CustomCaptchaController@getCustomCaptcha');
            }
        }

        /* @var Factory $validator */
        $validator = $this->app['validator'];

        // Validator extensions
        $validator->extend('customcaptcha', function ($attribute, $value, $parameters) {
            
            return customcaptcha_check($value);
        });

        // Validator extensions
        $validator->extend('customcaptcha_api', function ($attribute, $value, $parameters) {
            return customcaptcha_api_check($value, $parameters[0], $parameters[1] ?? 'default');
        });
        $validator->replacer('customcaptcha', function ($message) {
            return $message === 'validation.customcaptcha' ? trans('plugins/captcha::captcha.failed_validate') : $message;
        });
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
}
    

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
        Helper::autoload(__DIR__ . '/../../helpers');
       
        
        // Merge configs
        $this->mergeConfigFrom(
            __DIR__ . '/../config/customcaptcha.php',
            'customcaptcha'
        );

        // Bind customcaptcha
        $this->app->bind('customcaptcha', function ($app) {
            return new CustomCaptcha(
                $app['Illuminate\Filesystem\Filesystem'],
                $app['Illuminate\Contracts\Config\Repository'],
                $app['Intervention\Image\ImageManager'],
                $app['Illuminate\Session\Store'],
                $app['Illuminate\Hashing\BcryptHasher'],
                $app['Illuminate\Support\Str']
            );
        });
        
      
    }
    public function addSettings($data = null)
    {
        return $data . view('packages/custom-captcha::setting')->render();

    }
}
