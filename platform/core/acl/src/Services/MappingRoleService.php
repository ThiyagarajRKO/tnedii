<?php

namespace Impiger\ACL\Services;

use Auth;
use Impiger\ACL\Repositories\Interfaces\UserInterface;
use Impiger\Support\Services\ProduceServiceInterface;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\ACL\Events\RoleUpdateEvent;
use DB;
use Impiger\ACL\Models\Role;

class MappingRoleService implements ProduceServiceInterface
{
    /**
     * @var UserInterface
     */
    protected $userRepository;
    /**
     * @var RoleInterface
     */
    protected $roleRepository;
    /**
     * MappingRoleService constructor.
     * @param UserInterface $userRepository
     * @param RoleInterface $roleRepository
     */
    public function __construct(UserInterface $userRepository,
        RoleInterface $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param Request $request
     * @return bool|Exception
     * @customized Sabari Shankar.Parthiban
     */
    public function execute(Request $request)
    {
        if(!$request->input('role_id')) {
            return false;
        }
        
            $user = $this->userRepository->findOrFail($request->input('id', $request->user()->getKey()));
            $roles = $this->roleRepository->getByWhereIn('id',$request->input('role_id'));
            $this->removeRoleMappingIds($user->id,$request->input('role_id'));

            $roleData;
                if (!empty($roles)) {
                    foreach($roles as $role){
                        $roleData = $role;
                        $data['user_id'] = $user->id;
                        $data['role_id'] = $role->id;
                        $existsRoles = DB::table("role_users")->where(["role_id"=>$role->id,"user_id"=>$user->id])->first();
                        if($existsRoles){
                            $role->users()->updateOrCreate(['user_id'=>$user->id],$data);
                        }else{
                            $role->users()->attach($user->id);
                        }
                    }
//                    event(new RoleUpdateEvent($role));
                    $this->updateUserRolePermissions($role,$user);
                }
            
        return $user;
    }
    /**
     * @param $userID and $roleIds
     * 
     * @customized Sabari Shankar.Parthiban
     */
    protected function removeRoleMappingIds($userId,$roleIds){
        $userRoles = get_user_roles($userId);
        $removeIds = [];
        if(!empty($userRoles)){
            foreach($userRoles as $userRole){
                if(!in_array($userRole->role_id, $roleIds)){
                    $removeIds[] = $userRole->role_id;
                }
            }
            if(!empty($removeIds)){
                DB::table('role_users')->where("user_id",$userId)->whereIn('role_id',$removeIds)->delete();
            }
        }
    }
    
    /**
     * @param $role and $user
     * 
     * @customized Sabari Shankar.Parthiban
     */
    protected function updateUserRolePermissions($role,$user){
         $permissions = $role->permissions;
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

        cache()->forget(md5('cache-dashboard-menu-' . Auth::id()));
    }
}
