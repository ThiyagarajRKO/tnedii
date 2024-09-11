<?php

namespace Impiger\MasterDetail\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class Village extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'village';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'name','parish_id','is_enabled'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    
    
}
