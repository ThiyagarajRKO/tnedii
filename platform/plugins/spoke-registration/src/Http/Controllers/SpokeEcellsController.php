<?php

namespace Impiger\SpokeRegistration\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\SpokeRegistration\Http\Requests\SpokeEcellsRequest;
use Impiger\SpokeRegistration\Repositories\Interfaces\SpokeEcellsInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\SpokeRegistration\Tables\SpokeEcellsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\SpokeRegistration\Forms\SpokeEcellsForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class SpokeEcellsController extends BaseController
{
    /**
     * @var SpokeEcellsInterface
     */
    protected $spokeEcellsRepository;

    /**
     * @param SpokeEcellsInterface $spokeEcellsRepository
     */
    public function __construct(SpokeEcellsInterface $spokeEcellsRepository)
    {
        $this->spokeEcellsRepository = $spokeEcellsRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param SpokeEcellsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SpokeEcellsTable $table)
    {
        page_title()->setTitle(trans('plugins/spoke-registration::spoke-ecells.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/spoke-registration::spoke-ecells.create'));

        return $formBuilder->create(SpokeEcellsForm::class)->renderForm();
    }

    /**
     * @param SpokeEcellsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(SpokeEcellsRequest $request, BaseHttpResponse $response )
    {
        
        
        $spokeEcells = $this->spokeEcellsRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(SPOKE_ECELLS_MODULE_SCREEN_NAME, $request, $spokeEcells));
        
        
        return $response
            ->setPreviousUrl(route('spoke-ecells.index'))
            ->setNextUrl(route('spoke-ecells.edit', $spokeEcells->id))
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
        
        $spokeEcells = $this->spokeEcellsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $spokeEcells));

        $name = ($spokeEcells->name) ? ' "' . $spokeEcells->name . '"' : "";
        page_title()->setTitle(trans('plugins/spoke-registration::spoke-ecells.edit') . $name);

        return $formBuilder->create(SpokeEcellsForm::class, ['model' => $spokeEcells])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $spokeEcells = $this->spokeEcellsRepository->findOrFail($id);
        $name = ($spokeEcells->name) ? ' "' . $spokeEcells->name . '"' : "";
        page_title()->setTitle(trans('plugins/spoke-registration::spoke-ecells.view') . $name);

        return $formBuilder->create(SpokeEcellsForm::class, ['model' => $spokeEcells, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param SpokeEcellsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, SpokeEcellsRequest $request, BaseHttpResponse $response )
    {
        
        $spokeEcells = $this->spokeEcellsRepository->findOrFail($id);
        
        
        $spokeEcells->fill($request->input());

        $this->spokeEcellsRepository->createOrUpdate($spokeEcells);

        event(new UpdatedContentEvent(SPOKE_ECELLS_MODULE_SCREEN_NAME, $request, $spokeEcells));
        
        
        return $response
            ->setPreviousUrl(route('spoke-ecells.index'))
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
            $spokeEcells = $this->spokeEcellsRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('spoke-ecells', array($id), "Impiger\SpokeRegistration\Models\SpokeEcells");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->spokeEcellsRepository->delete($spokeEcells);
            
            event(new DeletedContentEvent(SPOKE_ECELLS_MODULE_SCREEN_NAME, $request, $spokeEcells));

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

        $dataExist = CrudHelper::isDependentDataExist('spoke-ecells', $ids, "Impiger\SpokeRegistration\Models\SpokeEcells");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $spokeEcells = $this->spokeEcellsRepository->findOrFail($id);
            $this->spokeEcellsRepository->delete($spokeEcells);
            
            event(new DeletedContentEvent(SPOKE_ECELLS_MODULE_SCREEN_NAME, $request, $spokeEcells));
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
            $bulkUpload = new BulkImport(new \Impiger\SpokeRegistration\Models\SpokeEcells);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\SpokeRegistration\Models\SpokeEcells')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\SpokeRegistration\Models\SpokeEcells')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\SpokeRegistration\Models\SpokeEcells')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
