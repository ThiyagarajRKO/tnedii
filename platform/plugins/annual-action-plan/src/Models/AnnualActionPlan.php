<?php

namespace Impiger\AnnualActionPlan\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class AnnualActionPlan extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'annual_action_plan';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'name','financial_year_id','division_id','officer_incharge_designation_id','duration','no_of_batches','budget_per_program','total_budget','batch_size','training_module','created_by','remarks'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('annual_action_plan.*')->where('annual_action_plan.id',$this->id)->first();
	}
}
