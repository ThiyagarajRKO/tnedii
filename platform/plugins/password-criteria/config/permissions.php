<?php

return [
    [
        'name' => 'Password criterias',
        'flag' => 'password-criteria.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'password-criteria.create',
        'parent_flag' => 'password-criteria.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'password-criteria.edit',
        'parent_flag' => 'password-criteria.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'password-criteria.destroy',
        'parent_flag' => 'password-criteria.index',
    ],
];
