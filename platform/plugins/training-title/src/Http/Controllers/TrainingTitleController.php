<?php

namespace Impiger\TrainingTitle\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\TrainingTitle\Http\Requests\TrainingTitleRequest;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\TrainingTitle\Tables\TrainingTitleTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\TrainingTitle\Forms\TrainingTitleForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class TrainingTitleController extends BaseController
{
    /**
     * @var TrainingTitleInterface
     */
    protected $trainingTitleRepository;

    /**
     * @param TrainingTitleInterface $trainingTitleRepository
     */
    public function __construct(TrainingTitleInterface $trainingTitleRepository)
    {
        $this->trainingTitleRepository = $trainingTitleRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/training-title/js/training-title.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param TrainingTitleTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TrainingTitleTable $table)
    {
        page_title()->setTitle(trans('plugins/training-title::training-title.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/training-title::training-title.create'));

        return $formBuilder->create(TrainingTitleForm::class)->renderForm();
    }

    /**
     * @param TrainingTitleRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(TrainingTitleRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);

        $request['name'] = CrudHelper::formatRows($request->input('annual_action_plan_id'), 'database', 'annual_action_plan|id|name', '', '');
        
        $trainingTitle = $this->trainingTitleRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(TRAINING_TITLE_MODULE_SCREEN_NAME, $request, $trainingTitle));
        
        
        return $response
            ->setPreviousUrl(route('training-title.index'))
            ->setNextUrl(route('training-title.edit', $trainingTitle->id))
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
        
        $trainingTitle = $this->trainingTitleRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $trainingTitle));

        $name = ($trainingTitle->name) ? ' "' . $trainingTitle->name . '"' : "";
        page_title()->setTitle(trans('plugins/training-title::training-title.edit') . $name);

        return $formBuilder->create(TrainingTitleForm::class, ['model' => $trainingTitle])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $trainingTitle = $this->trainingTitleRepository->findOrFail($id);
        $name = ($trainingTitle->name) ? ' "' . $trainingTitle->name . '"' : "";
        page_title()->setTitle(trans('plugins/training-title::training-title.view') . $name);

        return $formBuilder->create(TrainingTitleForm::class, ['model' => $trainingTitle, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param TrainingTitleRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, TrainingTitleRequest $request, BaseHttpResponse $response )
    {
        
        $trainingTitle = $this->trainingTitleRepository->findOrFail($id);
                
        $trainingTitle->fill($request->input());

        $trainingTitle->name = CrudHelper::formatRows($trainingTitle->annual_action_plan_id, 'database', 'annual_action_plan|id|name', '', '');

        $this->trainingTitleRepository->createOrUpdate($trainingTitle);

        event(new UpdatedContentEvent(TRAINING_TITLE_MODULE_SCREEN_NAME, $request, $trainingTitle));
        
        
        return $response
            ->setPreviousUrl(route('training-title.index'))
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
            $trainingTitle = $this->trainingTitleRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('training-title', array($id), "Impiger\TrainingTitle\Models\TrainingTitle");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->trainingTitleRepository->delete($trainingTitle);
            
            event(new DeletedContentEvent(TRAINING_TITLE_MODULE_SCREEN_NAME, $request, $trainingTitle));

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

        $dataExist = CrudHelper::isDependentDataExist('training-title', $ids, "Impiger\TrainingTitle\Models\TrainingTitle");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $trainingTitle = $this->trainingTitleRepository->findOrFail($id);
            $this->trainingTitleRepository->delete($trainingTitle);
            
            event(new DeletedContentEvent(TRAINING_TITLE_MODULE_SCREEN_NAME, $request, $trainingTitle));
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
            $bulkUpload = new BulkImport(new \Impiger\TrainingTitle\Models\TrainingTitle);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\TrainingTitle\Models\TrainingTitle')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\TrainingTitle\Models\TrainingTitle')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\TrainingTitle\Models\TrainingTitle')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }

    public function subscribeToEvent($id, Request $request, BaseHttpResponse $response) {
        \Log::info("subscribeToEvent");
        // \Log::info($request);
        
        $trainingTitle = $this->trainingTitleRepository->findOrFail($id);
        $condition = array('user_id' => \Auth::id());
        $entrepreneur = \Impiger\Entrepreneur\Models\Entrepreneur::where($condition)->first();
        if(!$entrepreneur) {
            return $response->setError(true)->setMessage('Please login as a candidate/entrepreneur/student before subscribe!');
            // dd('test');
        //     throw ValidationException::withMessages([
        //         'trainee' => ['Please login as a candidate/entrepreneur/student before subscribe!'],
        //    ]);
        }

        $condition['entrepreneur_id'] = $entrepreneur->id;
        $condition['training_title_id'] = $id;
       

        $trainee = \Impiger\Entrepreneur\Models\Trainee::where($condition)->first();

        if($trainee) {
            \Log::info($condition);
            \Log::info(json_encode($trainee));
            return $response->setError(true)->setMessage('You have already subscribed!');
            // throw ValidationException::withMessages([
            //         'trainee' => ['You have already subscribed!'],
            // ]);
        }
        $condition['annual_action_plan_id'] = $trainingTitle->annual_action_plan_id;
        $condition['division_id'] = $trainingTitle->division_id;
        $condition['financial_year_id'] = $trainingTitle->financial_year_id;

        $trainee = app(\Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface::class)->createOrUpdate($condition);

        return $response
        ->setPreviousUrl(route('trainee.index'))
        ->setMessage('Subscribed successfully!');

        // return $response
        //     ->setPreviousUrl(route('training-title.index'))
        //     ->setMessage('Subscribed successfully!');
    }

   
}
