<?php

return [
    [
        'name'        => 'Activity Logs',
        'flag'        => 'audit-log.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name'        => 'View',
        'flag'        => 'audit-log.view',
        'parent_flag' => 'audit-log.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'audit-log.destroy',
        'parent_flag' => 'audit-log.index',
    ],
    [
        'name'        => 'Delete All',
        'flag'        => 'audit-log.empty',
        'parent_flag' => 'audit-log.index',
    ],
];