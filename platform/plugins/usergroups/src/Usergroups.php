<?php

namespace Impiger\Usergroups;

use Impiger\Usergroups\Repositories\Interfaces\UsergroupsInterface;
use Impiger\Usergroups\Repositories\Interfaces\UsergroupEntityInterface;
use Illuminate\Support\Arr;
use Impiger\Usergroups\Models\Usergroups as UsergroupModel;
use Theme;
use Request;

class Usergroups
{
    /**
     * @var UsergroupsInterface
     */
    protected $usergroupsRepository;

    /**
     * @var UsergroupEntityInterface
     */
    protected $usergroupEntityRepository;

    /**
     * Usergroups constructor.
     * @param UsergroupsInterface $usergroupsRepository
     * @param UsergroupEntityInterface $usergroupEntityRepository
     */
    public function __construct(UsergroupEntityInterface $usergroupEntityRepository, UsergroupsInterface $usergroupsRepository)
    {
        $this->usergroupEntityRepository = $usergroupEntityRepository;
        $this->usergroupsRepository = $usergroupsRepository;
    }



    public function getUserGroups($roleIds = []) {
        $data = [];
        $userGroups = $this->usergroupsRepository->getModel();
        $userGroups = $userGroups::all();
        foreach ($userGroups as $userGroup) {
            if (!empty($roleIds)) {
                if (is_array($roleIds)) {
                    foreach ($roleIds as $roleId) {
                        if (in_array($roleId, $userGroup->roles)) {
                            if (!in_array($userGroup->id, $data))
                                $data[] = $userGroup->id;
                        }
                    }
                }else {
                    if (in_array($roleIds, $userGroup->roles)) {
                        $data[] = $userGroup->id;
                    }
                }
            } else {
                $data[] = $userGroup->id;
            }
        }
        return $data;
    }
	
    public function getGroupedEntities($roleIds)
    {
        $entity = [];
        $userGroupIds = $this->getUserGroups($roleIds);
        $userGroupEntities = $this->usergroupEntityRepository->getByWhereIn('usergroup_id', $userGroupIds);
        if (!empty($userGroupEntities)) {
            foreach ($userGroupEntities as $userGroupEntity) {
                $entity['crud_id'][] = $userGroupEntity->crud_id;
                $entity['usergroup_id'][] = $userGroupEntity->usergroup_id;
            }
        }
        return $entity;
    }
    
    public function getUserGroupRoles($entityId){
        $roleIds = $userGroup =[];
        $userGroupEntity = $this->usergroupEntityRepository->getFirstBy(['crud_id'=>$entityId]);
        if(!empty($userGroupEntity)){
            $userGroup = $this->usergroupsRepository->findById($userGroupEntity->usergroup_id);
        }
        if(!empty($userGroup)){
            $roleIds = $userGroup->roles;
        }
        return $roleIds;
    }

    public function getUserGroupRolesUsingLogin()
    {
        $user = \Auth::user();

        if (!$user->applyDataLevelSecurity()) {
            return \Impiger\ACL\Models\Role::where('id' ,'>' ,0)->pluck('id')->toArray();
        } else {
            $roleId = $user->roles->pluck('id')->toArray();
            $roleId = implode(",", $roleId);
            $result = UsergroupModel::whereJsonContains('roles', $roleId)->get()->first();

            if ($result && isset($result->roles)) {
                return $result->roles;
            }
        }

        return false;
    }
}
