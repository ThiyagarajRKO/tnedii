<?php

namespace Impiger\SpokeRegistration\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use Impiger\Workflows\Traits\WorkflowProperty;

class SpokeEcells extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    use WorkflowProperty;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'spoke_ecells';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'spoke_registration_id','name','logo','description','is_enabled'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    
    
}
