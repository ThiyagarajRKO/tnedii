<?php

return [[
                                'name' => 'Backend menus',
                                'flag' => 'backend-menu.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'backend-menu.create',
            'parent_flag' => 'backend-menu.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'backend-menu.edit',
            'parent_flag' => 'backend-menu.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'backend-menu.destroy',
            'parent_flag' => 'backend-menu.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'backend-menu.export',
            'parent_flag' => 'backend-menu.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'backend-menu.print',
            'parent_flag' => 'backend-menu.index',
            ]
			];
