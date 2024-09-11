<?php

return [[
                                'name' => 'Hub institutions',
                                'flag' => 'hub-institution.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'hub-institution.create',
            'parent_flag' => 'hub-institution.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'hub-institution.edit',
            'parent_flag' => 'hub-institution.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'hub-institution.destroy',
            'parent_flag' => 'hub-institution.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'hub-institution.export',
            'parent_flag' => 'hub-institution.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'hub-institution.print',
            'parent_flag' => 'hub-institution.index',
            ]
			];
