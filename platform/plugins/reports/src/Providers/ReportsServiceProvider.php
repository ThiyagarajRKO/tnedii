<?php

namespace Impiger\Reports\Providers;

use Impiger\Reports\Models\Reports;
use Illuminate\Support\ServiceProvider;
use Impiger\Reports\Repositories\Caches\ReportsCacheDecorator;
use Impiger\Reports\Repositories\Eloquent\ReportsRepository;
use Impiger\Reports\Repositories\Interfaces\ReportsInterface;
use Impiger\Base\Supports\Helper;
use Illuminate\Support\Facades\Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class ReportsServiceProvider extends ServiceProvider {

    use LoadAndPublishDataTrait;

    public function register() {
        $this->app->bind(ReportsInterface::class, function () {
            return new ReportsCacheDecorator(new ReportsRepository(new Reports));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot() {
        $this->setNamespace('plugins/reports')
                ->loadAndPublishConfigurations(['permissions'])
                ->loadMigrations()
                ->loadAndPublishTranslations()
                ->loadAndPublishViews()
                ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-plugins-reports',
                'priority' => 5,
                'parent_id' => null,
                'name' => 'plugins/reports::reports.name',
                'icon' => 'fa fa-book',
                'url' => route('reports.index'),
                'permissions' => ['reports.index'],
            ]);

            dashboard_menu()->registerItem([
                        'id' => 'cms-plugins-district-wise',
                        'priority' =>1,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'District Wise Beneficiaties Abstract',
                        'icon' => '',
                        'url' => route('reports.district_abstract'),
                        'permissions' => ['reports.district_abstract'],
                    ])                    
                    ->registerItem([
                        'id' => 'cms-plugins-program-wise',
                        'priority' => 2,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'Program Wise Beneficiaties Abstract',
                        'icon' => '',
                        'url' => route('reports.program_abstract'),
                        'permissions' => ['reports.program_abstract'],
                    ])
                    ->registerItem([
                        'id' => 'cms-plugins-community-wise',
                        'priority' => 3,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'Community Wise Beneficiaties Abstract',
                        'icon' => '',
                        'url' => route('reports.community_abstract'),
                        'permissions' => ['reports.community_abstract'],
                    ])
                    ->registerItem([
                        'id' => 'cms-plugins-religion-wise',
                        'priority' => 4,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'Religion Wise Beneficiaties Abstract',
                        'icon' => '',
                        'url' => route('reports.religion_abstract'),
                        'permissions' => ['reports.religion_abstract'],
                    ])
                    ->registerItem([
                        'id' => 'cms-plugins-disrtrict-wise-details',
                        'priority' => 4,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'District Wise Beneficiaties Textual',
                        'icon' => '',
                        'url' => route('reports.report_textual',['title' => base64_encode('District Wise Beneficiaties Textual')]),
                        'permissions' => ['reports.district_textual'],
                    ])
                    ->registerItem([
                        'id' => 'cms-plugins-program-wise-details',
                        'priority' => 5,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'Program Wise Beneficiaties Textual',
                        'icon' => '',
                        'url' => route('reports.report_textual',['title' => base64_encode('Program Wise Beneficiaties Textual')]),
                        'permissions' => ['reports.program_textual'],
                    ])
                    ->registerItem([
                        'id' => 'cms-plugins-community-wise-details',
                        'priority' => 5,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'Community Wise Beneficiaties Textual',
                        'icon' => '',
                        'url' => route('reports.report_textual',['title' => base64_encode('Community Wise Beneficiaties Textual')]),
                        'permissions' => ['reports.community_textual'],
                    ])
                    ->registerItem([
                        'id' => 'cms-plugins-religion-wise-details',
                        'priority' => 5,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'Religion Wise Beneficiaties Textual',
                        'icon' => '',
                        'url' => route('reports.report_textual',['title' => base64_encode('Religion Wise Beneficiaties Textual')]),
                        'permissions' => ['reports.religion_textual'],
                    ])
                    ->registerItem([
                        'id' => 'cms-plugins-candidate-type-wise-details',
                        'priority' => 5,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'Candidate Type Wise Beneficiaties Textual',
                        'icon' => '',
                        'url' => route('reports.report_textual',['title' => base64_encode('Candidate Type Wise Beneficiaties Textual')]),
                        'permissions' => ['reports.candidate_textual'],
                    ])
                   ->registerItem([
                        'id' => 'cms-plugins-pia-wise',
                        'priority' => 5,
                        'parent_id' => 'cms-plugins-reports',
                        'name' => 'PIA Wise Beneficiaties Abstract',
                        'icon' => '',
                        'url' => route('reports.pia_abstract'),
                        'permissions' => ['reports.pia_abstract'],
                    ])

            ;
//            implode(",",\Impiger\Reports\Http\Controllers\ReportsController::getReportsMenu());
        });
    }

}
