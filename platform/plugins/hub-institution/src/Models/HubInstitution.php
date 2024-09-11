<?php

namespace Impiger\HubInstitution\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class HubInstitution extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hub_institutions';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'hub_type_id','hub_code','name','address','phone_no','year_of_establishment','pincode','email','accreditations','city','website','district','is_enabled'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    
}
