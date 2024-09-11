<?php

namespace Impiger\IncubationCenter\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class IncubationCenter extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'incubation_centers';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'district_id','center_name','manager_name','establishment_date','no_of_incubatees'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('incubation_centers.*')->where('incubation_centers.id',$this->id)->first();
	}
}
