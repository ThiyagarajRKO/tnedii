<?php

namespace Impiger\AnnualActionPlan\Providers;

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
        // add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'renderAnnualActionPlanBudgetBarChart'], 0, 2);
        $this->app->booted(function () use ($formBuilder) {
            
            #{register_external_submodule}
            if (function_exists('add_shortcode')) {
                $this->formBuilder = $formBuilder;
                $that = $this;
                CrudHelper::declareShortCode($that, 'annual-action-plan');
            }
        });
    }

    public function renderAnnualActionPlanBudgetBarChart($widgets, $widgetSettings) {
        
        $user = \Auth::user();
        $loginRoles = $user->roles()->get()->pluck('slug')->toArray();
        if ($user->hasPermission('annual-action-plan.index')) {
            
            $actionPlanTotalBudget = CrudHelper::getActionPlanTotalBudget();
            if($actionPlanTotalBudget) {
                \Log::info("renderAnnualActionPlanBudgetBarChart -> action plan");
                \Log::info(json_encode($actionPlanTotalBudget));

                foreach ($actionPlanTotalBudget as $value) {
                    $totalBudget[] = array(
                        'title' => $value['title'],
                        'cnt' => $value['cnt'],
                        'key' => 'total_budget'
                    );
                    $approvedBudget[] = array(
                        'title' => $value['label'],
                        'cnt' => $value['budget_approved'],
                        'key' => 'budget_approved'
                    );
                }

                $actionPlanBudgetConfig = [
                    array(
                        'title' => 'Total Budget',
                        'slug' => 'total-budget',
                        "is_sub_stats" => "1",
                        "stats_type" => 'bar',
                        'data' => $totalBudget
                    ),
                    array(
                        'title' => 'Approved Budget',
                        'slug' => 'budget-approved',
                        "is_sub_stats" => "1",
                        "stats_type" => 'bar',
                        'data' => $approvedBudget
                    )
                ]
                ;

                \Log::info(json_encode($actionPlanBudgetConfig));
            }
            // $actionPlanBudgetConfig = CrudHelper::getActionPlanBudget();

            return (new \Impiger\Crud\Supports\CrudDashboardWidgetInstance)
            ->setPermission('annual-action-plan.index')
            ->setKey('annual-action-plan-total-budget')
            ->setTitle('Annual Action Plan Total Budget')
            ->setIcon('fa fa-inr')
            ->setRoute(route('annual-action-plan.index'))
            ->setHasSubStats(true)
            ->setSubStats($actionPlanBudgetConfig)
            ->setStatsConfig([])
            ->setColumn('col-md-6 col-sm-6')
            ->setBodyClass('scroll-table')
            ->setModule('annual-action-plan')
            ->setSubModule('annual-action-plan')
            ->setStatsDisplayType('bar')
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
                Theme::asset()
                    ->container('footer')
                    ->usePath(false)
                    ->add('annual-action-plan-custom-js', asset('vendor/core/plugins/annual-action-plan/js/annual-action-plan.js'), ['jquery'], [], '1.0.0');
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
