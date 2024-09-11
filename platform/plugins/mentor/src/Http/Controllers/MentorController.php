<?php

namespace Impiger\Mentor\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Mentor\Http\Requests\MentorRequest;
use Impiger\Mentor\Repositories\Interfaces\MentorInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Mentor\Tables\MentorTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Mentor\Forms\MentorForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class MentorController extends BaseController
{
    /**
     * @var MentorInterface
     */
    protected $mentorRepository;

    /**
     * @param MentorInterface $mentorRepository
     */
    public function __construct(MentorInterface $mentorRepository)
    {
        $this->mentorRepository = $mentorRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js',
            'vendor/core/plugins/mentor/js/mentor.js',
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param MentorTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MentorTable $table)
    {
        page_title()->setTitle(trans('plugins/mentor::mentor.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/mentor::mentor.create'));

        return $formBuilder->create(MentorForm::class)->renderForm();
    }

    /**
     * @param MentorRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(MentorRequest $request, BaseHttpResponse $response, \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface $entrepreneurRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService )
    {
        $request['username'] = $request->input('email');
        // $request['password'] = CrudHelper::randomPassword();
        // $user = $service->execute($request);
        $user = CrudHelper::createEntrepreneurUser($request, $coreUserRepository,$activateUserService,MENTOR_ROLE_SLUG,true);
        $request['user_id'] = $user->id;
        $entrepreneurExists = $entrepreneurRepository->getFirstBy(['email'=>$request['email']]);
        if(!$entrepreneurExists){
            $entrepreneur = $entrepreneurRepository->createOrUpdate($request->input());
        } else {
            $entrepreneur = $entrepreneurExists;
        }

        $request['entrepreneur_id'] = $entrepreneur->id;
        $mentor = $this->mentorRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(MENTOR_MODULE_SCREEN_NAME, $request, $mentor));
                
        return $response
            ->setPreviousUrl(route('mentor.index'))
            ->setNextUrl(route('mentor.edit', $mentor->id))
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
        
        if ($request->has('change_profile')) {
            if($request->user()->getKey() != $request->get('change_profile')) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
        }
        $mentor = $this->mentorRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $mentor));

        $name = ($mentor->name) ? ' "' . $mentor->name . '"' : "";
        page_title()->setTitle(trans('plugins/mentor::mentor.edit') . $name);

        return $formBuilder->create(MentorForm::class, ['model' => $mentor])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $mentor = $this->mentorRepository->findOrFail($id);
        $name = ($mentor->name) ? ' "' . $mentor->name . '"' : "";
        page_title()->setTitle(trans('plugins/mentor::mentor.view') . $name);

        return $formBuilder->create(MentorForm::class, ['model' => $mentor, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param MentorRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, MentorRequest $request, BaseHttpResponse $response , \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\MappingRoleService $service, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        
        if ($request->has('change_profile')) {
            if($request->user()->getKey() != $request->get('change_profile')) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
        }

        $mentor = $this->mentorRepository->findOrFail($id);
        $password = $request->input('password');
        if($mentor->password != $request->input('password')) {
            $request['password'] = \Hash::make($password);
        } else {
            unset($request['password']);
        }
        $coreuser = CrudHelper::updateCoreUser($mentor->user_id, $request, $coreUserRepository, $service, $activateUserService);

        if(!$coreuser->success) {
            return $response
                ->setError()
                ->setMessage($coreuser->errorMsg)
                ->withInput();
        }
        $request['password'] = $password;
        $entrepreneurRepository = app(\Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface::class);
        $entrepreneurExists = $entrepreneurRepository->findOrFail($mentor->entrepreneur_id);
        if($entrepreneurExists){
            $entrepreneurExists->fill($request->input());
            $entrepreneur = $entrepreneurRepository->createOrUpdate($entrepreneurExists);
        }

        $mentor->fill($request->input());

        $this->mentorRepository->createOrUpdate($mentor);

        event(new UpdatedContentEvent(MENTOR_MODULE_SCREEN_NAME, $request, $mentor));
        
        
        return $response
            ->setPreviousUrl(route('mentor.index'))
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
            $mentor = $this->mentorRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('mentor', array($id), "Impiger\Mentor\Models\Mentor");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->mentorRepository->delete($mentor);
            $coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail($mentor->user_id);
                    if($coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete($coreUser);
                       CrudHelper::destroyUserSession($coreUser);
                    }
            event(new DeletedContentEvent(MENTOR_MODULE_SCREEN_NAME, $request, $mentor));

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

        $dataExist = CrudHelper::isDependentDataExist('mentor', $ids, "Impiger\Mentor\Models\Mentor");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $mentor = $this->mentorRepository->findOrFail($id);
            $this->mentorRepository->delete($mentor);
            $coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail($mentor->user_id);
                    if($coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete($coreUser);
                       CrudHelper::destroyUserSession($coreUser);
                    }
            event(new DeletedContentEvent(MENTOR_MODULE_SCREEN_NAME, $request, $mentor));
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
            $bulkUpload = new BulkImport(new \Impiger\Mentor\Models\Mentor);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\Mentor\Models\Mentor')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\Mentor\Models\Mentor')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\Mentor\Models\Mentor')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
