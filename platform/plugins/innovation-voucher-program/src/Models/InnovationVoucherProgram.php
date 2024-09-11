<?php

namespace Impiger\InnovationVoucherProgram\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class InnovationVoucherProgram extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'innovation_voucher_programs';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'application_number','voucher_type','project_title','mobile_number','email_id','created_by','updated_by','problem_of_sector','scope_objective','outcomes_deliverables','role_of_knowledge_partner','budjet','team_capability','nature_of_innovation','impact','project_need','competetive','level_of_impact','capability_capacity','collabaration_with_knowledge_partner','pitch_for_your_project','project_based','main_sector','additional_sector','duration','envisaged_timeline','project_cost','estimated_cost','presentation','attachments','is_agree','state','district_id','reference_link'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'envisaged_timeline'=>'json',
        'additional_sector'=>'json',
    ];

    public function ivp_company_details() {
                return $this->hasOne('Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails', 'innovation_voucher_program_id', 'id');
            }

	public function ivp_knowledge_partners() {
                return $this->hasOne('Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner', 'innovation_voucher_program_id', 'id');
            }

	
    
    public function innovation_voucher_programs() {
                return $this->belongsTo('Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram', 'innovation_voucher_program_id');
            }
    
}
