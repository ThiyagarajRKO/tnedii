<?php

namespace Impiger\ACL\Http\Controllers;

use Assets;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Media\Services\ThumbnailService;
use File;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Impiger\ACL\Forms\PasswordForm;
use Impiger\ACL\Forms\ProfileForm;
use Impiger\ACL\Forms\UserForm;
use Impiger\ACL\Tables\UserTable;
use Impiger\ACL\Http\Requests\CreateUserRequest;
use Impiger\ACL\Http\Requests\UpdatePasswordRequest;
use Impiger\ACL\Http\Requests\UpdateProfileRequest;
use Impiger\ACL\Models\UserMeta;
use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\ACL\Repositories\Interfaces\UserInterface;
use Impiger\ACL\Services\ChangePasswordService;
use Impiger\ACL\Services\CreateUserService;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Forms\FormBuilder;
use Impiger\Base\Http\Controllers\BaseController;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Media\Repositories\Interfaces\MediaFileInterface;
use Impiger\ACL\Http\Requests\AvatarRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RvMedia;
use Throwable;
/*
 * @customized Sabari Shankar.Parthiban
*/
use DB;
use Impiger\ACL\Services\MappingRoleService;
use Impiger\ACL\Models\User;
use Impiger\ACL\Services\MappingEntityService;
use App\Models\Crud;
use Impiger\ACL\Models\Role;
use Impiger\Usergroups\Models\Usergroups;
use Illuminate\Support\Arr;

class UserController extends BaseController
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
     * @var MediaFileInterface
     */
    protected $fileRepository;

    /**
     * UserController constructor.
     * @param UserInterface $userRepository
     * @param RoleInterface $roleRepository
     * @param MediaFileInterface $fileRepository
     */
    public function __construct(
        UserInterface $userRepository,
        RoleInterface $roleRepository,
        MediaFileInterface $fileRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param UserTable $dataTable
     * @return Factory|View
     *
     * @throws Throwable
     */
    public function index(UserTable $dataTable)
    {
        page_title()->setTitle(trans('core/acl::users.users'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable']);

        return $dataTable->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     * @customized Sabari Shankar.Parthiban
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('core/acl::users.create_new_user'));
        if (is_plugin_active('password-criteria')) {
            apply_filters(BASE_FILTER_ADD_PASSWORD_CRITERIA);
        }
        return $formBuilder->create(UserForm::class)->renderForm();
    }

    /**
     * @param CreateUserRequest $request
     * @param CreateUserService $service
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CreateUserRequest $request, CreateUserService $service, BaseHttpResponse $response)
    {
        $user = $service->execute($request);

        event(new CreatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

        return $response
            ->setPreviousUrl(route('users.index'))
            ->setNextUrl(route('users.profile.view', $user->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        if ($request->user()->getKey() == $id) {
            return $response
                ->setError()
                ->setMessage(trans('core/acl::users.delete_user_logged_in'));
        }

        try {
            $user = $this->userRepository->findOrFail($id);

            if (!$request->user()->isSuperUser() && $user->isSuperUser()) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/acl::users.cannot_delete_super_user'));
            }

            $this->userRepository->delete($user);
            event(new DeletedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

            return $response->setMessage(trans('core/acl::users.deleted'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('core/acl::users.cannot_delete'));
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            if ($request->user()->getKey() == $id) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/acl::users.delete_user_logged_in'));
            }
            try {
                $user = $this->userRepository->findOrFail($id);
                if (!$request->user()->isSuperUser() && $user->isSuperUser()) {
                    continue;
                }
                $this->userRepository->delete($user);
                event(new DeletedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
            } catch (Exception $exception) {
                return $response
                    ->setError()
                    ->setMessage($exception->getMessage());
            }
        }

        return $response->setMessage(trans('core/acl::users.deleted'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return Factory|View| RedirectResponse
     * @customized Sabari Shankar.Parthiban
     */
    public function getUserProfile($id, Request $request, FormBuilder $formBuilder)
    {
         Assets::addScripts(['bootstrap-pwstrength', 'cropper','jquery-ui', 'jqueryTree'])
            ->addStyles(['jquery-ui', 'jqueryTree'])
            ->addScriptsDirectly('vendor/core/core/acl/js/profile.js')
            ->addScriptsDirectly('vendor/core/core/acl/js/user.js')
            ->addScriptsDirectly(['vendor/core/core/base/libraries/customListBox.js'])
            ->addStylesDirectly('vendor/core/core/acl/css/custom-style.css');


        if (is_plugin_active('password-criteria')) {
            apply_filters(BASE_FILTER_ADD_PASSWORD_CRITERIA);
        }
        /**
     * @customized Haritha Murugavel
     */
        if (is_plugin_active('student')) {
            Assets::addScriptsDirectly('vendor/core/plugins/student/js/optout.js');
        }

        $user = $this->userRepository->findOrFail($id);
        if(is_plugin_active('user')){
            $user->imp_user_id = \Impiger\User\Models\User::select('id')->where('user_id',$user->id)->pluck('id')->first();
        }
        if(is_plugin_active('student')){
            $user->imp_student_id = \Impiger\Student\Models\Student::select('id')->where('user_id',$user->id)->pluck('id')->first();
        }
        if(is_plugin_active('alumni')){
            $user->alumni_id = \Impiger\Alumni\Models\Alumni::select('id')->where('user_id',$user->id)->pluck('id')->first();
        }

        page_title()->setTitle(trans(':name', ['name' => $user->name]));

        $form = $formBuilder
            ->create(ProfileForm::class, ['model' => $user])
            ->setUrl(route('users.update-profile', $user->id));
        $passwordForm = $formBuilder
            ->create(PasswordForm::class)
            ->setUrl(route('users.change-password', $user->id));

        $canChangeProfile = $request->user()->getKey() == $id || $request->user()->isSuperUser();
        $canChangePassword = false;
        if( $request->has('action') && $request->input('action') == 'reset-password') {
              $canChangePassword = true;
              $passwordForm->add('referrer', 'hidden', [
                'label'      => 'Previous Url',
                'default_value' => $request->server('HTTP_REFERER')
            ]);
        }
        if (!$canChangeProfile && !$canChangePassword) {
            $form->disableFields();
            $form->removeActionButtons();
            $form->setActionButtons(' ');
            $passwordForm->disableFields();
            $passwordForm->removeActionButtons();
            $passwordForm->setActionButtons(' ');
        }

        /*  @customized Ramesh.Esakki begin */
        $hideUserProfile = false;
        if( $request->has('user_navigation') || $request->has('action')) {
            $hideUserProfile = true;
        }
        /*  @customized Ramesh.Esakki end */

        if ($request->user()->isSuperUser() || $canChangePassword) {
            $passwordForm->remove('old_password');
        }
        $form = $form->renderForm();
        $passwordForm = $passwordForm->renderForm();
        /*  @customized Sabari Shankar.Parthiban start */
        $roles = app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin(false,true,true);
        $mappedRoles = $this->getMappedRolesByUser($user->id);
        $userRoles = Role::whereIn("id",$mappedRoles)->get();
        $userGroupEntities = [];
        if (is_plugin_active('usergroups')) {
            $userGroupEntities = app(\Impiger\Usergroups\Usergroups::class)->getGroupedEntities($mappedRoles);
        }
            $entities = $this->getEntityData($userGroupEntities,$mappedRoles);
            $mappedEntities = get_user_mapped_entity_ids($user->id);
            $userEntities = $this->getUserEntityData($user->id);


        $flags = app(\Impiger\ACL\Roles::class)->getAvailablePermissions();
        $children = app(\Impiger\ACL\Roles::class)->getPermissionTree($flags);
        $active = array_keys($user->permissions);
        if(setting('user_level_permission')){
            Assets::addStyles(['jquery-ui', 'jqueryTree'])
                ->addScripts(['jquery-ui', 'jqueryTree'])
                ->addScriptsDirectly(['vendor/core/plugins/crud/js/crud_utils.js'])
                ->addScriptsDirectly('vendor/core/core/acl/js/role.js')
                ->addStylesDirectly(['vendor/core/plugins/crud/css/module_custom_styles.css']);
        }
        return view('core/acl::users.profile.base', compact('user', 'form', 'passwordForm', 'canChangeProfile','roles','id','mappedRoles','entities','mappedEntities','userEntities','userRoles','userGroupEntities', 'hideUserProfile','active', 'flags', 'children','canChangePassword'));
        /*  @customized Sabari Shankar.Parthiban end */
    }

    /**
     * @param int $id
     * @param UpdateProfileRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateProfile($id, UpdateProfileRequest $request, BaseHttpResponse $response)
    {
        $user = $this->userRepository->findOrFail($id);

        $currentUser = $request->user();
        if (($currentUser->hasPermission('users.update-profile') && $currentUser->getKey() === $user->id) ||
            $currentUser->isSuperUser()
        ) {
            if ($user->email !== $request->input('email')) {
                $users = $this->userRepository->getModel()
                    ->where('email', $request->input('email'))
                    ->where('id', '<>', $user->id)
                    ->count();
                if ($users) {
                    return $response
                        ->setError()
                        ->setMessage(trans('core/acl::users.email_exist'))
                        ->withInput();
                }
            }

            if ($user->username !== $request->input('username')) {
                $users = $this->userRepository->getModel()
                    ->where('username', $request->input('username'))
                    ->where('id', '<>', $user->id)
                    ->count();
                if ($users) {
                    return $response
                        ->setError()
                        ->setMessage(trans('core/acl::users.username_exist'))
                        ->withInput();
                }
            }
        }

        $user->fill($request->input());
        $this->userRepository->createOrUpdate($user);
        do_action(USER_ACTION_AFTER_UPDATE_PROFILE, USER_MODULE_SCREEN_NAME, $request, $user);

        event(new UpdatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
        return $response->setPreviousUrl(route('user.index'))->setMessage(trans('core/acl::users.update_profile_success'));
    }

    /**
     * @param int $id
     * @param UpdatePasswordRequest $request
     * @param ChangePasswordService $service
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postChangePassword(
        $id,
        UpdatePasswordRequest $request,
        ChangePasswordService $service,
        BaseHttpResponse $response
    ) {
        $request->merge(['id' => $id]);
        /*  @customized Sabari Shankar.Parthiban start */
            if (is_plugin_active('password-criteria')) {
                do_Action(MAINTAIN_PASSWORD_HISTORY,$request);
            }
        /*  @customized Sabari Shankar.Parthiban end */
        $result = $service->execute($request);

        if ($result instanceof Exception) {
            return $response
                ->setError()
                ->setMessage($result->getMessage());
        }
        /*  @customized Sabari Shankar.Parthiban start */
        if($request->has('force_change') && $request->input('force_change')){
            app(UserInterface::class)->update(['id' =>$id], ['last_login' => now()]);

            if(is_plugin_active('vendor-request')) {
                app(\Impiger\VendorRequest\Repositories\Interfaces\VendorRequestInterface::class)->update(['user_id' =>$id], ['is_registered' => 1]);
            }
            return redirect()->route('dashboard.index');
        }
        if($request->has('referrer')){
            if(is_plugin_active('vendor-request')) {
                app(\Impiger\VendorRequest\Repositories\Interfaces\VendorRequestInterface::class)->update(['user_id' =>$id], ['temp_password' => $request->input('password')]);
            }
            return $response->setNextUrl($request->input('referrer'))->setMessage(trans('core/acl::users.password_update_success'));
        }
        /*  @customized Sabari Shankar.Parthiban start */
        return $response->setMessage(trans('core/acl::users.password_update_success'));
    }

    /**
     * @param int $id
     * @param AvatarRequest $request
     * @param ThumbnailService $thumbnailService
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postAvatar($id, AvatarRequest $request, ThumbnailService $thumbnailService, BaseHttpResponse $response)
    {
        try {
            $user = $this->userRepository->findOrFail($id);

            $result = RvMedia::handleUpload($request->file('avatar_file'), 0, 'users');

            if ($result['error'] != false) {
                return $response->setError()->setMessage($result['message']);
            }

            $avatarData = json_decode($request->input('avatar_data'));

            $file = $result['data'];

            $thumbnailService
                ->setImage(RvMedia::getRealPath($file->url))
                ->setSize((int)$avatarData->width, (int)$avatarData->height)
                ->setCoordinates((int)$avatarData->x, (int)$avatarData->y)
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '.' . File::extension($file->url))
                ->save('crop');

            $this->fileRepository->forceDelete(['id' => $user->avatar_id]);

            $user->avatar_id = $file->id;

            $this->userRepository->createOrUpdate($user);

            return $response
                ->setMessage(trans('core/acl::users.update_avatar_success'))
                ->setData(['url' => RvMedia::url($file->url)]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param string $theme
     * @return RedirectResponse
     */
    public function getTheme($theme)
    {
        if (Auth::check() && !app()->environment('demo')) {
            UserMeta::setMeta('admin-theme', $theme);
        }

        session()->put('admin-theme', $theme);

        try {
            return redirect()->back();
        } catch (Exception $exception) {
            return redirect()->route('access.login');
        }
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function makeSuper($id, BaseHttpResponse $response)
    {
        try {
            $user = $this->userRepository->findOrFail($id);

            $user->updatePermission(ACL_ROLE_SUPER_USER, true);
            $user->updatePermission(ACL_ROLE_MANAGE_SUPERS, true);
            $user->super_user = 1;
            $user->manage_supers = 1;
            $this->userRepository->createOrUpdate($user);

            return $response
                ->setNextUrl(route('users.index'))
                ->setMessage(trans('core/base::system.supper_granted'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setNextUrl(route('users.index'))
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function removeSuper($id, Request $request, BaseHttpResponse $response)
    {
        if ($request->user()->getKey() == $id) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::system.cannot_revoke_yourself'));
        }

        $user = $this->userRepository->findOrFail($id);

        $user->updatePermission(ACL_ROLE_SUPER_USER, false);
        $user->updatePermission(ACL_ROLE_MANAGE_SUPERS, false);
        $user->super_user = 0;
        $user->manage_supers = 0;
        $this->userRepository->createOrUpdate($user);

        return $response
            ->setMessage(trans('core/base::system.supper_revoked'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param MappingRoleService $service
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @customized Sabari Shankar.Parthiban
     */
    public function postRoleMapping(
        $id,
        Request $request,
        MappingRoleService $service,
        BaseHttpResponse $response
    ) {
        $request->merge(['id' => $id]);
        $result = $service->execute($request);

        if ($result instanceof Exception) {
            return $response
                ->setError()
                ->setMessage($result->getMessage());
        }

        if(!setting('user_level_permission') && !setting('enable_dls')){
            return $response->setNextUrl(route('user.index'))->setMessage(trans('core/base::notices.update_success_message'));
        }
        $response = $response
            ->setMessage(trans('core/base::notices.update_success_message'));
        if ($request->has('user_navigation')) {
            $qryStr = "";
            $qryStr = "?user_navigation=" . $request->has('user_navigation') . "&dls=1";
            $qryStr = ($request->has('navback')) ? $qryStr.= '&navback='.$request->get('navback') : $qryStr;
            $response = $response->setNextUrl(route('users.profile.view', $id . $qryStr));
        }
         return $response;
    }
    /*
     * @customized Sabari Shankar.Parthiban
     */
    public function getMappedRolesByUser($userId){
        $roleIds=[];
        $userRoles = DB::table("role_users")->where("user_id",$userId)->get();
        $childRoles =  app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin();
        if (!empty($userRoles)) {
            foreach ($userRoles as $role) {
                if(in_array($role->role_id,$childRoles)){
                    $roleIds[] = $role->role_id;
                }
            }
        }

        return $roleIds;
    }
    /*
     * @customized Sabari Shankar.Parthiban
     */
    public function getEntityData($groupedEntities, $mappedRoles)
    {
        $entityData=[];
        $entities = \App\Utils\CrudHelper::getCrudEntities($groupedEntities,$mappedRoles);

        if(!empty($entities)){
            $entityData['root'] = $entities;
            foreach ($entities as $entity) {
            $tableName = $entity->module_db;
            $conds =get_common_condition($tableName);
            $query =DB::table($tableName)->whereRaw($conds);
            $model = get_model_from_table($tableName);
            if ($model && Auth::user()->applyDataLevelSecurity()) {
                $model = new $model();
                $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, [$tableName.".*"], false);
            }
            $entityData[$tableName]['data'] = $query->get();
            if (is_plugin_active('usergroups') && is_plugin_active($entity->module_name)) {
                $entityRoles = app(\Impiger\Usergroups\Usergroups::class)->getUserGroupRoles($entity->id);
                $entityData[$tableName]['roles'] = (!empty($entityRoles)) ? $entityRoles : $mappedRoles;
            } else {
                $entityData[$tableName]['roles'] = $mappedRoles;
            }
        }
        }
        return $entityData;
    }
    /**
     * @param int $id
     * @param Request $request
     * @param MappingEntityService $service
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @customized Sabari Shankar.Parthiban
     */
    public function postEntityMapping(
        $id,
        Request $request,
        MappingEntityService $service,
        BaseHttpResponse $response
    ) {
        $request->merge(['id' => $id]);
        $result = $service->execute($request);

        if ($result instanceof Exception) {
            return $response
                ->setError()
                ->setMessage($result->getMessage());
        }

        if($request->has('navback')) {
            return $response->setNextUrl(route(Arr::get(config('general.back_to_navigation'),$request->get('navback'))))->setMessage(trans('core/base::notices.update_success_message'));
        } else {
            return $response->setNextUrl(route('user.index'))->setMessage(trans('core/base::notices.update_success_message'));
        }
        
        // return $response->setPreviousUrl(url()->previous())->setNextUrl(redirect()->back()->getTargetUrl())->setMessage(trans('core/base::notices.update_success_message'));
    }
    /*
     * @customized Sabari Shankar.Parthiban
     */
    public function getUserEntityData($userID)
    {
        $entityData = [];
        $entities = DB::table("user_permissions")->where("user_id", $userID)->get();
        if (!empty($entities)) {
            foreach ($entities as $entity) {
                $entity_id = $entity->reference_key;
                $entityData['entity_id'][] = $entity_id;
                $entityData[$entity_id][] = $entity->reference_id;
                $entityData[$entity_id]["role_id"][$entity->reference_id] = ($entity->role_id) ? json_decode($entity->role_id, true) : [];
            }
        }
        return $entityData;
    }
    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @customized Sabari Shankar.Parthiban
     */
    public function postPermissionMapping(
        $id,
        Request $request,
        BaseHttpResponse $response
    ) {
        $request->merge(['id' => $id]);
        $user = $this->userRepository->findOrFail($id);
        $user->permissions = app(\Impiger\ACL\Roles::class)->cleanPermission($request->input('flags'));
        $user->save();
        if(!setting('enable_dls')){
            return $response->setNextUrl(route('user.index'))->setMessage(trans('core/base::notices.update_success_message'));
        }
        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }
}
