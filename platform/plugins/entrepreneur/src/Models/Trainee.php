<?php

namespace Impiger\Entrepreneur\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class Trainee extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trainees';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'user_id','entrepreneur_id','division_id','financial_year_id','annual_action_plan_id','training_title_id','certificate_status','certificate_generated_at'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    
    
}
