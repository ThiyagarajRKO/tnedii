<?php

namespace Impiger\Reports\Http\Controllers;

use Assets;
use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Reports\Http\Requests\ReportsRequest;
use Impiger\Reports\Repositories\Interfaces\ReportsInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Reports\Tables\ReportsTable;
use Impiger\Reports\Tables\DistrictWiseReportsTable;
use Impiger\Reports\Tables\DistrictWiseDetailsReportsTable;
use Impiger\Reports\Tables\ProgramWiseReportsTable;
use Impiger\Reports\Tables\PIAWiseReportsTable;
use Impiger\Reports\Tables\ProgramWiseDetailsReportsTable;
use Impiger\Reports\Tables\CommunityWiseReportsTable;
use Impiger\Reports\Tables\ReligionWiseReportsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Reports\Forms\ReportsForm;
use Impiger\Base\Forms\FormBuilder;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;

class ReportsController extends BaseController
{
    /**
     * @var ReportsInterface
     */
    protected $reportsRepository;

    /**
     * @param ReportsInterface $reportsRepository
     */
    public function __construct(ReportsInterface $reportsRepository)
    {
        $this->reportsRepository = $reportsRepository;
        Assets::addScriptsDirectly([
             'vendor/core/plugins/crud/js/laravel_sql_parser.js',
            ]);
         Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/msme-candidate-details/js/msme-candidate-details.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param ReportsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ReportsTable $table)
    {
        page_title()->setTitle(trans('plugins/reports::reports.name'));

        return $table->renderTable();
    }
    
    /**
     * @param ReportsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function getDistrictWiseBeneficariesCount(DistrictWiseReportsTable $table)
    {
        page_title()->setTitle(trans('plugins/reports::reports.name'));

        return $table->renderTable();
    }
    
    /**
     * @param ReportsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function getDistrictWiseBeneficariesDetails(DistrictWiseDetailsReportsTable $table)
    {
        $pageTitle = base64_decode(request()->get('title')) ." ".trans('plugins/reports::reports.name');
        page_title()->setTitle($pageTitle);

        return $table->renderTable();
    }
     /**
     * @param ReportsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function getProgramWiseBeneficariesCount(ProgramWiseReportsTable $table)
    {
        page_title()->setTitle(trans('plugins/reports::reports.name'));

        return $table->renderTable();
    }
    
    public function getCommunityWiseBeneficariesCount(CommunityWiseReportsTable $table)
    {
        page_title()->setTitle(trans('plugins/reports::reports.name'));

        return $table->renderTable();
    }
    
    public function getReligionWiseBeneficariesCount(ReligionWiseReportsTable $table)
    {
        page_title()->setTitle(trans('plugins/reports::reports.name'));

        return $table->renderTable();
    }
    
    public function getPiaWiseBeneficariesCount(PIAWiseReportsTable $table)
    {
        page_title()->setTitle(trans('plugins/reports::reports.name'));

        return $table->renderTable();
    }
    
    

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/reports::reports.create'));

        return $formBuilder->create(ReportsForm::class)->renderForm();
    }

    /**
     * @param ReportsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ReportsRequest $request, BaseHttpResponse $response)
    {
        $reports = $this->reportsRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(REPORTS_MODULE_SCREEN_NAME, $request, $reports));

        return $response
            ->setPreviousUrl(route('reports.index'))
            ->setNextUrl(route('reports.edit', $reports->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $reports = $this->reportsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $reports));

        page_title()->setTitle(trans('plugins/reports::reports.edit') . ' "' . $reports->name . '"');

        return $formBuilder->create(ReportsForm::class, ['model' => $reports])->renderForm();
    }

    /**
     * @param int $id
     * @param ReportsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ReportsRequest $request, BaseHttpResponse $response)
    {
        $reports = $this->reportsRepository->findOrFail($id);

        $reports->fill($request->input());

        $this->reportsRepository->createOrUpdate($reports);

        event(new UpdatedContentEvent(REPORTS_MODULE_SCREEN_NAME, $request, $reports));

        return $response
            ->setPreviousUrl(route('reports.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $reports = $this->reportsRepository->findOrFail($id);

            $this->reportsRepository->delete($reports);

            event(new DeletedContentEvent(REPORTS_MODULE_SCREEN_NAME, $request, $reports));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $reports = $this->reportsRepository->findOrFail($id);
            $this->reportsRepository->delete($reports);
            event(new DeletedContentEvent(REPORTS_MODULE_SCREEN_NAME, $request, $reports));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
    
    public static function getFilters(){
        $filters = [
            'district' => [
                'title'    => 'District',
                'type'     => 'select',
                'choices' => CrudHelper::getSelectBoxChoices([], 'external', 'district', 'name', 'id'),
            ],
            'training_title' => [
                'title'    => 'Program',
                'type'     => 'select',
                'choices' => CrudHelper::getSelectBoxChoices([], 'external', 'training_title', 'name', 'id'),
            ],
            'gender' => [
                'title'    => 'Gender',
                'type'     => 'select',
                'choices' => \Impiger\MasterDetail\Models\MasterDetail::where(['attribute' => 'gender'])->pluck('name', 'id')->toArray(),
            ],
            'community' => [
                'title'    => 'Community',
                'type'     => 'select',
                'choices' => \Impiger\MasterDetail\Models\MasterDetail::where(['attribute' => 'community'])->pluck('name', 'id')->toArray(),
            ],
            'religion' => [
                'title'    => 'Religion',
                'type'     => 'select',
                'choices' => \Impiger\MasterDetail\Models\MasterDetail::where(['attribute' => 'religion'])->pluck('name', 'id')->toArray(),
            ],
            'candidate_type' => [
                'title'    => 'Candidate Type',
                'type'     => 'select',
                'choices' => \Impiger\MasterDetail\Models\MasterDetail::where(['attribute' => 'candidate_type'])->pluck('name', 'id')->toArray(),
            ],
        ];
        
        return $filters;
    }
    
    public static function applyFilterCondition($repository, $query, string $key, string $operator, ?string $value) {
        $dbKey = $key;
        $table = $repository->getTable();
        if (strpos($key, '.') !== -1) {
            $key = Arr::last(explode('.', $key));
        } else {
            $dbKey = $table . '.' .$key;
        }

        switch ($key) {
            case 'created_at':
            case 'updated_at':
                if (!$value) {
                    break;
                }

                $value = \Carbon\Carbon::createFromFormat(config('core.base.general.date_format.date'), $value)->toDateString();
                $query = $query->whereDate($dbKey, $operator, $value);
                break;
            case 'district':
                if(!$value){
                    break;
                }
                $query = $query->where('D.id',$value);
                break;
            case 'training_title':
                if(!$value){
                    break;
                }
                $query = $query->where('TT.id',$value);
                break;
            case 'gender':
                if(!$value){
                    break;
                }
                $query = $query->leftjoin('attribute_options AS G','G.id','entrepreneurs.gender_id')->where('G.id',$value);
                break;
            case 'community':
                if(!$value){
                    break;
                }
                $query = $query->leftjoin('attribute_options AS C','C.id','entrepreneurs.community')->where('C.id',$value);
                break;
            case 'religion':
                if(!$value){
                    break;
                }
                $query = $query->leftjoin('attribute_options AS R','R.id','entrepreneurs.religion_id')->where('R.id',$value);
                break;
            case 'candidate_type':
                if(!$value){
                    break;
                }
                $query = $query->leftjoin('attribute_options AS CA','CA.id','entrepreneurs.candidate_type_id')->where('CA.id',$value);
                break;
            default:
            /* @Customized By Ramesh Esakki */
                if (is_null($value)) {
                    break;
                }
                    if ($operator === 'like') {
                        $query = $query->where($dbKey, $operator, '%' . $value . '%');
                        break;
                    }

                    if ($operator !== '=') {
                        $value = (float)$value;
                    }
                    $query = $query->where($dbKey, $operator, $value);
        }


        return $query;
    }
    
    public static function  getReportConfigs($module,$slug){
        if(!$module && !$slug){
            return redirect()->back();
        }
        $reportConfigs = [];
        $cruds = DB::table('cruds')->where('module_name',$module)->first();
        if($cruds){
            $moduleLower = strtolower($cruds->module_name);
            $moduleUpper = ucfirst(Str::camel($cruds->module_name));
            $parentModuleUpper = ucfirst(Str::camel($cruds->module_name));
            if($cruds->parent_id){
                $parent = DB::table('cruds')->where('id',$cruds->oarent_id)->first();
                $parentModuleUpper = ucfirst(Str::camel($parent->module_name));
            }
            $config = CF_decode_json($cruds->report_config);
            foreach($config as $row){                
                $reportConfigs['model'] = 'Impiger\\'.$parentModuleUpper.'\Models\\'.$moduleUpper;
                $reportConfigs['title'] = $row['title'];
                $reportConfigs['slug'] = $row['slug'];
                $reportConfigs['columns'] = $row['field'];          
                $reportConfigs['selectedColumns'] = explode(",",$row['sel_fields']); 
               
                
                $reportConfigs['query'] = $row['sql_query'];                
            }
        }
        return $reportConfigs;
    }
    
    public static function getReportsPermissions(){
        $cruds = DB::table('cruds')->whereNotNull('report_config')->get();
        $permissions = [
            [
      'name' => 'Reports',
     'flag' => 'plugins.reports'
    ]
        ];
        if($cruds){
            foreach($cruds as $crud){
                $config = CF_decode_json($crud->report_config);
                foreach($config as $row){ 
                    $permissions[] = [
                            'name' => $row['title'] ,
                            'flag' => $row['slug'] . ".index",
                            'parent_flag' => 'plugins.reports' 
                        ];
                }
            }
        }
        return $permissions;
    }
    public static function getReportsMenu(){
        $cruds = DB::table('cruds')->whereNotNull('report_config')->get();
        $menus = [];
        if($cruds){
            foreach($cruds as $crud){
                $config = CF_decode_json($crud->report_config);
                foreach($config as $row){ 
                    $menus[]= 
                            "dashboard_menu()->registerItem([
                                'id'          => 'cms-plugin-'".$row['slug'].",
                                'priority'    => 1 ,
                                'parent_id'   => 'cms-plugin-reports',
                                'name'        => ".$row['title'].",
                                'icon'        => null,
                                'url'         => route('".$row['slug'].".index'),
                                'permissions' => ['".$row['slug'].".index'],
                            ]);" 
                        ;
                }
            }
        }
        return $menus;
    }
    
}
