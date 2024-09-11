<?php

return [[
                            'name' => 'Users',
                            'flag' => 'plugins.user'
                        ],[
                            'name' => 'Users',
                            'flag' => 'user.index',
                            'parent_flag' => 'plugins.user'
                        ],
			[
            'name' => 'Create',
            'flag' => 'user.create',
            'parent_flag' => 'user.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'user.edit',
            'parent_flag' => 'user.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'user.destroy',
            'parent_flag' => 'user.index',
            ],

			[
            'name' => 'User addresses',
            'flag' => 'user-address.index',
            'parent_flag' => 'plugins.user',
            ],
			[
            'name' => 'Create',
            'flag' => 'user-address.create',
            'parent_flag' => 'user-address.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'user-address.edit',
            'parent_flag' => 'user-address.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'user-address.destroy',
            'parent_flag' => 'user-address.index',
            ]


			];
