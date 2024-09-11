<?php

return [[
                                'name' => 'Financial years',
                                'flag' => 'financial-year.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'financial-year.create',
            'parent_flag' => 'financial-year.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'financial-year.edit',
            'parent_flag' => 'financial-year.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'financial-year.destroy',
            'parent_flag' => 'financial-year.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'financial-year.export',
            'parent_flag' => 'financial-year.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'financial-year.print',
            'parent_flag' => 'financial-year.index',
            ],
			[
            'name' => 'Enable_disable',
            'flag' => 'financial-year.enable_disable',
            'parent_flag' => 'financial-year.index',
            ]
			];
