<?php

return [
    [
        'name' => 'Knowledge partners',
        'flag' => 'plugins.knowledge-partner'
    ], [
        'name' => 'Knowledge partners',
        'flag' => 'knowledge-partner.index',
        'parent_flag' => 'plugins.knowledge-partner'
    ],
    //[
    //    'name' => 'Create',
    //    'flag' => 'knowledge-partner.create',
    //    'parent_flag' => 'knowledge-partner.index',
    //],
    [
        'name' => 'Edit',
        'flag' => 'knowledge-partner.edit',
        'parent_flag' => 'knowledge-partner.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'knowledge-partner.destroy',
        'parent_flag' => 'knowledge-partner.index',
    ],
    //[
    //    'name' => 'Enable_disable',
    //    'flag' => 'knowledge-partner.enable_disable',
    //    'parent_flag' => 'knowledge-partner.index',
    //],
    [
        'name' => 'Export',
        'flag' => 'knowledge-partner.export',
        'parent_flag' => 'knowledge-partner.index',
    ],
    [
        'name' => 'Print',
        'flag' => 'knowledge-partner.print',
        'parent_flag' => 'knowledge-partner.index',
    ],
];
