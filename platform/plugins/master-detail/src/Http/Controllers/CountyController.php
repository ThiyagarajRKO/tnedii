<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\CountyRequest;
use Impiger\MasterDetail\Repositories\Interfaces\CountyInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\CountyTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\CountyForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class CountyController extends BaseController
{
    /**
     * @var CountyInterface
     */
    protected $countyRepository;

    /**
     * @param CountyInterface $countyRepository
     */
    public function __construct(CountyInterface $countyRepository)
    {
        $this->countyRepository = $countyRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param CountyTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(CountyTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::county.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::county.create'));

        return $formBuilder->create(CountyForm::class)->renderForm();
    }

    /**
     * @param CountyRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CountyRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $county = $this->countyRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(COUNTY_MODULE_SCREEN_NAME, $request, $county));
        
        
        return $response
            ->setPreviousUrl(route('county.index'))
            ->setNextUrl(route('county.edit', $county->id))
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
        
        $county = $this->countyRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $county));

        $name = ($county->name) ? ' "' . $county->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::county.edit') . $name);

        return $formBuilder->create(CountyForm::class, ['model' => $county])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $county = $this->countyRepository->findOrFail($id);
        $name = ($county->name) ? ' "' . $county->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::county.view') . $name);

        return $formBuilder->create(CountyForm::class, ['model' => $county, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param CountyRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CountyRequest $request, BaseHttpResponse $response )
    {
        
        $county = $this->countyRepository->findOrFail($id);
        
        
        $county->fill($request->input());

        $this->countyRepository->createOrUpdate($county);

        event(new UpdatedContentEvent(COUNTY_MODULE_SCREEN_NAME, $request, $county));
        
        
        return $response
            ->setPreviousUrl(route('county.index'))
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
            $county = $this->countyRepository->findOrFail($id);
        
            $dataExist = CrudHelper::isDependentDataExist('county', array($id), "Impiger\MasterDetail\Models\County");
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->countyRepository->delete($county);
            
            event(new DeletedContentEvent(COUNTY_MODULE_SCREEN_NAME, $request, $county));

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
        
        $dataExist = CrudHelper::isDependentDataExist('county', $ids, "Impiger\MasterDetail\Models\County");
            
        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $county = $this->countyRepository->findOrFail($id);
            $this->countyRepository->delete($county);
            
            event(new DeletedContentEvent(COUNTY_MODULE_SCREEN_NAME, $request, $county));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\County);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\County')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\County')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\County')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
