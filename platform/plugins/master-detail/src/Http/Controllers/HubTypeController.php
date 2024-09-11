<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\HubTypeRequest;
use Impiger\MasterDetail\Repositories\Interfaces\HubTypeInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\HubTypeTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\HubTypeForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class HubTypeController extends BaseController
{
    /**
     * @var HubTypeInterface
     */
    protected $hubTypeRepository;

    /**
     * @param HubTypeInterface $hubTypeRepository
     */
    public function __construct(HubTypeInterface $hubTypeRepository)
    {
        $this->hubTypeRepository = $hubTypeRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param HubTypeTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(HubTypeTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::hub-type.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::hub-type.create'));

        return $formBuilder->create(HubTypeForm::class)->renderForm();
    }

    /**
     * @param HubTypeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(HubTypeRequest $request, BaseHttpResponse $response )
    {
        
        
        $hubType = $this->hubTypeRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(HUB_TYPE_MODULE_SCREEN_NAME, $request, $hubType));
        
        
        return $response
            ->setPreviousUrl(route('hub-type.index'))
            ->setNextUrl(route('hub-type.edit', $hubType->id))
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
        
        $hubType = $this->hubTypeRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $hubType));

        $name = ($hubType->name) ? ' "' . $hubType->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::hub-type.edit') . $name);

        return $formBuilder->create(HubTypeForm::class, ['model' => $hubType])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $hubType = $this->hubTypeRepository->findOrFail($id);
        $name = ($hubType->name) ? ' "' . $hubType->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::hub-type.view') . $name);

        return $formBuilder->create(HubTypeForm::class, ['model' => $hubType, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param HubTypeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, HubTypeRequest $request, BaseHttpResponse $response )
    {
        
        $hubType = $this->hubTypeRepository->findOrFail($id);
        
        
        $hubType->fill($request->input());

        $this->hubTypeRepository->createOrUpdate($hubType);

        event(new UpdatedContentEvent(HUB_TYPE_MODULE_SCREEN_NAME, $request, $hubType));
        
        
        return $response
            ->setPreviousUrl(route('hub-type.index'))
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
            $hubType = $this->hubTypeRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('hub-type', array($id), "Impiger\MasterDetail\Models\HubType");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->hubTypeRepository->delete($hubType);
            
            event(new DeletedContentEvent(HUB_TYPE_MODULE_SCREEN_NAME, $request, $hubType));

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

        $dataExist = CrudHelper::isDependentDataExist('hub-type', $ids, "Impiger\MasterDetail\Models\HubType");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $hubType = $this->hubTypeRepository->findOrFail($id);
            $this->hubTypeRepository->delete($hubType);
            
            event(new DeletedContentEvent(HUB_TYPE_MODULE_SCREEN_NAME, $request, $hubType));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\HubType);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\HubType')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\HubType')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\HubType')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
