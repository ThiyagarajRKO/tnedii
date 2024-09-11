<?php

namespace Impiger\Workflows\Listeners;

use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;
use Exception;
use CustomWorkflow;
use Workflow;
use Illuminate\Http\Request;
use Impiger\Workflows\Models\Workflows;
use Impiger\Workflows\Support\WorkflowsSupport;
use Arr;
use ZeroDaHero\LaravelWorkflow\Events\Transistion;
use Impiger\Workflows\Http\Controllers\PublicController;

class WorkflowsEventSubscriber
{

    /**
     * Handle workflow leave event.
     */
    public function onLeave($event)
    {
        /** Symfony\Component\Workflow\Event\LeaveEvent */
        $originalEvent = $event->getOriginalEvent();
        do_action(WORKFLOWS_MODULE_AUDIT_TRAIL_ACTION, $originalEvent->getWorkflowName(), $originalEvent);
    }

    /**
     * Handle workflow announce event.
     */
    public function onAnnounce($event)
    {
        if($this->checkOtherEvent($event)){
            /** Symfony\Component\Workflow\Event\AnnounceEvent */
            $originalEvent = $event->getOriginalEvent();
            dispatch(function () use($originalEvent){
                do_action(WORKFLOW_NOTIFICATION, $originalEvent->getWorkflowName(), $originalEvent);
            })->afterResponse();
            
          }
    }

    /**
     * Handle workflow Guard event.
     */
    public function onGuardListener(GuardEvent $event)
    {
        $originalEvent = $event->getOriginalEvent();
        $transitionName = $originalEvent->getTransition()->getName();
        $workflowConfig = Workflows::where(['module_controller' => $originalEvent->getWorkflowName()])->get()->first();
        
        $filtered = Arr::where($workflowConfig->workflowPermissions->toArray(), function ($value, $key) use ($transitionName) {
            return $value['transition'] == $transitionName;
        });
        $allowedIds = Arr::get(array_values($filtered), '0.user_permissions');
        $workflow = $originalEvent->getWorkflow();
        $transition = $originalEvent->getTransition();
        $transitionMetadata = $workflow->getMetadataStore()->getTransitionMetadata($transition); // transition object
        if(!empty($transitionMetadata) && Arr::get($transitionMetadata,'action') == "stateChangeOnUpdate"){
            return $event;
        } 
        
        if (!WorkflowsSupport::isAuthenticatedUser($allowedIds)) {
            $event->setBlocked(true, 'Not authorized');
        }
    }

    public function subscribe($events)
    {
        $workflowConfig = config('plugins.workflows.workflow', []);
        $supportedPlugins = array_keys($workflowConfig);

        foreach ($supportedPlugins as $plugin) {
            if (!$plugin) {
                continue;
            }

            $events->listen(
                'workflow.' . $plugin . '.leave',
                'Impiger\Workflows\Listeners\WorkflowsEventSubscriber@onLeave'
            );

            $events->listen(
                'workflow.' . $plugin . '.announce',
                'Impiger\Workflows\Listeners\WorkflowsEventSubscriber@onAnnounce'
            );
            
            $events->listen(
                'workflow.' . $plugin . '.transition',
                'Impiger\Workflows\Listeners\WorkflowsEventSubscriber@onTransition'
            );

            $transitions = Arr::get($workflowConfig[$plugin], "transitions");

            foreach ($transitions as $k => $val) {
                $events->listen(
                    'workflow.' . $plugin . '.guard.' . $k,
                    'Impiger\Workflows\Listeners\WorkflowsEventSubscriber@onGuardListener'
                );
            }
        }
    }
    /**
     * Handle workflow transition event.
     */
    public function onTransition($event) {
        $originalEvent = $event->getOriginalEvent();
        $workflow = $originalEvent->getWorkflow();
        $transitionName = $originalEvent->getTransition();
        $transitionMetadata = $workflow->getMetadataStore()->getTransitionMetadata($transitionName); // transition object
        if(!empty($transitionMetadata) && Arr::get($transitionMetadata,'action')){
            PublicController::applyTransistionMetaEvent($originalEvent,$transitionMetadata['action']);
        }
        return true;
    }

   public function checkOtherEvent($event){
       $originalEvent = $event->getOriginalEvent();
        $workflow = $originalEvent->getWorkflow();
        $transitionName = $originalEvent->getTransition();
        $transitionMetadata = $workflow->getMetadataStore()->getTransitionMetadata($transitionName); // transition object
        if(!empty($transitionMetadata) && Arr::get($transitionMetadata,'action')){
            return false;
        }
        return true;
   }
}
