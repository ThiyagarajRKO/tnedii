<?php

namespace Impiger\Entrepreneur\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class Entrepreneur extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'entrepreneurs';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'user_id','email','password','prefix_id','name','care_of','father_name','gender_id','dob','aadhaar_no','mobile','physically_challenged','address','district_id','pincode','religion_id','community','candidate_type_id','student_type_id','student_school_name','student_standard_name','student_college_name','student_course_name','hub_institution_id','student_year','spoke_registration_id','qualification_id','entrepreneurial_category_id','activity_name','photo_path','scheme','msme_candidate_detail_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('entrepreneurs.*')->where('entrepreneurs.id',$this->id)->first();
	}
}
