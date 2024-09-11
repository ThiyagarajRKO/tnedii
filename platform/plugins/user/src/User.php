<?php

namespace Impiger\User;

use Impiger\User\Repositories\Interfaces\UserInterface;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;
use Request;

class User
{
    /**
     * @var UsergroupsInterface
     */
    protected $userRepository;

    /**
     * Usergroups constructor.
     * @param UsergroupsInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getInstituteUsersByRoles($roles = [], $instituteId = null)
    {
        $user = \Auth::user();

        $model = $this->userRepository->getModel();
        $query = $model::select(['impiger_users.id', 'first_name AS text'])
            ->join('role_users', 'role_users.id', '=', 'impiger_users.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
            ->whereIn('roles.slug', $roles);

        if ($instituteId || ($user && $user->applyDataLevelSecurity())) {
            $instituteIds = ($instituteId) ? [$instituteId] : CrudHelper::getInstituteIdsByLogin();
            $query = $query->join('user_permissions', function ($join) {
                $join->on('user_permissions.user_id', '=', 'impiger_users.user_id');
                $join->where('user_permissions.reference_type', '=', 'Impiger\Institution\Models\Institution');
            });

            $query = $query->whereIn('user_permissions.reference_id', $instituteIds);
        }

        return $query->get();
    }
}
