<?php

namespace Impiger\TrainingTitle\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\TrainingTitle\Http\Requests\OnlineTrainingSessionRequest;
use Impiger\TrainingTitle\Repositories\Interfaces\OnlineTrainingSessionInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\TrainingTitle\Tables\OnlineTrainingSessionTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\TrainingTitle\Forms\OnlineTrainingSessionForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;
use Arr;

class OnlineTrainingSessionController extends BaseController
{
    /**
     * @var OnlineTrainingSessionInterface
     */
    protected $onlineTrainingSessionRepository;
    public $user;
    public $loginRoles;

    /**
     * @param OnlineTrainingSessionInterface $onlineTrainingSessionRepository
     */
    public function __construct(OnlineTrainingSessionInterface $onlineTrainingSessionRepository)
    {
        $this->onlineTrainingSessionRepository = $onlineTrainingSessionRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param OnlineTrainingSessionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(OnlineTrainingSessionTable $table)
    {
        page_title()->setTitle(trans('plugins/training-title::online-training-session.name'));
        // $user = \Auth::user();
        // $loginRoles = $this->user->roles()->get()->pluck('slug')->toArray();
        // dd($loginRoles);
        // if ($user->hasPermission('online-training-session.index')) {

        // }
        $candidate = getMsmeCandidate(\Auth::id());
        $isMsmeCandidate = Arr::get($candidate, 'isMsmeCandidate');
        $msmeScheme = Arr::get($candidate, 'msmeScheme');
        $sessions = [];
        // $attOptionMeme = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',["msme_scheme"])->pluck('id')->toArray();
       
        $attOption = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',["msme_scheme","online_sessions"])->pluck('slug','id')->toArray();
        $schemeSlugs = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',["msme_scheme"])->pluck('id','slug')->toArray();
        $attOptiononlineSessions = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',["online_sessions"])->pluck('name','slug')->toArray();
        
        $data = [
            'uploadRoute' => 'online-training-session.import',
            'template'=>'online-training-session',
            'sessions' => $sessions,
            'sessionHeaders' => $attOptiononlineSessions,
            'isMsmeCandidate' => $isMsmeCandidate
        ];
        $schemeType = ($attOption[$msmeScheme]== MSME_AABCS_SLUG) ? $schemeSlugs[MSME_NEEDS_SLUG] : $msmeScheme;

        if($isMsmeCandidate && $msmeScheme && $schemeType) {
            $onlineSessions = \Impiger\TrainingTitle\Models\OnlineTrainingSession::where('type', $schemeType)->get()->toArray();
            if($onlineSessions) {
                foreach ($onlineSessions as $key => $value) {
                    $sessions[Arr::get($attOption, $value['header'])][] = $value;
                }
            }

            $data['sessions'] = $sessions; 
        }
        

        // dd($attOption, $attOptiononlineSessions, $sessions);

        return $table->renderTable($data);
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/training-title::online-training-session.create'));

        return $formBuilder->create(OnlineTrainingSessionForm::class)->renderForm();
    }

    /**
     * @param OnlineTrainingSessionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(OnlineTrainingSessionRequest $request, BaseHttpResponse $response )
    {
        
        
        $onlineTrainingSession = $this->onlineTrainingSessionRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(ONLINE_TRAINING_SESSION_MODULE_SCREEN_NAME, $request, $onlineTrainingSession));
        
        
        return $response
            ->setPreviousUrl(route('online-training-session.index'))
            ->setNextUrl(route('online-training-session.edit', $onlineTrainingSession->id))
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
        
        $onlineTrainingSession = $this->onlineTrainingSessionRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $onlineTrainingSession));

        $name = ($onlineTrainingSession->name) ? ' "' . $onlineTrainingSession->name . '"' : "";
        page_title()->setTitle(trans('plugins/training-title::online-training-session.edit') . $name);

        return $formBuilder->create(OnlineTrainingSessionForm::class, ['model' => $onlineTrainingSession])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $onlineTrainingSession = $this->onlineTrainingSessionRepository->findOrFail($id);
        $name = ($onlineTrainingSession->name) ? ' "' . $onlineTrainingSession->name . '"' : "";
        page_title()->setTitle(trans('plugins/training-title::online-training-session.view') . $name);

        return $formBuilder->create(OnlineTrainingSessionForm::class, ['model' => $onlineTrainingSession, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param OnlineTrainingSessionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, OnlineTrainingSessionRequest $request, BaseHttpResponse $response )
    {
        
        $onlineTrainingSession = $this->onlineTrainingSessionRepository->findOrFail($id);
        
        
        $onlineTrainingSession->fill($request->input());

        $this->onlineTrainingSessionRepository->createOrUpdate($onlineTrainingSession);

        event(new UpdatedContentEvent(ONLINE_TRAINING_SESSION_MODULE_SCREEN_NAME, $request, $onlineTrainingSession));
        
        
        return $response
            ->setPreviousUrl(route('online-training-session.index'))
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
            $onlineTrainingSession = $this->onlineTrainingSessionRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('online-training-session', array($id), "Impiger\TrainingTitle\Models\OnlineTrainingSession");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->onlineTrainingSessionRepository->delete($onlineTrainingSession);
            
            event(new DeletedContentEvent(ONLINE_TRAINING_SESSION_MODULE_SCREEN_NAME, $request, $onlineTrainingSession));

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

        $dataExist = CrudHelper::isDependentDataExist('online-training-session', $ids, "Impiger\TrainingTitle\Models\OnlineTrainingSession");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $onlineTrainingSession = $this->onlineTrainingSessionRepository->findOrFail($id);
            $this->onlineTrainingSessionRepository->delete($onlineTrainingSession);
            
            event(new DeletedContentEvent(ONLINE_TRAINING_SESSION_MODULE_SCREEN_NAME, $request, $onlineTrainingSession));
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
            $bulkUpload = new BulkImport(new \Impiger\TrainingTitle\Models\OnlineTrainingSession);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\TrainingTitle\Models\OnlineTrainingSession')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\TrainingTitle\Models\OnlineTrainingSession')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\TrainingTitle\Models\OnlineTrainingSession')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
