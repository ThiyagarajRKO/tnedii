<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\QualificationsRequest;
use Impiger\MasterDetail\Repositories\Interfaces\QualificationsInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\QualificationsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\QualificationsForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class QualificationsController extends BaseController
{
    /**
     * @var QualificationsInterface
     */
    protected $qualificationsRepository;

    /**
     * @param QualificationsInterface $qualificationsRepository
     */
    public function __construct(QualificationsInterface $qualificationsRepository)
    {
        $this->qualificationsRepository = $qualificationsRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param QualificationsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(QualificationsTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::qualifications.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::qualifications.create'));

        return $formBuilder->create(QualificationsForm::class)->renderForm();
    }

    /**
     * @param QualificationsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(QualificationsRequest $request, BaseHttpResponse $response )
    {
        
        
        $qualifications = $this->qualificationsRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(QUALIFICATIONS_MODULE_SCREEN_NAME, $request, $qualifications));
        
        
        return $response
            ->setPreviousUrl(route('qualifications.index'))
            ->setNextUrl(route('qualifications.edit', $qualifications->id))
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
        
        $qualifications = $this->qualificationsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $qualifications));

        $name = ($qualifications->name) ? ' "' . $qualifications->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::qualifications.edit') . $name);

        return $formBuilder->create(QualificationsForm::class, ['model' => $qualifications])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $qualifications = $this->qualificationsRepository->findOrFail($id);
        $name = ($qualifications->name) ? ' "' . $qualifications->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::qualifications.view') . $name);

        return $formBuilder->create(QualificationsForm::class, ['model' => $qualifications, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param QualificationsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, QualificationsRequest $request, BaseHttpResponse $response )
    {
        
        $qualifications = $this->qualificationsRepository->findOrFail($id);
        
        
        $qualifications->fill($request->input());

        $this->qualificationsRepository->createOrUpdate($qualifications);

        event(new UpdatedContentEvent(QUALIFICATIONS_MODULE_SCREEN_NAME, $request, $qualifications));
        
        
        return $response
            ->setPreviousUrl(route('qualifications.index'))
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
            $qualifications = $this->qualificationsRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('qualifications', array($id), "Impiger\MasterDetail\Models\Qualifications");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->qualificationsRepository->delete($qualifications);
            
            event(new DeletedContentEvent(QUALIFICATIONS_MODULE_SCREEN_NAME, $request, $qualifications));

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

        $dataExist = CrudHelper::isDependentDataExist('qualifications', $ids, "Impiger\MasterDetail\Models\Qualifications");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $qualifications = $this->qualificationsRepository->findOrFail($id);
            $this->qualificationsRepository->delete($qualifications);
            
            event(new DeletedContentEvent(QUALIFICATIONS_MODULE_SCREEN_NAME, $request, $qualifications));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Qualifications);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Qualifications')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Qualifications')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Qualifications')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
