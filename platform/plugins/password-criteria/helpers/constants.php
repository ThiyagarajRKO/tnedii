<?php

if (!defined('PASSWORD_CRITERIA_MODULE_SCREEN_NAME')) {
    define('PASSWORD_CRITERIA_MODULE_SCREEN_NAME', 'password-criteria');
}
// Password Criteria Settings
if (!defined('ALLOWED_LOGIN_INVALID_ATTEMPT')) {
    define('ALLOWED_LOGIN_INVALID_ATTEMPT', 3);
}
if (!defined('PWD_ALLOWED_SPECIAL_CHAR')) {
    define('PWD_ALLOWED_SPECIAL_CHAR', serialize(array(1 => "!,@,#,$,%,^,&,*,(,),_,-")));
}
if (!defined('MIN_PWD_NUMBER_COUNT')) {
    define('MIN_PWD_NUMBER_COUNT', 1);
}
if (!defined('BASE_FILTER_ADD_PASSWORD_CRITERIA')) {
    define('BASE_FILTER_ADD_PASSWORD_CRITERIA', 'add_criteria');
}
if (!defined('BASE_FILTER_CHECK_PASSWORD_CRITERIA')) {
    define('BASE_FILTER_CHECK_PASSWORD_CRITERIA', 'check_criteria');
}

if (!defined('MAINTAIN_PASSWORD_HISTORY')) {
    define('MAINTAIN_PASSWORD_HISTORY', 'maintain_password_history');
}
if (!defined('CHECK_PASSWORD_EXPIRY')) {
    define('CHECK_PASSWORD_EXPIRY', 'check_password_expiry');
}