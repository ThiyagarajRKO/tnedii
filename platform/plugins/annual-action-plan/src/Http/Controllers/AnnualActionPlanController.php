<?php

namespace Impiger\AnnualActionPlan\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\AnnualActionPlan\Http\Requests\AnnualActionPlanRequest;
use Impiger\AnnualActionPlan\Repositories\Interfaces\AnnualActionPlanInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\AnnualActionPlan\Tables\AnnualActionPlanTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\AnnualActionPlan\Forms\AnnualActionPlanForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class AnnualActionPlanController extends BaseController
{
    /**
     * @var AnnualActionPlanInterface
     */
    protected $annualActionPlanRepository;

    /**
     * @param AnnualActionPlanInterface $annualActionPlanRepository
     */
    public function __construct(AnnualActionPlanInterface $annualActionPlanRepository)
    {
        $this->annualActionPlanRepository = $annualActionPlanRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/annual-action-plan/js/annual-action-plan.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param AnnualActionPlanTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AnnualActionPlanTable $table)
    {
        page_title()->setTitle(trans('plugins/annual-action-plan::annual-action-plan.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/annual-action-plan::annual-action-plan.create'));

        return $formBuilder->create(AnnualActionPlanForm::class)->renderForm();
    }

    /**
     * @param AnnualActionPlanRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(AnnualActionPlanRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $annualActionPlan = $this->annualActionPlanRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(ANNUAL_ACTION_PLAN_MODULE_SCREEN_NAME, $request, $annualActionPlan));
        
        
        return $response
            ->setPreviousUrl(route('annual-action-plan.index'))
            ->setNextUrl(route('annual-action-plan.edit', $annualActionPlan->id))
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
        
        $annualActionPlan = $this->annualActionPlanRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $annualActionPlan));

        $name = ($annualActionPlan->name) ? ' "' . $annualActionPlan->name . '"' : "";
        page_title()->setTitle(trans('plugins/annual-action-plan::annual-action-plan.edit') . $name);

        return $formBuilder->create(AnnualActionPlanForm::class, ['model' => $annualActionPlan])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $annualActionPlan = $this->annualActionPlanRepository->findOrFail($id);
        $name = ($annualActionPlan->name) ? ' "' . $annualActionPlan->name . '"' : "";
        page_title()->setTitle(trans('plugins/annual-action-plan::annual-action-plan.view') . $name);

        return $formBuilder->create(AnnualActionPlanForm::class, ['model' => $annualActionPlan, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param AnnualActionPlanRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, AnnualActionPlanRequest $request, BaseHttpResponse $response )
    {
        
        $annualActionPlan = $this->annualActionPlanRepository->findOrFail($id);
        
        
        $annualActionPlan->fill($request->input());

        $this->annualActionPlanRepository->createOrUpdate($annualActionPlan);

        event(new UpdatedContentEvent(ANNUAL_ACTION_PLAN_MODULE_SCREEN_NAME, $request, $annualActionPlan));
        
        
        return $response
            ->setPreviousUrl(route('annual-action-plan.index'))
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
            $annualActionPlan = $this->annualActionPlanRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('annual-action-plan', array($id), "Impiger\AnnualActionPlan\Models\AnnualActionPlan");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->annualActionPlanRepository->delete($annualActionPlan);
            
            event(new DeletedContentEvent(ANNUAL_ACTION_PLAN_MODULE_SCREEN_NAME, $request, $annualActionPlan));

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

        $dataExist = CrudHelper::isDependentDataExist('annual-action-plan', $ids, "Impiger\AnnualActionPlan\Models\AnnualActionPlan");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $annualActionPlan = $this->annualActionPlanRepository->findOrFail($id);
            $this->annualActionPlanRepository->delete($annualActionPlan);
            
            event(new DeletedContentEvent(ANNUAL_ACTION_PLAN_MODULE_SCREEN_NAME, $request, $annualActionPlan));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param ImportCustomFieldsAction $action
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function postImport(Request $request, BaseHttpResponse $response)
    {
           try {
            $request->validate([
                'file' => "required",
            ]);
            $bulkUpload = new BulkImport(new \Impiger\AnnualActionPlan\Models\AnnualActionPlan);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\AnnualActionPlan\Models\AnnualActionPlan')))." has been uploaded successfully");
            return $response->setMessage(trans('core/base::notices.bulk_upload_success_message'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors=[];
            foreach ($failures as $failure) {
                $error = $failure->values();
                $error['row'] = $failure->row();
                $error['error'] = implode(",",$failure->errors());
                if(false!==$key = array_search($failure->row(),array_column($errors,'row'))){
                    $errors[$key]['error'].="\r\n".implode(",",$failure->errors());
                }else{
                    $errors[]=$error;
                }
            }
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\AnnualActionPlan\Models\AnnualActionPlan')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\AnnualActionPlan\Models\AnnualActionPlan')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
