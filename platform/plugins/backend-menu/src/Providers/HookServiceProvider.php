<?php

namespace Impiger\BackendMenu\Providers;

use Illuminate\Support\ServiceProvider;
use Impiger\BackendMenu\Forms\BackendMenuForm;
use Impiger\BackendMenu\Tables\BackendMenuTable;
use Impiger\Base\Forms\FormBuilder;
use Impiger\BackendMenu\Repositories\Interfaces\BackendMenuInterface;
use Theme;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var Form Builder Utils
     */
    protected $formBuilder;

    /**
     * @var Table Utils
     */
    protected $table;

    /**
     * @var BackendMenuInterface
     */
    protected $backend_menuRepository;

    /**
     * @throws \Throwable
     */
    public function boot(FormBuilder $formBuilder, BackendMenuInterface $backend_menuRepository, BackendMenuTable $table)
    {
        $this->backend_menuRepository = $backend_menuRepository;
        $this->app->booted(function () use ($formBuilder, $table) {
            
            #{register_external_submodule}
            
            if (function_exists('add_shortcode')) {
                $this->formBuilder = $formBuilder;
                $this->table = $table;
            }
        });
    }

    #{render_table_short_code_method}

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
                Theme::asset()
                    ->usePath(false)
                    ->add('backend-menu-table0-css', asset('/vendor/core/core/base/libraries/font-awesome/css/fontawesome.min.css'), [], [], '1.0.0')
                    ->add('backend-menu-table1-css', asset('vendor/core/core/base/libraries/datatables/media/css/dataTables.bootstrap.min.css'), [], [], '1.0.0')
                    ->add('backend-menu-table2-css', asset('vendor/core/core/base/libraries/datatables/extensions/Buttons/css/buttons.bootstrap.min.css'), [], [], '1.0.0')
                    ->add('backend-menu-table3-css', asset('vendor/core/core/base/libraries/datatables/extensions/Responsive/css/responsive.bootstrap.min.css'), [], [], '1.0.0')
                    ->add('backend-menu-table4-css', asset('vendor/core/core/table/css/table.css'), [], [], '1.0.0')
                    ->add('backend-menu-table5-css', asset('vendor/core/plugins/crud/css/module_custom_styles.css'), [], [], '1.0.0');
                Theme::asset()->container('footer')->writeScript('customScript', "var ImpigerVariables = {};
                                                    ImpigerVariables.languages = {};
                                                    ImpigerVariables.languages.tables = {export:'Export',csv:'csv',print:'print',reset:'reset',reload:'reload',excel:'excel'};");
                Theme::asset()
                    ->container('footer')
                    ->usePath(false)
                    ->add('shortcode-table1-js', asset('vendor/core/core/base/libraries/datatables/media/js/jquery.dataTables.min.js'), ['jquery'], [], '1.0.0')
                    ->add('backend-menu-table2-js', asset('vendor/core/core/base/libraries/datatables/media/js/dataTables.bootstrap.min.js'), ['jquery'], [], '1.0.0')
                    ->add('backend-menu-table3-js', asset('vendor/core/core/base/libraries/datatables/extensions/Buttons/js/dataTables.buttons.min.js'), ['jquery'], [], '1.0.0')
                    ->add('backend-menu-table4-js', asset('vendor/core/core/base/libraries/datatables/extensions/Buttons/js/buttons.colVis.min.js'), ['jquery'], [], '1.0.0')
                    ->add('backend-menu-table5-js', asset('vendor/core/core/base/libraries/datatables/extensions/Buttons/js/buttons.bootstrap.min.js'), ['jquery'], [], '1.0.0')
                    ->add('backend-menu-table6-js', asset('vendor/core/core/base/libraries/datatables/extensions/Responsive/js/dataTables.responsive.min.js'), ['jquery'], [], '1.0.0')
                    ->add('backend-menu-table7-js', asset('vendor/core/core/table/js/table.js'), ['jquery'], [], '1.0.0')
                    ->add('backend-menu-table8-js', asset('vendor/core/core/table/js/filter.js'), ['jquery'], [], '1.0.0');
            });
        }
    }
}
