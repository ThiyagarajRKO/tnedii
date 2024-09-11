<?php

return [[
                                'name' => 'Mentees',
                                'flag' => 'mentee.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'mentee.create',
            'parent_flag' => 'mentee.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'mentee.edit',
            'parent_flag' => 'mentee.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'mentee.destroy',
            'parent_flag' => 'mentee.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'mentee.export',
            'parent_flag' => 'mentee.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'mentee.print',
            'parent_flag' => 'mentee.index',
            ]
			];
