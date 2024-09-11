<?php

namespace Impiger\MasterDetail\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class Subcounty extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subcounty';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'name','county_id','is_enabled'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    
    
}
