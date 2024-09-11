<?php

if (!defined('WORKFLOWS_MODULE_SCREEN_NAME')) {
    define('WORKFLOWS_MODULE_SCREEN_NAME', 'workflows');
}

if (!defined('WORKFLOW_PERMISSION_MODULE_SCREEN_NAME')) {
    define('WORKFLOW_PERMISSION_MODULE_SCREEN_NAME', 'workflow-permission');
}

if (!defined('WORKFLOWS_MODULE_AUDIT_TRAIL_ACTION')) {
    define('WORKFLOWS_MODULE_AUDIT_TRAIL_ACTION', 'workflows_audit_trail_action');
}

if (!defined('APPLY_WORKFLOW_TRANSITION')) {
    define('APPLY_WORKFLOW_TRANSITION', 'apply_workflow_transition');
}

if (!defined('APPLY_WORKFLOW_INITIAL_TRANSITION')) {
    define('APPLY_WORKFLOW_INITIAL_TRANSITION', 'apply_workflow_initial_transition');
}

if (!defined('LOAD_WORKFLOW_ASSETS')) {
    define('LOAD_WORKFLOW_ASSETS', 'load_workflow_assets');
}

if (!defined('WORKFLOW_NOTIFICATION')) {
    define('WORKFLOW_NOTIFICATION', 'workflow_notification');
}


if (!defined('WORKFLOW_NOTIFICATION_MSG')) {
    define('WORKFLOW_NOTIFICATION_MSG', 'Hi {receiver_name},<br/><br/>{approver_name} has been change the {status} in {module}.<br/>Approver Comments  :  {comments}<br/><br/>Regards,<br/>Admin.');
}

if(!defined('WORKFLOW_EMAIL_CONFIG_VARIABLES')){
    define('WORKFLOW_EMAIL_CONFIG_VARIABLES', serialize(array('receiver_name'=>'Receiver Name','module'=>'Module Name','approver_name'=>'Approver Name','status'=>'Status','comments'=>'Comments')));
}

if (!defined('WORKFLOW_TRANSITION_MODULE_SCREEN_NAME')) {
    define('WORKFLOW_TRANSITION_MODULE_SCREEN_NAME', 'workflow-transition');
}

if (!defined('WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE')) {
    define('WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE', 1);
}

if (!defined('WORKFLOW_PERMISSION_SPECIFIC_TO_USER')) {
    define('WORKFLOW_PERMISSION_SPECIFIC_TO_USER', 2);
}

if (!defined('WORKFLOW_ATTACHMENT_CONFIG')) {
    define('WORKFLOW_ATTACHMENT_CONFIG', false);
}