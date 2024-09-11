<?php

namespace Impiger\Workflows\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;

class TransistionConfig extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workflow_transition_config';

    /**
     * @var array
     */
    protected $fillable = [
        'workflow_permission_id',
        'attachment_type',
        'attachment_content',
    ];

    
}
