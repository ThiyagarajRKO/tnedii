<?php

namespace Impiger\TrainingTitle\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class TrainingTitle extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'training_title';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'division_id','financial_year_id','annual_action_plan_id','name','code','venue','email','phone','vendor_id','officer_incharge_designation_id','fee_paid','fee_amount','private_workshop','training_start_date','training_end_date','webinar_link','small_content','description','footer_note','left_signature','left_signature_name','left_signature_file','middle_signature','middle_signature_name','middle_signature_file','right_signature','right_signature_name','right_signature_file'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('training_title.*')->where('training_title.id',$this->id)->first();
	}
}
