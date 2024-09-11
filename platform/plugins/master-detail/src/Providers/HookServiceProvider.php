<?php

namespace Impiger\MasterDetail\Providers;

use Illuminate\Support\ServiceProvider;
use Impiger\Base\Forms\FormBuilder;
use Theme;
use App\Utils\CrudHelper;
use Str;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var Form Builder Utils
     */
    protected $formBuilder;

    /**
     * @throws \Throwable
     */
    public function boot(FormBuilder $formBuilder)
    {
        
        $this->app->booted(function () use ($formBuilder) {
            
            
			#{register_external_submodule}
            if (function_exists('add_shortcode')) {
                $this->formBuilder = $formBuilder;
                $that = $this;
                CrudHelper::declareShortCode($that, 'master-detail');
            }
        });
    }

    

    public function buildFormUsingShortcode($shortCode)
    {
        $slug = CrudHelper::getCrudModuleSlugUsingShortcode($shortCode, $this->shortcodeMap);

        if(!$slug) {
            return false;
        }

        $this->loadFormAssets();
        if ($shortCode->id) {
            $repository = app()->make('Impiger\\'.$slug['parentModule'].'\Repositories\Interfaces\\'.$slug['moduleUpper'].'Interface');
            $model = $repository->findOrFail($shortCode->id);
            $method = $this->formBuilder->create('Impiger\\'.$slug['parentModule'].'\Forms\\'.$slug['moduleUpper'].'Form', ['model' => $model])
                ->setFormOption('url', $slug['moduleLower'].'/updatedata/' . $shortCode->id);
        } else {
            $method = $this->formBuilder->create('Impiger\\'.$slug['parentModule'].'\Forms\\'.$slug['moduleUpper'].'Form')
                ->setFormOption('url', $slug['moduleLower'].'/postdata');
        }

        return $method
               ->setFormOption('template', 'plugins/crud::theme-form.form-no-wrap')
               ->setActionButtons(view('plugins/crud::module.shortcodeactionbtn')->render())
               ->renderForm();
    }

    public function buildTableUsingShortcode($shortCode)
    {
        $slug = CrudHelper::getCrudModuleSlugUsingShortcode($shortCode, $this->shortcodeMap);

        if(!$slug) {
            return false;
        }

        $table = app()->make('Impiger\\'.$slug['parentModule'].'\Tables\\'.$slug['moduleUpper'].'Table');
        $this->loadTableAssets();
        return $table->setView('core/table::base-table')
            ->setOptions(['shortcode' => true])
            ->setHasFilter(false)
            ->setTableConfig($shortCode)
            ->setAjaxUrl(route('public.'.$slug['moduleLower'].'.index'))
            ->renderTable();
    }

    public function loadFormAssets()
    {
        if (defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
            $this->app->booted(function () {
                
            });
        }
    }

    public function loadTableAssets()
    {
        if (defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
            $this->app->booted(function () {
                CrudHelper::loadTableAssets();
            });
        }
    }
}
