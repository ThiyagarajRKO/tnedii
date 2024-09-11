<?php

return [[
                            'name' => 'Spoke registrations',
                            'flag' => 'plugins.spoke-registration'
                        ],[
                            'name' => 'Spoke registrations',
                            'flag' => 'spoke-registration.index',
                            'parent_flag' => 'plugins.spoke-registration'
                        ],
			[
            'name' => 'Create',
            'flag' => 'spoke-registration.create',
            'parent_flag' => 'spoke-registration.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'spoke-registration.edit',
            'parent_flag' => 'spoke-registration.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'spoke-registration.destroy',
            'parent_flag' => 'spoke-registration.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'spoke-registration.export',
            'parent_flag' => 'spoke-registration.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'spoke-registration.print',
            'parent_flag' => 'spoke-registration.index',
            ],
			[
            'name' => 'Spoke ecells',
            'flag' => 'spoke-ecells.index',
            'parent_flag' => 'plugins.spoke-registration',
            ],
			[
            'name' => 'Create',
            'flag' => 'spoke-ecells.create',
            'parent_flag' => 'spoke-ecells.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'spoke-ecells.edit',
            'parent_flag' => 'spoke-ecells.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'spoke-ecells.destroy',
            'parent_flag' => 'spoke-ecells.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'spoke-ecells.export',
            'parent_flag' => 'spoke-ecells.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'spoke-ecells.print',
            'parent_flag' => 'spoke-ecells.index',
            ]
			];
