<?php

namespace Impiger\ACL;

use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\ACL\Models\Role;
use Illuminate\Support\Arr;


class Roles
{
    /**
     * @var RoleInterface
     */
    protected $roleRepository;

    /**
     * Roles constructor.
     * @param RoleInterface $roleRepository
     */
    public function __construct(RoleInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getChildRolesUsingLogin($returnIdOnly = true,$applyRawCondition = true, $ignoreRole = false)
    {
        $user = \Auth::user();
        $roleIds = [];
        if($user){
        if ($user->super_user || in_array(SUPERADMIN_ROLE_SLUG,$user->roles()->get()->pluck('slug')->toArray())) {
            $query = Role::where('id', '>', 0);
            /*@ Customized By Sabari Shankar Parthiban start */
            $rawCondition = get_common_condition('roles');
            if($applyRawCondition && !empty($rawCondition)){
                $query = Role::whereRaw($rawCondition)->where('id', '>', 0);
            }
            if($ignoreRole) {
                $query = $query->whereNotIn('roles.slug', EXCLUDE_ROLES_IN_USER_LIST);
            }
            $roleIds = $query->pluck('id')->toArray();
            /*@ Customized By Sabari Shankar Parthiban end */
        } else {
            $roleId = $user->roles->pluck('id')->toArray();
            $query = Role::whereIn('id', $roleId)->select('child_roles');
            /*@ Customized By Sabari Shankar Parthiban start */
            $rawCondition = get_common_condition('roles');
            if($applyRawCondition && !empty($rawCondition)){
                $query = Role::whereRaw($rawCondition)->whereIn('id', $roleId)->select('child_roles');
            }
            if($ignoreRole) {
                $query = $query->whereNotIn('roles.slug', EXCLUDE_ROLES_IN_USER_LIST);
            }
            $result = $query->get();
            /*@ Customized By Sabari Shankar Parthiban end */
            if ($result && count($result) > 0) {
                foreach ($result as $role) {
                    if (\Arr::has($role->child_roles, 0)) {
                        $roleIds = array_merge($roleIds, $role->child_roles);
                    }
                }
            }
        }
        }
        if ($returnIdOnly) {
            return $roleIds;
        }
        
        return ($roleIds) ? Role::whereIn('id', $roleIds)->get() : [];
    }
    /**
     * @return array
     */
    public function getAvailablePermissions(): array {
        $permissions = [];

        $configuration = config('cms-permissions');
        if (!empty($configuration)) {
            foreach ($configuration as $config) {
                $permissions[$config['flag']] = $config;
            }
        }

        $types = ['core', 'packages', 'plugins'];

        foreach ($types as $type) {
            $permissions = array_merge($permissions, $this->getAvailablePermissionForEachType($type));
        }

        return $permissions;
    }

    /**
     * @param string $type
     * @return array
     * @customized Sabari Shankar.Parthiban
     */
    protected function getAvailablePermissionForEachType($type) {
        $permissions = [];
        $userPermissions = [];
        // available roles for the user
        $user = auth()->user();
        if (!$user->super_user) {
            $userPermissions = $user->permissions;
        }
        foreach (scan_folder(platform_path($type)) as $module) {
            $configuration = config(strtolower($type . '.' . $module . '.permissions'));
            if (!empty($configuration)) {
                foreach ($configuration as $config) {
                    if (!empty($userPermissions)) {
                        if (array_key_exists($config['flag'], $userPermissions)) {
                            $permissions[$config['flag']] = $config;
                        }
                    } else {
                        $permissions[$config['flag']] = $config;
                    }
                }
            }
        }

        return $permissions;
    }

    /**
     * @param array $permissions
     * @return array
     */
    public function getPermissionTree($permissions): array {
        $sortedFlag = $permissions;
        sort($sortedFlag);
        $children['root'] = $this->getChildren('root', $sortedFlag);

        foreach (array_keys($permissions) as $key) {
            $childrenReturned = $this->getChildren($key, $permissions);
            if (count($childrenReturned) > 0) {
                $children[$key] = $childrenReturned;
            }
        }

        return $children;
    }

    /**
     * @param int $parentId
     * @param array $allFlags
     * @return mixed
     */
    protected function getChildren($parentId, array $allFlags) {
        $newFlagArray = [];
        foreach ($allFlags as $flagDetails) {
            if (Arr::get($flagDetails, 'parent_flag', 'root') == $parentId) {
                $newFlagArray[] = $flagDetails['flag'];
            }
        }
        return $newFlagArray;
    }
    /**
     * Return a correctly type casted permissions array
     * @param array $permissions
     * @return array
     */
    public function cleanPermission($permissions)
    {
        if (!$permissions) {
            return [];
        }

        $cleanedPermissions = [];
        foreach ($permissions as $permissionName) {
            $cleanedPermissions[$permissionName] = true;
        }

        return $cleanedPermissions;
    }
    public function getChildUserIdsUsingLogin($returnIdOnly = true,$applyRawCondition = true, $ignoreRole = false)
    {
        $userIds =[];
        $roleIds = $this->getChildRolesUsingLogin($returnIdOnly, $applyRawCondition);
        if($roleIds){
            $userIds = \DB::table('role_users')->whereIn('role_id',$roleIds)->get()->pluck('user_id'); 
        }
        return $userIds;
    }
}
