<?php

namespace Impiger\User\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\User\Http\Requests\UserRequest;
use Impiger\User\Repositories\Interfaces\UserInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\User\Tables\UserTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\User\Forms\UserForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class UserController extends BaseController
{
    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/user/js/user.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param UserTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(UserTable $table)
    {
        page_title()->setTitle(trans('plugins/user::user.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/user::user.create'));

        return $formBuilder->create(UserForm::class)->renderForm();
    }

    /**
     * @param UserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(UserRequest $request, BaseHttpResponse $response , \Impiger\ACL\Services\CreateUserService $service)
    {
        $request['username'] = $request->input('email');
        $request['password'] = CrudHelper::randomPassword();
            $user = $service->execute($request);
            $request['user_id'] = $user->id;

        $user = $this->userRepository->createOrUpdate($request->input());
//        CrudHelper::createUpdateSubforms($request, $user, 'user_addresses',false,'');
		
        event(new CreatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
        CrudHelper::sendEmailConfig('user','{"create":"1","edit":null,"subject":"'.APP_NAME.' - Login credentials","message":"Dear {first_name},\u003Cbr\u003E\u003Cbr\u003ENew account has been created in Emircom.\u003Cbr\u003E\u003Cbr\u003EUsername : {email}\u003Cbr\u003EPassword : '.$request->input('password').'\u003Cbr\u003EKindly use this \u003Ca href='.getUserDomainUrl($user->user_id).'\u003EURL\u003C\/a\u003E to login\u003Cbr\u003E\u003Cbr\u003EIf you are wrong person, Please ignore this email.\u003Cbr\u003E\u003Cbr\u003EThank you","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email","default_reciever":null}',$user);

        return $response
            ->setPreviousUrl(route('users.profile.view',((\str_contains(url()->current(), 'edit-profile/')) ? $user->user_id : $user->user_id.'?user_navigation='.$user->id)))
            ->setNextUrl(route('user.edit', $user->id))
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
        $user = $this->userRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $user));

        $name = ($user->name) ? ' "' . $user->name . '"' : "";
        page_title()->setTitle(trans('plugins/user::user.edit') . $name);

        return $formBuilder->create(UserForm::class, ['model' => $user])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $user = $this->userRepository->findOrFail($id);
        $name = ($user->name) ? ' "' . $user->name . '"' : "";
        page_title()->setTitle(trans('plugins/user::user.view') . $name);

        return $formBuilder->create(UserForm::class, ['model' => $user, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param UserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, UserRequest $request, BaseHttpResponse $response , \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\MappingRoleService $service, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        
            if ($request->has('change_profile')) {
                if($request->user()->getKey() != $request->get('change_profile')) {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
            }
        $user = $this->userRepository->findOrFail($id);
        $coreuser = CrudHelper::updateCoreUser($user->user_id, $request, $coreUserRepository, $service, $activateUserService);

            if(!$coreuser->success) {
                return $response
                    ->setError()
                    ->setMessage($coreuser->errorMsg)
                    ->withInput();
            }
//        CrudHelper::createUpdateSubforms($request, $user, 'user_addresses',$id,'');
		
        $user->fill($request->input());

        $this->userRepository->createOrUpdate($user);

        event(new UpdatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
        
        
        return $response
            ->setPreviousUrl(route('users.profile.view',((\str_contains(url()->current(), 'edit-profile/')) ? $user->user_id : $user->user_id.'?user_navigation='.$user->id)))
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
            $user = $this->userRepository->findOrFail($id);
        
            $dataExist = CrudHelper::isDependentDataExist('user', array($id), "Impiger\User\Models\User");
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->userRepository->delete($user);
            $coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail($user->user_id);
                    if($coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete($coreUser);
                       CrudHelper::destroyUserSession($coreUser);
                    }
            event(new DeletedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

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
        
        $dataExist = CrudHelper::isDependentDataExist('user', $ids, "Impiger\User\Models\User");
            
        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $user = $this->userRepository->findOrFail($id);
            $this->userRepository->delete($user);
            $coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail($user->user_id);
                    if($coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete($coreUser);
                       CrudHelper::destroyUserSession($coreUser);
                    }
            event(new DeletedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
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
            $bulkUpload = new BulkImport(new \Impiger\User\Models\User);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\User\Models\User')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\User\Models\User')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\User\Models\User')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}
