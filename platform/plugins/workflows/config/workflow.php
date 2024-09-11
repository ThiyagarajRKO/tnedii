<?php
$workflows = app(\Impiger\Workflows\Models\Workflows::class)->where('is_enabled',1)->get()->keyBy('module_controller');
foreach($workflows as $key => $workflowConfig) {
    $moreConfig = [];
    $moreConfig = [
        'type' => "workflow",
        'marking_store' => [
            'type' => 'single_state', // or 'single_state'
            'property' => $workflowConfig->module_property, // this is the property on the model
        ],
        'supports' => [get_model_from_table($workflowConfig->module_controller)],
        'marking_property' => $workflowConfig->module_property,
        'initial_places' => $workflowConfig->initial_state,
        'audit_trail' => [
            'enabled' => true
        ],
        'metadata' => [ 
            'title' => $workflowConfig->name,
            'initial_state' => $workflowConfig->initial_state,
            'module_property' => $workflowConfig->module_property,
        ]
    ];

//    $transitionConfig = $workflowConfig->with('transitions')->get()->pluck('transitions')->first();
    $transitionConfig = $workflowConfig->transitions;
    $transitions = [];
    $places = [];
    foreach($transitionConfig as $trans) {
        $actions = [];
        if($trans['action']) {
            $actions = ["action" => $trans['action']];
        }

        $transitions[$trans->id] = [
            'from' => $trans->from_state,
            'to' => $trans->to_state,
            'name' => $trans->id,
            'title' => $trans->name,
            'metadata' => $actions
        ];

        if(!in_array($trans->from_state, $places)) {
            $places[] = $trans->from_state;
        }

        if(!in_array($trans->to_state, $places)) {
            $places[] = $trans->to_state;
        }
    }
    $moreConfig['transitions'] = $transitions;
    $moreConfig['places'] = $places;
    $workflows[$key] = $moreConfig;
}

return $workflows->toArray();
