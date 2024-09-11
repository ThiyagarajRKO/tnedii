<?php

namespace Impiger\SimpleSlider\Providers;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Shortcode\Compilers\Shortcode;
use Impiger\SimpleSlider\Repositories\Interfaces\SimpleSliderInterface;
use Illuminate\Support\ServiceProvider;
use Theme;
use Log;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->booted(function () {
            if (!$this->app->isDownForMaintenance()) {
                if (setting('simple_slider_using_assets', true) && defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
                    Theme::asset()
                        ->container('footer')
                        ->usePath(false)
                        ->add('owl.carousel',
                            asset('vendor/core/plugins/simple-slider/libraries/owl-carousel/owl.carousel.css'), [], [],
                            '1.0.0')
                        ->add('simple-slider-css', asset('vendor/core/plugins/simple-slider/css/simple-slider.css'), [],
                            [], '1.0.0')
                        ->add('carousel',
                            asset('vendor/core/plugins/simple-slider/libraries/owl-carousel/owl.carousel.js'),
                            ['jquery'], [], '1.0.0')
                        ->add('simple-slider-js', asset('vendor/core/plugins/simple-slider/js/simple-slider.js'),
                            ['jquery'], [], '1.0.0');
                }

                if (function_exists('shortcode')) {
                    add_shortcode('simple-slider',
                        trans('plugins/simple-slider::simple-slider.simple_slider_shortcode_name'),
                        trans('plugins/simple-slider::simple-slider.simple_slider_shortcode_description'),
                        [$this, 'render']);

                    shortcode()->setAdminConfig('simple-slider', function () {
                        $sliders = $this->app->make(SimpleSliderInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED]);

                        return view('plugins/simple-slider::partials.simple-slider-admin-config', compact('sliders'))->render();
                    });
                }
            }

            add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSettings'], 301);
        });
    }

    /**
     * @param Shortcode $shortcode
     * @return null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function render($shortcode)
    {
        $slider = $this->app->make(SimpleSliderInterface::class)->getFirstBy([
            'key'    => $shortcode->key,
            'status' => BaseStatusEnum::PUBLISHED,
        ]);
        
        $youtube_slider = $this->app->make(SimpleSliderInterface::class)->getFirstBy([
            'key'    => 'youtube-slider',
            'status' => BaseStatusEnum::PUBLISHED,
        ]);

        if (empty($slider) && empty($youtube_slider)) {
            return null;
        }

        // return view(apply_filters(SIMPLE_SLIDER_VIEW_TEMPLATE, 'plugins/simple-slider::sliders'), ['sliders' => $slider->sliderItems]);
        
         /* @Customized By Ubaidur start */

         $vMimeType = array('mp4', 'mpeg', 'avi', 'ogv', 'ts', 'webm', '3gp', '3g2');
         $iMimeType = array('tif','tiff','webp','svg','png','jpeg','jpg','gif','bmp','avif');

        $image_sliders = array();
        $video_sliders = array();

        foreach ($slider->sliderItems as $key => $value) {
            $fileExtention = substr($value->image, strrpos($value->image, '.') + 1);
            $type="image";
            if(in_array($fileExtention,$iMimeType)){
                $type="image";
            }else if(in_array($fileExtention,$iMimeType)){
                $type="video";                
            }
            // Log::info((Array)$value);
            $data[] = array (
                'id' => $value->id,
                'title' => $value->title,
                'description' => $value->description,
                'type' => $type,
                'link' => $value->link,
                'image' => $value->image,
                'order' => $value->order,
                'simple_slider_id' => $value->simple_slider_id,
            );
        }
        $youtube_data = [];
        if(isset($youtube_slider->sliderItems))
        {
            foreach ($youtube_slider->sliderItems as $key => $value) {
                $youtube_data[] = array (
                    'id' => $value->id,
                    'title' => $value->title,
                    'description' => $value->description,
                    'type' => 'image',
                    'link' => $value->link,
                    'image' => $value->image,
                    'order' => $value->order,
                    'simple_slider_id' => $value->simple_slider_id,
                );
            }
        }
        Log::info("render sliders");
        Log::info($data);
        $image_sliders = $this->typeFilter($data, ['image']);
        $video_sliders = $this->typeFilter($data, ['video']);

        $sliders = array('video_sliders' => $video_sliders, 'image_sliders' => $image_sliders, 'sliders' => $slider->sliderItems, 'youtube_sliders' => $youtube_data);

        return view(apply_filters(SIMPLE_SLIDER_VIEW_TEMPLATE, 'plugins/simple-slider::sliders'), $sliders);
        /* @Customized By Ubaidur end */
    }

    /* @Customized By Ubaidur start */
    public function typeFilter($data, $type) {
        return  array_filter(
            $data,
            function ($value, $key) use ($type) {
                return in_array($value['type'], $type);
            },
            ARRAY_FILTER_USE_BOTH 
        );
    }
    /* @Customized By Ubaidur end */

    /**
     * @param null $data
     * @return string
     * @throws \Throwable
     */
    public function addSettings($data = null)
    {
        return $data . view('plugins/simple-slider::setting')->render();
    }
}
