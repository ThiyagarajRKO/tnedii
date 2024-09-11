<?php

namespace Impiger\IncubationCenter\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\IncubationCenter\Http\Requests\IncubationCenterRequest;
use Impiger\IncubationCenter\Repositories\Interfaces\IncubationCenterInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\IncubationCenter\Tables\IncubationCenterTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\IncubationCenter\Forms\IncubationCenterForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class IncubationCenterController extends BaseController
{
    /**
     * @var IncubationCenterInterface
     */
    protected $incubationCenterRepository;

    /**
     * @param IncubationCenterInterface $incubationCenterRepository
     */
    public function __construct(IncubationCenterInterface $incubationCenterRepository)
    {
        $this->incubationCenterRepository = $incubationCenterRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param IncubationCenterTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(IncubationCenterTable $table)
    {
        page_title()->setTitle(trans('plugins/incubation-center::incubation-center.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/incubation-center::incubation-center.create'));

        return $formBuilder->create(IncubationCenterForm::class)->renderForm();
    }

    /**
     * @param IncubationCenterRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(IncubationCenterRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $incubationCenter = $this->incubationCenterRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(INCUBATION_CENTER_MODULE_SCREEN_NAME, $request, $incubationCenter));
        
        
        return $response
            ->setPreviousUrl(route('incubation-center.index'))
            ->setNextUrl(route('incubation-center.edit', $incubationCenter->id))
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
        
        $incubationCenter = $this->incubationCenterRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $incubationCenter));

        $name = ($incubationCenter->name) ? ' "' . $incubationCenter->name . '"' : "";
        page_title()->setTitle(trans('plugins/incubation-center::incubation-center.edit') . $name);

        return $formBuilder->create(IncubationCenterForm::class, ['model' => $incubationCenter])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $incubationCenter = $this->incubationCenterRepository->findOrFail($id);
        $name = ($incubationCenter->name) ? ' "' . $incubationCenter->name . '"' : "";
        page_title()->setTitle(trans('plugins/incubation-center::incubation-center.view') . $name);

        return $formBuilder->create(IncubationCenterForm::class, ['model' => $incubationCenter, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param IncubationCenterRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, IncubationCenterRequest $request, BaseHttpResponse $response )
    {
        
        $incubationCenter = $this->incubationCenterRepository->findOrFail($id);
        
        
        $incubationCenter->fill($request->input());

        $this->incubationCenterRepository->createOrUpdate($incubationCenter);

        event(new UpdatedContentEvent(INCUBATION_CENTER_MODULE_SCREEN_NAME, $request, $incubationCenter));
        
        
        return $response
            ->setPreviousUrl(route('incubation-center.index'))
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
            $incubationCenter = $this->incubationCenterRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('incubation-center', array($id), "Impiger\IncubationCenter\Models\IncubationCenter");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->incubationCenterRepository->delete($incubationCenter);
            
            event(new DeletedContentEvent(INCUBATION_CENTER_MODULE_SCREEN_NAME, $request, $incubationCenter));

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

        $dataExist = CrudHelper::isDependentDataExist('incubation-center', $ids, "Impiger\IncubationCenter\Models\IncubationCenter");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $incubationCenter = $this->incubationCenterRepository->findOrFail($id);
            $this->incubationCenterRepository->delete($incubationCenter);
            
            event(new DeletedContentEvent(INCUBATION_CENTER_MODULE_SCREEN_NAME, $request, $incubationCenter));
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
            $bulkUpload = new BulkImport(new \Impiger\IncubationCenter\Models\IncubationCenter);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\IncubationCenter\Models\IncubationCenter')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\IncubationCenter\Models\IncubationCenter')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\IncubationCenter\Models\IncubationCenter')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
