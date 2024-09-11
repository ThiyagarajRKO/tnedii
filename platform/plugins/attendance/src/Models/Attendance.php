<?php

namespace Impiger\Attendance\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class Attendance extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attendance';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'financial_year_id','attendance_date','present','absent','annual_action_plan_id','training_title_id','entrepreneur_id','remark'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    #{belongsToFn}
    public function join_fields(){ 
	return $this->select('attendance.id','attendance.financial_year_id',DB::raw('IFNULL(attendance.annual_action_plan_id,T.annual_action_plan_id) AS annual_action_plan_id'),DB::raw('IFNULL(attendance.training_title_id,T.training_title_id) AS training_title_id'),'attendance.attendance_date','attendance.present','attendance.absent','attendance.created_at','attendance.updated_at','attendance.deleted_at','E.id AS entrepreneur_id','attendance.annual_action_plan_id','attendance.training_title_id')->rightJoin('entrepreneurs AS E','attendance.entrepreneur_id','=','E.id')->join('trainees AS T','T.entrepreneur_id','=','E.id')->join('training_title AS TT','TT.id','=','T.training_title_id')->whereRaw('TT.training_start_date <= NOW()')->whereRaw('TT.training_end_date_time > NOW()')->groupBy('attendance.entrepreneur_id')->where('attendance.id',$this->id)->first();
	}
}
