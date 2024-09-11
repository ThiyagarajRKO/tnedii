<?php

return [
    [
        'name' => 'Usergroups',
        'flag' => 'usergroups.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'usergroups.create',
        'parent_flag' => 'usergroups.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'usergroups.edit',
        'parent_flag' => 'usergroups.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'usergroups.destroy',
        'parent_flag' => 'usergroups.index',
    ],
    	[
            'name' => 'Export',
            'flag' => 'usergroups.export',
            'parent_flag' => 'usergroups.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'usergroups.print',
            'parent_flag' => 'usergroups.index',
            ],
    [
        'name' => 'UsergroupEntity',
        'flag' => 'usergroupsentity.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'usergroupsentity.create',
        'parent_flag' => 'usergroupsentity.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'usergroupsentity.edit',
        'parent_flag' => 'usergroupsentity.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'usergroupsentity.destroy',
        'parent_flag' => 'usergroupsentity.index',
    ],
];
