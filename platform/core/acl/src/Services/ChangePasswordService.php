<?php

namespace Impiger\ACL\Services;

use Illuminate\Support\Facades\Auth;
use Impiger\ACL\Repositories\Interfaces\UserInterface;
use Impiger\Support\Services\ProduceServiceInterface;
use Exception;
use Hash;
use Illuminate\Http\Request;

class ChangePasswordService implements ProduceServiceInterface
{
    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * ChangePasswordService constructor.
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return bool|Exception
     */
    public function execute(Request $request)
    {
        if (!$request->user()->isSuperUser()) {
            if ($request->has('old_password') && !Hash::check($request->input('old_password'), $request->user()->getAuthPassword())) {
                return new Exception(trans('core/acl::users.current_password_not_valid'));
            }
        }

        $user = $this->userRepository->findOrFail($request->input('id', $request->user()->getKey()));

        $user->password = Hash::make($request->input('password'));
        $this->userRepository->createOrUpdate($user);

        /* customized by Sabari Shankar.parthiban
         * When Admin can change the user password after sometime changed user session is activated admin session is destroyed
        if ($user->id != $request->user()->id) {
            Auth::setUser($user)->logoutOtherDevices($request->input('password'));
        }
        */
         if ($user->id != $request->user()->id) {
            \App\Utils\CrudHelper::destroyUserSession($user);
        }
        /* customized by Sabari Shankar.parthiban end*/
        do_action(USER_ACTION_AFTER_UPDATE_PASSWORD, USER_MODULE_SCREEN_NAME, $request, $user);

        return $user;
    }
}
