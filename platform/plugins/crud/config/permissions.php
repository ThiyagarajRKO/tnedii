<?php
return [
    [
        'name' => 'Cruds',
        'flag' => 'crud.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'crud.create',
        'parent_flag' => 'crud.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'crud.edit',
        'parent_flag' => 'crud.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'crud.destroy',
        'parent_flag' => 'crud.index',
    ],
    [
        'name'        => 'Enable',
        'flag'        => 'crud.enable',
        'parent_flag' => 'crud.index',
    ],


]; ?>
