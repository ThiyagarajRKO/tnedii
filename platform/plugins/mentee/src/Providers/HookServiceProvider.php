<?php

namespace Impiger\Mentee\Providers;

use Illuminate\Support\ServiceProvider;
use Impiger\Base\Forms\FormBuilder;
use Theme;
use App\Utils\CrudHelper;
use Str;
use Illuminate\Support\Arr;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var Form Builder Utils
     */
    protected $formBuilder;
    protected $stats;
    /**
     * @throws \Throwable
     */
    public function boot(FormBuilder $formBuilder)
    {
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'getMenteeRegionWisePieStats'], 15, 2);
        $this->app->booted(function () use ($formBuilder) {
            
            #{register_external_submodule}
            if (function_exists('add_shortcode')) {
                $this->formBuilder = $formBuilder;
                $that = $this;
                CrudHelper::declareShortCode($that, 'mentee');
            }
        });
    }

    public function getMenteeRegionWisePieStats($widgets, $widgetSettings)
    {
        $config = [
            'key' => 'pie-mentees-by-region',
            'title' => trans('plugins/mentee::mentee.name'),
            'icon' => 'fa fa-user',
            'color' => '#3598dc',
            'route' => route('mentee.index'),
        ];
        return $this->renderDashboardStats($widgets, $widgetSettings, $config);
    }

    public function renderDashboardStats($widgets, $widgetSettings, $config)
    {
        $user = \Auth::user();
        $loginRoles = $user->roles()->get()->pluck('slug')->toArray();
        if ($user->hasPermission('mentee.index')) {
            $regions = \Impiger\MasterDetail\Models\Region::pluck('name');
            \Log::info("getMenteeRegionWisePieStats");
            \Log::info(json_encode($regions));
            $extraColors = THEME_EXTRA_COLORS;
            $data = ($this->stats) ? $this->stats : app(\Impiger\Mentee\Repositories\Interfaces\MenteeInterface::class)->getMenteesCountByRegionWise();
            $subStatsData = [];
            $index = 0;
            foreach ($regions as $region) {
                $subStatsData[] = [
                    "title" => $region,
                    "cnt" => Arr::get($data, strtolower($region)),
                    "color" => $extraColors[$index]
                ];
                $index++;
                // strtolower($region)
            }
                    
            return (new \App\Utils\Supports\CrudDashboardWidgetInstance)
                ->setPermission('mentee.index')
                ->setKey($config['key'])
                ->setTitle($config['title'])
                ->setIcon($config['icon'])
                ->setColor($config['color'])
                ->setRoute($config['route'])
                ->setHasSubStats(true)
                ->setStatsTotal(Arr::get($data, 'menteesTotalCount'))
                ->setSubStats($subStatsData)
                ->setBodyClass('scroll-table')
                ->setStatsDisplayType('pie')
                ->setColumn('col-md-6 col-sm-6')
                ->init($widgets, $widgetSettings);
        }
        return $widgets;
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
               ->setFormOption('template', 'theme-form.form-no-wrap')
               ->setActionButtons(view('module.shortcodeactionbtn')->render())
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
