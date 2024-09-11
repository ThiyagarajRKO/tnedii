<?php

return [[
                                'name' => 'Training title financial details',
                                'flag' => 'training-title-financial-detail.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'training-title-financial-detail.create',
            'parent_flag' => 'training-title-financial-detail.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'training-title-financial-detail.edit',
            'parent_flag' => 'training-title-financial-detail.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'training-title-financial-detail.destroy',
            'parent_flag' => 'training-title-financial-detail.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'training-title-financial-detail.export',
            'parent_flag' => 'training-title-financial-detail.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'training-title-financial-detail.print',
            'parent_flag' => 'training-title-financial-detail.index',
            ]
			];
