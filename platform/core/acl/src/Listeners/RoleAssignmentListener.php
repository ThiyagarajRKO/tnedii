<?php

namespace Impiger\ACL\Listeners;

use Exception;
use Illuminate\Support\Facades\Auth;
use Impiger\ACL\Events\RoleAssignmentEvent;

class RoleAssignmentListener
{

    /**
     * Handle the event.
     *
     * @param RoleAssignmentEvent $event
     * @return void
     *
     * @throws Exception
     * @customized Sabari Shankar.Parthiban
     */
    public function handle(RoleAssignmentEvent $event)
    {
        $permissions = $event->role->permissions;
        $permissions[ACL_ROLE_SUPER_USER] = $event->user->super_user;
        $permissions[ACL_ROLE_MANAGE_SUPERS] = $event->user->manage_supers;
        /*  @customized Sabari Shankar.Parthiban start */
        $multiPermission = get_multi_role_permissions($event->user->id);
        $permissions = array_merge($permissions,$multiPermission);
        $event->user->permissions = $permissions;
        $event->user->save();
        /*  @customized Sabari Shankar.Parthiban end */
        cache()->forget(md5('cache-dashboard-menu-' . Auth::id()));
    }
}
