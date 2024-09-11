<?php

namespace Impiger\TrainingTitle\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class OnlineTrainingSession extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;
    
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'online_training_sessions';
    
    
    /**
     * @var array
     */
    protected $fillable = [
        'header','title','sub_title','url','type'
    ];

    /**
     * @var array
     */
    protected $casts = [
        
    ];

    
    
    
    
}
