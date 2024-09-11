<?php

namespace Impiger\MasterDetail\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class District extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'district';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'name','code','country_id','region_id','is_enabled'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    
    public function join_fields(){ 
	return $this->select('district.*')->where('district.id',$this->id)->first();
	}
}
