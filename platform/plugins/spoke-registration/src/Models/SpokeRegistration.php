<?php

namespace Impiger\SpokeRegistration\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use Impiger\Workflows\Traits\WorkflowProperty;

class SpokeRegistration extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    use WorkflowProperty;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'spoke_registration';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'name','stream_of_institution','category','affiliation','hub_institution_id','year_of_establishment','locality_type','institute_state','program_level','has_incubator','address','pin_code','city','district_id','phone_no','email','website','advisory_commitee','department_faculty_coordinators','location_of_e_cell','availability_space','internet','telephone','budget','is_enabled'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'advisory_commitee' =>'json','department_faculty_coordinators' =>'json'
    ];

    
    
    
    public function join_fields(){ 
	return $this->select('spoke_registration.*')->where('spoke_registration.id',$this->id)->first();
	}
}
