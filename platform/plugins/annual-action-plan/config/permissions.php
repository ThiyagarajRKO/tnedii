<?php

return [[
                                'name' => 'Annual action plans',
                                'flag' => 'annual-action-plan.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'annual-action-plan.create',
            'parent_flag' => 'annual-action-plan.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'annual-action-plan.edit',
            'parent_flag' => 'annual-action-plan.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'annual-action-plan.destroy',
            'parent_flag' => 'annual-action-plan.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'annual-action-plan.export',
            'parent_flag' => 'annual-action-plan.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'annual-action-plan.print',
            'parent_flag' => 'annual-action-plan.index',
            ]
			];
