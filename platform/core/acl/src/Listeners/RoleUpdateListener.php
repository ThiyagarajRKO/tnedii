<?php

namespace Impiger\ACL\Listeners;

use Exception;
use Illuminate\Support\Facades\Auth;
use Impiger\ACL\Events\RoleUpdateEvent;
/*
 * @customized Sabari Shankar.Parthiban
 */
use Impiger\ACL\Models\Role;
use Illuminate\Support\Arr;

class RoleUpdateListener
{
    /**
     * Handle the event.
     *
     * @param RoleUpdateEvent $event
     * @return void
     *
     * @throws Exception
     * @customized Sabari Shankar.Parthiban
     */
    public function handle(RoleUpdateEvent $event)
    {
        $permissions = $event->role->permissions;
        foreach ($event->role->users()->get() as $user) {
            /*  @customized Sabari Shankar.Parthiban start */
            $preparedPermission = $permissions;
            $multiPermission = get_multi_role_permissions($user->id);
            $preparedPermission[ACL_ROLE_SUPER_USER] = $user->super_user;
            $preparedPermission[ACL_ROLE_MANAGE_SUPERS] = $user->manage_supers;
            $preparedPermission = array_merge($preparedPermission,$multiPermission);
            $user->permissions = $preparedPermission;
            $user->save();
            //data level security
            $userPermissions = $user->userPermission;
            if(!empty($userPermissions)){
               foreach($userPermissions as $userPermission){
                $allPermissions = get_permission_role_ids($userPermission->role_id);
                $userPermission->role_permissions = $allPermissions;
                $userPermission->save();
            } 
            }
            /*  @customized Sabari Shankar.Parthiban end */
            
        }

        cache()->forget(md5('cache-dashboard-menu-' . Auth::id()));
    }
}
