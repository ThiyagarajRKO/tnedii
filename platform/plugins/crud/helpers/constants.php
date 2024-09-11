<?php

if (!defined('CRUD_MODULE_SCREEN_NAME')) {
    define('CRUD_MODULE_SCREEN_NAME', 'crud');
}

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

if (!defined('RECENT_TRAINING')) {
    define('RECENT_TRAINING','recent_training');
}

if(!defined('INTEGRITY_CONSTRAINT_VIOLATION_ERROR_CODE')){
    define('INTEGRITY_CONSTRAINT_VIOLATION_ERROR_CODE',1048);
}

if(!defined('INTEGRITY_ERROR_MESSAGES')){
    define('INTEGRITY_ERROR_MESSAGES',array(INTEGRITY_CONSTRAINT_VIOLATION_ERROR_CODE => '{column_name} field is required'));
}