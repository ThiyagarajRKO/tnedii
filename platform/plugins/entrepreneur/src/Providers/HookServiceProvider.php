<?php

namespace Impiger\Entrepreneur\Providers;

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
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'renderEntrepreneurListByCandidateTypePieChart'], 15, 2);
        // add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'renderCertificateCompletedStats'], 16, 2);
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'renderTotalMentorMenteeStats'], 15, 2);
        $this->app->booted(function () use ($formBuilder) {
                        
			#{register_external_submodule}
            if (function_exists('add_shortcode')) {
                $this->formBuilder = $formBuilder;
                $that = $this;
                CrudHelper::declareShortCode($that, 'entrepreneur');
            }
        });
    }

    public function renderEntrepreneurListByCandidateTypePieChart($widgets, $widgetSettings) {
        
        $user = \Auth::user();
        $loginRoles = $user->roles()->get()->pluck('slug')->toArray();
        // if (inArrayAny(FEES_COLLECTION_STATS_PIE_CHART_ROLE, $loginRoles)) {
            $entrepreneurByCandidateTypeConfig = CrudHelper::getEntrepreneurListByCandidateType();

            return (new \Impiger\Crud\Supports\CrudDashboardWidgetInstance)
            ->setPermission('entrepreneur.index')
            ->setKey('Entrepreneurs')
            ->setTitle('Entrepreneurs')
            ->setIcon('fa fa-inr')
            ->setRoute(route('entrepreneur.index'))
            ->setHasSubStats(true)
            ->setSubStats($entrepreneurByCandidateTypeConfig)
            ->setStatsConfig([])
            ->setColumn('col-md-6 col-sm-6')
            ->setBodyClass('scroll-table')
            ->setModule('entrepreneur')
            ->setSubModule('entrepreneur')
            ->setStatsDisplayType('pie')
            ->init($widgets, $widgetSettings);
        // }
        return $widgets;
    }

    public function renderTotalMentorMenteeStats($widgets, $widgetSettings) {
        // CrudHelper::getTotalMentorMentees();
        $mentor = CrudHelper::getTotalMentor();
        $mentee = CrudHelper::getTotalMentees();
        $vendor = CrudHelper::getTotalVendor();
        // dd($mentor);
        // admin/mentor?filter_table_id=plugins-spoke-registration-table&filter_columns[]=spoke_registration.wf_status&filter_operators[]==&filter_values[]=approved

        if($mentor && $mentee && $vendor) {

            $total = $mentor[0]['cnt'] + $mentee[0]['cnt'] + $vendor[0]['cnt'];

            $subStats = [
                array(
                    "title" => $mentor[0]['title'],
                    "is_sub_stats" => "1",
                    "parent_stats_id" => "candidate",
                    "route" => "admin/mentors",
                    "order" => "9",
                    "stats_type" => null,
                    "operation_type" => "CNT",
                    "show_backend" => "1",
                    "show_frontend" => "0",
                    "cnt" => $mentor[0]['cnt']
                ),
                array(
                    "title" => $mentee[0]['title'],
                    "is_sub_stats" => "1",
                    "parent_stats_id" => "candidate",
                    "route" => "admin/mentees",
                    "order" => "10",
                    "stats_type" => null,
                    "operation_type" => "CNT",
                    "show_backend" => "1",
                    "show_frontend" => "0",
                    "cnt" => $mentee[0]['cnt']
                ),
                array(
                    "title" => $vendor[0]['title'],
                    "is_sub_stats" => "1",
                    "parent_stats_id" => "candidate",
                    "route" => "admin/vendors",
                    "order" => "11",
                    "stats_type" => null,
                    "operation_type" => "CNT",
                    "show_backend" => "1",
                    "show_frontend" => "0",
                    "cnt" => $vendor[0]['cnt']
                )
            ];

            $user = \Auth::user();
            $loginRoles = $user->roles()->get()->pluck('slug')->toArray();
            if ($user->hasPermission('entrepreneur.index') && $user->hasPermission('vendor.index')) {
            // $entrepreneurByCandidateTypeConfig = CrudHelper::getEntrepreneurListByCandidateType();
            return (new \Impiger\Crud\Supports\CrudDashboardWidgetInstance)
            ->setPermission('entrepreneur.index')
            ->setKey('candidate')
            ->setTitle('Candidates')
            ->setIcon('fa fa-inr')
            ->setColor('#32c5d2')
            ->setStatsOrder(9)
            ->setRoute(route('entrepreneur.index'))
            ->setHasSubStats(true)
            ->setSubStats($subStats)
            ->setStatsConfig([
                "title" => "Candidates",
		        "slug" => "candidates",
                "parent_stats_id" => null,
                "route" => "admin/entrepreneur",
                "stats_type" => ["stats"],
		        "operation_type" => "CNT",
            ])
            ->setStatsTotal($total)
            ->setStatsDisplayType('stats')
            ->setModule('entrepreneur')
            ->setSubModule('entrepreneur')
            ->setType('stats')
            ->init($widgets, $widgetSettings);
            }
        return $widgets;

        }
        
    }

    public function renderCertificateCompletedStats($widgets, $widgetSettings) {
        
        $certstats = CrudHelper::getCertificateCompleted();
        if($certstats) {
            $user = \Auth::user();
            $loginRoles = $user->roles()->get()->pluck('slug')->toArray();
            if ($user->hasPermission('trainee.index')) {
                // $entrepreneurByCandidateTypeConfig = CrudHelper::getEntrepreneurListByCandidateType();
                // 
                // admin/trainees?filter_table_id=plugins-trainee-table&filter_columns[]=trainees.certificate_status&filter_operators[]==&filter_values[]=1
                return (new \Impiger\Crud\Supports\CrudDashboardWidgetInstance)
                ->setPermission('trainee.index')
                ->setKey('certificate-completed')
                ->setTitle($certstats[0]['title'])
                ->setIcon('fa fa-inr')
                ->setColor('#32c5d2')
                ->setStatsOrder(10)
                ->setRoute('admin/trainees?filter_table_id=plugins-trainee-table&filter_columns[]=trainees.certificate_status&filter_operators[]==&filter_values[]=1')
                ->setHasSubStats(true)
                ->setSubStats([])
                ->setStatsConfig([
                    "title" => "Certificate Completed",
                    "slug" => "certificate-completed",
                    "parent_stats_id" => null,
                    "route" => "admin/trainees?filter_table_id=plugins-trainee-table&filter_columns[]=trainees.certificate_status&filter_operators[]==&filter_values[]=1",
                    "stats_type" => ["stats"],
                    "operation_type" => "CNT",
                ])
                ->setStatsTotal($certstats[0]['cnt'])
                ->setStatsDisplayType('stats')
                ->setModule('trainee')
                ->setSubModule('trainee')
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
                    ->add('entrepreneur-custom-js', asset('vendor/core/plugins/entrepreneur/js/entrepreneur.js'), ['jquery'], [], '1.0.0');
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
