<?php

namespace Impiger\ACL\Providers;

use Impiger\ACL\Repositories\Interfaces\UserInterface;
use Impiger\Dashboard\Supports\DashboardWidgetInstance;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Throwable;
/*  @customized Sabari Shankar.Parthiban */
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Schema;
use Arr;

class HookServiceProvider extends ServiceProvider {

    public function boot() {
//        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addUserStatsWidget'], 12, 2);

        /*  @customized Sabari Shankar.Parthiban start */
        if(setting('enable_dls')){
            add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'getDataBasedOnUserPermissions'], 156, 4);
        }
        /*  @customized Sabari Shankar.Parthiban end */
       add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'applyRoleHierarchyCondition'], 158, 3);
    }

    /**
     * @param array $widgets
     * @param Collection $widgetSettings
     * @return array
     * @throws Throwable
     */
    public function addUserStatsWidget($widgets, $widgetSettings) {
        $users = $this->app->make(UserInterface::class)->count();

        return (new DashboardWidgetInstance)
                        ->setType('stats')
                        ->setPermission('users.index')
                        ->setTitle(trans('core/acl::users.users'))
                        ->setKey('widget_total_users')
                        ->setIcon('fas fa-users')
                        ->setColor('#3598dc')
                        ->setStatsTotal($users)
                        ->setRoute(route('users.index'))
                        ->init($widgets, $widgetSettings);
    }

    /**
     * @return array
     * @customized Sabari Shankar.Parthiban
     */
    public function getDataBasedOnUserPermissions($query, $model, array $selectedColumns = [], bool $list = true) {
        //        if ($model && $this->isSupportedModule(get_class($model))) {
        /**
         * @var Eloquent $model
         */
        $table = $model->getTable();
        $fillable = $model->getFillable();
        $pathInfo = \Request::getPathInfo();
        $roleIds = app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin(true, false);
        if (empty($selectedColumns)) {
            $selectedColumns = [$table . '.*'];
        }
        if (isFillableField($model, 'entity_id') && $list) {
            $selectedColumns[] = DB::raw('(select module_name from cruds where cruds.id =' . $table . '.entity_type) as entity_type_text, ' . $table . '.entity_type AS "entity_type_id"');
        }
        $user = Auth::user();
        if (($user && $user->applyDataLevelSecurity())) {
            $userentity = $user->userEntity(); 
            if(empty($userentity) && in_array('created_by',$fillable)){
				if($user->is_admin){
                 $query = $query->where($table.'.created_by',$user->id);
                if(in_array('user_id',$fillable)){
                    $query = $query->orWhere($table.'.user_id',$user->id);
                }				
				}else{
					$query = $query->where($table.'.created_by',$user->id);
				}
                return $query;        
            }
            if (isFillableField($model, 'entity_id')) {
                $query = $query->where(function($query) use ($userentity, $table) {
                    foreach ($userentity as $key => $ids) {
                        $query = $query->orWhere(function($query) use ($key, $ids, $table) {
                            $query = $query->where($table . '.entity_type', $key)
                                    ->whereIn($table . '.entity_id', $ids);
                        });
                    }
                    $query = $query->OrWhere(function($query) use($table) {
                        $query = $query->whereNull($table . '.entity_type')
                                ->orWhereNull($table . '.entity_id');
                    });
                });
            } else {
                if ($user) {
                    $loginRoles = $user->roles()->get()->pluck('id')->toArray();
                }

                if ($model && $this->isSupportedModule(get_class($model))) {
                    $query = $query
                            ->select($selectedColumns)
                            ->leftjoin('user_permissions AS UP', 'UP.reference_id', $table . '.id')
                            ->where('UP.reference_type', get_class($model))
                            ->where('UP.user_id', $user->id)
                            ->where('UP.is_retired', 0);
                }
                if (in_array('created_by',$fillable)) {
                    $roleIds = app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin(true, false);
                    if($user->is_admin){						
                    $query = $query
                            ->select($selectedColumns)
                            ->join('user_permissions AS UP', 'UP.user_id', $table . '.created_by');
                            $query = $query->where(function($query) use ($userentity,$table,$fillable,$user,$roleIds) {
                        foreach ($userentity as $key => $ids) {
                            $query = $query->orWhere(function($query) use ($key, $ids) {
                                $query = $query->where('UP.reference_key', $key)
                                        ->whereIn('UP.reference_id', $ids);
                            });
                        }                      
                        
                        $query->whereRaw($table . '.created_by = UP.user_id OR '.$table .'.created_by ='.$user->id);
                    });      
                    $query->groupBy($table.'.id');
					}else{
						$query->whereRaw($table .'.created_by ='.$user->id);
					}
                }
                if (get_class($model) == "Impiger\User\Models\User") {
                    if (empty($userentity)) {
                        return $query->where($table . '.user_id', $user->id);
                    }
                    $joinTable = (str_contains($pathInfo, 'register')) ? 'deployed_users' : 'user_permissions';
                    $joinField = (str_contains($pathInfo, 'register')) ? ['imp_user_id', 'id'] : ['user_id', 'user_id'];
                    if (!joinTableExists($query, $joinTable)) {
                        $query = $query
                                ->leftjoin($joinTable . ' AS UP', 'UP.' . $joinField[0], $table . '.' . $joinField[1]);
                    }
                    $query = $query->where(function($query) use ($userentity, $joinTable,$fillable) {
                        foreach ($userentity as $key => $ids) {
                            $query = $query->orWhere(function($query) use ($key, $ids) {
                                $query = $query->where('UP.reference_key', $key)
                                        ->whereIn('UP.reference_id', $ids);
                            });
                        }
                        
                        if (in_array('created_by', $fillable)) {
                            $query->where($table . '.created_by', 'UP.user_id');
                        }
                    });
                }
                if (in_array(get_class($model),config('core.acl.general.audit_histories_model', []))) {
                    $userIds = app(\Impiger\ACL\Roles::class)->getChildUserIdsUsingLogin(true, false);
                    if (empty($userentity)) {
                        $query =  $query->where($table . '.user_id', $user->id);
                    }
                    if($userIds){
                        $query = $query->whereIn('user_id',$userIds);
                    }                    
                    if(isVendorUser()){
                        $referenceId = getVendorIdbyLogin();
                        if($referenceId){                           
                            $query = $query->orWhere(function($query) use ($referenceId) {
                                 $query = $query->where('module', VENDOR_REQUEST_MODULE_SCREEN_NAME)
                                         ->where('reference_id', $referenceId);
                             });
                        }
                    }
                }
            }
            if ($table == 'roles') {
                $query = $query->orWhere($table . '.is_system', 1);
            }
            if (is_plugin_active('multidomain')) {
                $instituteModel = config('plugins.multidomain.general.domain_supported_entity', "");
                $joinTable = (str_contains($pathInfo, 'register')) ? 'deployed_users' : 'user_permissions';
                $joinField = (str_contains($pathInfo, 'register')) ? ['imp_user_id', 'id'] : ['user_id', 'user_id'];
                $instituteIds = app(\Impiger\Multidomain\Multidomain::class)->getInstituteByCurrentDomainId();
                if (in_array(get_class($model), config('plugins.multidomain.general.domain_supported_user', []))) {
                    $joinField[1] = ($table == 'user_permissions') ? 'id' : $joinField[1];
                    $table = ($table == 'user_permissions') ? 'users' : $table;
                    if ($joinTable == 'user_permissions') {
                        $selectedColumns = array_merge($selectedColumns, [
                            'UP.role_id as roles',
                        ]);
                    }
                    if (!empty($instituteIds)) {
                        if (!joinTableExists($query, $joinTable)) {
                            $query = $query
                                    ->leftjoin($joinTable . ' AS UP', 'UP.' . $joinField[0], $table . '.' . $joinField[1]);
                        }
                        $query = $query
                                ->where('UP.reference_type', $instituteModel)
                                ->whereIn('UP.reference_id', $instituteIds);
                    }
                }
            }
        }
        if ($user && !$user->applyDataLevelSecurity() && !$user->super_user && in_array(get_class($model),config('core.acl.general.audit_histories_model', []))) {
            $query = $query->whereNotIn($table.'.user_id', [1]);
        }
        $query = $query
                ->select($selectedColumns);
        //        }
        return $query;
    }

    /**
     * @return array
     * @customized Ramesh Esakki
     */
    public function applyRoleHierarchyCondition($query, $model, array $selectedColumns = []) {
        $user = Auth::user();
        $pathInfo = \Request::getPathInfo();
        if (!$user) {
            return $query;
        }
        if ($user && $user->super_user) {
            return $query;
        }

        if (!empty($model) && in_array(get_class($model), config('core.acl.general.role_hierarchy_supported', [])) && !str_contains($pathInfo, 'register')) {
            $roleSlug = $user->roles->pluck('slug')->toArray();
            if(in_array(SUPERADMIN_ROLE_SLUG,$roleSlug)) {
                return $query;
            } elseif(in_array(SUPERADMIN_ROLE_SLUG,$roleSlug)) {
                $query->whereNotIn('roles.slug', [SUPERADMIN_ROLE_SLUG]);
                return $query;
            }

            if (in_array(get_class($model), config('core.acl.general.user_model', [])) && !str_contains($pathInfo, 'register')) {
                $query = $query->leftJoin('audit_histories', function ($join) {
                    $join->on('audit_histories.reference_id', '=', 'impiger_users.id');
                    $join->on('audit_histories.module', '=', DB::raw('"user"'));
                    $join->on('audit_histories.action', '=', DB::raw('"created"'));
                });
            }
            if (get_class($model) == "Impiger\ACL\Models\Role") {
                $roleIds = app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin(true, false);
            } else {
                $roleIds = app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin();
            }
            
            if (isValidArray($roleIds)) {
                $query = $query->where(function ($query) use ($roleIds, $model) {
                    $query->whereIn('roles.id', $roleIds);
                    if (in_array(get_class($model), config('core.acl.general.user_model', []))) {
                        $query->orWhere('audit_histories.user_id', '=', Auth::id());
                    }
                });
            } else if (in_array(get_class($model), config('core.acl.general.user_model', []))) {
                $query->where('audit_histories.user_id', '=', Auth::id());
            }
        }
        return $query;
    }

    /*
     * @customized Sabari Shankar.Parthiban
     */

    protected function isSupportedModule($model) {
        $modelObj = new $model;
        $table = $modelObj->getTable();
        $entities = array_flip(getAppEntitiesFromSession(true));
        $supportedModuleId = Arr::get($entities, $table);
        $user = Auth::user();

        if(!$user) {
            return false;
        }
        $userentity = $user->userEntity();
        $entityIds = array_keys($userentity);

        if ($entityIds && $supportedModuleId && in_array($supportedModuleId, $entityIds)) {
            return true;
        }
        return false;
    }

}
