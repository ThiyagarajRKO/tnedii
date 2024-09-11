<?php

namespace Impiger\BackendMenu\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class BackendMenu extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'backend_menus';

    /**
     * @var array
     */
    protected $fillable = [
        'menu_id','parent_id','name','url','icon','priority','permissions','target','active'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    

}
