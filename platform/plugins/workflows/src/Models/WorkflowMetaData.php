<?php

namespace Impiger\Workflows\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;

class WorkflowMetaData extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workflow_meta_data';

    /**
     * @var array
     */
    protected $fillable = [
        'workflow_id','transition_name','meta_data'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
        'meta_data' => 'json'
    ];
    public function workflows() {
                return $this->belongsTo('Impiger\Workflows\Models\Workflows', 'workflow_id');
            }
}
