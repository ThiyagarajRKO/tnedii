<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\VillageRequest;
use Impiger\MasterDetail\Repositories\Interfaces\VillageInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\VillageTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\VillageForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class VillageController extends BaseController
{
    /**
     * @var VillageInterface
     */
    protected $villageRepository;

    /**
     * @param VillageInterface $villageRepository
     */
    public function __construct(VillageInterface $villageRepository)
    {
        $this->villageRepository = $villageRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param VillageTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(VillageTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::village.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::village.create'));

        return $formBuilder->create(VillageForm::class)->renderForm();
    }

    /**
     * @param VillageRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(VillageRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $village = $this->villageRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(VILLAGE_MODULE_SCREEN_NAME, $request, $village));
        
        
        return $response
            ->setPreviousUrl(route('village.index'))
            ->setNextUrl(route('village.edit', $village->id))
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
        
        $village = $this->villageRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $village));

        $name = ($village->name) ? ' "' . $village->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::village.edit') . $name);

        return $formBuilder->create(VillageForm::class, ['model' => $village])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $village = $this->villageRepository->findOrFail($id);
        $name = ($village->name) ? ' "' . $village->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::village.view') . $name);

        return $formBuilder->create(VillageForm::class, ['model' => $village, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param VillageRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, VillageRequest $request, BaseHttpResponse $response )
    {
        
        $village = $this->villageRepository->findOrFail($id);
        
        
        $village->fill($request->input());

        $this->villageRepository->createOrUpdate($village);

        event(new UpdatedContentEvent(VILLAGE_MODULE_SCREEN_NAME, $request, $village));
        
        
        return $response
            ->setPreviousUrl(route('village.index'))
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
            $village = $this->villageRepository->findOrFail($id);
        
            $dataExist = CrudHelper::isDependentDataExist('village', array($id), "Impiger\MasterDetail\Models\Village");
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->villageRepository->delete($village);
            
            event(new DeletedContentEvent(VILLAGE_MODULE_SCREEN_NAME, $request, $village));

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
        
        $dataExist = CrudHelper::isDependentDataExist('village', $ids, "Impiger\MasterDetail\Models\Village");
            
        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $village = $this->villageRepository->findOrFail($id);
            $this->villageRepository->delete($village);
            
            event(new DeletedContentEvent(VILLAGE_MODULE_SCREEN_NAME, $request, $village));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Village);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Village')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Village')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Village')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
