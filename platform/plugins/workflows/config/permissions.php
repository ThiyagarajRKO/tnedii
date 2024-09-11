<?php

return [
    [
        'name' => 'Workflows',
        'flag' => 'workflows.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'workflows.create',
        'parent_flag' => 'workflows.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'workflows.edit',
        'parent_flag' => 'workflows.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'workflows.destroy',
        'parent_flag' => 'workflows.index',
    ],
    [
        'name' => 'Export',
        'flag' => 'workflows.export',
        'parent_flag' => 'workflows.index',
    ],
    [
        'name' => 'Print',
        'flag' => 'workflows.print',
        'parent_flag' => 'workflows.index',
    ],
    [
    'name' => 'Enable Disable',
    'flag' => 'workflows.enable_disable',
    'parent_flag' => 'workflows.index',
    ],
    [
    'name' => 'Map Permission',
    'flag' => 'workflows.map_permission',
    'parent_flag' => 'workflows.index',
    ]
];
