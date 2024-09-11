<?php

return [[
                            'name' => 'Entrepreneurs',
                            'flag' => 'plugins.entrepreneur'
                        ],[
                            'name' => 'Entrepreneurs',
                            'flag' => 'entrepreneur.index',
                            'parent_flag' => 'plugins.entrepreneur'
                        ],
			[
            'name' => 'Create',
            'flag' => 'entrepreneur.create',
            'parent_flag' => 'entrepreneur.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'entrepreneur.edit',
            'parent_flag' => 'entrepreneur.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'entrepreneur.destroy',
            'parent_flag' => 'entrepreneur.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'entrepreneur.export',
            'parent_flag' => 'entrepreneur.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'entrepreneur.print',
            'parent_flag' => 'entrepreneur.index',
            ],
			[
            'name' => 'Trainees',
            'flag' => 'trainee.index',
            'parent_flag' => 'plugins.entrepreneur',
            ],
			[
            'name' => 'Create',
            'flag' => 'trainee.create',
            'parent_flag' => 'trainee.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'trainee.edit',
            'parent_flag' => 'trainee.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'trainee.destroy',
            'parent_flag' => 'trainee.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'trainee.export',
            'parent_flag' => 'trainee.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'trainee.print',
            'parent_flag' => 'trainee.index',
            ]
			];
