<?php

namespace Impiger\InnovationVoucherProgram\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class IvpCompanyDetails extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ivp_company_details';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'company_name','designation','company_address','company_classification','website','certificate','registration_number','registration_date','annual_turnover','no_of_employees'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    public function innovation_voucher_programs() {
                return $this->belongsTo('Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram', 'innovation_voucher_program_id');
            }
    
}
