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
use App\Models\Crud;
use Impiger\ACL\Models\UserPermission;

class MappingEntityService implements ProduceServiceInterface {

    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * @var RoleInterface
     */
    protected $roleRepository;

    /**
     * MappingEntityService constructor.
     * @param UserInterface $userRepository
     * @param RoleInterface $roleRepository
     */
    public function __construct(UserInterface $userRepository, RoleInterface $roleRepository) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param Request $request
     * @return bool|Exception
     * @customized sabari shankar parthiban
     */
    public function execute(Request $request) {
        $user = $this->userRepository->findOrFail($request->input('id', $request->user()->getKey()));
        $entityIds = $request->input('entity_ids');
        $referenceIds = $request->input('reference_ids');
        $referenceTypes = $request->input('reference_types');
        $roleIds = $request->input('role_ids');
        if (empty($entityIds)) {
            DB::table('user_permissions')->where('user_id', $user->id)->delete();
        } else {
            $entities = Crud::whereIn("id", $entityIds)->get();
            if (!empty($entities)) {
                foreach ($entities as $entity) {
                    $referenceId = "";
                    if (!empty($referenceIds) && isset($referenceIds[$entity->id])) {
                        $referenceId = $referenceIds[$entity->id];
                        foreach ($referenceId as $reference) {
                            $cond['user_id'] = $user->id;
                            $cond['reference_id'] = $reference;
                            $cond['reference_type'] = $referenceTypes[$entity->id];
                            $existsEntity = UserPermission::where($cond)->count();
                            $data = $cond;
                            $data['reference_key'] = $entity->id;
                            if (isset($roleIds[$entity->id][$reference])) {
                                $data['role_id'] = $roleIds[$entity->id][$reference];
                                $allPermissions = get_permission_role_ids($roleIds[$entity->id][$reference]);
                                $data['role_permissions'] = $allPermissions;
                            }
                            if ($existsEntity) {
                                UserPermission::where($cond)->update($data);
                            } else {
                                UserPermission::create($data);
                            }
                        }
                    }
                    $this->removeEntityMappingIds($user->id, $entity->id, $referenceId);
                }
                $this->removeEntityMappingIds($user->id, $entityIds);
            }
        }

        return $user;
    }

    /**
     * @param $userID,$referenceId and $referencetypeIds
     * 
     * @customized sabari shankar parthiban
     */
    protected function removeEntityMappingIds($userId, $referenceKey, $referenceIds = null) {
        if (Auth::user()->is_admin || Auth::user()->super_user) {
            $userEntities = get_user_entities($userId);
            $removeIds = $removeReferenceKey = [];
            if (!$referenceIds && !is_array($referenceKey)) {
                $removeReferenceKey[] = $referenceKey;
            }
            if (!empty($userEntities)) {
                foreach ($userEntities as $userEntity) {
                    if (is_array($referenceIds) && !in_array($userEntity->reference_id, $referenceIds)) {
                        if ($referenceKey == $userEntity->reference_key) {
                            $removeIds[] = $userEntity->id;
                        }
                    }
                    if (is_array($referenceKey) && !in_array($userEntity->reference_key, $referenceKey)) {
                        $removeReferenceKey[] = $userEntity->reference_key;
                    }
                }
                if (!empty($removeIds)) {
                    DB::table('user_permissions')->where('user_id', $userId)
                            ->whereIn('id', $removeIds)->delete();
                }
                if (!empty($removeReferenceKey)) {
                    DB::table('user_permissions')->where('user_id', $userId)
                            ->whereIn('reference_key', $removeReferenceKey)->delete();
                }
            }
        }
    }

}
