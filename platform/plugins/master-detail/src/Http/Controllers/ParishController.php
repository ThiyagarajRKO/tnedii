<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\ParishRequest;
use Impiger\MasterDetail\Repositories\Interfaces\ParishInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\ParishTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\ParishForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class ParishController extends BaseController
{
    /**
     * @var ParishInterface
     */
    protected $parishRepository;

    /**
     * @param ParishInterface $parishRepository
     */
    public function __construct(ParishInterface $parishRepository)
    {
        $this->parishRepository = $parishRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param ParishTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ParishTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::parish.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::parish.create'));

        return $formBuilder->create(ParishForm::class)->renderForm();
    }

    /**
     * @param ParishRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ParishRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $parish = $this->parishRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(PARISH_MODULE_SCREEN_NAME, $request, $parish));
        
        
        return $response
            ->setPreviousUrl(route('parish.index'))
            ->setNextUrl(route('parish.edit', $parish->id))
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
        
        $parish = $this->parishRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $parish));

        $name = ($parish->name) ? ' "' . $parish->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::parish.edit') . $name);

        return $formBuilder->create(ParishForm::class, ['model' => $parish])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $parish = $this->parishRepository->findOrFail($id);
        $name = ($parish->name) ? ' "' . $parish->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::parish.view') . $name);

        return $formBuilder->create(ParishForm::class, ['model' => $parish, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param ParishRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ParishRequest $request, BaseHttpResponse $response )
    {
        
        $parish = $this->parishRepository->findOrFail($id);
        
        
        $parish->fill($request->input());

        $this->parishRepository->createOrUpdate($parish);

        event(new UpdatedContentEvent(PARISH_MODULE_SCREEN_NAME, $request, $parish));
        
        
        return $response
            ->setPreviousUrl(route('parish.index'))
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
            $parish = $this->parishRepository->findOrFail($id);
        
            $dataExist = CrudHelper::isDependentDataExist('parish', array($id), "Impiger\MasterDetail\Models\Parish");
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->parishRepository->delete($parish);
            
            event(new DeletedContentEvent(PARISH_MODULE_SCREEN_NAME, $request, $parish));

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
        
        $dataExist = CrudHelper::isDependentDataExist('parish', $ids, "Impiger\MasterDetail\Models\Parish");
            
        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $parish = $this->parishRepository->findOrFail($id);
            $this->parishRepository->delete($parish);
            
            event(new DeletedContentEvent(PARISH_MODULE_SCREEN_NAME, $request, $parish));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Parish);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Parish')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Parish')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Parish')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
