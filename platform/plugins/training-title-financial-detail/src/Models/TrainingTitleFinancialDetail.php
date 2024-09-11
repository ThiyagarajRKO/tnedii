<?php

namespace Impiger\TrainingTitleFinancialDetail\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class TrainingTitleFinancialDetail extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'training_title_financial_details';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'division_id','financial_year_id','annual_action_plan_id','training_title_id','budget_approved','actual_expenditure','edi_admin_cost','revenue_generated','neft_cheque_no','neft_cheque_date','updated_by'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    
}
