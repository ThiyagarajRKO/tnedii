<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\SpecializationsRequest;
use Impiger\MasterDetail\Repositories\Interfaces\SpecializationsInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\SpecializationsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\SpecializationsForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class SpecializationsController extends BaseController
{
    /**
     * @var SpecializationsInterface
     */
    protected $specializationsRepository;

    /**
     * @param SpecializationsInterface $specializationsRepository
     */
    public function __construct(SpecializationsInterface $specializationsRepository)
    {
        $this->specializationsRepository = $specializationsRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param SpecializationsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SpecializationsTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::specializations.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::specializations.create'));

        return $formBuilder->create(SpecializationsForm::class)->renderForm();
    }

    /**
     * @param SpecializationsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(SpecializationsRequest $request, BaseHttpResponse $response )
    {
        
        
        $specializations = $this->specializationsRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(SPECIALIZATIONS_MODULE_SCREEN_NAME, $request, $specializations));
        
        
        return $response
            ->setPreviousUrl(route('specializations.index'))
            ->setNextUrl(route('specializations.edit', $specializations->id))
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
        
        $specializations = $this->specializationsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $specializations));

        $name = ($specializations->name) ? ' "' . $specializations->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::specializations.edit') . $name);

        return $formBuilder->create(SpecializationsForm::class, ['model' => $specializations])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $specializations = $this->specializationsRepository->findOrFail($id);
        $name = ($specializations->name) ? ' "' . $specializations->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::specializations.view') . $name);

        return $formBuilder->create(SpecializationsForm::class, ['model' => $specializations, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param SpecializationsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, SpecializationsRequest $request, BaseHttpResponse $response )
    {
        
        $specializations = $this->specializationsRepository->findOrFail($id);
        
        
        $specializations->fill($request->input());

        $this->specializationsRepository->createOrUpdate($specializations);

        event(new UpdatedContentEvent(SPECIALIZATIONS_MODULE_SCREEN_NAME, $request, $specializations));
        
        
        return $response
            ->setPreviousUrl(route('specializations.index'))
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
            $specializations = $this->specializationsRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('specializations', array($id), "Impiger\MasterDetail\Models\Specializations");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->specializationsRepository->delete($specializations);
            
            event(new DeletedContentEvent(SPECIALIZATIONS_MODULE_SCREEN_NAME, $request, $specializations));

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

        $dataExist = CrudHelper::isDependentDataExist('specializations', $ids, "Impiger\MasterDetail\Models\Specializations");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $specializations = $this->specializationsRepository->findOrFail($id);
            $this->specializationsRepository->delete($specializations);
            
            event(new DeletedContentEvent(SPECIALIZATIONS_MODULE_SCREEN_NAME, $request, $specializations));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Specializations);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Specializations')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Specializations')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Specializations')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
