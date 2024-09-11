<?php

namespace Impiger\MasterDetail\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class Holiday extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'holidays';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'date','title','financial_year_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    
    
}
