<?php

namespace Impiger\Vendor\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class Vendor extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vendors';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'user_id','name','pia_constitution_id','pia_mainactivity_id','email','password','contact_number','phone','address','district_id','pincode','date_of_establishment','name_principal','seating_capacity','audio_video','rest_dining','access_distance','accommodation_facility','refreshment_provision','experience_year','achivements','profile'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('vendors.*')->where('vendors.id',$this->id)->first();
	}
}
