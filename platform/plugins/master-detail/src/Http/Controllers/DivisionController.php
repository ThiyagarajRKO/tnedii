<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\DivisionRequest;
use Impiger\MasterDetail\Repositories\Interfaces\DivisionInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\DivisionTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\DivisionForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class DivisionController extends BaseController
{
    /**
     * @var DivisionInterface
     */
    protected $divisionRepository;

    /**
     * @param DivisionInterface $divisionRepository
     */
    public function __construct(DivisionInterface $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param DivisionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(DivisionTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::division.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::division.create'));

        return $formBuilder->create(DivisionForm::class)->renderForm();
    }

    /**
     * @param DivisionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(DivisionRequest $request, BaseHttpResponse $response )
    {
        
        
        $division = $this->divisionRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(DIVISION_MODULE_SCREEN_NAME, $request, $division));
        
        
        return $response
            ->setPreviousUrl(route('division.index'))
            ->setNextUrl(route('division.edit', $division->id))
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
        
        $division = $this->divisionRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $division));

        $name = ($division->name) ? ' "' . $division->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::division.edit') . $name);

        return $formBuilder->create(DivisionForm::class, ['model' => $division])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $division = $this->divisionRepository->findOrFail($id);
        $name = ($division->name) ? ' "' . $division->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::division.view') . $name);

        return $formBuilder->create(DivisionForm::class, ['model' => $division, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param DivisionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, DivisionRequest $request, BaseHttpResponse $response )
    {
        
        $division = $this->divisionRepository->findOrFail($id);
        
        
        $division->fill($request->input());

        $this->divisionRepository->createOrUpdate($division);

        event(new UpdatedContentEvent(DIVISION_MODULE_SCREEN_NAME, $request, $division));
        
        
        return $response
            ->setPreviousUrl(route('division.index'))
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
            $division = $this->divisionRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('division', array($id), "Impiger\MasterDetail\Models\Division");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->divisionRepository->delete($division);
            
            event(new DeletedContentEvent(DIVISION_MODULE_SCREEN_NAME, $request, $division));

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

        $dataExist = CrudHelper::isDependentDataExist('division', $ids, "Impiger\MasterDetail\Models\Division");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $division = $this->divisionRepository->findOrFail($id);
            $this->divisionRepository->delete($division);
            
            event(new DeletedContentEvent(DIVISION_MODULE_SCREEN_NAME, $request, $division));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Division);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Division')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Division')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Division')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
