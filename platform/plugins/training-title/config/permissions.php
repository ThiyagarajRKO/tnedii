<?php

return [[
                            'name' => 'Training titles',
                            'flag' => 'plugins.training-title'
                        ],[
                            'name' => 'Training titles',
                            'flag' => 'training-title.index',
                            'parent_flag' => 'plugins.training-title'
                        ],
			[
            'name' => 'Create',
            'flag' => 'training-title.create',
            'parent_flag' => 'training-title.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'training-title.edit',
            'parent_flag' => 'training-title.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'training-title.destroy',
            'parent_flag' => 'training-title.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'training-title.export',
            'parent_flag' => 'training-title.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'training-title.print',
            'parent_flag' => 'training-title.index',
            ],
			[
            'name' => 'Online training sessions',
            'flag' => 'online-training-session.index',
            'parent_flag' => 'plugins.training-title',
            ],
			[
            'name' => 'Create',
            'flag' => 'online-training-session.create',
            'parent_flag' => 'online-training-session.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'online-training-session.edit',
            'parent_flag' => 'online-training-session.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'online-training-session.destroy',
            'parent_flag' => 'online-training-session.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'online-training-session.export',
            'parent_flag' => 'online-training-session.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'online-training-session.print',
            'parent_flag' => 'online-training-session.index',
            ]
			];
