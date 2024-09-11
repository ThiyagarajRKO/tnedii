<?php

return [
    'entity_filter_supported_models'=>['impiger_users'],
    'dls_supported_models' => [
          "Impiger\User\Models\User", 
          "Impiger\Entrepreneur\Models\Entrepreneur", 
          "Impiger\Mentor\Models\Mentor", 
          "Impiger\Vendor\Models\Vendor"
    ],
    'user_supported_models' => ["Impiger\User\Models\User"],
    'user_entity_supported_models' => [
          "Impiger\HubInstitution\Models\HubInstitution",
          "Impiger\SpokeRegistration\Models\SpokeRegistration"
     ],    
    'entity_models' => [
          "Impiger\MasterDetail\Models\Region",
          "Impiger\MasterDetail\Models\District",
          "Impiger\MasterDetail\Models\Division",
          "Impiger\HubInstitution\Models\HubInstitution",
          "Impiger\SpokeRegistration\Models\SpokeRegistration"
     ],
    'entity_relation_key' => [
        'divisions' => 'division_id',
        'spoke_registration' => 'spoke_registration_id',
        'hub_institutions' => 'hub_institution_id',
        'regions' => 'region_id',
        'district' => 'district_id',
    ],
    'certificate_supported_models' => ['Impiger\Entrepreneur\Models\Trainee'],
    //Razorpay Payment Gateway
    'payment_gateway_supported_modue' => [
        "Impiger\TrainingTitle\Models\TrainingTitle"
   ],
   'back_to_navigation' => [
        'entrepreneur' => 'entrepreneur.index',
        'mentor' => 'mentor.index',
        'vendor' => 'vendor.index'
   ],
   'navigation_supported_models' => [
        "Impiger\Entrepreneur\Models\Entrepreneur" => "entrepreneur",
        "Impiger\Mentor\Models\Mentor" => "mentor",
        "Impiger\Vendor\Models\Vendor" => "vendor"
   ],
   'financial_year_supported_modules' => [
        "Impiger\AnnualActionPlan\Models\AnnualActionPlan",
        "Impiger\Attendance\Models\Attendance",
        "Impiger\TrainingTitle\Models\TrainingTitle",
        "Impiger\Entrepreneur\Models\Trainee",
        "Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail"
   ]
];
