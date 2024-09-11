<?php

namespace Impiger\Workflows\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Workflows\Http\Requests\WorkflowsRequest;
use Impiger\Workflows\Repositories\Interfaces\WorkflowsInterface;
use Impiger\Workflows\Repositories\Interfaces\WorkflowPermissionInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Workflows\Tables\WorkflowsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Workflows\Forms\WorkflowsForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use Workflow;
use Illuminate\Support\Facades\Validator;
use Impiger\Workflows\Models\Workflows;
use Impiger\Workflows\Support\WorkflowsSupport;
use Arr;
use App\Utils\CrudHelper;
use Impiger\ACL\Models\UserPermission;

class PublicController extends BaseController
{
    /**
     * @var WorkflowsInterface
     */
    protected $workflowsRepository;

    /**
     * @var WorkflowPermissionInterface
     */
    protected $workflowPermissionRepository;

    /**
     * @param WorkflowsInterface $workflowsRepository
     */
    public function __construct(WorkflowsInterface $workflowsRepository, WorkflowPermissionInterface $workflowPermissionRepository)
    {
        $this->workflowsRepository = $workflowsRepository;
        $this->workflowPermissionRepository = $workflowPermissionRepository;
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function applyWorkflow(Request $request, BaseHttpResponse $response)
    {
        $userId = $request->user()->getKey();
        $customValidation =[];
//        if($request->has('custom_input') && !empty($request->input('custom_input'))){
//            foreach($request->input('custom_input') as $customInput){
//                if($customInput['validation']){
//                    $customValidation[$customInput['field']]=$customInput['validation'];
//                }
//            }
//        }
        $validator = Validator::make($request->input(), array_merge([
            'id'  => 'required',
            'class'   => 'required',
            'change_status' => 'required',
            'remarks' => 'required',
        ],$customValidation));

        if ($validator->fails()) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Data invalid!<br/>') . ' ' . implode('<br/> ', $validator->errors()->all()) . '.');
        }

        if (!class_exists($request->input('class'))) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Workflow Module not supported!'));
        }

        try {
            $input = $request->input('class')::find($request->input('id'));
            $model = $input->getModel();
            $workflow = Workflow::get($input);
//            if($request->has('custom_input') && !empty($request->input('custom_input'))){
//                $table = $input->getTable();
//                $customData=[];
//                foreach($request->input('custom_input') as $customInput){
//                    if($customInput['store_table'] == $table){
//                        $input->{$customInput['field']} = $request->input($customInput['field']);
//                    }else{
//                        if($customInput['store_table'] == 'deployed_users'){
//                            $this->storeDeployedUserData($request,$input);
//                        }
//                        $customData[$customInput['field']] = $request->input($customInput['field']);
//                        \DB::table($customInput['store_table'])->where('id',$input->id)->update($customData);
//                    }
//                }
//            }

            if ($workflow->can($input, $request->input('change_status'))) {
                // Apply a transition
                $workflow->apply($input, $request->input('change_status'), [$request->input('remarks')]);
                $input->save(); // Don't forget to persist the state
                
                $redirectModule = getModuleDetails($input->getTable()); 
                $setData = (is_plugin_active($redirectModule) && \Illuminate\Support\Facades\Route::has($redirectModule.'.index')) ? ['previous_url'=>route($redirectModule.'.index')] : [];
                return $response
                    ->setData($setData)
                    ->setMessage(__('Updated status successfully!'));
            } else {
                return $response
                    ->setError()
                    ->setMessage('Un Aouthorized Access.');
            }
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    public static function applyTransistionMetaEvent($event,$tansitionMeta){
        self::$tansitionMeta($event);
    }

    protected static function stateChangeOnUpdate($event) {
        \Log::info("Workflow State Changed Successfully");
        return true;
    }

    protected static function createUser($event ) {
        /**
         * @var User $user
         */
        $data = $event->getSubject();
        $user = new \Impiger\ACL\Models\User;
        if(!isset($data->email_id)) {
            \Log::info("Email Field does not exist to create user profile");
            return false;
        }

        $userExist = $user::where('email', $data->email_id)->pluck('id')->first();

        if($userExist) {
            \Log::info("User already exist.");
            return false;
        }

        //$user->email = (isset($data->contact_email) && $data->contact_email) ? $data->contact_email : $data->email_id;
        $user->email = $data->email_id;
        $user->username = $data->email_id;
        $randomPassword = CrudHelper::randomPassword();
        $user->password = \Hash::make($randomPassword);
        $user->first_name = $data->company_name;
        $user->last_name= "";
        $user->save();
        // activate the user
        app(\Impiger\ACL\Services\ActivateUserService::class)->activate($user);
        $data->user_id = $user->id;
        if(isFillableField($data, 'temp_password')){
            $data->temp_password = $randomPassword;
        }
        $data->save();
        $role = \Impiger\ACL\Models\Role::where('slug', VENDOR_ROLE_SLUG)->whereNull('deleted_at')->first();
        $role->users()->attach($user->id);
        event(new \Impiger\ACL\Events\RoleAssignmentEvent($role, $user));    
        $user->domain_href = getUserDomainUrl($user->id);  
        $user->temp_password = $randomPassword;  
        $user->contact_email = $data->contact_email;  
        dispatch(function () use($user,$event){
            CrudHelper::sendEmailConfig('user','{"create":"1","edit":null,"subject":"'.USER_LOGIN_CREDENTIALS_SUBJECT.'","message": "'.get_setting_email_template_content('core', 'base', 'user').'","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email|contact_email","default_reciever":null}',$user);
            do_action(WORKFLOW_NOTIFICATION, $event->getWorkflowName(), $event);
        })->afterResponse();
       
    }
    
    protected static function activateUser($event ) {
        /**
         * @var User $user
         */
        $data = $event->getSubject();
        $user = new \Impiger\ACL\Models\User;
        $impigerUser = new \Impiger\User\Models\User;
        
        $userIds = UserPermission::where(['reference_id'=>$data->id,'reference_key'=>getEntityId($data->getTable(),'module_db')])->get()->pluck('user_id');
        

        $users = $user::whereIn('id',$userIds)->get();
        
        if($users){
            foreach($users as $user){                
                if (!app(\Impiger\ACL\Repositories\Interfaces\ActivationInterface::class)->completed($user)) {
                    app(\Impiger\ACL\Services\ActivateUserService::class)->activate($user);
                    $impigerUser = $impigerUser::where('user_id',$user->id)->first();
                    $user->domain_href = getUserDomainUrl($user->id);  
                    $user->temp_password = $impigerUser->password; 
//                    dispatch(function () use($user,$event){
                        CrudHelper::sendEmailConfig('user','{"create":"1","edit":null,"subject":"'.USER_LOGIN_CREDENTIALS_SUBJECT.'","message": "'.get_setting_email_template_content('core', 'base', 'user').'","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email","default_reciever":null}',$user);
                        
//                    })->afterResponse();
                }
            }
        }
        
       do_action(WORKFLOW_NOTIFICATION, $event->getWorkflowName(), $event); 
        
            
        
       
    }
    
    public function getOptions(Request $request, BaseHttpResponse $response)
    {
        if ($request->has('table')) {
            $table = $request->input('table');
            $whereRaw = get_common_condition($table);
            $selectedColumns = ["id", "name as text"];
            $query = \DB::table($table)->select($selectedColumns)
                ->orderBy("name", "ASC");
                if($request->has('condn'))
                {
                    $query = $query->whereRaw($request->input('condn'));

                }
            if($whereRaw) {
                    $query = $query->whereRaw($whereRaw);
                }

            $model = get_model_from_table($table);
            if ($model) {
                $model = new $model();
                $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $selectedColumns, false);
            }
            return $query->get()->toArray();
        } else {
            return $response->setError(true)
                ->setMessage("Required param missing");
        }
    }








}
