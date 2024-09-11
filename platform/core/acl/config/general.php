<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activations
    |--------------------------------------------------------------------------
    |
    | Here you may specify the activations model used and the time (in seconds)
    | which activation codes expire. By default, activation codes expire after
    | three days. The lottery is used for garbage collection, expired
    | codes will be cleared automatically based on the provided odds.
    |
    */

    'activations' => [

        'expires' => 259200,

        'lottery' => [2, 100],

    ],

    'backgrounds' => [
        'vendor/core/core/acl/images/backgrounds/1.png',

    ],

    // Supported module should already used roles table to fetch record
    'role_hierarchy_supported' => [
        'Impiger\ACL\Models\Role',
        'Impiger\ACL\Models\User',
        'Impiger\User\Models\User'
    ],

    'user_model' => ["Impiger\User\Models\User"],
    'audit_histories_model' => ["Impiger\AuditLog\Models\AuditHistory"],


];
