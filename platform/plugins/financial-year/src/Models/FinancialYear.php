<?php

namespace Impiger\FinancialYear\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class FinancialYear extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'financial_year';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'session_year','session_start','session_end','is_running','description','is_enabled'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    public function join_fields(){ 
        return $this->select('financial_year.*')->where('financial_year.id',$this->id)->first();
    }
    
    
    #{belongsToFn}
    
}
