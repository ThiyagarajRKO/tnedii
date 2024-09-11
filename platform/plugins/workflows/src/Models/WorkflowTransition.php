<?php

namespace Impiger\Workflows\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;

class WorkflowTransition extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workflow_transitions';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'from_state',
        'to_state',
        'action_complete_label',
        'action',
        'icon',
        'color',
        'custom_input',
        'is_notification_enabled',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
