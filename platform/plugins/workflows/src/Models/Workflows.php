<?php

namespace Impiger\Workflows\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;

class Workflows extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workflows';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'module_controller',
        'module_property',
        'initial_state',
        'status',
        'permission_specific_to',
        'is_enabled'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    /**
     * The users that belong to the role.
     */
    public function workflowPermissions()
    {
        $cndns = [
            'reference_type' => 'Impiger\Institution\Models\Institution',
            'reference_id' => 1
        ];
        return $this->hasMany(WorkflowPermission::class)->where($cndns);
    }

	public function transitions() {
        return $this->hasMany('Impiger\Workflows\Models\WorkflowTransition', 'workflow_id', 'id');
    }
	public function workflow_meta_data() {
        return $this->hasMany('Impiger\Workflows\Models\WorkflowMetaData', 'workflow_id', 'id');
    }
}
