<?php

namespace Impiger\User\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleUser extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'role_users';

    /**
     * @var array
     */
    protected $fillable = [
        'role_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'role_id' =>'json'
    ];

    

}
