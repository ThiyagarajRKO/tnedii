<?php

namespace Impiger\User\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\User\Http\Requests\RoleUserRequest;
use Impiger\User\Repositories\Interfaces\RoleUserInterface;
use Impiger\User\Tables\RoleUserTable;
use Exception;
use Illuminate\Routing\Controller;

class RoleUserPublicController extends Controller
{
    /**
     * @var RoleUserInterface
     */
    protected $roleUserRepository;

    /**
     * @param RoleUserInterface $roleUserRepository
     */
    public function __construct(RoleUserInterface $roleUserRepository)
    {
        $this->roleUserRepository = $roleUserRepository;
    }

    /**
     * @param RoleUserTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(RoleUserTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param RoleUserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(RoleUserRequest $request, BaseHttpResponse $response)
    {
        try {
            $roleUser = $this->roleUserRepository->getModel();
            $roleUser->fill($request->input());
            $this->roleUserRepository->createOrUpdate($roleUser);
            event(new CreatedContentEvent(ROLE_USER_MODULE_SCREEN_NAME, $request, $roleUser));

            return $response->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/roleUser::failed_msg'));
        }
    }

    /**
     * @param RoleUserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, RoleUserRequest $request, BaseHttpResponse $response)
    {
        try {
            $roleUser = $this->roleUserRepository->findOrFail($id);
            $roleUser->fill($request->input());
            $this->roleUserRepository->createOrUpdate($roleUser);
            event(new UpdatedContentEvent(ROLE_USER_MODULE_SCREEN_NAME, $request, $roleUser));

            return $response->setMessage(trans('core/base::notices.update_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/roleUser::failed_msg'));
        }
    }
}
