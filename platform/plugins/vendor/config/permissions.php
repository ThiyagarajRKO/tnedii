<?php

return [[
                                'name' => 'Vendors',
                                'flag' => 'vendor.index'
                            ],
			[
            'name' => 'Create',
            'flag' => 'vendor.create',
            'parent_flag' => 'vendor.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'vendor.edit',
            'parent_flag' => 'vendor.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'vendor.destroy',
            'parent_flag' => 'vendor.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'vendor.export',
            'parent_flag' => 'vendor.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'vendor.print',
            'parent_flag' => 'vendor.index',
            ]
			];
