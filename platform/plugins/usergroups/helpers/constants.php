<?php

if (!defined('USERGROUPS_MODULE_SCREEN_NAME')) {
    define('USERGROUPS_MODULE_SCREEN_NAME', 'usergroups');
}
if (!defined('DEPENDANT_MODULE_IN_USERGROUPS')) {
    define('DEPENDANT_MODULE_IN_USERGROUPS', array(array("table_name"=> "usergroup_entity",
                    "dependent_key"=> "usergroup_id",
                    "dependent_module"=> "Usergroup Entity")));
}