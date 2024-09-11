<?php

namespace Impiger\TrainingTitle\Providers;

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
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'renderTotalTrainingProgramsStats'], 20, 2);
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'renderCurrentYearTotalProgramsStats'], 21, 2);
        $this->app->booted(function () use ($formBuilder) {
            
            #{register_external_submodule}
            if (function_exists('add_shortcode')) {
                $this->formBuilder = $formBuilder;
                $that = $this;
                CrudHelper::declareShortCode($that, 'training-title');
            }
        });
    }

    public function renderTotalTrainingProgramsStats($widgets, $widgetSettings) {
        // CrudHelper::getTotalMentorMentees();
        $totalPrograms = CrudHelper::getTotalTrainingPrograms();
        $freePrograms = CrudHelper::getTotalFreeTrainingPrograms();
        $paidPrograms = CrudHelper::getTotalPaidTrainingPrograms();
        // dd($mentor);
        // admin/mentor?filter_table_id=plugins-spoke-registration-table&filter_columns[]=spoke_registration.wf_status&filter_operators[]==&filter_values[]=approved
        // \Log::info("renderTotalTrainingProgramsStats");
        // \Log::info(json_encode($totalPrograms));
        // \Log::info(json_encode($freePrograms));
        // \Log::info(json_encode($paidPrograms));

        if($totalPrograms && $freePrograms && $paidPrograms) {

            \Log::info("inside if");

            $subStats = [
                array(
                    "title" => $freePrograms[0]['title'],
                    "is_sub_stats" => "1",
                    "parent_stats_id" => "total-training-programs",
                    "route" => "admin/training-titles?filter_table_id=plugins-training-title-table&filter_columns[]=training_title.fee_paid&filter_operators[]==&filter_values[]=1",
                    "order" => "1",
                    "stats_type" => null,
                    "operation_type" => "CNT",
                    "show_backend" => "1",
                    "show_frontend" => "0",
                    "cnt" => $freePrograms[0]['cnt']
                ),
                array(
                    "title" => $paidPrograms[0]['title'],
                    "is_sub_stats" => "1",
                    "parent_stats_id" => "total-training-programs",
                    "route" => "admin/training-titles?filter_table_id=plugins-training-title-table&filter_columns[]=training_title.fee_paid&filter_operators[]==&filter_values[]=2",
                    "order" => "2",
                    "stats_type" => null,
                    "operation_type" => "CNT",
                    "show_backend" => "1",
                    "show_frontend" => "0",
                    "cnt" => $paidPrograms[0]['cnt']
                ),
               
            ];

            $user = \Auth::user();
            $loginRoles = $user->roles()->get()->pluck('slug')->toArray();
            if ($user->hasPermission('training-title.index') && !in_array(CANDIDATE_ROLE_SLUG, $loginRoles)) {
            // $entrepreneurByCandidateTypeConfig = CrudHelper::getEntrepreneurListByCandidateType();
            return (new \Impiger\Crud\Supports\CrudDashboardWidgetInstance)
            ->setPermission('training-title.index')
            ->setKey('total-training-programs')
            ->setTitle($totalPrograms[0]['title'])
            ->setIcon('fa fa-inr')
            ->setColor('#32c5d2')
            ->setStatsOrder(1)
            ->setRoute(route('training-title.index'))
            ->setHasSubStats(true)
            ->setSubStats($subStats)
            ->setStatsConfig([
                "title" => $totalPrograms[0]['title'],
		        "slug" => "total-training-programs",
                "parent_stats_id" => null,
                "route" => "admin/training-title",
                "stats_type" => ["stats"],
		        "operation_type" => "CNT",
            ])
            ->setStatsTotal($totalPrograms[0]['cnt'])
            ->setStatsDisplayType('stats')
            ->setModule('training-title')
            ->setSubModule('training-title')
            ->setType('stats')
            ->init($widgets, $widgetSettings);
            }
            return $widgets;

        }
    }

    public function renderCurrentYearTotalProgramsStats($widgets, $widgetSettings) {
        // CrudHelper::getTotalMentorMentees();
        $currentYearTotalPrograms = CrudHelper::getCurrentYearTotalTrainingPrograms();
        $completedPrograms = CrudHelper::getCurrentYearTotalCompletedTrainingPrograms();
        $futurePrograms = CrudHelper::getCurrentYearTotalUpcomingTrainingPrograms();
        // \Log::info("renderCurrentYearTotalPrograms");
        // \Log::info(json_encode($currentYearTotalPrograms));
        // \Log::info(json_encode($completedPrograms));
        // \Log::info(json_encode($futurePrograms));

        if($currentYearTotalPrograms && $completedPrograms && $futurePrograms) {

            \Log::info("inside if");
            // admin/training-title?filter_table_id=plugins-training-title-table&filter_columns[]=training_title.training_end_date&filter_operators[]=<&filter_values[]=date('Y-m-d')
            $subStats = [
                array(
                    "title" => $completedPrograms[0]['title'],
                    "is_sub_stats" => "1",
                    "parent_stats_id" => "current-year-training-programs",
                    "route" => "admin/training-title?filter_table_id=plugins-training-title-table&filter_columns[]=training_title.training_end_date&filter_operators[]=<&filter_values[]=".date('Y-m-d'),
                    "order" => "1",
                    "stats_type" => null,
                    "operation_type" => "CNT",
                    "show_backend" => "1",
                    "show_frontend" => "0",
                    "cnt" => $completedPrograms[0]['cnt']
                ),
                array(
                    "title" => $futurePrograms[0]['title'],
                    "is_sub_stats" => "1",
                    "parent_stats_id" => "current-year-training-programs",
                    "route" => "admin/training-title?filter_table_id=plugins-training-title-table&filter_columns[]=training_title.training_start_date&filter_operators[]=>&filter_values[]=".date('Y-m-d'),
                    "order" => "2",
                    "stats_type" => null,
                    "operation_type" => "CNT",
                    "show_backend" => "1",
                    "show_frontend" => "0",
                    "cnt" => $futurePrograms[0]['cnt']
                ),
               
            ];

            $total = 0;
            if(isset($currentYearTotalPrograms[0]) && isset($currentYearTotalPrograms[0]['cnt'])) {
                $total = $currentYearTotalPrograms[0]['cnt'];
            }

            $user = \Auth::user();
            $loginRoles = $user->roles()->get()->pluck('slug')->toArray();
            
            if ($user->hasPermission('training-title.index') && !in_array(CANDIDATE_ROLE_SLUG, $loginRoles)) {
            // $entrepreneurByCandidateTypeConfig = CrudHelper::getEntrepreneurListByCandidateType();
                return (new \Impiger\Crud\Supports\CrudDashboardWidgetInstance)
                ->setPermission('training-title.index')
                ->setKey('current-year-training-programs')
                ->setTitle($currentYearTotalPrograms[0]['title'])
                ->setIcon('fa fa-inr')
                ->setColor('#32c5d2')
                ->setStatsOrder(2)
                ->setRoute(route('training-title.index'))
                ->setHasSubStats(true)
                ->setSubStats($subStats)
                ->setStatsConfig([
                    "title" => $currentYearTotalPrograms[0]['title'],
                    "slug" => "current-year-training-programs",
                    "parent_stats_id" => null,
                    "route" => "admin/training-title",
                    "stats_type" => ["stats"],
                    "operation_type" => "CNT",
                ])
                ->setStatsTotal($total)
                ->setStatsDisplayType('stats')
                ->setModule('training-title')
                ->setSubModule('training-title')
                ->setType('stats')
                ->init($widgets, $widgetSettings);
            }
            return $widgets;

        }
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
                    ->add('training-title-custom-js', asset('vendor/core/plugins/training-title/js/training-title.js'), ['jquery'], [], '1.0.0');
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
