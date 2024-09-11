<?php

namespace Impiger\Usergroups\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usergroups extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'usergroups';
    
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
        'description',
        'roles',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'roles' => 'json',
    ];
    /**
     * @param string $value
     * @return array
     */
    public function getRolesAttribute($value)
    {
        try {
            return json_decode($value, true) ?: [];
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * Set mutator for the "roles" attribute.
     *
     * @param array $roles
     * @return void
     */
    public function setRolesAttribute(array $roles)
    {
        $this->attributes['roles'] = $roles ? json_encode($roles) : '';
    }
}
