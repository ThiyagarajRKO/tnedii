<?php

namespace Impiger\InnovationVoucherProgram\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class IvpKnowledgePartner extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ivp_knowledge_partner_details';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'organization_type','organization_name','contact_person','designation','mobile_number','email_id','responsibilities','attachment'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    public function innovation_voucher_programs() {
                return $this->belongsTo('Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram', 'innovation_voucher_program_id');
            }
    public function join_fields(){ 
	return $this->select('ivp_knowledge_partner_details.*')->where('ivp_knowledge_partner_details.id',$this->id)->first();
	}
}
