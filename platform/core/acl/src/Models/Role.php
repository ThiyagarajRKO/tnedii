<?php

namespace Impiger\ACL\Models;

use Impiger\ACL\Traits\PermissionTrait;
use Impiger\Base\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
/* @Customized By Sabari Shankar Parthiban Start */
use Illuminate\Database\Eloquent\SoftDeletes;
/* @Customized By Sabari Shankar Parthiban End */

class Role extends BaseModel
{
    use PermissionTrait;
    /* @Customized By Sabari Shankar Parthiban Start */
        use SoftDeletes;
    /* @Customized By Sabari Shankar Parthiban End */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

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
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'permissions',
        'description',
        'is_default',
        'created_by',
        'updated_by',
        /* @Customized By Sabari Shankar.Parthiban */
        'is_admin',
        'is_system',
        'entity_type',
        'entity_id',
        'is_enabled',
        /* @Customized By Ramesh.Esakki */
        'child_roles',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'permissions' => 'json',
        'child_roles' => 'json',
    ];

    /**
     * @param string $value
     * @return array
     */
    public function getPermissionsAttribute($value)
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
    public function setPermissionsAttribute(array $permissions)
    {
        $this->attributes['permissions'] = $permissions ? json_encode($permissions) : '';
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        if ($this->exists) {
            $this->users()->detach();
        }

        return parent::delete();
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users', 'role_id', 'user_id')->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }
}
