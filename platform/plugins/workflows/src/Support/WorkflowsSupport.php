<?php

namespace Impiger\Workflows\Support;

use Impiger\Base\Enums\BaseStatusEnum;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Workflow;
use Impiger\Workflows\Models\Workflows;
use Arr;
use Exception;

class WorkflowsSupport
{
    /**
     * @var Application|mixed
     */
    protected $app;
    protected $cacheValues = [];

    /**
     * CustomFieldSupport constructor.
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->app = app();
    }

    /**
     * @param string | array $module
     * @return $this
     */
    public function registerModule($module): self
    {
        if (!is_array($module)) {
            $module = [$module];
        }

        $configKey = 'plugins.workflows.general.supported';

        config([
            $configKey => array_merge(config($configKey, []), $module),
        ]);

        return $this;
    }

    /**
     * @return array
     */
    public function isSupportedModule(string $module): bool
    {
        return in_array($module, $this->supportedModules());
    }

    /**
     * @return array
     */
    public function supportedModules()
    {
        return config('plugins.workflows.general.supported', []);
    }

    /**
     * @return array
     */
    public function supportedModuleTables()
    {
        return config('plugins.workflows.general.supported_module_tables', []);
    }

    /**
     * @return array
     */
    public function supportedModuleActions()
    {
        $allActions = config('plugins.workflows.general.supported_actions', []);
        return $allActions;
    }

    /**
     * @return array
     */
    public function getInitialState($module)
    {
        return config('plugins.workflows.workflow.'.$module.'.initial_places', '');
    }

    /**
     * @return array
     */
    public function getWorkflowConfig()
    {
        $supportedModules = $this->supportedModules();
        $configArr = [];
        $plugins = get_active_plugins();

        foreach ($supportedModules as $module) {
            if (!$module) {
                continue;
            }

            if(in_array($module, $plugins)) {
                $workflows = config('plugins.'.$module.'.workflow', []);

                foreach ($workflows as $key => $config) {
                    $configArr[$key] = $config;
                }
            }
        }

        return $configArr;
    }

    /**
     * @return array
     */
    public function getWorkflowEnabledTransitions($input, $workflowName = null)
    {
        $output = [];

        if(!$input) {
            return false;
        }

        $workflow = Workflow::get($input, $workflowName);
        $property = ($workflow->getMetadataStore()->getMetadata('module_property'))?:'status';
        $relations = ($workflow->getMetadataStore()->getMetadata('relation'))?:"";
        if($relations && $relations = 'many'){
            $checkState = $workflow->getMetadataStore()->getMetadata('check_state');
            $checkField = $workflow->getMetadataStore()->getMetadata('check_field');
            $checkStatus = $workflow->getMetadataStore()->getMetadata('status');
            $additionalCheckField = $workflow->getMetadataStore()->getMetadata('additional_check_field');
            $relationKey = $workflow->getMetadataStore()->getMetadata('relation_key');
            if($input->{$property} != $checkState) {
                $relationValue = $input->{$relationKey};
                if(!$additionalCheckField || ($additionalCheckField && !in_array($relationValue, $this->cacheValues))) {
                    $checkOtherStates = $this->checkOtherStates($input,$relationKey,$relationValue,$property,$checkState,$checkField);
                    if($checkOtherStates){
                        $output = ($checkOtherStates == $checkField) ? $checkStatus[0] : $checkStatus[1];
                        return $output;
                    }
                }
            } else {
                if($additionalCheckField && $input->$additionalCheckField != NULL) {
                    $this->cacheValues[] = $input->$relationKey;
                }
            }
        }
        $transitions = [];

        try {
            $transitions = $workflow->getEnabledTransitions($input);
        } catch (Exception $ex) {

        }
        $workflowConfig = Workflows::where(['module_controller' => $workflowName])->get()->first();
        $output = $this->workflowPermissionCheck($transitions, $workflowConfig->workflowPermissions);
        return $output;
    }

    public function workflowPermissionCheck($transitions, $allowedTransitions)
    {
        $transitions = array_filter($transitions, function($value) use ($allowedTransitions) {
            foreach($allowedTransitions as $trans) {
                $permissions = $trans['user_permissions'];
                if($trans['transition'] == $value->getName() && SELF::isAuthenticatedUser($permissions)) {
                    return $value;
                }
            }
        });

        return $transitions;
    }

    public static function isAuthenticatedUser($allowedIds) {
        if(!is_array($allowedIds) || !is_array($allowedIds)) {
            return false;
        }
        $isAuth = false;

        if(Arr::has($allowedIds, WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE)) {
            $user = \Auth::user();
            $loginRoleIds = $user->roles->pluck('id')->toArray();
            $isAuth = !empty(array_intersect($loginRoleIds, $allowedIds[WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE]));
        }

        if($isAuth) {
            return true;
        }

        if(Arr::has($allowedIds, WORKFLOW_PERMISSION_SPECIFIC_TO_USER)) {
            $isAuth = !empty(array_intersect([\Auth::id()], $allowedIds[WORKFLOW_PERMISSION_SPECIFIC_TO_USER]));
        }

        return $isAuth;
    }

    public static function getWorkflowAllTransitions($slug)
    {
        $transitions = [];
        $transitions = Workflows::where(['module_controller' => $slug])->with('transitions')->get()->pluck('transitions')->first();
        $transitions = ($transitions) ? $transitions->keyBy('id')->toArray() : [];
        return $transitions;
    }

    public static function getWorkflowTransitionConfig($workflowId, $transition, $subject) {
        if (!$transition && !$workflowId) {
            return false;
        }
        $attachments = [];
        $conds = ['workflows_id' => $workflowId, 'transition' => $transition];
        $transistions = \Impiger\Workflows\Models\WorkflowPermission::where($conds)->first();
        if (!empty($transistions)) {
            $configs = $transistions->configs;
            if (!empty($configs)) {
                $i = 1;
                $storagePath = 'storage/';
                foreach ($configs as $config) {
                    if ($config->attachment_type && $config->attachment_content) {
                        $fileName = 'workflow_attachment' . $i;
                        $content = $config->attachment_content;
                        $emailAttachContent = \App\Utils\CrudHelper::getReplaceContent($content, $subject);
                        $fileContent = array('content' => $emailAttachContent, 'title' => '');
                        if ($config->attachment_type == 'pdf') {
                            $pdfFileName = $storagePath . $fileName;
                            $pdf = \PDF::loadView('plugins/workflows::export.export-pdf', $fileContent)->setPaper('a4', 'landscape')->save($pdfFileName . '.pdf');
                            $attachments[] = url('/storage/') . '/' . $fileName . '.pdf';
                        } else {
                            \Excel::store(new \Impiger\Workflows\Exports\DataExport($fileContent), $fileName . '.' . $config->attachment_type);
                            $attachments[] = url('/storage/') . '/' . $fileName . '.' . $config->attachment_type;
                        }
                        $i++;
                    }
                }
                return $attachments;
            } else {
                return $attachments;
            }
        }
    }

    public function checkOtherStates($input,$key,$value,$property,$checkState,$checkField) {
        $id = $input->id;
        $model = $input->getmodel();
        $data = $model::where($key,$value)->whereNotIn('id',array($id))->get();
        $sameInstitute = [];
        foreach($data as $item){
        if($item->{$property} == $checkState ){
            if($item->{$checkField} == $input->{$checkField}){
                $sameInstitute[$id]=$checkField;
            }else{
                $sameInstitute[$id]='others';
            }
        }
        }
        if(!empty($sameInstitute)){
            return $sameInstitute[$id];
        }
        return false;
    }

    public function checkPermission($workflowName) {
        $workflowConfig = Workflows::where(['module_controller' => $workflowName])->get()->first();
        $workFlowPermissions = $workflowConfig->workflowPermissions;
        foreach($workFlowPermissions as $workFlowPermission){
            $permissions = $workFlowPermission->user_permissions;
            if(SELF::isAuthenticatedUser($permissions)){
                return true;
            }
        }
        return false;
    }

     public static function getWorkflowAllStates($slug)
    {
        $states = null;
        $workflowConfigs = config('plugins.workflows.workflow', []);

        if (isset($workflowConfigs[$slug])) {
            $states = $workflowConfigs[$slug]['places'];
        }

        return $states;
    }

    public static function getWorkflowHistory($moduleName, $id)
    {
        $modules = [];
        $modules[] = $moduleName;
        $mod = \App\Utils\CrudHelper::getModuleNameUsingModuleDBField($moduleName);
        $modules[] = $mod;
        $history = \Impiger\AuditLog\Models\AuditHistory::where(['reference_id' => $id])->whereIn('module', $modules)->where(function($query) use($moduleName){
            $query->where(['type' => 'workflow']);
            $query->orWhere(['type' => 'info', 'action' => 'created']);
        })
        ->orderBy('created_at', 'desc')->get();
        return $history;
    }
    
    public static function getNextStateUsers($module,$fromState = null,$field = 'email'){
        $initialState = config('plugins.workflows.workflow.'.$module.'.initial_places', '');
        $fromState = ($fromState) ? $fromState : $initialState;
        $workflowConfig = Workflows::where(['module_controller' => $module])->get()->first();
        $workFlowTransitions = $workflowConfig->transitions()->where('from_state',$fromState)->get();
        $transitions = ($workFlowTransitions) ? $workFlowTransitions->pluck('id') : [];
        $workFlowPermissions = $workflowConfig->workflowPermissions()->whereIn('transition',$transitions)->get();
        $emailIds=SELF::getUsers($workFlowPermissions,$field);
        
        return $emailIds;
    }
    
    public static function getUsers($workFlowPermissions,$field){
        $data = [];
        foreach($workFlowPermissions as $workFlowPermission){
            $permissions = $workFlowPermission->user_permissions;
            if(Arr::has($permissions, WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE)) {
            $roleUsers = \Impiger\ACL\Models\Role::join('role_users AS RU','RU.role_id','=','roles.id')
                        ->join('users AS U','RU.user_id','=','U.id')->select('U.email','U.id')->whereIn('roles.id',$permissions[WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE])->get();
            if($roleUsers){
                $data = $roleUsers->pluck($field)->toArray();
            }
        }        

        if(Arr::has($permissions, WORKFLOW_PERMISSION_SPECIFIC_TO_USER)) {
            $users = \Impiger\ACL\Models\User::whereIn('id',$permissions[WORKFLOW_PERMISSION_SPECIFIC_TO_USER])->get();
            if($users){
                $data = array_merge($data,$users->pluck($field)->toArray());
            }
        }
        }
        return $data;
    }

    public static function getUpdateStateUsers($module,$state){
        $userIds = [];
        if(!$module && $state){
            return false;
        }
        $workflowConfig = Workflows::where(['module_controller' => $module])->get()->first();
        $workFlowTransitions = $workflowConfig->transitions()->where(['from_state'=>$state,'action'=>'stateChangeOnUpdate'])->get();
        $transitions = ($workFlowTransitions) ? $workFlowTransitions->pluck('id') : [];
        $workFlowPermissions = $workflowConfig->workflowPermissions()->whereIn('transition',$transitions)->get();
        $userIds = SELF::getUsers($workFlowPermissions,'id');
        return in_array(\Auth::id(), $userIds);
    }
}
