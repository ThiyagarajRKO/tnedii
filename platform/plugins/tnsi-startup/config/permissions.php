<?php

return [[
                                'name' => 'Tnsi startups',
                                'flag' => 'tnsi-startup.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'tnsi-startup.create',
            'parent_flag' => 'tnsi-startup.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'tnsi-startup.edit',
            'parent_flag' => 'tnsi-startup.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'tnsi-startup.destroy',
            'parent_flag' => 'tnsi-startup.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'tnsi-startup.export',
            'parent_flag' => 'tnsi-startup.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'tnsi-startup.print',
            'parent_flag' => 'tnsi-startup.index',
            ]
			];
