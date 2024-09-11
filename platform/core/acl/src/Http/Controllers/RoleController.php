<?php

namespace Impiger\ACL\Http\Controllers;

use Impiger\ACL\Forms\RoleForm;
use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Base\Forms\FormBuilder;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\ACL\Events\RoleAssignmentEvent;
use Impiger\ACL\Events\RoleUpdateEvent;
use Impiger\ACL\Tables\RoleTable;
use Impiger\ACL\Http\Requests\RoleCreateRequest;
use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\ACL\Repositories\Interfaces\UserInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Impiger\Base\Supports\Helper;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;
/* @customized by Sabari Shankar.Parthiban */
use Illuminate\Support\Facades\Auth;
use App\Utils\CrudHelper;

class RoleController extends BaseController
{
    /**
     * @var RoleInterface
     */
    protected $roleRepository;

    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * RoleController constructor.
     * @param RoleInterface $roleRepository
     * @param UserInterface $userRepository
     */
    public function __construct(RoleInterface $roleRepository, UserInterface $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param RoleTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(RoleTable $dataTable)
    {
        /* @Customized By Sabari Shankar Parthiban  start */
        \Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/crud_utils.js'
        ])->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
        ]);
        /* @Customized By Sabari Shankar Parthiban  end */
        page_title()->setTitle(trans('core/acl::permissions.role_permission'));

        return $dataTable->renderTable();
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, BaseHttpResponse $response)
    {
        $role = $this->roleRepository->findOrFail($id);
        /* @Customized By Sabari Shankar Parthiban Start */
        $dataExist = CrudHelper::isDependentDataExistCore('role',DEPENDANT_MODULE_IN_ROLE, array($id));
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }
        /* @Customized By Sabari Shankar Parthiban End */    
        $role->delete();

        Helper::clearCache();

        return $response->setMessage(trans('core/acl::permissions.delete_success'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }
          /* @Customized By Sabari Shankar Parthiban Start */
            $excludeRoles=[];
            $dataExist = CrudHelper::isDependentDataExistCore('role',DEPENDANT_MODULE_IN_ROLE, $ids);
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }
        /* @Customized By Sabari Shankar Parthiban End */
        foreach ($ids as $id) {
            $role = $this->roleRepository->findOrFail($id);
            /* @Customized By Sabari Shankar Parthiban Start */
                if($role->is_system){
                    $excludeRoles[]=$role->name;
                }else{
                    $role->delete();
                }
            /* @Customized By Sabari Shankar Parthiban End */
        }
        /* @Customized By Sabari Shankar Parthiban Start */
                if(!empty($excludeRoles)){
                    return $response
                    ->setError()
                    ->setMessage(implode(",",$excludeRoles)." these are system roles so,can't able to delete");
                }
            /* @Customized By Sabari Shankar Parthiban End */
        Helper::clearCache();

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $role = $this->roleRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $role));

        page_title()->setTitle(trans('core/acl::permissions.details') . ' - ' . e($role->name));

        return $formBuilder->create(RoleForm::class, ['model' => $role])->renderForm();
    }

    /**
     * @param int $id
     * @param RoleCreateRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function update($id, RoleCreateRequest $request, BaseHttpResponse $response)
    {
        /* @Customized by Sabari Shankar parthiban add has condition*/
        if ($request->has('is_default') && $request->input('is_default')) {
            $this->roleRepository->getModel()->where('id', '!=', $id)->update(['is_default' => 0]);
        }

        $role = $this->roleRepository->findOrFail($id);

        $role->name = $request->input('name');
        $role->permissions = $this->cleanPermission($request->input('flags'));
        $role->description = $request->input('description');
        $role->updated_by = $request->user()->getKey();
        $role->child_roles = $request->input('child_roles');
        /* @customized by Sabari Shankar.Parthiban start */
        $role->is_default = ($request->has('is_default')) ? $request->input('is_default') : $role->is_default;
        $user = Auth::user();
        $isAdmin = ($user->isSuperUser()) ? $request->input('is_admin') : $role->is_admin ;
        $role->is_admin = $isAdmin;
        $isSystem = ($user->is_admin || $user->isSuperUser()) ? $request->input('is_system') : $role->is_system ;
        $role->is_system = $isSystem;
        $role->entity_type  = ($request->has('entity_type')) ? $request->input('entity_type') : NULL;
        $role->entity_id  = ($request->has('entity_id')) ? $request->input('entity_id') : NULL;
        /* @customized by Sabari Shankar.Parthiban end */
        $this->roleRepository->createOrUpdate($role);

        Helper::clearCache();

        event(new RoleUpdateEvent($role));

        return $response
            ->setPreviousUrl(route('roles.index'))
            ->setNextUrl(route('roles.edit', $id))
            ->setMessage(trans('core/acl::permissions.modified_success'));
    }

    /**
     * Return a correctly type casted permissions array
     * @param array $permissions
     * @return array
     */
    protected function cleanPermission($permissions)
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

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('core/acl::permissions.create_role'));

        return $formBuilder->create(RoleForm::class)->renderForm();
    }

    /**
     * @param RoleCreateRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(RoleCreateRequest $request, BaseHttpResponse $response)
    {
        /* @Customized by Sabari Shankar parthiban add has condition*/
        if ($request->has('is_default') && $request->input('is_default')) {
            $this->roleRepository->getModel()->where('id', '>', 0)->update(['is_default' => 0]);
        }
        if(!$request->input('flags')){
            $request['flags'] = DEFAULT_PERMISSIONS;
        }
        $role = $this->roleRepository->createOrUpdate([
            'name'        => $request->input('name'),
            'slug'        => $this->roleRepository->createSlug($request->input('name'), 0),
            'permissions' => $this->cleanPermission($request->input('flags')),
            'description' => $request->input('description'),
            'is_default'  => $request->has('is_default') ? $request->input('is_default'):0,
            'created_by'  => $request->user()->getKey(),
            'updated_by'  => $request->user()->getKey(),
            /* @customized by Sabari Shankar.Parthiban start*/
            'is_admin'  => ($request->has('is_admin')) ? $request->input('is_admin') : 0,
            'is_system'  => ($request->has('is_system')) ? $request->input('is_system') : 0,
            'entity_type'  => ($request->has('entity_type')) ? $request->input('entity_type') : NULL,
            'entity_id'  => ($request->has('entity_id')) ? $request->input('entity_id') : NULL,
            /* @customized by Sabari Shankar.Parthiban end*/
        ]);
        /* @customized by Sabari Shankar.Parthiban start*/
        $user = Auth::user();
        if($user && !$user->is_admin && !$user->isSuperUser()){
                  foreach($user->role_ids as $roleId){
                    $currentRole = $this->roleRepository->findOrFail($roleId);
                    $childRoles = $currentRole->child_roles;
                    $childRoles[] = $role->id;
                    $currentRole->child_roles = $childRoles;
                    $currentRole->save();
                  }
        }
        /* @customized by Sabari Shankar.Parthiban end*/
        return $response
            ->setPreviousUrl(route('roles.index'))
            ->setNextUrl(route('roles.edit', $role->id))
            ->setMessage(trans('core/acl::permissions.create_success'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getDuplicate($id, BaseHttpResponse $response)
    {
        $baseRole = $this->roleRepository->findOrFail($id);

        $role = $this->roleRepository->createOrUpdate([
            'name'        => $baseRole->name . ' (Duplicate)',
            'slug'        => $this->roleRepository->createSlug($baseRole->slug, 0),
            'permissions' => $baseRole->permissions,
            'description' => $baseRole->description,
            'created_by'  => $baseRole->created_by,
            'updated_by'  => $baseRole->updated_by,
        ]);

        return $response
            ->setPreviousUrl(route('roles.edit', $baseRole->id))
            ->setNextUrl(route('roles.edit', $role->id))
            ->setMessage(trans('core/acl::permissions.duplicated_success'));
    }

    /**
     * @return array
     */
    public function getJson()
    {
        $pl = [];
        foreach ($this->roleRepository->all() as $role) {
            $pl[] = [
                'value' => $role->id,
                'text'  => $role->name,
            ];
        }

        return $pl;
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     */
    public function postAssignMember(Request $request, BaseHttpResponse $response)
    {
        $user = $this->userRepository->findOrFail($request->input('pk'));
        $role = $this->roleRepository->findOrFail($request->input('value'));

        $user->roles()->sync([$role->id]);

        event(new RoleAssignmentEvent($role, $user));

        return $response;
    }
    /**
     * @return array
     * @customized Sabari Shankar.Parthiban
     */
    public function getRoleListJson()
    {
        $pl = [];
        $roles = $this->roleRepository->all();
        $rawCondition = get_common_condition('roles');
        if(!empty($rawCondition)){
            $roles = \Impiger\ACL\Models\Role::whereRaw($rawCondition)->get();
        }
        foreach ($roles as $role) {
            $pl["results"][] = [
                'id' => $role->id,
                'text'  => $role->name,
            ];
        }

        return $pl;
    }
    /** @customized by Sabari Shankar Parthiban
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $roles = $this->roleRepository->findOrFail($id);
        $name = ($roles->name) ? ' "' . $roles->name . '"' : "";
        page_title()->setTitle(trans('plugins/crud::crud.view_role') . $name);

        return $formBuilder->create(RoleForm::class, ['model' => $roles, 'isView' => true])->renderForm();
    }
}
