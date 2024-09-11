<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\SubcountyRequest;
use Impiger\MasterDetail\Repositories\Interfaces\SubcountyInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\SubcountyTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\SubcountyForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class SubcountyController extends BaseController
{
    /**
     * @var SubcountyInterface
     */
    protected $subcountyRepository;

    /**
     * @param SubcountyInterface $subcountyRepository
     */
    public function __construct(SubcountyInterface $subcountyRepository)
    {
        $this->subcountyRepository = $subcountyRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param SubcountyTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SubcountyTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::subcounty.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::subcounty.create'));

        return $formBuilder->create(SubcountyForm::class)->renderForm();
    }

    /**
     * @param SubcountyRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(SubcountyRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $subcounty = $this->subcountyRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(SUBCOUNTY_MODULE_SCREEN_NAME, $request, $subcounty));
        
        
        return $response
            ->setPreviousUrl(route('subcounty.index'))
            ->setNextUrl(route('subcounty.edit', $subcounty->id))
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
        
        $subcounty = $this->subcountyRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $subcounty));

        $name = ($subcounty->name) ? ' "' . $subcounty->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::subcounty.edit') . $name);

        return $formBuilder->create(SubcountyForm::class, ['model' => $subcounty])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $subcounty = $this->subcountyRepository->findOrFail($id);
        $name = ($subcounty->name) ? ' "' . $subcounty->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::subcounty.view') . $name);

        return $formBuilder->create(SubcountyForm::class, ['model' => $subcounty, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param SubcountyRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, SubcountyRequest $request, BaseHttpResponse $response )
    {
        
        $subcounty = $this->subcountyRepository->findOrFail($id);
        
        
        $subcounty->fill($request->input());

        $this->subcountyRepository->createOrUpdate($subcounty);

        event(new UpdatedContentEvent(SUBCOUNTY_MODULE_SCREEN_NAME, $request, $subcounty));
        
        
        return $response
            ->setPreviousUrl(route('subcounty.index'))
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
            $subcounty = $this->subcountyRepository->findOrFail($id);
        
            $dataExist = CrudHelper::isDependentDataExist('subcounty', array($id), "Impiger\MasterDetail\Models\Subcounty");
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->subcountyRepository->delete($subcounty);
            
            event(new DeletedContentEvent(SUBCOUNTY_MODULE_SCREEN_NAME, $request, $subcounty));

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
        
        $dataExist = CrudHelper::isDependentDataExist('subcounty', $ids, "Impiger\MasterDetail\Models\Subcounty");
            
        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $subcounty = $this->subcountyRepository->findOrFail($id);
            $this->subcountyRepository->delete($subcounty);
            
            event(new DeletedContentEvent(SUBCOUNTY_MODULE_SCREEN_NAME, $request, $subcounty));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Subcounty);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Subcounty')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Subcounty')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Subcounty')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
