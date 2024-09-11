<?php

namespace Impiger\MsmeCandidateDetails\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class MsmeCandidateDetails extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'msme_candidate_details';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'scheme','candidate_msme_ref_id','candidate_name','care_of','father_husband_name','spouse_name','gender','mobile_no','email','dob','qualification','district_id','address','is_enrolled','photo','category','enroll_start_date','enroll_to_date'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    /**
     * @return string
     */
//    public function getPhotoAttribute($value)
//    {
//        $iMimeType = array('tif','tiff','webp','svg','png','jpeg','jpg','gif','bmp','avif');
//        
//        if($value){
//            $fileExtention = substr($value, strrpos($value, '.') + 1);
//            if(in_array($fileExtention,$iMimeType)){
//                $value="/storage/".$value;
//            }else{
//                $value = $value;
//            }
//        }
//        return $value;
//    }
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('msme_candidate_details.*')->where('msme_candidate_details.id',$this->id)->first();
	}
}
