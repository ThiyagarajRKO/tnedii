<?php

namespace Impiger\TrainingTitleFinancialDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\TrainingTitleFinancialDetail\Http\Requests\TrainingTitleFinancialDetailRequest;
use Impiger\TrainingTitleFinancialDetail\Repositories\Interfaces\TrainingTitleFinancialDetailInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\TrainingTitleFinancialDetail\Tables\TrainingTitleFinancialDetailTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\TrainingTitleFinancialDetail\Forms\TrainingTitleFinancialDetailForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class TrainingTitleFinancialDetailController extends BaseController
{
    /**
     * @var TrainingTitleFinancialDetailInterface
     */
    protected $trainingTitleFinancialDetailRepository;

    /**
     * @param TrainingTitleFinancialDetailInterface $trainingTitleFinancialDetailRepository
     */
    public function __construct(TrainingTitleFinancialDetailInterface $trainingTitleFinancialDetailRepository)
    {
        $this->trainingTitleFinancialDetailRepository = $trainingTitleFinancialDetailRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/training-title-financial-detail/js/training-title-financial-detail.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param TrainingTitleFinancialDetailTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TrainingTitleFinancialDetailTable $table)
    {
        page_title()->setTitle(trans('plugins/training-title-financial-detail::training-title-financial-detail.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/training-title-financial-detail::training-title-financial-detail.create'));

        return $formBuilder->create(TrainingTitleFinancialDetailForm::class)->renderForm();
    }

    /**
     * @param TrainingTitleFinancialDetailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(TrainingTitleFinancialDetailRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $trainingTitleFinancialDetail = $this->trainingTitleFinancialDetailRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(TRAINING_TITLE_FINANCIAL_DETAIL_MODULE_SCREEN_NAME, $request, $trainingTitleFinancialDetail));
        
        
        return $response
            ->setPreviousUrl(route('training-title-financial-detail.index'))
            ->setNextUrl(route('training-title-financial-detail.edit', $trainingTitleFinancialDetail->id))
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
        
        $trainingTitleFinancialDetail = $this->trainingTitleFinancialDetailRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $trainingTitleFinancialDetail));

        $name = ($trainingTitleFinancialDetail->name) ? ' "' . $trainingTitleFinancialDetail->name . '"' : "";
        page_title()->setTitle(trans('plugins/training-title-financial-detail::training-title-financial-detail.edit') . $name);

        return $formBuilder->create(TrainingTitleFinancialDetailForm::class, ['model' => $trainingTitleFinancialDetail])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $trainingTitleFinancialDetail = $this->trainingTitleFinancialDetailRepository->findOrFail($id);
        $name = ($trainingTitleFinancialDetail->name) ? ' "' . $trainingTitleFinancialDetail->name . '"' : "";
        page_title()->setTitle(trans('plugins/training-title-financial-detail::training-title-financial-detail.view') . $name);

        return $formBuilder->create(TrainingTitleFinancialDetailForm::class, ['model' => $trainingTitleFinancialDetail, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param TrainingTitleFinancialDetailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, TrainingTitleFinancialDetailRequest $request, BaseHttpResponse $response )
    {
        
        $trainingTitleFinancialDetail = $this->trainingTitleFinancialDetailRepository->findOrFail($id);
        
        
        $trainingTitleFinancialDetail->fill($request->input());

        $this->trainingTitleFinancialDetailRepository->createOrUpdate($trainingTitleFinancialDetail);

        event(new UpdatedContentEvent(TRAINING_TITLE_FINANCIAL_DETAIL_MODULE_SCREEN_NAME, $request, $trainingTitleFinancialDetail));
        
        
        return $response
            ->setPreviousUrl(route('training-title-financial-detail.index'))
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
            $trainingTitleFinancialDetail = $this->trainingTitleFinancialDetailRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('training-title-financial-detail', array($id), "Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->trainingTitleFinancialDetailRepository->delete($trainingTitleFinancialDetail);
            
            event(new DeletedContentEvent(TRAINING_TITLE_FINANCIAL_DETAIL_MODULE_SCREEN_NAME, $request, $trainingTitleFinancialDetail));

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

        $dataExist = CrudHelper::isDependentDataExist('training-title-financial-detail', $ids, "Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $trainingTitleFinancialDetail = $this->trainingTitleFinancialDetailRepository->findOrFail($id);
            $this->trainingTitleFinancialDetailRepository->delete($trainingTitleFinancialDetail);
            
            event(new DeletedContentEvent(TRAINING_TITLE_FINANCIAL_DETAIL_MODULE_SCREEN_NAME, $request, $trainingTitleFinancialDetail));
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
            $bulkUpload = new BulkImport(new \Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
