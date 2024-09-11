<?php

//$reportPermissions = \Impiger\Reports\Http\Controllers\ReportsController::getReportsPermissions();
//
//return $reportPermissions;

return [
    [
        'name' => 'Reports',
        'flag' => 'reports.index'
    ],
    [
        'name' => 'District Wise Beneficiaries',
        'flag' => 'reports.district_abstract',
        'parent_flag' => 'reports.index',
    ],
    [
        'name' => 'District Wise Beneficiaries Details',
        'flag' => 'reports.district_textual',
        'parent_flag' => 'reports.index',
    ],
    [
        'name' => 'Program Wise Beneficiaries',
        'flag' => 'reports.program_abstract',
        'parent_flag' => 'reports.index',
    ],
    [
        'name' => 'Program Wise Beneficiaries Details',
        'flag' => 'reports.program_textual',
        'parent_flag' => 'reports.index',
    ],
    [
        'name' => 'Community Wise Beneficiaties Details',
        'flag' => 'reports.community_textual',
        'parent_flag' => 'reports.index',
    ],
    [
        'name' => 'Community Wise Beneficiaties',
        'flag' => 'reports.community_abstract',
        'parent_flag' => 'reports.index',
    ],
    [
        'name' => 'Religion Wise Beneficiaties Details',
        'flag' => 'reports.religion_textual',
        'parent_flag' => 'reports.index',
    ],
    [
        'name' => 'Religion Wise Beneficiaties',
        'flag' => 'reports.religion_abstract',
        'parent_flag' => 'reports.index',
    ],
    [
        'name' => 'Canidate Type Wise Beneficiaties Details',
        'flag' => 'reports.candidate_textual',
        'parent_flag' => 'reports.index',
    ],
];
