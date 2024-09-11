<?php

return [
    [
        'name' => 'Innovation voucher programs',
        'flag' => 'plugins.innovation-voucher-program'
    ], [
        'name' => 'Innovation voucher programs',
        'flag' => 'innovation-voucher-program.index',
        'parent_flag' => 'plugins.innovation-voucher-program'
    ],
    [
        'name' => 'Create',
        'flag' => 'innovation-voucher-program.create',
        'parent_flag' => 'innovation-voucher-program.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'innovation-voucher-program.edit',
        'parent_flag' => 'innovation-voucher-program.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'innovation-voucher-program.destroy',
        'parent_flag' => 'innovation-voucher-program.index',
    ],
    [
        'name' => 'Enable_disable',
        'flag' => 'innovation-voucher-program.enable_disable',
        'parent_flag' => 'innovation-voucher-program.index',
    ],
    [
        'name' => 'Export',
        'flag' => 'innovation-voucher-program.export',
        'parent_flag' => 'innovation-voucher-program.index',
    ],
    [
        'name' => 'Print',
        'flag' => 'innovation-voucher-program.print',
        'parent_flag' => 'innovation-voucher-program.index',
    ],
    [
        'name' => 'Ivp company details',
        'flag' => 'ivp-company-details.index',
        'parent_flag' => 'plugins.innovation-voucher-program',
    ],
    [
        'name' => 'Create',
        'flag' => 'ivp-company-details.create',
        'parent_flag' => 'ivp-company-details.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'ivp-company-details.edit',
        'parent_flag' => 'ivp-company-details.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'ivp-company-details.destroy',
        'parent_flag' => 'ivp-company-details.index',
    ],
    [
        'name' => 'Export',
        'flag' => 'ivp-company-details.export',
        'parent_flag' => 'ivp-company-details.index',
    ],
    [
        'name' => 'Print',
        'flag' => 'ivp-company-details.print',
        'parent_flag' => 'ivp-company-details.index',
    ],
    [
        'name' => 'Ivp knowledge partners',
        'flag' => 'ivp-knowledge-partner.index',
        'parent_flag' => 'plugins.innovation-voucher-program',
    ],
    [
        'name' => 'Create',
        'flag' => 'ivp-knowledge-partner.create',
        'parent_flag' => 'ivp-knowledge-partner.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'ivp-knowledge-partner.edit',
        'parent_flag' => 'ivp-knowledge-partner.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'ivp-knowledge-partner.destroy',
        'parent_flag' => 'ivp-knowledge-partner.index',
    ],
    [
        'name' => 'Export',
        'flag' => 'ivp-knowledge-partner.export',
        'parent_flag' => 'ivp-knowledge-partner.index',
    ],
    [
        'name' => 'Print',
        'flag' => 'ivp-knowledge-partner.print',
        'parent_flag' => 'ivp-knowledge-partner.index',
    ]
];
