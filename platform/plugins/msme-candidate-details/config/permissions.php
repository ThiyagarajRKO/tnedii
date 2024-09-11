<?php

return [[
                                'name' => 'Msme candidate details',
                                'flag' => 'msme-candidate-details.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'msme-candidate-details.create',
            'parent_flag' => 'msme-candidate-details.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'msme-candidate-details.edit',
            'parent_flag' => 'msme-candidate-details.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'msme-candidate-details.destroy',
            'parent_flag' => 'msme-candidate-details.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'msme-candidate-details.export',
            'parent_flag' => 'msme-candidate-details.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'msme-candidate-details.print',
            'parent_flag' => 'msme-candidate-details.index',
            ]
			];
