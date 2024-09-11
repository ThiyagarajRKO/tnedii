<?php
if (!defined('IS_ENABLED')) {
    define('IS_ENABLED', 1);
}
if (!defined('ADD_CUSTOM_ACTION')) {
    define('ADD_CUSTOM_ACTION', 'add_custom_actions');
}

if (!defined('MONTHS')) {
    define('MONTHS', array('1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'));
}

if (!defined('MONTHS_IN_SHORT')) {
    define('MONTHS_IN_SHORT', array('1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'May', '6' => 'Jun', '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'));
}
if(!defined('APP_NAME')){
    define('APP_NAME',  "[".env("APP_NAME")."]");
}
if (!defined('STATS_OPERATION')) {
    define('STATS_OPERATION', [
        'SUM' => 'Sum', 'COUNT' => 'Count', 'AVG' => 'Average', 'MIN' => 'Minimum',
        'Max' => 'Maximum'
    ]);
}

if (!defined('DASHBOARD_STATS_TYPE')) {
    define('DASHBOARD_STATS_TYPE', ['stats' => 'Stats', 'pie' => 'Pie Chart', 'table' => 'Table', 'bar' => 'Bar']);
}
if (!defined('DASHBOARD_OPERATION_TYPE')) {
    define('DASHBOARD_OPERATION_TYPE', ['CNT' => 'Count', 'SUM' => 'Sum', 'MAX' => 'Max', 'MIN' => 'Min', 'AVG' => 'Average']);
}
if (!defined('RENDER_CAPTCHA_FIELD')) {
    define('RENDER_CAPTCHA_FIELD', 'render-captcha-field');
}
if (!defined('HIDE_CUSTOM_CAPTCHA')) {
    define('HIDE_CUSTOM_CAPTCHA', TRUE);
}
if (!defined('IMP_USER_TABLE')) {
    define('IMP_USER_TABLE','impiger_users');
}
if (!defined('IMP_USER_TABLE1')) {
    define('IMP_USER_TABLE1','impiger_users1');
}
if (!defined('DEFAULT_PERMISSIONS')) {
    define('DEFAULT_PERMISSIONS',array('media.index','files.index','files.create'));
}
if (!defined('SUPERADMIN_ROLE_SLUG')) {
    define('SUPERADMIN_ROLE_SLUG','superadmin');
}
if (!defined('EXCLUDE_ROLES_IN_USER_LIST')) {
    define('EXCLUDE_ROLES_IN_USER_LIST',[]);
}

if (!defined('VENDOR_ROLE_SLUG')) {
    define('VENDOR_ROLE_SLUG', 'vendor');
}

if(!defined('USER_LOGIN_CREDENTIALS_SUBJECT')) {
    define('USER_LOGIN_CREDENTIALS_SUBJECT', APP_NAME." - Login credentials");
}

if(!defined('USER_LOGIN_CREDENTIALS_MSG')) {
    define('USER_LOGIN_CREDENTIALS_MSG', "Dear {first_name},\u003Cbr\u003E\u003Cbr\u003ENew account has been created in Emircom.\u003Cbr\u003E\u003Cbr\u003EUsername : {email}\u003Cbr\u003EPassword : {temp_password}\u003Cbr\u003EKindly use this \u003Ca href='{domain_href}'\u003EURL\u003C\/a\u003E to login\u003Cbr\u003E\u003Cbr\u003EIf you are wrong person, Please ignore this email.\u003Cbr\u003E\u003Cbr\u003EThank you");
}

if(!defined('ENTITY_CUSTOM_FIELD')) {
    define('ENTITY_CUSTOM_FIELD', ['vendors'=>'company_name','impiger_users'=>'first_name']);
}
if(!defined('SHOW_CRUD_GENERATOR_MENU')) {
    define('SHOW_CRUD_GENERATOR_MENU', TRUE);
}

if(!defined('PENDING_REQUEST_REMINDER_SUBJECT')) {
    define('PENDING_REQUEST_REMINDER_SUBJECT', APP_NAME." - Reminder: Pending Vendor Request");
}

if(!defined('PENDING_REQUEST_REMINDER_MSG')) {
    define('PENDING_REQUEST_REMINDER_MSG', "Dear {name},<br><br> There are {pending_request} pending vendor requests.So kindly approve/reject those requests .<br><br><a href='{domain_href}'> Click here </a> to know more details<br><br>Regards,<br>Admin");
}
if (!defined('SCHEDULER_DAILY_TIME')) {
    define('SCHEDULER_DAILY_TIME','21:30');
}
if (!defined('CUSTOM_CAPTCHA_CONFIG')) {
    define('CUSTOM_CAPTCHA_CONFIG','custom');
}
if (!defined('HUB_ROLE_SLUG')) {
    define('HUB_ROLE_SLUG','hub');
}
if (!defined('SPOKE_ROLE_SLUG')) {
    define('SPOKE_ROLE_SLUG','spoke');
}
if (!defined('SPOKE_STUDENT_ROLE_SLUG')) {
    define('SPOKE_STUDENT_ROLE_SLUG','spoke-student');
}
if (!defined('CANDIDATE_ROLE_SLUG')) {
    define('CANDIDATE_ROLE_SLUG','candidate');
}
if (!defined('MENTOR_ROLE_SLUG')) {
    define('MENTOR_ROLE_SLUG','mentor');
}
if (!defined('TRAINER_ROLE_SLUG')) {
    define('TRAINER_ROLE_SLUG','trainer');
}
if (!defined('INNOVATOR_ROLE_SLUG')) {
    define('INNOVATOR_ROLE_SLUG','innovators');
}
if (!defined('REGIONAL_ADMIN_ROLE_SLUG')) {
    define('REGIONAL_ADMIN_ROLE_SLUG','regional-admin');
}
if (!defined('MSME_CANDIDATE_API_CONFIG')) {
    define('MSME_CANDIDATE_API_CONFIG',array("NEEDS" => ["url" =>"https://msmeonline.tn.gov.in/needs/needs_edi_api.php","token"=>"edWoaPXNrEf4A" ],
                                             "UYEGP" => ["url"=>"https://msmeonline.tn.gov.in/uyegp/uyegp_edi_api.php","token"=>"ed2p0K0v9SlLY"],
                                             "AABCS" => ["url"=>"https://msmeonline.tn.gov.in/aabcs/aabcs_edi_api.php","token"=>"ed75X6/Hme8A6"]
                                            
        ));
}

if (!defined('OUR_SERVICES_UI_SLUG')) {
    
    define('OUR_SERVICES_UI_SLUG',[
        'entrepreneurs' => ['class' => 'osc-ent', 'fa_icon' => 'fa fa-suitcase', 'class_icon' => 'entrepreneurs-bg'],
        'startup-business' => ['class' => 'osc-startup', 'fa_icon' => 'fa fa-line-chart', 'class_icon' => 'startup-bg'],
        'innovation-programme' => ['class' => 'osc-innovation', 'fa_icon' => 'fa fa-rocket', 'class_icon' => 'innovation-bg'],
        'trainings' => ['class' => 'osc-training', 'fa_icon' => 'fa fa-signal', 'class_icon' => 'training-bg'],
        'tnsi' => ['class' => 'osc-tnsi', 'fa_icon' => 'fa fa-graduation-cap', 'class_icon' => 'tnsi-bg']
    ]);
}

if (!defined('APPLY_EVENT_FEE_FLAG')) {
    define('APPLY_EVENT_FEE_FLAG',['free' => 1, 'paid' => 2]);
}
if (!defined('EXCLUDE_CATEGORY_SLUGS')) {
    define('EXCLUDE_CATEGORY_SLUGS',['our-services']);
}
if (!defined('NEWSLETTER_CATEGORY_SLUG')) {
    define('NEWSLETTER_CATEGORY_SLUG','news-letters');
}
if (!defined('EXCLUDE_ABBREVATION_WORD')) {
    define('EXCLUDE_ABBREVATION_WORD',['of','on','is','be']);
}
if (!defined('EXCLUDE_CANDIDATE_TYPE_SLUG')) {
    define('EXCLUDE_CANDIDATE_TYPE_SLUG',['spokestudent-candidate-type']);
}

if (!defined('THEME_EXTRA_COLORS')) {
    define('THEME_EXTRA_COLORS',['#20c997','#17a2b8','#ffc107','#3598dc','#32c5d2','#FFBC09']);
}
if (!defined('MSME_CERTIFICATE_DAYS')) {
    define('MSME_CERTIFICATE_DAYS',['NEEDS' => 14, 'UYEGP' => 3,'AABCS' => 15]);
}

if (!defined('MSME_NEEDS_SLUG')) {
    define('MSME_NEEDS_SLUG','needs-msme-scheme');
}
if (!defined('MSME_UYEGP_SLUG')) {
    define('MSME_UYEGP_SLUG','uyegp-msme-scheme');
}
if (!defined('MSME_AABCS_SLUG')) {
    define('MSME_AABCS_SLUG','aabcs-msme-scheme');
}



