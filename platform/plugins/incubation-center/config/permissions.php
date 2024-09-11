<?php

return [[
                                'name' => 'Incubation centers',
                                'flag' => 'incubation-center.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'incubation-center.create',
            'parent_flag' => 'incubation-center.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'incubation-center.edit',
            'parent_flag' => 'incubation-center.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'incubation-center.destroy',
            'parent_flag' => 'incubation-center.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'incubation-center.export',
            'parent_flag' => 'incubation-center.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'incubation-center.print',
            'parent_flag' => 'incubation-center.index',
            ]
			];
