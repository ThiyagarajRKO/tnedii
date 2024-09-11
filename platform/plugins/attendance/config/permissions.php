<?php

return [[
                            'name' => 'Attendances',
                            'flag' => 'plugins.attendance'
                        ],[
                            'name' => 'Attendances',
                            'flag' => 'attendance.index',
                            'parent_flag' => 'plugins.attendance'
                        ],
			[
            'name' => 'Create',
            'flag' => 'attendance.create',
            'parent_flag' => 'attendance.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'attendance.edit',
            'parent_flag' => 'attendance.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'attendance.destroy',
            'parent_flag' => 'attendance.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'attendance.export',
            'parent_flag' => 'attendance.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'attendance.print',
            'parent_flag' => 'attendance.index',
            ],
			[
            'name' => 'Inline_edit',
            'flag' => 'attendance.inline_edit',
            'parent_flag' => 'attendance.index',
            ],
			[
            'name' => 'Attendance remarks',
            'flag' => 'attendance-remark.index',
            'parent_flag' => 'plugins.attendance',
            ],
			[
            'name' => 'Create',
            'flag' => 'attendance-remark.create',
            'parent_flag' => 'attendance-remark.index',
            ],
			[
            'name' => 'Edit',
            'flag' => 'attendance-remark.edit',
            'parent_flag' => 'attendance-remark.index',
            ],
			[
            'name' => 'Delete',
            'flag' => 'attendance-remark.destroy',
            'parent_flag' => 'attendance-remark.index',
            ],
			[
            'name' => 'Export',
            'flag' => 'attendance-remark.export',
            'parent_flag' => 'attendance-remark.index',
            ],
			[
            'name' => 'Print',
            'flag' => 'attendance-remark.print',
            'parent_flag' => 'attendance-remark.index',
            ],	[
        'name' => 'View',
        'flag' => 'attendance.view',
        'parent_flag' => 'attendance.index',
    ],
			];
