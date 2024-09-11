<?php

namespace Impiger\Workflows\Providers;

use Assets;
use Workflow;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;
use Throwable;
use CustomWorkflow;
use DB;
use DateTime;
use EmailHandler;
use Impiger\ACL\Models\User;
use Exception;
use Illuminate\Support\Str;
use App\Utils\CrudHelper;
use MetaBox;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addWorkflowActionBox'], 12, 2);

        if (defined('APPLY_WORKFLOW_TRANSITION')) {
            add_filter(APPLY_WORKFLOW_TRANSITION, [$this, 'viewWorkflowFilter'], 10, 2);
        }

        if (defined('LOAD_WORKFLOW_ASSETS')) {
            add_action(LOAD_WORKFLOW_ASSETS, [$this, 'loadWorkflowAssets'], 10, 0);
        }

        if (defined('WORKFLOW_NOTIFICATION')) {
            add_action(WORKFLOW_NOTIFICATION, [$this, 'workflowNotification'], 45, 3);
        }

        if (defined('APPLY_WORKFLOW_INITIAL_TRANSITION')) {
            add_filter(APPLY_WORKFLOW_INITIAL_TRANSITION, [$this, 'applyWorkflowInitialTransition'], 10, 2);
        }
        /* @Customized By Ramesh Esakki  - Start -*/
        if (defined('AUDIT_LOG_MODULE_SCREEN_NAME')) {
            add_action(WORKFLOWS_MODULE_AUDIT_TRAIL_ACTION, [$this, 'workflowAudit'], 45, 2);
        }
        /* @Customized By Ramesh Esakki  - End -*/
    }

    /**
     * @param string $context
     * @param $object
     */
    public function addWorkflowActionBox($context, $object)
    {
        $pathInfo = request()->getPathInfo();
        if (str_contains($pathInfo, 'viewdetail') && $object && in_array($object->getTable(), CustomWorkflow::supportedModuleTables()) && $context == "side") {
            MetaBox::addMetaBox(
                'workflow_wrap',
                'Status',
                [$this, 'workflowMetaField'],
                get_class($object),
                $context,
                'default'
            );
        }
    }



    /**
     * @return string
     * @throws \Throwable
     */
    public function workflowMetaField()
    {
        $data = null;
        $args = func_get_args();
        if ($args[0] && $args[0]->id) {
            $data = $args[0];
        }
        $className = $args[1];
        $workflow = \Workflow::get(new $className);
        $property = ($workflow->getMetadataStore()->getMetadata('module_property'));
        $tableName = ($data) ? $data->getTable() : "";
        $transitionConfig = CustomWorkflow::getWorkflowEnabledTransitions($data, $tableName);
        $transitions = $transkeys = [];
        if (!empty($transitionConfig) && is_array($transitionConfig)) {
            foreach ($transitionConfig as $key => $trans) {
                $transkeys[] = intval($trans->getName());
            }
        }

        $transitions = \Impiger\Workflows\Models\Workflows::where(['module_controller' => $workflow->getName(), 'is_enabled' => 1])
            ->with('transitions', function ($query) use ($transkeys) {
                $query->whereIn('id', $transkeys);
            })->get()->pluck('transitions')->first();
        $transitions = ($transitions) ? $transitions->keyBy('id')->toArray() : [];
        $history = CustomWorkflow::getWorkflowHistory($tableName, $data->id);
        $initialState = CustomWorkflow::getInitialState($tableName);
        $workflowTitle = ($workflow->getMetadataStore()->getMetadata('title'));;
        $histories = [];
        foreach($history as $row) {
            $user = \Impiger\ACL\Models\User::find($row->user_id);
            if($user){
                $row->user_name = $user->first_name. " ". $user->last_name;
                $row->avatar_url = $user->avatar_url;
                $row->roles = ($user->roles()) ? implode(",", $user->roles()->pluck('name')->toArray()) : "";
            }else{
                $row->user_name = "Register from Online";
                $row->avatar_url = (new \Impiger\Base\Supports\Avatar)->create('System')->toBase64();
            }            
            $request = json_decode($row->request);
            $row->workflow_title = $workflowTitle;
            $row->transition_name = $initialState;
            $row->transition_state = $initialState;
            if(isset($request->transition)) {
                $row->workflow_title = $request->workflow_title;
                $row->transition_name = (isset($request->transition)) ? $request->transition->name : "";
                $row->transition_state = (isset($request->transition)) ? $request->transition->to_state : "";
            }
            $histories[] = $row;
        }
        return view('plugins/workflows::workflow', compact('histories','data', 'property', 'transitions', 'className'))->render();
    }

    /**
     * @param className Str
     */
    public function viewWorkflowFilter($className, $id)
    {
        if (!$id || !$className) {
            return false;
        }

        if (class_exists($className)) {
            $item = $className::find($id);
            if ($item && in_array(
                $className,
                config('plugins.workflows.general.supported', [])
            )) {
                if ($className) {
                    $workflow = \Workflow::get(new $className);
                    $property = ($workflow->getMetadataStore()->getMetadata('module_property')) ?: 'status';
                    $labelClass = ($workflow->getMetadataStore()->getMetadata('label_class')) ?: [];
                    $transitions = CustomWorkflow::getWorkflowEnabledTransitions($item, $className);
                    return view('plugins/workflows::workflow-transitions', compact('transitions', 'item', 'property', 'workflow', 'labelClass'))->render();
                }
            }
        }
    }

    /**
     * @param stdClass $post
     */
    public function loadWorkflowAssets()
    {
        if (is_plugin_active('workflows')) {
            Assets::addScriptsDirectly('vendor/core/plugins/workflows/js/workflow.js');
            //                ->addScripts(['jquery-ui']);
        }
    }

    /**
     * @param className Str
     */
    public function workflowNotification($screen, $event, $extraContent = null)
    {
        /** @var App\Model $post */
        $subject = $event->getSubject();
        $fillable = $subject->getFillable();
        $table = $subject->getTable();
        $transition = $event->getTransition()->getName();
        $fromTransition = $event->getTransition()->getFroms();
        $toTransition = $event->getTransition()->getTos();
        $stateTransition = ucfirst($fromTransition[0]) . " to " . ucfirst($toTransition[0]);
        $context = $event->getContext();
        $comments = (is_array($context)) ? $context[0] : 0;
        $userId = Auth::id();
        $receiverName = "";
        $receiverEmail = "";
        $userIdConfig = config('plugins.crud.general.supported_auth_models', []);

        if (isFillableField($subject, 'user_id')) {
            $userId = (in_array(get_class($subject), $userIdConfig)) ? $subject->user_id : getUserId(IMP_USER_TABLE, $subject->user_id);
        }
        if (isFillableField($subject, 'email_id') || isFillableField($subject, 'email')) {
            $userId = ($subject->email) ?: $subject->email_id;
        }

        $createdBy = $this->getCreatedUserId($screen, $subject);
        $userId = ($createdBy) ?: $userId;

        $currentWorkflow = \DB::table('workflows')->where('module_controller', $table)->first();
        $data = array(
            'comments' => $comments,
            'transition_name' => $transition,
            'reference_id' => $subject->id,
            'reference_type' => $screen,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
            'created_by' => Auth::id(),
        );
        \DB::table('workflow_history')->insert($data);
        if (str_contains($screen, 'vendor')) {
            if (isFillableField($subject, 'company_name')) {
                $receiverName = ($subject->company_name);
            }
        }

        $wnPermission = \Impiger\Workflows\Models\WorkflowTransition::where(['id' => $transition,'is_notification_enabled' => 1])->get()->first();
        
        try {
            if (preg_match("/(.+)@(.+)\.(.+)/i", $userId)) {
                $receiverEmail = $userId;
                $receiverName = ($receiverName) ?: ucfirst(str_replace(".", " ", strstr($receiverEmail, '@', true)));
            } else {
                $receiver = User::where('id', $userId)->first();
                $receiverEmail = $receiver->email;
                $receiverName = $receiver->first_name . " " . $receiver->last_name;
            }
            if($screen == 'tnsi_startup'){
                $receiverEmail = $subject->team_members[0][0]['value'];
                $receiverName = $subject->team_members[0][3]['value'];
            }
            $approverName = Auth::user()->first_name . " " . Auth::user()->last_name;
            $emailSubject = ucfirst(Str::camel($screen)) . ' Approval Process';
            $message = WORKFLOW_NOTIFICATION_MSG;
            if (!empty($currentWorkflow)) {
                $emailSubject = ($currentWorkflow->email_subject) ?: $emailSubject;
                $message = ($currentWorkflow->email_content) ?: $message;
                $workflowAttachments = CustomWorkflow::getWorkflowTransitionConfig($currentWorkflow->id, $transition, $subject);
            }
            $search = array('{receiver_name}', '{module}', '{status}', '{approver_name}', '{comments}');
            $replace = array($receiverName, ucfirst($currentWorkflow->name), $stateTransition, $approverName, $comments);
            $msg = str_replace($search, $replace, $message);
            $msg = CrudHelper::getReplaceContent($msg, $subject);
            if ($extraContent) {
                $msg = $this->appendExtraContent($msg, $extraContent);
            }
            $args['attachments'] = ($workflowAttachments) ?: "";

            if($wnPermission) {
                EmailHandler::send($msg, $emailSubject, $receiverEmail, $args);
            }
            $this->removeUnwantedFiles($workflowAttachments);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    protected function removeUnwantedFiles($files)
    {
        if (empty($files)) {
            return true;
        }
        $removedFiles = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $removedFiles[] = class_basename($file);
            }
        } else {
            $removedFiles[] = class_basename($files);
        }
        \Storage::delete($removedFiles);
        return true;
    }

    protected function appendExtraContent($content, $extraContent)
    {
        if (!$extraContent) {
            return $content;
        }
        $rewriteContent = $content;
        if (Str::contains($content, ['{{ footer }}'])) {
            $slice = Str::beforeLast($content, "{{ footer }}");
            $slice .= $extraContent . "\r\n\r\n{{ footer }}";
            $rewriteContent = $slice;
        } else {
            $rewriteContent .= $extraContent;
        }
        return $rewriteContent;
    }

    protected function getCreatedUserId($moduleName, $data)
    {
        if (!$moduleName && !$data) {
            return null;
        }
        if (in_array($moduleName, config('plugins.workflows.general.exclude_screen', []))) {
            return null;
        }
        $module = str_contains($moduleName, '_') ? str_replace("_", "-", $moduleName) : $moduleName;
        $dataModel = $data->getModel();
        $dataTable = $data->getTable();
        $fillable = $data->getFillable();
        $cond['module'] = $module;
        $cond['action'] = 'created';
        $cond['reference_id'] = $data->id;
        if (\Schema::hasColumn($dataTable, 'name')) {
            $cond['reference_name'] = $data->name;
        }
        $auditHistories = \DB::table('audit_histories')->where($cond)->where('user_id', '!=', Auth::id())->orderBy('id', 'DESC')->first();

        if (!empty($auditHistories)) {
            return $auditHistories->user_id;
        } else if (isFillableField($dataModel, 'created_by') && $data->created_by != Auth::id()) {
            return $data->created_by;
        } else {
            \Arr::forget($cond, 'action');
            $auditHistories = \DB::table('audit_histories')->where($cond)->whereRaw('action LIKE "%Performed transition%"')
                ->where('user_id', '!=', Auth::id())
                ->orderBy('id', 'DESC')->first();
            if (!empty($auditHistories)) {
                return $auditHistories->user_id;
            }
        }
        return null;
    }

    /**
     * @Customized By Ramesh Esakki
     * @param string $screen
     * @param Request $request
     * @param stdClass $data
     */
    public function workflowAudit($screen, $event)
    {
        /** @var App\Model $post */
        $subject = $event->getSubject();
        $transitionId = $event->getTransition()->getName();
        $from = implode(', ', array_keys($event->getMarking()->getPlaces()));
        $to = implode(', ', $event->getTransition()->getTos());
        $transitionDetail = \Impiger\Workflows\Models\WorkflowTransition::where(['id' => $transitionId])->first();

        $msg = 'Performed transition ' .$transitionDetail->name . '(' . $from . ' -> ' . $to.').';
        request()->merge([
            'transition_id' => $transitionId,
            'workflow_name' => $event->getWorkflowName(),
            'workflow_title' => $event->getWorkflow()->getMetadataStore()->getMetadata('title'),
            'reference_id' => $subject->id,
            'from' => $from,
            'to' => $to,
            'transition' => $transitionDetail
        ]);
        $moduleName = \App\Utils\CrudHelper::getModuleNameUsingModuleDBField($screen);
        event(new \Impiger\AuditLog\Events\AuditHandlerEvent(
            $moduleName,
            $msg,
            $subject->id,
            \AuditLog::getReferenceName($moduleName, $subject),
            'workflow'
        ));
    }
}
