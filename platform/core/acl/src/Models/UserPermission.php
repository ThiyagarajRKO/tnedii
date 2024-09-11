<?php

namespace Impiger\ACL\Models;

use Illuminate\Support\Facades\Auth;
use Impiger\Base\Models\BaseModel;

class UserPermission extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_permissions';
    
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'reference_id',
        'reference_type',
        'reference_key',
        'role_id',
        'role_permissions',
        'is_retired',
        'retire_after_restore'
    ];
    
    protected $casts = [
        'role_id' =>  'array','role_permissions' => 'json'
    ];

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    /**
     * @param string $value
     * @return array
     */
    public function getRolePermissionsAttribute($value)
    {
        try {
            return json_decode($value, true) ?: [];
        } catch (Exception $exception) {
            return [];
        }
    }
    /**
     * Set mutator for the "permissions" attribute.
     *
     * @param array $permissions
     * @return void
     */
    public function setRolePermissionsAttribute(array $permissions)
    {
        $this->attributes['role_permissions'] = $permissions ? json_encode($permissions) : '';
    }

    /**
     * @customized by @Ramesh.Esakki
     * @param string $entityType
     * @param string $userId
     * @return array
     */
    public static function getUserEntityId($entityType, $userId)
    {
        try {
            return self::where([
                'user_id' => $userId,
                'reference_type'     => $entityType,
            ])->select('reference_id')->pluck('reference_id')->toArray();
        } catch (\Exception $exception) {
            return [];
        }
    }
}
