<?php

namespace Impiger\User\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\User\Http\Requests\UserRequest;
use Impiger\User\Repositories\Interfaces\UserInterface;
use Impiger\User\Tables\UserTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class UserPublicController extends Controller
{
    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(UserTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param UserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(UserRequest $request, BaseHttpResponse $response, \Impiger\ACL\Services\CreateUserService $service, \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        try {

            $request['username'] = $request->input('email');
            $request['password'] = CrudHelper::randomPassword();
            // $coreUser = $service->execute($request);
            $coreUser = CrudHelper::createCoreUserAndAssignRoleAndPermission($request, $coreUserRepository, $activateUserService, INNOVATOR_ROLE_SLUG, true);
            $request['user_id'] = $coreUser->id;

            $user = $this->userRepository->getModel();
            $table = $user->getTable();

            $user->fill($request->input());
            $this->userRepository->createOrUpdate($user);
            // CrudHelper::createUpdateSubforms($request, $user, 'user_addresses',false,'');

            CrudHelper::uploadFiles($request, $user);
            event(new CreatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
            CrudHelper::sendEmailConfig('user', '{"create":"1","edit":null,"subject":"' . APP_NAME . ' - Login credentials","message":"Dear {first_name},\u003Cbr\u003E\u003Cbr\u003ENew account has been created in ' . APP_NAME . '.\u003Cbr\u003E\u003Cbr\u003EUsername:{email}\u003Cbr\u003EPassword:' . $request->input('password') . '\u003Cbr\u003EKindly use this \u003Ca href=' . getUserDomainUrl($user->user_id) . '\u003EURL\u003C\/a\u003E to login\u003Cbr\u003E\u003Cbr\u003EIf you are wrong person, Please ignore this email.\u003Cbr\u003E\u003Cbr\u003EThank you","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email","default_reciever":null}', $user);
            return $response
                ->setPreviousUrl(url('/form-response?form=user'))
                ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            // info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/user::failed_msg'));
        }
    }

    public function registerIVP(UserRequest $request, \Impiger\ACL\Services\CreateUserService $service, \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        try {
            $request['username'] = $request->input('email');
            $request['password'] = CrudHelper::randomPassword();
            $coreUser = CrudHelper::createCoreUserAndAssignRoleAndPermission($request, $coreUserRepository, $activateUserService, INNOVATOR_ROLE_SLUG, true);
            $request['user_id'] = $coreUser->id;

            $user = $this->userRepository->getModel();
            $table = $user->getTable();

            $user->fill($request->input());
            $this->userRepository->createOrUpdate($user);
            CrudHelper::uploadFiles($request, $user);
            event(new CreatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
            CrudHelper::sendEmailConfig('user', '{"create":"1","edit":null,"subject":"' . APP_NAME . ' - Login credentials","message":"Dear {first_name},\u003Cbr\u003E\u003Cbr\u003ENew account has been created in ' . APP_NAME . '.\u003Cbr\u003E\u003Cbr\u003EUsername:{email}\u003Cbr\u003EPassword:' . $request->input('password') . '\u003Cbr\u003EKindly use this \u003Ca href=' . getUserDomainUrl($user->user_id) . '\u003EURL\u003C\/a\u003E to login\u003Cbr\u003E\u003Cbr\u003EIf you are wrong person, Please ignore this email.\u003Cbr\u003E\u003Cbr\u003EThank you","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email","default_reciever":null}', $user);

            return response()->json([
                'success' => true,
                'message' => trans('core/base::notices.create_success_message'),
                'data' => [
                    'user_id' => $user->id,
                    'username' => $request->input('email'),
                ],
            ], 201); // 201 Created
        } catch (Exception $exception) {
            info($exception->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('plugins/user::failed_msg'),
            ], 500); // 500 Internal Server Error
        }
    }


    /**
     * @param UserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, UserRequest $request, BaseHttpResponse $response)
    {
        try {
            $user = $this->userRepository->findOrFail($id);
            CrudHelper::createUpdateSubforms($request, $user, 'user_addresses', $id, '');

            $user->fill($request->input());
            $this->userRepository->createOrUpdate($user);
            event(new UpdatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
            CrudHelper::sendEmailConfig('user', '{"create":"1","edit":null,"subject":"' . APP_NAME . ' - Login credentials","message":"Dear {first_name},\u003Cbr\u003E\u003Cbr\u003ENew account has been created in Emircom.\u003Cbr\u003E\u003Cbr\u003EUsername:{email}\u003Cbr\u003EPassword:' . $request->input('password') . '\u003Cbr\u003EKindly use this \u003Ca href=' . getUserDomainUrl($user->user_id) . '\u003EURL\u003C\/a\u003E to login\u003Cbr\u003E\u003Cbr\u003EIf you are wrong person, Please ignore this email.\u003Cbr\u003E\u003Cbr\u003EThank you","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email","default_reciever":null}', $user);
            return $response
                ->setPreviousUrl(url('/form-response?form=user'))
                ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/user::failed_msg'));
        }
    }
}
