<?php
use Impiger\ACL\Models\Role;
if (!function_exists('get_login_background')) {
    /**
     * @return string
     */
    function get_login_background(): string
    {
        $images = setting('login_screen_backgrounds', []);

        if (is_array($images)) {
            $images = array_filter($images);
        }

        if (empty($images) || !is_array($images)) {
            return url(Arr::random(config('core.acl.general.backgrounds', [])));
        }

        $image = Arr::random($images);

        if (!$image) {
            return url(Arr::random(config('core.acl.general.backgrounds', [])));
        }

        return RvMedia::getImageUrl($image);
    }
}
/* @customized Sabari Shankar.Parthiban Start */
if (!function_exists('get_user_roles')) {
    /**
     * @param int $userId
     * @return object
     * @created Sabari Shankar.Parthiban
     */
    function get_user_roles($userId)
    {
        $userRoles = DB::table("role_users")->where("user_id",$userId)->get();
        return $userRoles;
    }
}
if (!function_exists('get_multi_role_permissions')) {

    /**
     * @param int $userId
     * @return array
     * @created Sabari Shankar.Parthiban
     */
    function get_multi_role_permissions($userId) {
        $userRoles = get_user_roles($userId);
        $multiRolePermissions = [];
        if (!empty($userRoles)) {
            $rolePermissions = [];
            foreach ($userRoles as $userRole) {
                $rolePermissions[] = Role::findOrFail($userRole->role_id)->permissions;
            }
            $multiRolePermissions = Arr::collapse($rolePermissions);
        }
        return $multiRolePermissions;
    }

}
if (!function_exists('get_user_entities')) {
    /**
     * @param int $userId
     * @return object
     * @created Sabari Shankar.Parthiban
     */
    function get_user_entities($userId)
    {
        $userEntities = DB::table("user_permissions")->where("user_id",$userId)->get();
        return $userEntities;
    }
}
if (!function_exists('get_user_mapped_entity_ids')) {
    /**
     * @param int $userId
     * @return object
     * @created Sabari Shankar.Parthiban
     */
    function get_user_mapped_entity_ids($userId)
    {
        $entityIds = [];
        $userEntities = get_user_entities($userId);
        if (!empty($userEntities)) {
            foreach ($userEntities as $entity) {
                $entityIds[] = $entity->reference_key;
            }
        }

        return $entityIds;
    }
}
if (!function_exists('get_permission_role_ids')) {
    /**
     * @param array $roleIds
     * @return object
     * @created Sabari Shankar.Parthiban
     */
    function get_permission_role_ids($roleIds)
    {
        $multiRolePermissions = [];
        if (!empty($roleIds)) {
            $rolePermissions = [];
            if(is_array($roleIds)){
                foreach ($roleIds as $role) {
                    $rolePermissions[] = Role::findOrFail($role)->permissions;
                }
            }else{
                $rolePermissions[] = Role::findOrFail($roleIds)->permissions;
            }
            
            $multiRolePermissions = Arr::collapse($rolePermissions);
        }
        return $multiRolePermissions;
    }
}
if (!function_exists('get_model_from_table')) {
    /**
     * @param string $table
     * @return object
     * @created Sabari Shankar.Parthiban
     */
    function get_model_from_table($table) {
        $cruds=DB::table('cruds')->where("module_db",$table)->first();
        if(!empty($cruds)){
            $module = $cruds->module_name;
            if($cruds->parent_id){
                $parentModule = DB::table('cruds')->where("id",$cruds->parent_id)->first();
                $module = $parentModule->module_name;
            }
            $name = $cruds->module_name;
            $modelClass = "Impiger\{Module}\Models\{Name}";
            $search = array("{Module}","{Name}");
            $replace = array(ucfirst(Str::camel($module)),ucfirst(Str::camel($name)));
            $modelClass = str_replace($search, $replace, $modelClass);
            return $modelClass;
        }else{
            foreach (get_declared_classes() as $class) {
                if (is_subclass_of($class, 'Illuminate\Database\Eloquent\Model')) {
                    $model = new $class;
                    if ($model->getTable() === $table)
                        return $class;
                }
            }
        }

        return false;
    }

}
if (!function_exists('get_entity_id')) {
    /**
     * @param string $moduleClass
     * @return object
     * @created Sabari Shankar.Parthiban
     */
    function get_entity_id($modelClass) {
        $model = new $modelClass;
            $table = $model->getTable();
            $table = "institutions";
            $entity_id = App\Models\Crud::where("module_db",$table)->first()->id;
            return $entity_id;
    }

}

if (!function_exists('get_entities')) {
    /**
     * @return object
     * @created Sabari Shankar.Parthiban
     */
    function get_entities() {
       $entities = App\Models\Crud::where("is_entity",1)->get();
       return $entities;
    }

}
if (!function_exists('get_reference_id_by_domain')) {
    /**
     * @return object
     * @created Sabari Shankar.Parthiban
     */
    function get_reference_id_by_domain() {
        $referenceId = '';
        if(!is_plugin_active('multidomain')){
            return $referenceId = '';
        }
        $domainId = app(\Impiger\Multidomain\Multidomain::class)->getCurrentAdminDomainId();

        if ($domainId) {
            $entities = get_entities();

            foreach ($entities as $entity) {
                $isDomainExist = Illuminate\Support\Facades\Schema::hasColumn($entity->module_db, 'domain_id');
                if ($isDomainExist) {
                    $referenceId = DB::table($entity->module_db)->where("domain_id", $domainId)->first();
                    if($referenceId){
                        return $referenceId->id;
                    }
                }
            }
        }

        return $referenceId;
    }

}

if (!function_exists('isSupportedModule')) {
    /**
     * @param string $module
     * @return array
     * @created Sabari Shankar.Parthiban
     */
    function isSupportedModule(string $module): bool
    {
        return in_array($module, supportedModules());
    }
}
if (!function_exists('supportedModules')) {
    /**
     * @return array
     * @created Sabari Shankar.Parthiban
     */
    function supportedModules()
    {
        return config('plugins.usergroups.general.supported', []);
    }
}
/* @customized Sabari Shankar.Parthiban End */
if (!function_exists('arrayInsertAfter')) {
    /**
     * @return array
     * @customized ramesh esakki
     */
    function arrayInsertAfter( array $array, $key, array $new ) {
        $keys = array_keys( $array );
        $index = array_search( $key, $keys );
        $pos = false === $index ? count( $array ) : $index + 1;
        return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
    }

}
