<?php
$workflowSupportedTables = app(\Impiger\Workflows\Models\Workflows::class)->where('is_enabled', 1)->get()->pluck('module_controller')->toArray();
return [
    'exclude_screen' => [],
    'supported' => app(\App\Models\Crud::class)->whereIn('module_db', $workflowSupportedTables)->get()->pluck('module_name')->toArray(),
    'supported_module_tables' => $workflowSupportedTables,
    'supported_actions' => [
        ''=> 'Select',
        'createUser' => 'Create User',
        'stateChangeOnUpdate' => 'State Change On During Update',
        'activateUser' => 'Activate User',
    ]
];
