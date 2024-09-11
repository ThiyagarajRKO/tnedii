<?php

namespace Impiger\ACL\Models;

use Impiger\ACL\Notifications\ResetPasswordNotification;
use Impiger\ACL\Traits\PermissionTrait;
use Impiger\Base\Supports\Avatar;
use Impiger\Media\Models\MediaFile;
use Exception;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RvMedia;
/* @Customized By Sabari Shankar Parthiban Start */
use Illuminate\Database\Eloquent\SoftDeletes;
/* @Customized By Sabari Shankar Parthiban End */

/**
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use PermissionTrait;
    use Notifiable;
    /* @Customized By Sabari Shankar Parthiban Start */
        use SoftDeletes;
    /* @Customized By Sabari Shankar Parthiban End */

    /**
     * {@inheritDoc}
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'first_name',
        'last_name',
        'password',
        'avatar_id',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
     * @var array
     */
    protected $casts = [
        'permissions' => 'json',
    ];

    /**
     * Always capitalize the first name when we retrieve it
     * @param string $value
     * @return string
     */
    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Always capitalize the last name when we retrieve it
     * @param string $value
     * @return string
     */
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * @return string
     * @deprecated since v5.15
     */
    public function getFullName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /**
     * @return BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo(MediaFile::class)->withDefault();
    }

    /**
     * @return UrlGenerator|string
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar->url ? RvMedia::url($this->avatar->url) : (new Avatar)->create($this->name)->toBase64();
    }

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
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * @return boolean
     */
    public function isSuperUser()
    {
        return $this->super_user || $this->hasAccess(ACL_ROLE_SUPER_USER);
    }

    /**
     * @param string $permission
     * @return boolean
     */
    public function hasPermission($permission)
    {
        if ($this->isSuperUser()) {
            return true;
        }

        return $this->hasAccess($permission);
    }

    /**
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions)
    {
        if ($this->isSuperUser()) {
            return true;
        }

        return $this->hasAnyAccess($permissions);
    }

    /**
     * @return array
     */
    public function authorAttributes()
    {
        return [
            'name'   => $this->name,
            'email'  => $this->email,
            'avatar' => $this->avatar_url,
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Returns the activations relationship.
     *
     * @return HasMany
     */
    public function activations()
    {
        return $this->hasMany(Activation::class, 'user_id');
    }

    /**
     * {@inheritDoc}
     */
    public function inRole($role)
    {
        $roleId = null;
        if ($role instanceof Role) {
            $roleId = $role->getKey();
        }

        foreach ($this->roles as $instance) {
            /**
             * @var Role $instance
             */
            if ($role instanceof Role) {
                if ($instance->getKey() === $roleId) {
                    return true;
                }
            } elseif ($instance->getKey() == $role || $instance->slug == $role) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        if ($this->exists) {
            $this->activations()->delete();
            $this->roles()->detach();
        }

        return parent::delete();
    }
    /* @customized Sabari Shankar.Parthiban start */
    /**
     * @return HasMany
     * @customized Sabari Shankar.Parthiban
     */
    public function userPermission(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }

    /*
     * @customized Sabari Shankar.Parthiban
     */
    public function userEntity()
    {
        $entity = [];
        $userPermissions = $this->userPermission;
        $roleSlug = $this->roles()->pluck('slug')->toArray();


        if (!empty($userPermissions)) {
            foreach ($userPermissions as $userPermission) {
                $entity[$userPermission->reference_key][] = $userPermission->reference_id;
            }
        }
        return $this->entity = $entity;
    }

    /*
     * @customized Ramesh.Esakki
     */
    public function getUserSpecificEntity($entityType = null)
    {
        $entity = [];
        if (!$entityType) {
            return $entity;
        }

        $entityDetail = \App\Models\Crud::where('is_entity', 1)->select(['id', 'module_db'])->where('module_name', $entityType)->first();

        if (!$entityDetail) {
            return $entity;
        }

        $userPermissions = $this->userPermission;

        if(!empty($userPermissions)) {
            foreach ($userPermissions as $userPermission) {
                $entity[$userPermission->reference_key][] = $userPermission->reference_id;
            }
        }

        $specEntIds = \Arr::get($entity, $entityDetail->id);
        $specEntIds = ($specEntIds) ? $specEntIds : [];
        $whereRaw = get_common_condition($entityDetail->module_db);
        $query = \DB::table($entityDetail->module_db)->select(['id','name']);
        if($whereRaw){
            $query=$query->whereRaw($whereRaw);
        }
        if($this->applyDataLevelSecurity()){
            $query=$query->whereIn('id', $specEntIds);
        }

        return $query->get();
    }

    /**
     * Get related agent tickets.
     *  @customized Sabari Shankar.Parthiban
     */
    public function assigneeOpenTickets()
    {
        if (is_plugin_active('ticketing-system')) {
            return $this->hasMany(\Impiger\TicketingSystem\Models\TicketingSystem::class, 'assign_to')->where('state', TICKET_INITIAL_STATE);
        }
    }
    /* Check is admin role user */
    public function getisAdminAttribute()
    {
        $referenceId = get_reference_id_by_domain();
        if (empty($referenceId)) {
            foreach ($this->roles as $role) {
                if ($role->is_admin) {
                    return $role->is_admin;
                }
            }
        }
        return false;
    }

    public function getroleIdsAttribute(){
        $roleIds =[];
        foreach ($this->roles as $role) {
            $roleIds[]=$role->id;
        }
        $referenceId = get_reference_id_by_domain();
        if (!empty($referenceId)) {
            $userPermissions = $this->userPermission()->where("reference_id", $referenceId)->first();
            if (!empty($userPermissions)) {
                $roleIds = $userPermissions->role_id;
            }
        }
        return $roleIds;
    }

    public function applyDataLevelSecurity()
    {
        if(!setting('enable_dls')){
            return false;
        }

        if ($this->super_user != 1 && (isset($this->is_admin) && $this->is_admin != 1)) {
            return true;
        }
        return false;
    }
    /*
     * check if the user have admin role like TVET_admin or institute_admin or organization_admin
     */
    public function admins(){
        if ($this->super_user == 1 || (isset($this->is_admin) && $this->is_admin == 1) || str_contains($this->roles->pluck('slug'),'admin')) {
            return true;
        }
        return false;
    }
    /**
     * @customized Sabari Shankar.Parthiban end
     */


    public function getStudentProfileIdUsingLogin()
    {
        $id = \Auth::id();
        if ($id) {
            return \Impiger\Student\Models\Student::where('user_id', $id)->pluck('id')->first();
        }
        return false;
    }
}
