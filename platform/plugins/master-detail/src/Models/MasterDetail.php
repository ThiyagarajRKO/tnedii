<?php

namespace Impiger\MasterDetail\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterDetail extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attribute_options';

    /**
     * @var array
     */
    protected $fillable = [
        'attribute','name','slug'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    

}
