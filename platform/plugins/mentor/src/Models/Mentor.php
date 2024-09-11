<?php

namespace Impiger\Mentor\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class Mentor extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mentors';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'user_id','entrepreneur_id','name','email','password','vendor_id','district_id','industry_id','specialization_id','experience_id','last_use_id','proficiency_level_id','qualification_id','achivements','resume','status_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('mentors.*')->where('mentors.id',$this->id)->first();
	}
}
