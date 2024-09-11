<?php

namespace Impiger\Attendance\Providers;

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
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'getCandidateAttendanceStats'], 15, 2);
        $this->app->booted(function () use ($formBuilder) {
            
            #{register_external_submodule}
            if (function_exists('add_shortcode')) {
                $this->formBuilder = $formBuilder;
                $that = $this;
                CrudHelper::declareShortCode($that, 'attendance');
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
                Theme::asset()
                    ->container('footer')
                    ->usePath(false)
                    ->add('attendance-custom-js', asset('vendor/core/plugins/attendance/js/attendance.js'), ['jquery'], [], '1.0.0');
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
    
    public function getCandidateAttendanceStats($widgets, $widgetSettings)
    {
        $config = [
            'key' => 'pie-candidate-attendance',
            'title' => trans('plugins/attendance::attendance.name'),
            'icon' => 'fa fa-clock',
            'color' => '#3598dc',
            'route' => route('attendance.index'),
        ];
        return $this->renderDashboardStats($widgets, $widgetSettings, $config);
    }
    public function renderDashboardStats($widgets, $widgetSettings, $config)
    {
        $user = \Auth::user();
        $loginRoles = $user->roles()->get()->pluck('slug')->toArray();
        $defaultData = array(
            'totalPresentDays' => 0,
            'totalAbsentDays' => 0,
            'totalDays' => 0
        );

        if (in_array(CANDIDATE_ROLE_SLUG, $loginRoles)) {
            $data = ($this->stats) ? $this->stats : app(\Impiger\Attendance\Repositories\Interfaces\AttendanceInterface::class)->getAttendanceByCandidate();
            if($data && !$data['totalDays']) {
                $data['totalDays'] = $defaultData['totalDays'];
            }

            $subStatsData = [
                [
                    "title" => "Present",
                    "cnt" => Arr::get($data, 'totalPresentDays'),
                    "color" => "#32c5d2"
                ],
                [
                    "title" => "Absent",
                    "cnt" => Arr::get($data, 'totalAbsentDays'),
                    "color" => "#e7505a"
                ]
            ];
        
            return (new \App\Utils\Supports\CrudDashboardWidgetInstance)
                ->setPermission('attendance.index')
                ->setKey($config['key'])
                ->setTitle($config['title'])
                ->setIcon($config['icon'])
                ->setColor($config['color'])
                ->setRoute($config['route'])
                ->setHasSubStats(true)
                ->setStatsTotal(Arr::get($data, 'totalDays'))
                ->setSubStats($subStatsData)
                ->setBodyClass('scroll-table')
                ->setStatsDisplayType('pie')
                ->setColumn('col-md-6 col-sm-6')
                ->init($widgets, $widgetSettings);
        }
        return $widgets;
    }
}
