<?php

return [[
                                'name' => 'Mentors',
                                'flag' => 'mentor.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'mentor.create',
            'parent_flag' => 'mentor.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'mentor.edit',
            'parent_flag' => 'mentor.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'mentor.destroy',
            'parent_flag' => 'mentor.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'mentor.export',
            'parent_flag' => 'mentor.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'mentor.print',
            'parent_flag' => 'mentor.index',
            ]
			];
