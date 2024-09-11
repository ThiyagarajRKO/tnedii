<?php

namespace Impiger\SpokeRegistration\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\SpokeRegistration\Http\Requests\SpokeRegistrationRequest;
use Impiger\SpokeRegistration\Repositories\Interfaces\SpokeRegistrationInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\SpokeRegistration\Tables\SpokeRegistrationTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\SpokeRegistration\Forms\SpokeRegistrationForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class SpokeRegistrationController extends BaseController
{
    /**
     * @var SpokeRegistrationInterface
     */
    protected $spokeRegistrationRepository;

    /**
     * @param SpokeRegistrationInterface $spokeRegistrationRepository
     */
    public function __construct(SpokeRegistrationInterface $spokeRegistrationRepository)
    {
        $this->spokeRegistrationRepository = $spokeRegistrationRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/spoke-registration/js/spoke-registration.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param SpokeRegistrationTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SpokeRegistrationTable $table)
    {
        page_title()->setTitle(trans('plugins/spoke-registration::spoke-registration.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/spoke-registration::spoke-registration.create'));

        return $formBuilder->create(SpokeRegistrationForm::class)->renderForm();
    }

    /**
     * @param SpokeRegistrationRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(SpokeRegistrationRequest $request, BaseHttpResponse $response , \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        
        
        $spokeRegistration = $this->spokeRegistrationRepository->createOrUpdate($request->input());
        $coreuser = CrudHelper::createImpigerUser($request,$spokeRegistration,$coreUserRepository,$activateUserService,SPOKE_ROLE_SLUG,false);
        event(new CreatedContentEvent(SPOKE_REGISTRATION_MODULE_SCREEN_NAME, $request, $spokeRegistration));
        
        
        return $response
            ->setPreviousUrl(route('spoke-registration.index'))
            ->setNextUrl(route('spoke-registration.edit', $spokeRegistration->id))
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
        
        $spokeRegistration = $this->spokeRegistrationRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $spokeRegistration));

        $name = ($spokeRegistration->name) ? ' "' . $spokeRegistration->name . '"' : "";
        page_title()->setTitle(trans('plugins/spoke-registration::spoke-registration.edit') . $name);

        return $formBuilder->create(SpokeRegistrationForm::class, ['model' => $spokeRegistration])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $spokeRegistration = $this->spokeRegistrationRepository->findOrFail($id);
        $name = ($spokeRegistration->name) ? ' "' . $spokeRegistration->name . '"' : "";
        page_title()->setTitle(trans('plugins/spoke-registration::spoke-registration.view') . $name);

        return $formBuilder->create(SpokeRegistrationForm::class, ['model' => $spokeRegistration, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param SpokeRegistrationRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, SpokeRegistrationRequest $request, BaseHttpResponse $response )
    {
        
        $spokeRegistration = $this->spokeRegistrationRepository->findOrFail($id);
        
        
        $spokeRegistration->fill($request->input());

        $this->spokeRegistrationRepository->createOrUpdate($spokeRegistration);

        event(new UpdatedContentEvent(SPOKE_REGISTRATION_MODULE_SCREEN_NAME, $request, $spokeRegistration));
        
        
        return $response
            ->setPreviousUrl(route('spoke-registration.index'))
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
            $spokeRegistration = $this->spokeRegistrationRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('spoke-registration', array($id), "Impiger\SpokeRegistration\Models\SpokeRegistration");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->spokeRegistrationRepository->delete($spokeRegistration);
            
            event(new DeletedContentEvent(SPOKE_REGISTRATION_MODULE_SCREEN_NAME, $request, $spokeRegistration));

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

        $dataExist = CrudHelper::isDependentDataExist('spoke-registration', $ids, "Impiger\SpokeRegistration\Models\SpokeRegistration");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $spokeRegistration = $this->spokeRegistrationRepository->findOrFail($id);
            $this->spokeRegistrationRepository->delete($spokeRegistration);
            
            event(new DeletedContentEvent(SPOKE_REGISTRATION_MODULE_SCREEN_NAME, $request, $spokeRegistration));
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
            $bulkUpload = new BulkImport(new \Impiger\SpokeRegistration\Models\SpokeRegistration);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\SpokeRegistration\Models\SpokeRegistration')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\SpokeRegistration\Models\SpokeRegistration')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\SpokeRegistration\Models\SpokeRegistration')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
