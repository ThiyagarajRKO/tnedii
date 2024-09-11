<?php

namespace Impiger\Workflows\Models;

use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Models\BaseModel;

class WorkflowPermission extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workflow_permissions';

    /**
     * @var array
     */
    protected $fillable = [
        'workflows_id',
        'reference_id',
        'reference_type',
        'transition',
        'workflows_id',
        'user_permissions'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
        'user_permissions' => 'json'
    ];
    
    public function configs() {
                return $this->hasMany(TransistionConfig::class, 'workflow_permission_id', 'id');
            }
}
