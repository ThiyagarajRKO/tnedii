<?php

namespace Impiger\TnsiStartup\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use Impiger\Workflows\Traits\WorkflowProperty;

class TnsiStartup extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    use WorkflowProperty;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tnsi_startup';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'region_id','hub_institution_id','spoke_registration_id','team_members','name','idea_about','is_your_idea','about_startup','problem_of_address','solution_of_problem','unique_selling_proposition','revenue_stream','description','duration','is_won','pitch_training','is_incubated','demo_link','about_tnsi','is_enabled','created_by'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'team_members' =>'json'
    ];

    
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('tnsi_startup.*','SR.district_id','D.region_id')->leftJoin('spoke_registration AS SR','SR.id','=','tnsi_startup.spoke_registration_id')
                ->leftJoin('district AS D','D.id','=','SR.district_id')->where('tnsi_startup.id',$this->id)->first();
	}
}
