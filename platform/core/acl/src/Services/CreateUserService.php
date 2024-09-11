<?php

namespace Impiger\ACL\Services;

use Impiger\ACL\Events\RoleAssignmentEvent;
use Impiger\ACL\Models\User;
use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\ACL\Repositories\Interfaces\UserInterface;
use Impiger\Support\Services\ProduceServiceInterface;
use Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CreateUserService implements ProduceServiceInterface
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
     * @var ActivateUserService
     */
    protected $activateUserService;

    /**
     * CreateUserService constructor.
     * @param UserInterface $userRepository
     * @param RoleInterface $roleRepository
     * @param ActivateUserService $activateUserService
     */
    public function __construct(
        UserInterface $userRepository,
        RoleInterface $roleRepository,
        ActivateUserService $activateUserService
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->activateUserService = $activateUserService;
    }

    /**
     * @param Request $request
     *
     * @return User|false|Model|mixed
     * @customized Sabari Shankar.Parthiban
     */
    public function execute(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->userRepository->createOrUpdate($request->input());

        if ($request->has('username') && $request->has('password')) {
            $this->userRepository->update(['id' => $user->id], [
                'username' => $request->input('username'),
                'password' => Hash::make($request->input('password')),
            ]);

            if ($this->activateUserService->activate($user) && $request->input('role_id')) {
                /*  @customized Sabari Shankar.Parthiban start */
//                $role = $this->roleRepository->findById($request->input('role_id'));
                // Multi role handling
                $roles = $this->roleRepository->getByWhereIn('id',$request->input('role_id'));
                $roleData;
                if (!empty($roles)) {
                    foreach($roles as $role){
                        $roleData = $role;
                        $role->users()->attach($user->id);
                    }
                    /*  @customized Sabari Shankar.Parthiban end */
                    event(new RoleAssignmentEvent($role, $user));
                }
            }
        }

        return $user;
    }
}
