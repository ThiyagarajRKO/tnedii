<?php

if (!defined('AUDIT_LOG_MODULE_SCREEN_NAME')) {
    define('AUDIT_LOG_MODULE_SCREEN_NAME', 'audit-log');
}

/* @Customized By Ramesh Esakki  - Start -*/
if (!defined('USER_ACTION_CRUD_MANAGEMENT')) {
    define('USER_ACTION_CRUD_MANAGEMENT', 'crud-audit-log');
}
/* @Customized By Ramesh Esakki  - End -*/


if (!defined('NOTIFICATION_EXCLUDE_MODULES')) {
    define('NOTIFICATION_EXCLUDE_MODULES', ['crud','master-detail','multidomain','of the system','to the system','page','role','vendor-document-details',
                                            'workflow','workflow-permission','password-criteria','Role']);
}
if (!defined('NOTIFICATION_DURATION_DAYS')) {
    define('NOTIFICATION_DURATION_DAYS', 10);
}