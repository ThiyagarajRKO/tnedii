<?php

namespace Impiger\Attendance\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class AttendanceRemark extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attendance_remarks';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'training_title_id','entrepreneur_id','remark','created_by'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    
    
}
